<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class WhatsappSessionService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected int $timeout;
    protected ?string $socketPath;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.whatsapp_session.url', env('WHATSAPP_SESSION_URL', 'http://127.0.0.1:3001')), '/');
        $this->secretKey = config('services.whatsapp_session.secret', env('WHATSAPP_SESSION_SECRET', 'wa_session_secret_key_2026'));
        $this->timeout = (int) env('WHATSAPP_SESSION_TIMEOUT', 15);
        $this->socketPath = env('WHATSAPP_SESSION_SOCKET', base_path('whatsapp-session-server/whatsapp.sock'));
    }

    protected function resolveSocketPath(): ?string
    {
        $explicit = env('WHATSAPP_SESSION_SOCKET');
        if (!empty($explicit) && strlen($explicit) < 100 && file_exists($explicit)) {
            return $explicit;
        }

        // Linux AF_UNIX has a hard limit of 108 bytes on sockaddr_un.sun_path.
        // Deep shared-hosting directory paths exceed this limit and cause cURL error 6.
        // Use short paths in /tmp/ to guarantee it stays well within the 108-byte limit.
        $candidates = [
            '/tmp/wa_session.sock',
            '/tmp/whatsapp.sock',
            '/tmp/whatsapp_session.sock',
            $explicit,
            base_path('services/whatsapp-session-server/whatsapp'),
            base_path('services/whatsapp-session-server/whatsapp.sock'),
            base_path('whatsapp-session-server/whatsapp'),
            base_path('whatsapp-session-server/whatsapp.sock'),
        ];

        foreach ($candidates as $candidate) {
            if (!empty($candidate) && strlen($candidate) < 100 && file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Internal request dispatcher with automatic UNIX socket / TCP fallback.
     */
    protected function request(string $method, string $endpoint, array $data = [], ?int $timeout = null)
    {
        $timeout = $timeout ?? $this->timeout;
        $socketPath = $this->resolveSocketPath();

        // 1. Try UNIX domain socket if a socket candidate file exists
        if (!empty($socketPath)) {
            try {
                $client = Http::timeout($timeout)
                    ->withHeaders($this->getHeaders())
                    ->withOptions([
                        'curl' => [
                            CURLOPT_UNIX_SOCKET_PATH => $socketPath,
                        ],
                    ]);

                $url = 'http://localhost' . $endpoint;
                return $this->executeHttpMethod($client, $method, $url, $data);
            } catch (Exception $e) {
                // Stale socket file is common after Node crash; clean up and fall back to TCP quietly.
                if (file_exists($socketPath) && (str_contains($e->getMessage(), 'cURL error 7') || str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'Could not connect'))) {
                    @unlink($socketPath);
                }
                Log::debug("WhatsappSessionService UNIX socket ({$socketPath}) unavailable; trying TCP ({$this->baseUrl}).");
            }
        }

        // 2. Fallback to TCP (baseUrl, e.g. http://127.0.0.1:3001)
        try {
            $client = Http::timeout($timeout)->withHeaders($this->getHeaders());
            $url = "{$this->baseUrl}{$endpoint}";
            return $this->executeHttpMethod($client, $method, $url, $data);
        } catch (Exception $e) {
            Log::warning("WhatsappSessionService TCP fallback ({$this->baseUrl}{$endpoint}) failed: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Helper to execute GET, POST, or DELETE HTTP calls.
     */
    protected function executeHttpMethod($client, string $method, string $url, array $data = [])
    {
        $method = strtolower($method);
        if ($method === 'post') {
            return $client->post($url, $data);
        } elseif ($method === 'delete') {
            return $client->delete($url, $data);
        } else {
            return $client->get($url, $data);
        }
    }

    /**
     * Check if the session service is reachable.
     */
    public function isServiceOnline(): bool
    {
        try {
            $response = $this->request('get', '/health', [], 5);
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Initialize or start a WhatsApp session.
     */
    public function initSession(string $sessionId): array
    {
        try {
            $response = $this->request('post', '/sessions/init', [
                'session_id' => $sessionId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => $response->json('message') ?? 'Failed to initialize session on bridge',
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService initSession error: {$e->getMessage()}", ['session_id' => $sessionId]);
            return [
                'status' => 'error',
                'message' => "WhatsApp Session Service is unreachable. Please ensure the Node service is running.",
            ];
        }
    }

    /**
     * Get live status of a WhatsApp session (QR code, connected, or disconnected).
     */
    public function getSessionStatus(string $sessionId): array
    {
        try {
            $response = $this->request('get', "/sessions/{$sessionId}/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => $response->json('message') ?? 'Failed to fetch session status',
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService getSessionStatus error: {$e->getMessage()}", ['session_id' => $sessionId]);
            return [
                'status' => 'error',
                'message' => "WhatsApp Session Service is unreachable.",
            ];
        }
    }

    /**
     * Disconnect / Logout and delete a WhatsApp session.
     */
    public function deleteSession(string $sessionId): array
    {
        try {
            $response = $this->request('delete', "/sessions/{$sessionId}");
            return $response->json() ?? ['status' => true];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService deleteSession error: {$e->getMessage()}", ['session_id' => $sessionId]);
            return [
                'status' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Fetch list of groups for a connected session.
     */
    public function getGroups(string $sessionId): array
    {
        try {
            $response = $this->request('get', "/sessions/{$sessionId}/groups", [], 30);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to fetch groups from WhatsApp account',
                'groups' => [],
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService getGroups error: {$e->getMessage()}", ['session_id' => $sessionId]);
            return [
                'status' => false,
                'message' => "Error communicating with session service: {$e->getMessage()}",
                'groups' => [],
            ];
        }
    }

    /**
     * Fetch participants of a specific group.
     */
    public function getGroupParticipants(string $sessionId, string $groupJid): array
    {
        try {
            $response = $this->request('get', "/sessions/{$sessionId}/groups/" . urlencode($groupJid) . "/participants", [], 30);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => false,
                'message' => $response->json('message') ?? 'Failed to fetch group participants',
                'participants' => [],
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService getGroupParticipants error: {$e->getMessage()}", [
                'session_id' => $sessionId,
                'group_jid' => $groupJid
            ]);
            return [
                'status' => false,
                'message' => "Error fetching group participants: {$e->getMessage()}",
                'participants' => [],
            ];
        }
    }

    /**
     * Send a text message via connected WhatsApp session.
     */
    public function sendMessage(string $sessionId, string $to, string $text): array
    {
        try {
            $cleanPhone = preg_replace('/\D/', '', $to);
            $response = $this->request('post', "/sessions/{$sessionId}/send-message", [
                'to' => $cleanPhone,
                'text' => $text,
            ], 20);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'status' => 'sent',
                    'message_id' => $data['message_id'] ?? null,
                    'to' => $cleanPhone,
                    'data' => $data,
                ];
            }

            $statusCode = $response->status();
            $rawBody = $response->body();
            $jsonMessage = $response->json('message') ?? $response->json('error');
            $errorMessage = $jsonMessage ?: ("HTTP {$statusCode}: " . Str::limit(strip_tags($rawBody), 180));

            Log::warning("WhatsappSessionService sendMessage failed: [HTTP {$statusCode}] {$errorMessage}", [
                'session_id' => $sessionId,
                'to' => $cleanPhone,
                'status_code' => $statusCode,
                'response_snippet' => Str::limit($rawBody, 300),
            ]);

            return [
                'success' => false,
                'status' => 'failed',
                'http_status' => $statusCode,
                'message' => $errorMessage,
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService sendMessage exception: {$e->getMessage()}", [
                'session_id' => $sessionId,
                'to' => $to,
            ]);
            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a document (PDF) via connected WhatsApp session.
     */
    public function sendDocument(string $sessionId, string $to, string $base64OrDataUri, string $fileName = 'document.pdf', string $caption = '', string $mimeType = 'application/pdf'): array
    {
        try {
            $cleanPhone = preg_replace('/\D/', '', $to);
            $response = $this->request('post', "/sessions/{$sessionId}/send-document", [
                'to' => $cleanPhone,
                'media' => $base64OrDataUri,
                'fileName' => $fileName,
                'caption' => $caption,
                'mimetype' => $mimeType,
            ], 60);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'status' => 'sent',
                    'message_id' => $data['message_id'] ?? null,
                    'to' => $cleanPhone,
                    'data' => $data,
                ];
            }

            $statusCode = $response->status();
            $rawBody = $response->body();
            $jsonMessage = $response->json('message') ?? $response->json('error');
            $errorMessage = $jsonMessage ?: ("HTTP {$statusCode}: " . Str::limit(strip_tags($rawBody), 180));

            return [
                'success' => false,
                'status' => 'failed',
                'http_status' => $statusCode,
                'message' => $errorMessage,
            ];
        } catch (Exception $e) {
            Log::error("WhatsappSessionService sendDocument exception: {$e->getMessage()}", [
                'session_id' => $sessionId,
                'to' => $to,
            ]);

            return [
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Restart the WhatsApp Node.js session server process.
     */
    public function restartServer(): array
    {
        $serverDir = base_path('whatsapp-session-server');
        $serverFile = $serverDir . '/server.js';
        $socketPath = '/tmp/wa_session.sock';

        if (!file_exists($serverFile)) {
            return [
                'success' => false,
                'message' => "server.js not found at: {$serverFile}",
                'bridgeOnline' => false,
            ];
        }

        try {
            // First attempt graceful restart via API if online
            if ($this->isServiceOnline()) {
                try {
                    $this->request('post', '/restart', [], 2);
                    usleep(600000);
                } catch (\Throwable $e) {}
            }

            if (function_exists('exec')) {
                if (PHP_OS_FAMILY === 'Windows') {
                    if (file_exists($socketPath)) {
                        @unlink($socketPath);
                    }
                    @pclose(@popen("start /B node \"{$serverDir}\\server.js\" >> \"{$serverDir}\\server.log\" 2>&1", "r"));
                } else {
                    @exec('pkill -9 -f "server.js" 2>/dev/null');
                    usleep(500000);
                    if (file_exists($socketPath)) {
                        @unlink($socketPath);
                    }

                    $nodeBin = 'node';
                    $which = '';
                    if (\function_exists('shell_exec')) {
                        $which = trim((string) @\shell_exec('which node 2>/dev/null'));
                    } elseif (\function_exists('exec')) {
                        $out = [];
                        @\exec('which node 2>/dev/null', $out);
                        $which = trim((string) ($out[0] ?? ''));
                    }
                    if (!empty($which) && @is_executable($which)) {
                        $nodeBin = $which;
                    } else {
                        $home = getenv('HOME') ?: ('/home/' . (get_current_user() ?: 'u378554361'));
                        $candidates = array_merge(
                            $home ? (@glob($home . '/.nvm/versions/node/*/bin/node') ?: []) : [],
                            ['/usr/local/bin/node', '/usr/bin/node', '/bin/node'],
                            @glob('/opt/alt/alt-nodejs*/root/usr/bin/node') ?: []
                        );
                        rsort($candidates);
                        foreach ($candidates as $cand) {
                            if (@file_exists($cand) && @is_executable($cand)) {
                                $nodeBin = $cand;
                                break;
                            }
                        }
                    }

                    $startCmd = "cd {$serverDir} && export PATH=\"\$PATH:/usr/local/bin:/usr/bin:~/.nvm/versions/node/$(ls ~/.nvm/versions/node 2>/dev/null | tail -n 1)/bin\"; nohup {$nodeBin} server.js >> {$serverDir}/server.log 2>&1 &";
                    @exec($startCmd);
                }
            }

            // Give the process 2 seconds to bind ports/sockets
            sleep(2);
            $isOnline = $this->isServiceOnline();

            return [
                'success' => $isOnline,
                'message' => $isOnline 
                    ? 'WhatsApp Session Bridge restarted successfully and is online.' 
                    : 'Restart command executed, but service has not responded yet. Please check server logs.',
                'bridgeOnline' => $isOnline,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Error restarting WhatsApp session bridge: ' . $e->getMessage(),
                'bridgeOnline' => false,
            ];
        }
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'X-Session-Secret' => $this->secretKey,
        ];
    }
}
