<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PersonalWhatsAppSetting;
use App\Models\User;
use App\Services\PersonalWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PersonalWhatsAppController extends Controller
{
    public function show(Request $request)
    {
        PersonalWhatsAppSetting::ensureTable();
        $branchId = $this->branchId($request);
        $row = PersonalWhatsAppService::forBranch($branchId);
        $live = PersonalWhatsAppService::liveStatus($row);

        return response()->json([
            'status' => true,
            'data' => PersonalWhatsAppService::publicPayload($row, $live),
        ]);
    }

    public function save(Request $request)
    {
        PersonalWhatsAppSetting::ensureTable();

        $validator = Validator::make($request->all(), [
            'send_method' => 'nullable|in:api,personal',
            'instance_id' => 'nullable|string|max:100',
            'api_token' => 'nullable|string|max:500',
            'api_url' => 'nullable|string|max:255',
            'media_url' => 'nullable|string|max:255',
            'message_template' => 'nullable|string|max:4000',
            'send_to_party' => 'nullable',
            'copy_to_self' => 'nullable',
            'attach_pdf' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $branchId = $this->branchId($request);
        $row = PersonalWhatsAppService::forBranch($branchId);

        if ($request->filled('send_method')) {
            $row->send_method = $request->send_method;
        }
        // Free Baileys bridge — ignore legacy Wasender tokens from older UI.
        if ($request->boolean('connect') || ($request->input('send_method') === 'personal' && $request->boolean('init_session'))) {
            $row->send_method = PersonalWhatsAppSetting::SEND_PERSONAL;
            $row->save();
            $connected = PersonalWhatsAppService::connect($row);
            $live = PersonalWhatsAppService::liveStatus($row);
            if (! empty($connected['qr'])) {
                $live['qr'] = $connected['qr'];
            }

            return response()->json([
                'status' => (bool) (($connected['success'] ?? true) || ! empty($connected['qr']) || ! empty($connected['connected'])),
                'message' => $connected['message'] ?? 'Personal WhatsApp connect started.',
                'data' => array_merge(PersonalWhatsAppService::publicPayload($row, $live), [
                    'qr' => $connected['qr'] ?? ($live['qr'] ?? null),
                    'already_logged' => ! empty($connected['connected']),
                ]),
            ], (! empty($connected['bridge_online']) || ! empty($connected['qr']) || ! empty($connected['connected'])) ? 200 : 422);
        }
        if ($request->exists('message_template')) {
            $row->message_template = (string) $request->message_template;
        }
        if ($request->exists('send_to_party')) {
            $row->send_to_party = $this->toBool($request->send_to_party);
        }
        if ($request->exists('copy_to_self')) {
            $row->copy_to_self = $this->toBool($request->copy_to_self);
        }
        if ($request->exists('attach_pdf')) {
            $row->attach_pdf = $this->toBool($request->attach_pdf);
        }
        $row->save();

        $live = PersonalWhatsAppService::liveStatus($row);

        return response()->json([
            'status' => true,
            'message' => 'Personal WhatsApp settings saved.',
            'data' => PersonalWhatsAppService::publicPayload($row, $live),
        ]);
    }

    public function status(Request $request)
    {
        $branchId = $this->branchId($request);
        $row = PersonalWhatsAppService::forBranch($branchId);
        $live = PersonalWhatsAppService::liveStatus($row);

        return response()->json([
            'status' => true,
            'data' => PersonalWhatsAppService::publicPayload($row, $live),
        ]);
    }

    public function qr(Request $request)
    {
        $branchId = $this->branchId($request);
        $row = PersonalWhatsAppService::forBranch($branchId);
        $qr = PersonalWhatsAppService::qr($row);

        if (! ($qr['success'] ?? false)) {
            return response()->json([
                'status' => false,
                'message' => $qr['message'] ?? 'Could not load QR.',
            ], 422);
        }

        return response()->json([
            'status' => true,
            'data' => $qr,
        ]);
    }

    public function logout(Request $request)
    {
        $branchId = $this->branchId($request);
        $row = PersonalWhatsAppService::forBranch($branchId);
        $result = PersonalWhatsAppService::logout($row);
        $live = PersonalWhatsAppService::liveStatus($row);

        return response()->json([
            'status' => (bool) ($result['success'] ?? false),
            'message' => $result['message'] ?? 'Logged out.',
            'data' => PersonalWhatsAppService::publicPayload($row, $live),
        ]);
    }

    private function toBool($value): bool
    {
        return in_array($value, [true, 1, '1', 'true', 'on', 'yes'], true);
    }

    private function branchId(Request $request): int
    {
        $user = Auth::guard('api')->user();
        $selectedSubAdminId = $request->query('selectedSubAdminId', $request->input('selectedSubAdminId'));
        $branchIdFilter = $request->query('branch_id', $request->input('branch_id'));

        if (! empty($selectedSubAdminId) && $selectedSubAdminId !== 'null') {
            return (int) $selectedSubAdminId;
        }
        if (! empty($branchIdFilter)) {
            return (int) $branchIdFilter;
        }

        if (($user->role ?? '') === 'staff') {
            return (int) ($user->branch_id ?? $user->id);
        }

        if (($user->role ?? '') === 'admin' && $selectedSubAdminId) {
            $subAdmin = User::find($selectedSubAdminId);
            if ($subAdmin) {
                return (int) $subAdmin->id;
            }
        }

        return (int) $user->id;
    }
}
