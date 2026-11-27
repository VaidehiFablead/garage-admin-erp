<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsappSessionService;

class CheckWhatsappServer extends Command
{
    protected $signature = 'whatsapp:check-server {--restart : Force restart the node server if stopped}';

    protected $description = 'Check WhatsApp QR & session bridge Node.js server health status and test auto-restart';

    public function handle(WhatsappSessionService $sessionService): int
    {
        $this->info('=================================================');
        $this->info(' WhatsApp Session Server (Node.js) Diagnostic');
        $this->info('=================================================');

        $serverDir = base_path('whatsapp-session-server');
        $serverFile = $serverDir . '/server.js';
        $logFile = $serverDir . '/server.log';

        if (!file_exists($serverFile)) {
            $this->error("server.js not found at: {$serverFile}");
            return Command::FAILURE;
        }
        $this->line("Directory: <comment>{$serverDir}</comment>");
        $this->line('server.js: <info>Found</info>');

        $nodeBinary = $this->resolveNodeBinary();
        if ($nodeBinary && ($nodeBinary === 'node' || @file_exists($nodeBinary))) {
            $this->line("Node Binary: <info>{$nodeBinary}</info>");
        } else {
            $this->line('Node Binary: <error>NOT FOUND in PATH or standard directories</error>');
        }

        $isBridgeOnline = false;
        try {
            $isBridgeOnline = $sessionService->isServiceOnline();
        } catch (\Throwable $e) {
            $isBridgeOnline = false;
        }

        $socketPath = '/tmp/wa_session.sock';
        $socketExists = file_exists($socketPath);
        if ($socketExists) {
            $this->line("UNIX Socket: <info>ONLINE / FOUND</info> ({$socketPath})");
        } else {
            $this->line("UNIX Socket: <comment>Not found</comment> ({$socketPath})");
        }

        $tcpUrl = 'http://127.0.0.1:3001/health';
        $tcpStatus = false;
        $tcpData = null;

        try {
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 2,
                    'ignore_errors' => true,
                    'header' => 'x-session-secret: ' . config('services.whatsapp_session.secret', 'wa_session_secret_key_2026') . "\r\n",
                ],
            ]);
            $res = @file_get_contents($tcpUrl, false, $ctx);
            if ($res) {
                $decoded = json_decode($res, true);
                if (isset($decoded['status']) && $decoded['status'] === 'ok') {
                    $tcpStatus = true;
                    $tcpData = $decoded;
                }
            }
        } catch (\Throwable $e) {
            $tcpStatus = false;
        }

        if ($tcpStatus) {
            $uptime = isset($tcpData['uptime']) ? round($tcpData['uptime']) . 's' : 'N/A';
            $sessions = $tcpData['active_sessions'] ?? 0;
            $this->line("TCP Port 3001: <info>ONLINE</info> (Uptime: {$uptime}, Active Sessions: {$sessions})");
        } else {
            $this->line('TCP Port 3001: <comment>Blocked/Offline (Normal on some shared hosts; traffic may use UNIX socket)</comment>');
        }

        if ($isBridgeOnline) {
            $this->line('Laravel API Bridge: <info>CONNECTED & OPERATIONAL</info>');
        } else {
            $this->line('Laravel API Bridge: <error>DISCONNECTED</error>');
        }

        $processFound = false;
        $pids = [];
        if (function_exists('exec')) {
            if (PHP_OS_FAMILY === 'Windows') {
                @exec('tasklist /FI "IMAGENAME eq node.exe" 2>NUL', $pids);
                if (count($pids) > 2) {
                    $processFound = true;
                    $this->line('Process: <info>Running (node.exe detected on Windows)</info>');
                }
            } else {
                @exec('pgrep -f "whatsapp-session-server/server.js" 2>/dev/null', $pids);
                if (empty($pids)) {
                    @exec('pgrep -f "server.js" 2>/dev/null', $pids);
                }
                $pids = array_values(array_filter($pids, function ($p) {
                    return (int) $p !== getmypid();
                }));
                if (!empty($pids)) {
                    $processFound = true;
                    $this->line('Process: <info>Running</info> (PIDs: ' . implode(', ', $pids) . ')');
                }
            }
        }

        if (!$processFound) {
            $this->line('Process: <comment>No active server.js process found</comment>');
        }

        $isHealthy = $isBridgeOnline || ($socketExists && $processFound) || $tcpStatus;
        $forceRestart = (bool) $this->option('restart');

        $this->newLine();
        if ($isHealthy && !$forceRestart) {
            $this->info('WhatsApp Session Bridge is HEALTHY and operational!');
        } else {
            if ($forceRestart) {
                $this->warn('Force restart requested via [--restart]. Stopping and reloading server.js...');
            } else {
                $this->error('WhatsApp Session Bridge is DOWN / OFFLINE!');
            }

            if ($forceRestart || $this->confirm('Would you like to start the Node server now?', true)) {
                $this->line("Starting Node server in background using [{$nodeBinary}]...");
                if (function_exists('exec')) {
                    if (PHP_OS_FAMILY === 'Windows') {
                        if (file_exists($socketPath)) {
                            @unlink($socketPath);
                        }
                        @pclose(@popen("start /B node \"{$serverDir}\\server.js\" >> \"{$serverDir}\\server.log\" 2>&1", 'r'));
                    } else {
                        @exec('pkill -9 -f "whatsapp-session-server/server.js" 2>/dev/null');
                        usleep(500000);
                        if (file_exists($socketPath)) {
                            @unlink($socketPath);
                        }
                        $startCmd = "cd {$serverDir} && export PATH=\"\$PATH:/usr/local/bin:/usr/bin:~/.nvm/versions/node/$(ls ~/.nvm/versions/node 2>/dev/null | tail -n 1)/bin\"; nohup {$nodeBinary} server.js >> {$serverDir}/server.log 2>&1 &";
                        @exec($startCmd);
                    }
                    sleep(2);
                    $this->info('Start command executed. Testing connection...');

                    $retest = false;
                    try {
                        $retest = $sessionService->isServiceOnline();
                    } catch (\Throwable $e) {
                        $retest = false;
                    }

                    if ($retest) {
                        $this->info('SUCCESS! Node server is now ONLINE & Bridge is CONNECTED!');
                    } else {
                        $this->warn('Bridge not responding yet. Check recent logs below.');
                    }
                } else {
                    $this->error("exec() is disabled in PHP. Run via SSH: cd {$serverDir} && nohup node server.js &");
                }
            }
        }

        if (file_exists($logFile)) {
            $this->newLine();
            $this->line("<comment>Recent Logs ({$logFile}):</comment>");
            $lines = file($logFile);
            $lastLines = array_slice($lines, -8);
            foreach ($lastLines as $l) {
                $this->line('  ' . trim($l));
            }
        }

        $this->newLine();
        return ($isBridgeOnline || $tcpStatus) ? Command::SUCCESS : Command::FAILURE;
    }

    protected function resolveNodeBinary(): string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return 'node';
        }

        $which = '';
        if (\function_exists('shell_exec')) {
            $which = trim((string) @\shell_exec('which node 2>/dev/null'));
        } elseif (\function_exists('exec')) {
            $out = [];
            @\exec('which node 2>/dev/null', $out);
            $which = trim((string) ($out[0] ?? ''));
        }
        if (!empty($which) && @is_executable($which)) {
            return $which;
        }

        $candidates = [
            '/usr/local/bin/node',
            '/usr/bin/node',
            '/bin/node',
        ];

        $home = getenv('HOME') ?: ('/home/' . (get_current_user() ?: ''));
        if ($home) {
            $nvm = @glob($home . '/.nvm/versions/node/*/bin/node');
            if (!empty($nvm)) {
                $candidates = array_merge($nvm, $candidates);
            }
            $candidates[] = $home . '/local/bin/node';
            $candidates[] = $home . '/.local/bin/node';
            $candidates[] = $home . '/bin/node';
        }

        $alt = @glob('/opt/alt/alt-nodejs*/root/usr/bin/node');
        if (!empty($alt)) {
            $candidates = array_merge($alt, $candidates);
        }

        foreach ($candidates as $bin) {
            if (!empty($bin) && @file_exists($bin) && @is_executable($bin)) {
                return $bin;
            }
        }

        return 'node';
    }
}
