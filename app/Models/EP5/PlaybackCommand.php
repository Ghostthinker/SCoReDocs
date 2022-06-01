<?php

namespace App\Models\EP5;

use App\Models\Media;
use App\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\EP5\PlaybackCommand
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $duration
 * @property int $timestamp
 * @property string|null $title
 * @property string $type
 * @property array $additional_fields
 * @property int $video_nid
 * @property int|null $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Media $media
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand byMediaId($mediaId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereAdditionalFields($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\PlaybackCommand whereVideoNid($value)
 * @mixin \Eloquent
 * @property int|null $sequence_id
 * @method static \Illuminate\Database\Eloquent\Builder|PlaybackCommand whereSequenceId($value)
 */
class PlaybackCommand extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'timestamp',
        'video_nid',
        'duration',
        'title',
        'type',
        'additional_fields',
        'sequence_id',
    ];

    protected $casts = [
        'timestamp' => 'integer',  // This is important
        'old_values' => 'json',
        'new_values' => 'json',
        'additional_fields' => 'json',
    ];

    protected $attributes = [
        'additional_fields' => '[]',
    ];

    public function scopeByMediaId($query, $mediaId)
    {
        return $query->where('video_nid', $mediaId);
    }

    public function media()
    {
        return $this->belongsTo(Media::class, 'video_nid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
