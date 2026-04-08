<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DrivingLicense extends Model
{
    protected $fillable = [
        'customer_id',
        'invoice_id',
        'license_number',
        'grade',
        'issue_date',
        'expiry_date',
        'serial_number',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public static $statuses = [
        'active' => 'Active',
        'expired' => 'Expired',
        'suspended' => 'Suspended',
    ];

    /**
     * Get the customer that owns the license.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the invoice associated with the license.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the user who created the license.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if license is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Get formatted issue date.
     */
    public function getFormattedIssueDate(): string
    {
        return $this->issue_date->format('d/m/Y');
    }

    /**
     * Get formatted expiry date.
     */
    public function getFormattedExpiryDate(): ?string
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : null;
    }

    /**
     * Generate unique serial number.
     */
    public static function generateSerialNumber(): string
    {
        do {
            $serial = str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('serial_number', $serial)->exists());
        
        return $serial;
    }

    /**
     * Generate unique license number.
     */
    public static function generateLicenseNumber(): string
    {
        do {
            $license = 'KDL-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
        } while (self::where('license_number', $license)->exists());
        
        return $license;
    }
}
