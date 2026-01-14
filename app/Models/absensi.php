<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class absensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_type',
        'assignment_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'legacy_status',
        'status',
        'lat',
        'lng',
        'accuracy',
        'worked_minutes',
        'note',
        'keterangan',
        'evidence_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function scopeOpenOverdue($query)
    {
        return $query->where('status', 'open')
            ->where('created_at', '<', now()->subDay());
    }

    public function scopeAutoClosed($query)
    {
        return $query->where('status', 'auto_closed');
    }
}
