<?php

namespace App\Models\EP5;

use App\Enums\MediaType;
use App\Models\Media;
use App\Rules\PermissionSet;
use App\Rules\Roles;
use App\User;
use Auth;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Role;

/**
 * App\Models\EP5\Annotation
 *
 * @property int $id
 * @property string $body
 * @property string|null $drawing_data
 * @property string|null $rating
 * @property int $timestamp
 * @property int $video_nid
 * @property int|null $user_id
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @property-read mixed $nid
 * @property-read mixed $perms
 * @property-read mixed $preview_medium
 * @property-read mixed $preview_thumb
 * @property-read mixed $timecode_formatted
 * @property-read mixed $userdata
 * @property-read \App\Models\Media $media
 * @property-read \App\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EP5\Annotation[] $replies
 * @property-read int|null $replies_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation byMediaId($mediaId)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereDrawingData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\EP5\Annotation whereVideoNid($value)
 * @mixin \Eloquent
 * @property int|null $sequence_id
 * @method static \Illuminate\Database\Eloquent\Builder|Annotation bySequenceId($sequenceId)
 * @method static \Illuminate\Database\Eloquent\Builder|Annotation whereSequenceId($value)
 * @property array|null $look_at
 * @property-read mixed $date_created
 * @property-read mixed $versions_count
 * @method static \Illuminate\Database\Eloquent\Builder|Annotation whereLookAt($value)
 */
class Annotation extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'body',
        'timestamp',
        'video_nid',
        'drawing_data',
        'rating',
        'sequence_id',
        'look_at'
    ];

    protected $casts = [
        'auditable_id' => 'integer',  // This is important
        'timestamp' => 'integer',  // This is important
        'old_values' => 'json',
        'new_values' => 'json',
        'drawing_data' => 'json', // This is important
        'look_at' => 'json',
    ];

    //Make it available in the json response, Nid
    protected $appends = ['userdata', 'nid', 'perms', 'preview_thumb', 'timecode_formatted', 'versions_count', 'date_created'];

    public function media()
    {
        return $this->belongsTo(Media::class, 'video_nid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Annotation::class, 'parent_id');
    }

    public function scopeByMediaId($query, $mediaId)
    {
        return $query->where('video_nid', $mediaId)->where('parent_id', null);
    }

    public function scopeBySequenceId($query, $sequenceId)
    {
        return $query->where('sequence_id', $sequenceId)->where('parent_id', null);
    }

    public function getUserdataAttribute()
    {
        $user = $this->user()->get()->first();
        return [
            'name' => $user->name,
            'uid' => $user->id,
            'picture' => '/assets/images/default_user.png',
        ];
    }

    public function getPreviewThumbAttribute()
    {
        $previewUrl = $this->getPreviewUrl('smallWatermarked');

        return $previewUrl;
    }

    public function getPreviewMediumAttribute()
    {
        $previewUrl = $this->getPreviewUrl('medium');
        return $previewUrl;
    }

    private function getPreviewUrl($imageStyle)
    {
        $config = config('evoli');
        $host = $config['host'];

        // 360 Video - pass look at parameters
        $attributes_urlecoded = '';
        if ($this->media->type == MediaType::THREE_SIXTY && !empty($this->look_at)) {
            $cameraPosition = [
                'camX' => $this->look_at['x'],
                'camY' => $this->look_at['y'],
                'camZ' => $this->look_at['z'],
            ];
            $attributes_urlecoded = urlencode(json_encode($cameraPosition));
        }

        return $host . '/media/' . $this->media->streamingId . '/frame/' . intval($this->timestamp) / 1000 . '/' . $imageStyle . '/' . $attributes_urlecoded;
    }

    public function getVersionsCountAttribute() {
        return $this->audits()->count();
    }

    public function getDateCreatedAttribute() {
        return $this->updated_at->setTimezone('Europe/Berlin')->format(config('app')['date_format']);
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

    public function getNidAttribute()
    {
        return $this->id;
    }

    public function getPermsAttribute(User $user = null)
    {
        if (!$user) {
            $user = Auth::getUser();
        }

        $updateBypassAccess = $user->can(PermissionSet::UPDATE_ANY_VIDEOCOMMENT);
        $updateAccess = $updateBypassAccess || ($user->can(PermissionSet::UPDATE_OWN_VIDEOCOMMENT) && $this->user_id == $user->id);

        $deleteBypassAccess = $user->can(PermissionSet::DELETE_ANY_VIDEOCOMMENT);
        $deleteAccess = $deleteBypassAccess || ($user->can(PermissionSet::DELETE_OWN_VIDEOCOMMENT) && $this->user_id == $user->id);

        $replyAccess = $user->can(PermissionSet::REPLY_TO_VIDEOCOMMENT);

        return [
            'delete' => $deleteAccess,
            'reply' => $replyAccess,
            'update' => $updateAccess,
        ];
    }

    /**
     * @param string $permission
     * @param User|null $user
     * @return bool
     */
    public function userHasPermission(string $permission, User $user = null) {
        $permissions = $this->getPermsAttribute($user);

        return !empty($permissions[$permission]);
    }
}
