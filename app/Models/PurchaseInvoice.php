<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;
    protected $table    = 'purchase_invoice'; // Specify the table name if different from the model name
    protected $fillable = [
        'branch_id',
        'invoice_number',
        'vendor_id',
        'bill_no',
        'products',
        'total_amount',
        'paid',
        'discount',
        'shipping',
        'grand_total',
        'remaining_amount',
        'gst_option',
        'status',
        'taxes',
        'isDeleted',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'products' => 'array', // Automatically convert JSON to array
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
    public function tax()
    {
        return $this->belongsTo(TaxRate::class, 'tax_rate_id');
    }
    public function purchases()
    {
        return $this->hasMany(Purchases::class, 'invoice_id');
    }
    public function payments()
    {
        return $this->hasMany(PaymentStore::class, 'purchase_id')
            ->where('isDeleted', 0);
    }

    /**
     * Ensure bill_no can store alphanumeric strings (e.g. 'INV-77197189') across tenant databases.
     */
    public static function ensureBillNoColumnIsString(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        try {
            $col = \Illuminate\Support\Facades\DB::selectOne("
                SELECT DATA_TYPE 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                  AND TABLE_NAME = 'purchase_invoice' 
                  AND COLUMN_NAME = 'bill_no'
            ");

            if ($col && in_array(strtolower($col->DATA_TYPE), ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'mediumint'])) {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `purchase_invoice` MODIFY `bill_no` VARCHAR(255) NULL");
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Could not alter purchase_invoice.bill_no: ' . $e->getMessage());
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = Carbon::now('Asia/Kolkata');
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });

        static::updating(function ($model) {
            $model->updated_at = Carbon::now('Asia/Kolkata');
        });

        static::saving(function ($model) {
            if (!empty($model->bill_no) && !is_numeric($model->bill_no)) {
                try {
                    $col = \Illuminate\Support\Facades\DB::selectOne("
                        SELECT DATA_TYPE 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                          AND TABLE_NAME = 'purchase_invoice' 
                          AND COLUMN_NAME = 'bill_no'
                    ");
                    if ($col && in_array(strtolower($col->DATA_TYPE), ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'mediumint'])) {
                        $digits = preg_replace('/\D/', '', (string) $model->bill_no);
                        $model->bill_no = $digits !== '' ? (int) substr($digits, 0, 9) : null;
                    }
                } catch (\Throwable) {}
            }
        });
    }
}
