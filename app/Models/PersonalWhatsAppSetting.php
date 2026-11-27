<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PersonalWhatsAppSetting extends Model
{
    protected $table = 'personal_whatsapp_settings';

    public const SEND_API = 'api';
    public const SEND_PERSONAL = 'personal';

    public const DEFAULT_TEMPLATE = "Greetings from {Firm_Name}\nWe are pleased to have you as a valuable customer. Please find the details of your transaction.\n\n{Transaction_Type} :\nInvoice Amount: {Invoice_Amount}\nBalance: {Transaction_Balance}\n\nThanks for doing business with us.\nRegards,\n{Firm_Name}";

    protected $fillable = [
        'branch_id',
        'send_method',
        'instance_id',
        'api_token',
        'api_url',
        'media_url',
        'connected_phone',
        'message_template',
        'send_to_party',
        'copy_to_self',
        'attach_pdf',
    ];

    protected $hidden = [
        'api_token',
    ];

    protected $casts = [
        'send_to_party' => 'boolean',
        'copy_to_self' => 'boolean',
        'attach_pdf' => 'boolean',
    ];

    public static function ensureTable(): void
    {
        if (Schema::hasTable('personal_whatsapp_settings')) {
            return;
        }

        try {
            Schema::create('personal_whatsapp_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('branch_id')->unique();
                $table->string('send_method', 20)->default('api');
                $table->string('instance_id')->nullable();
                $table->text('api_token')->nullable();
                $table->string('api_url')->nullable();
                $table->string('media_url')->nullable();
                $table->string('connected_phone')->nullable();
                $table->text('message_template')->nullable();
                $table->boolean('send_to_party')->default(true);
                $table->boolean('copy_to_self')->default(false);
                $table->boolean('attach_pdf')->default(true);
                $table->timestamps();
            });
        } catch (\Throwable $e) {
            Log::error('Could not create personal_whatsapp_settings table', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function isPersonalSelected(): bool
    {
        return ($this->send_method ?: self::SEND_API) === self::SEND_PERSONAL;
    }

    public function hasCredentials(): bool
    {
        // Free local Baileys: session id is enough. Legacy Wasender used api_token.
        return ! empty($this->instance_id) || ! empty($this->api_token);
    }

    public function sessionId(): string
    {
        if (! empty($this->instance_id) && str_starts_with((string) $this->instance_id, 'wa_sess_')) {
            return (string) $this->instance_id;
        }

        return 'wa_sess_branch_'.(int) $this->branch_id;
    }

    public function templateText(): string
    {
        $text = trim((string) $this->message_template);

        return $text !== '' ? $this->message_template : self::DEFAULT_TEMPLATE;
    }
}
