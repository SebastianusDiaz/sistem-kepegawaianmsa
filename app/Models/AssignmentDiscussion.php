<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentDiscussion extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'message',
        'file_path',
        'link',
        'type',
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
