<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageReads
 *
 * @property int $id
 * @property int $user_id
 * @property int $message_id
 * @property int|null $section_id
 * @property int $project_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Message $message
 * @property-read \App\Models\Project $project
 * @property-read \App\Models\Section|null $section
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads query()
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MessageReads whereUserId($value)
 * @mixin \Eloquent
 */
class MessageReads extends Model
{
    protected $fillable = [
      'user_id', 'message_id', 'section_id', 'project_id'
    ];

    public function user ()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function section ()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function project ()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function message ()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }

}
