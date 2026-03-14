<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assignment extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'editor_id',
        'reporter_id',
        'kerjasama_id',
        'title',
        'slug',
        'description',
        'location_name',
        'latitude',
        'longitude',
        'start_time',
        'deadline',
        'status',
        'priority',
        'evidence_link',
        'evidence_photo',
        'evidence_document',
        'staff_response_file',
        'staff_response_note',
        'staff_response_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'deadline' => 'datetime',
        'staff_response_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function kerjasama()
    {
        return $this->belongsTo(Kerjasama::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(Absensi::class);
    }
}
