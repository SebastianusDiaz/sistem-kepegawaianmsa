<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentAttachment extends Model
{
    protected $fillable = [
        'assignment_id',
        'file_path',
        'file_type', // 'photo' or 'document'
        'mime_type',
        'size',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}
