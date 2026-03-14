<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kerjasama extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'start_date',
        'end_date',
        'representative_name',
        'representative_phone',
        'representative_email',
        'pic_id',
        'file_path',
        'status',
        'rejection_note',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '>=', now()->toDateString());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date >= now()->toDateString();
    }
}
