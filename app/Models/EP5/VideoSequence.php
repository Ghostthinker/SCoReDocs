<?php

namespace App\Models\EP5;

use App;
use App\Enums\MediaType;
use App\Models\Media;
use App\User;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\EP5\VideoSequence
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $duration
 * @property int $timestamp
 * @property int $video_nid
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $author
 * @property-read mixed $endtime_formatted
 * @property-read mixed $last_modified_timestamp
 * @property-read mixed $preview_thumb
 * @property-read mixed $timecode_formatted
 * @property-read Media $media
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence byMediaId($mediaId)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereVideoNid($value)
 * @mixin \Eloquent
 * @property array|null $camera_look_at
 * @property int|null $camera_locked
 * @property array|null $camera_path
 * @property float|null $camera_yaw
 * @property float|null $camera_pitch
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCameraLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCameraLookAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCameraPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCameraPitch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|VideoSequence whereCameraYaw($value)
 */
class VideoSequence extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    private $userRepo;

    protected $fillable = [
        'title',
        'description',
        'timestamp',
        'duration',
        'user_id',
        'video_nid',
        'camera_look_at',
        'camera_locked',
        'camera_path',
        'camera_pitch',
        'camera_yaw'
    ];

    protected $appends = ['preview_thumb', 'timecode_formatted', 'endtime_formatted', 'author', 'last_modified_timestamp'];

    protected $casts = [
      'camera_look_at' => 'array',
      'camera_path' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        $this->userRepo = App::make('App\Repositories\UserRepositoryInterface');
        parent::__construct($attributes);
    }

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

    public function getTimecodeFormattedAttribute()
    {
        $absoluteSeconds = intval($this->timestamp) / 1000;

        $hours = intval(intval($absoluteSeconds) / 3600);
        $minutes = intval(($absoluteSeconds / 60) % 60);
        $seconds = intval($absoluteSeconds % 60);

        $milliSeconds = $this->timestamp - $seconds * 1000;

        $timestamp = str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($seconds, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($milliSeconds, 3, '0', STR_PAD_LEFT);

        if ($hours > 0) {
            $timestamp = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . $timestamp;
        }
        return $timestamp;
    }

    public function getEndtimeFormattedAttribute()
    {
        $absoluteSeconds = intval($this->timestamp) / 1000;
        $absoluteSeconds += $this->duration / 1000;

        $hours = intval(intval($absoluteSeconds) / 3600);
        $minutes = intval(($absoluteSeconds / 60) % 60);
        $seconds = intval($absoluteSeconds % 60);

        $milliSeconds = $this->duration >= 1000 ? $this->duration % 1000 : $this->duration;

        $timestamp = str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($seconds, 2, '0', STR_PAD_LEFT) . ':'
            . str_pad($milliSeconds, 3, '0', STR_PAD_LEFT);

        if ($hours > 0) {
            $timestamp = str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' . $timestamp;
        }
        return $timestamp;
    }

    public function getAuthorAttribute()
    {
        $latestAuthor = $this->audits()->latest()->first();

        if ($latestAuthor) {
            $id = $latestAuthor->user_id;
            $user = $this->userRepo->getWithProfile($id);
            $name = $user->name;
            $image = $user->profile ? $user->profile->avatar : '';
        } else {
            $id = $this->user_id;
            $user = $this->userRepo->getWithProfile($this->user_id);
            $name = $user->name;
            $image = $user->profile ? $user->profile->avatar : '';
        }
        return [
            'name' => $name,
            'id' => $id,
            'image' => $image
        ];
    }

    public function getLastModifiedTimestampAttribute()
    {
        return $this->updated_at->setTimezone('Europe/Berlin')->format(config('app')['date_format']);
    }

    public function getPreviewThumbAttribute()
    {
        $config = config('evoli');
        $host = $config['host'];

        // 360 Video - pass look at parameters
        $attributes_urlecoded = '';
        if ($this->media->type == MediaType::THREE_SIXTY) {
            if (!empty($this->camera_look_at)) {
                $cameraPosition = [
                    'camX' => $this->camera_look_at['x'],
                    'camY' => $this->camera_look_at['y'],
                    'camZ' => $this->camera_look_at['z'],
                ];
            } else {
                $cameraPosition = [
                    'camX' => 0,
                    'camY' => 0,
                    'camZ' => 0,
                ];
            }

            $attributes_urlecoded = urlencode(json_encode($cameraPosition));
        }

        return $host . '/media/' . $this->media->streamingId . '/frame/' . intval($this->timestamp) / 1000 . '/smallWatermarked/' . $attributes_urlecoded;
    }
}
