<?php

namespace App\Services;

use App\Models\PersonalWhatsAppSetting;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Free Personal WhatsApp via local Baileys bridge (whatsapp-session-server),
 * same approach as whatsapp-bulk-laravel — no paid Cloud API / Wasender.
 */
class PersonalWhatsAppService
{
    public const BASE_URL = 'local-baileys';

    public static function forBranch(int $branchId): PersonalWhatsAppSetting
    {
        PersonalWhatsAppSetting::ensureTable();

        return PersonalWhatsAppSetting::firstOrCreate(
            ['branch_id' => $branchId],
            [
                'send_method' => PersonalWhatsAppSetting::SEND_API,
                'message_template' => PersonalWhatsAppSetting::DEFAULT_TEMPLATE,
                'send_to_party' => true,
                'copy_to_self' => false,
                'attach_pdf' => true,
                'api_url' => self::BASE_URL,
            ]
        );
    }

    public static function isPersonalSelected(int $branchId): bool
    {
        PersonalWhatsAppSetting::ensureTable();

        $row = PersonalWhatsAppSetting::where('branch_id', $branchId)->first();

        return $row ? $row->isPersonalSelected() : false;
    }

    public static function publicPayload(PersonalWhatsAppSetting $row, ?array $live = null): array
    {
        return [
            'id' => $row->id,
            'branch_id' => $row->branch_id,
            'send_method' => $row->send_method ?: PersonalWhatsAppSetting::SEND_API,
            'has_token' => $row->hasCredentials(),
            'connected_phone' => $live['phone'] ?? $row->connected_phone,
            'message_template' => $row->templateText(),
            'send_to_party' => (bool) $row->send_to_party,
            'copy_to_self' => (bool) $row->copy_to_self,
            'attach_pdf' => (bool) $row->attach_pdf,
            'connected' => (bool) ($live['connected'] ?? false),
            'state' => $live['state'] ?? 'notAuthorized',
            'bridge_online' => (bool) ($live['bridge_online'] ?? false),
            'qr' => $live['qr'] ?? null,
            'message' => $live['message'] ?? null,
        ];
    }

    private static function bridge(): WhatsappSessionService
    {
        return app(WhatsappSessionService::class);
    }

    public static function ensureSessionRow(PersonalWhatsAppSetting $row): PersonalWhatsAppSetting
    {
        $sessionId = $row->sessionId();
        if ($row->instance_id !== $sessionId || $row->api_url !== self::BASE_URL) {
            $row->instance_id = $sessionId;
            $row->api_url = self::BASE_URL;
            if (empty($row->api_token) || $row->api_token === 'wasender') {
                $row->api_token = 'local-bridge';
            }
            $row->save();
        }

        return $row;
    }

    public static function connect(PersonalWhatsAppSetting $row): array
    {
        $row->send_method = PersonalWhatsAppSetting::SEND_PERSONAL;
        $row = self::ensureSessionRow($row);

        $bridge = self::bridge();
        if (! $bridge->isServiceOnline()) {
            $restart = $bridge->restartServer();
            if (! ($restart['bridgeOnline'] ?? false) && ! $bridge->isServiceOnline()) {
                return [
                    'success' => false,
                    'message' => 'WhatsApp bridge is offline. On the server run: cd whatsapp-session-server && npm install && npm start',
                    'bridge_online' => false,
                ];
            }
        }

        $init = $bridge->initSession($row->sessionId());
        if (($init['status'] ?? '') === 'error') {
            return [
                'success' => false,
                'message' => $init['message'] ?? 'Could not start WhatsApp session.',
                'bridge_online' => true,
            ];
        }

        // Brief wait for QR generation
        $status = $init;
        for ($i = 0; $i < 8; $i++) {
            $status = $bridge->getSessionStatus($row->sessionId());
            if (! empty($status['qr']) || ($status['status'] ?? '') === 'connected' || ! empty($status['is_connected'])) {
                break;
            }
            usleep(500000);
        }

        return self::mapBridgeStatus($row, $status, true);
    }

    public static function liveStatus(PersonalWhatsAppSetting $row): array
    {
        $bridge = self::bridge();
        $online = $bridge->isServiceOnline();
        if (! $online) {
            return [
                'connected' => false,
                'state' => 'bridgeOffline',
                'phone' => $row->connected_phone,
                'bridge_online' => false,
                'qr' => null,
                'message' => 'WhatsApp bridge offline. Start whatsapp-session-server (npm start).',
            ];
        }

        if (! $row->isPersonalSelected()) {
            return [
                'connected' => false,
                'state' => 'notConfigured',
                'phone' => null,
                'bridge_online' => true,
                'qr' => null,
                'message' => 'Select Personal WhatsApp, then click Connect.',
            ];
        }

        $row = self::ensureSessionRow($row);
        $status = $bridge->getSessionStatus($row->sessionId());

        return self::mapBridgeStatus($row, $status, false);
    }

    private static function mapBridgeStatus(PersonalWhatsAppSetting $row, array $status, bool $fromConnect): array
    {
        $state = strtolower((string) ($status['status'] ?? 'unknown'));
        $connected = ! empty($status['is_connected'])
            || in_array($state, ['connected', 'open', 'ready'], true);
        $phone = $status['phone'] ?? null;
        if (is_string($phone) && $phone !== '') {
            $phone = preg_replace('/[^0-9]/', '', $phone) ?: $phone;
        } else {
            $phone = $row->connected_phone;
        }

        if ($connected && $phone && $phone !== $row->connected_phone) {
            $row->connected_phone = $phone;
            $row->save();
        }
        if (! $connected && $fromConnect === false && empty($status['qr'])) {
            // keep stored phone for display until logout
        }

        $qr = $status['qr'] ?? null;
        if (is_string($qr) && $qr !== '' && ! str_starts_with($qr, 'data:image')) {
            $qr = 'data:image/png;base64,'.$qr;
        }

        return [
            'success' => true,
            'connected' => $connected,
            'already_logged' => $connected,
            'state' => $connected ? 'authorized' : ($qr ? 'qr_ready' : $state),
            'phone' => $phone,
            'bridge_online' => true,
            'qr' => $connected ? null : $qr,
            'message' => $connected
                ? 'Connected'
                : ($qr ? 'Scan the QR from WhatsApp → Linked devices.' : ($status['message'] ?? 'Waiting for QR…')),
        ];
    }

    public static function qr(PersonalWhatsAppSetting $row): array
    {
        $row->send_method = PersonalWhatsAppSetting::SEND_PERSONAL;
        $connect = self::connect($row);
        if (! ($connect['success'] ?? true) && empty($connect['qr']) && empty($connect['connected'])) {
            return [
                'success' => false,
                'message' => $connect['message'] ?? 'Could not load QR code.',
            ];
        }

        if (! empty($connect['connected'])) {
            return [
                'success' => true,
                'already_logged' => true,
                'connected' => true,
                'phone' => $connect['phone'] ?? $row->connected_phone,
                'message' => 'Already connected.',
            ];
        }

        if (! empty($connect['qr'])) {
            return [
                'success' => true,
                'already_logged' => false,
                'qr' => $connect['qr'],
                'message' => $connect['message'] ?? null,
            ];
        }

        $live = self::liveStatus($row);
        if (! empty($live['connected'])) {
            return [
                'success' => true,
                'already_logged' => true,
                'connected' => true,
                'phone' => $live['phone'] ?? $row->connected_phone,
                'message' => 'Already connected.',
            ];
        }
        if (! empty($live['qr'])) {
            return [
                'success' => true,
                'already_logged' => false,
                'qr' => $live['qr'],
            ];
        }

        return [
            'success' => false,
            'message' => $live['message'] ?? $connect['message'] ?? 'Could not load QR code.',
        ];
    }

    public static function logout(PersonalWhatsAppSetting $row): array
    {
        $row = self::ensureSessionRow($row);
        $bridge = self::bridge();
        if ($bridge->isServiceOnline()) {
            $bridge->deleteSession($row->sessionId());
        }
        $row->connected_phone = null;
        $row->save();

        return [
            'success' => true,
            'message' => 'Logged out.',
        ];
    }

    public static function sendSaleMessage(
        int $branchId,
        string $toPhone,
        array $vars,
        ?array $invoiceMeta = null
    ): array {
        $row = self::forBranch($branchId);
        if (! $row->isPersonalSelected()) {
            return ['success' => false, 'skipped' => true, 'message' => 'Personal WhatsApp is not selected.'];
        }

        $live = self::liveStatus($row);
        if (! ($live['connected'] ?? false)) {
            return ['success' => false, 'message' => 'Personal WhatsApp is not connected. Click Connect and scan the QR.'];
        }

        $text = self::fillTemplate($row->templateText(), $vars);
        $results = [];

        if ($row->send_to_party) {
            $results[] = self::deliver($row, $toPhone, $text, $invoiceMeta, (bool) $row->attach_pdf);
        }

        if ($row->copy_to_self && ! empty($live['phone'])) {
            $results[] = self::deliver($row, (string) $live['phone'], $text, $invoiceMeta, (bool) $row->attach_pdf);
        }

        $ok = collect($results)->contains(fn ($r) => ! empty($r['success']));
        $pdfOk = collect($results)->contains(fn ($r) => ! empty($r['pdf']));

        return [
            'success' => $ok,
            'pdf' => $pdfOk,
            'message' => $ok
                ? ($pdfOk ? 'WhatsApp sent with PDF.' : (data_get($results, '0.message') ?: 'Message sent.'))
                : (data_get($results, '0.message') ?: 'Send failed.'),
            'results' => $results,
        ];
    }

    public static function fillTemplate(string $template, array $vars): string
    {
        $map = [
            '{Firm_Name}' => (string) ($vars['firm_name'] ?? ''),
            '{Transaction_Type}' => (string) ($vars['transaction_type'] ?? 'Sale Invoice'),
            '{Invoice_Amount}' => (string) ($vars['invoice_amount'] ?? ''),
            '{Transaction_Balance}' => (string) ($vars['transaction_balance'] ?? ''),
            '{Invoice_No}' => (string) ($vars['invoice_no'] ?? ''),
            '{Customer_Name}' => (string) ($vars['customer_name'] ?? ''),
        ];

        return strtr($template, $map);
    }

    public static function saleVars($order, $customer, int $branchId, ?array $invoiceMeta = null): array
    {
        $setting = Setting::select('name')->where('branch_id', $branchId)->first();
        $total = number_format((float) ($order->total_amount ?? 0), 2);
        $pending = $total;
        if (isset($invoiceMeta['pending'])) {
            $pending = number_format((float) $invoiceMeta['pending'], 2);
        } elseif (isset($order->pending_amount)) {
            $pending = number_format((float) $order->pending_amount, 2);
        }

        return [
            'firm_name' => $setting->name ?? 'Company',
            'transaction_type' => 'Sale Invoice',
            'invoice_amount' => $total,
            'transaction_balance' => $pending,
            'invoice_no' => (string) ($order->order_number ?? $order->id),
            'customer_name' => (string) ($customer->name ?? 'Customer'),
        ];
    }

    private static function deliver(PersonalWhatsAppSetting $row, string $phone, string $text, ?array $invoiceMeta, bool $attachPdf): array
    {
        $to = self::toNumber($phone);
        if (! $to) {
            return ['success' => false, 'message' => 'Invalid phone number.'];
        }

        $sessionId = $row->sessionId();
        $bridge = self::bridge();

        if ($attachPdf) {
            $pdf = self::sendPdfDocument($row, $to, $text, $invoiceMeta);
            if (! empty($pdf['success'])) {
                return ['success' => true, 'message' => 'Sent with PDF.', 'pdf' => true];
            }
            Log::warning('Personal WhatsApp PDF send failed; falling back to text', [
                'to' => $to,
                'error' => $pdf['message'] ?? 'unknown',
            ]);
        }

        $msg = $bridge->sendMessage($sessionId, $to, $text);
        if (! ($msg['success'] ?? false)) {
            return [
                'success' => false,
                'pdf' => false,
                'message' => $msg['message'] ?? 'Send failed.',
            ];
        }

        if ($attachPdf) {
            $fileLabel = (string) data_get($invoiceMeta, 'file_name', 'Sale Invoice.pdf');
            $pdf = self::sendPdfDocument($row, $to, $fileLabel, $invoiceMeta);
            if (! empty($pdf['success'])) {
                return ['success' => true, 'message' => 'WhatsApp sent with PDF.', 'pdf' => true];
            }

            return [
                'success' => true,
                'pdf' => false,
                'message' => 'Message sent, but the PDF could not be attached.',
            ];
        }

        return ['success' => true, 'pdf' => false, 'message' => 'Message sent.'];
    }

    private static function sendPdfDocument(PersonalWhatsAppSetting $row, string $to, string $caption, ?array $invoiceMeta): array
    {
        $fileName = (string) data_get($invoiceMeta, 'file_name', 'sales_invoice.pdf');
        if (! str_ends_with(strtolower($fileName), '.pdf')) {
            $fileName .= '.pdf';
        }
        $fileName = preg_replace('/[^A-Za-z0-9._-]/', '_', $fileName) ?: 'sales_invoice.pdf';
        $caption = mb_substr($caption, 0, 1024);

        $bytes = self::invoicePdfBytes($invoiceMeta);
        if ($bytes === null || $bytes === '') {
            return ['success' => false, 'message' => 'Invoice PDF file was not found.'];
        }

        $dataUri = 'data:application/pdf;base64,'.base64_encode($bytes);
        $res = self::bridge()->sendDocument($row->sessionId(), $to, $dataUri, $fileName, $caption, 'application/pdf');

        if ($res['success'] ?? false) {
            return ['success' => true, 'message' => 'Sent with PDF.'];
        }

        return ['success' => false, 'message' => $res['message'] ?? 'Could not attach invoice PDF.'];
    }

    public static function servePublicInvoice(string $token, string $file)
    {
        $file = basename($file);
        if (! preg_match('/^invoice_[\w.-]+\.pdf$/i', $file)) {
            abort(404);
        }
        if (! hash_equals(self::invoicePublicToken($file), $token)) {
            abort(404);
        }

        $path = Storage::disk('public')->path('sales-invoices/'.$file);
        if (! is_file($path)) {
            $path = public_path('wa-invoices'.DIRECTORY_SEPARATOR.$file);
        }
        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$file.'"',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    private static function invoicePublicToken(string $fileName): string
    {
        return substr(hash_hmac('sha256', $fileName, (string) config('app.key')), 0, 24);
    }

    private static function invoicePdfBytes(?array $invoiceMeta): ?string
    {
        $relative = (string) data_get($invoiceMeta, 'relative_path', '');
        if ($relative !== '' && Storage::disk('public')->exists($relative)) {
            return Storage::disk('public')->get($relative);
        }

        $local = (string) data_get($invoiceMeta, 'local_path', '');
        if ($local !== '' && is_file($local)) {
            $bytes = file_get_contents($local);

            return $bytes === false ? null : $bytes;
        }

        $fileName = (string) data_get($invoiceMeta, 'file_name', '');
        if ($fileName !== '') {
            $publicCopy = public_path('wa-invoices'.DIRECTORY_SEPARATOR.$fileName);
            if (is_file($publicCopy)) {
                $bytes = file_get_contents($publicCopy);

                return $bytes === false ? null : $bytes;
            }
        }

        return null;
    }

    public static function toNumber(string $phone): ?string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '91'.substr($digits, 1);
        }

        if (strlen($digits) < 10 || strlen($digits) > 15) {
            return null;
        }

        return $digits;
    }
}
