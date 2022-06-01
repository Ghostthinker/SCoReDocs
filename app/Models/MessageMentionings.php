<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageMentionings
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $user_id
 * @property int|null $message_id
 * @property int|null $project_id
 * @property string|null $read_at
 * @property-read \App\Models\Message|null $message
 * @property-read \App\Models\Project|null $project
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereReadAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageMentionings whereUserId($value)
 * @mixin \Eloquent
 */
class MessageMentionings extends Model
{
    protected $fillable = [
      'user_id', 'message_id', 'read_at', 'project_id'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function message()
    {
        return $this->belongsTo(Message::class);
    }
}
