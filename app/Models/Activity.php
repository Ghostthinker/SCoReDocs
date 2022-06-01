<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'section_id', 'message', 'type', 'message_id'
    ];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user_read()
    {
        return $this->belongsToMany(User::class, 'user_read_activities', 'activity_id', 'user_id')->withTimestamps();
    }
}
