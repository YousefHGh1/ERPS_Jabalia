<?php

namespace App\Models;


use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CraftLicense extends Model
{
    use HasFactory, SoftDeletes;
    // Auditable trait disabled due to local environment issues
    // use Auditable;

    protected $fillable = [
        'file_number',
        'tax_number',
        'status',
        'last_license',
        'id_number',
        'phone_number',
        'owner_name',
        'property_owner',
        'property_owner_id',
        'ownership_entity',
        'first_license_year',
        'classification',
        'craft_type',
        'detailed_craft_type',
        'registration_date',
        'last_payment_date',
        'holiday_days',
        'licensed_operator',
        'is_licensed',
        'commercial_name',
        'neighborhood',
        'main_street',
        'address',
        'nearest_mosque',
        'gis_building_number',
        'street_number',
        'floor_number',
        'unit_number',
        'total_debts',
        'craft_fees',
        'payment_amount',
        'signboard_fees',
        'discount_percent',
        'remaining',
        'remaining_years',
        'advertisement_board',
        'number_of_stores',
        'board_in_meters',
        'final_price',
        'notes',
    ];

    protected $casts = [
        'total_debts' => 'decimal:2',
        'craft_fees' => 'decimal:2',
        'payment_amount' => 'decimal:2',
        'signboard_fees' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'remaining' => 'decimal:2',
        'final_price' => 'decimal:2',
        'registration_date' => 'date',
        'last_payment_date' => 'date',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function partners()
    {
        return $this->hasMany(CraftLicensePartner::class);
    }

    public function attachments()
    {
        return $this->hasMany(CraftLicenseAttachment::class);
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        if ($eventName == 'created') {
            $eventName = 'إنشاء';
        } elseif ($eventName == 'updated') {
            $eventName = 'تعديل';
        } elseif ($eventName == 'deleted') {
            $eventName = 'حذف';
        }
        return " تم {$eventName} رخصة حرفة ";
    }
    protected static function auditLogName(): string
    {
        return 'craft_licenses';
    }

    protected function auditAttributes(): array
    {
        return [
            'file_number', 'owner_name', 'commercial_name', 'status', 'craft_type', 'remaining',
        ];
    }

}