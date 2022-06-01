<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array $data
 * @property int $user_id
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUserId($value)
 * @mixin \Eloquent
 * @property \App\Models\Section|null $section
 * @property int $project
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereProject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSection($value)
 * @property int|null $section_id
 * @property int|null $parent_id
 * @property int|null $at_all_mentioning
 * @property-read Message|null $parent
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereAtAllMentioning($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Message whereSectionId($value)
 */
class Message extends Model
{
    protected $fillable = [
        'user_id', 'data', 'type', 'section_id', 'project', 'parent_id', 'at_all_mentioning'
    ];

    protected $casts = [
        'data' => 'json',
    ];

    protected $attributes = [
        'data' => '[]',
    ];

    public function getTextAttribute()
    {
        return $this->data['text'];
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent() {
        return $this->belongsTo(Message::class, 'parent_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function message_reads() {
        return $this->hasMany(MessageReads::class, 'message_id');
    }
}
