<?php

namespace App\Models;

use App;
use App\Enums\MediaStatus;
use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Media
 *
 * @property int $id
 * @property string $status
 * @property string|null $streamingURL_720p
 * @property string|null $streamingURL_1080p
 * @property string|null $streamingURL_2160p
 * @property string|null $previewURL
 * @property string|null $streamingId
 * @property string|null $fileName
 * @property string|null $caption
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $preview_url_sanitised
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media wherePreviewURL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereStreamingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereStreamingURL($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Media whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int|null $user_id
 * @property int $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EP5\Annotation[] $annotations
 * @property-read int|null $annotations_count
 * @property-read mixed $author
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Media whereUserId($value)
 */
class Media extends Model
{
    private $userRepo;

    protected $fillable = [
        'localUrl',
        'streamingURL_720p',
	    'streamingURL_1080p',
	    'streamingURL_2160p',
        'previewURL',
        'fileName',
        'caption',
        'type',
        'user_id',
    ];

    protected $attributes = [
        'status' => MediaStatus::CREATED,
        'type' => App\Enums\MediaType::DEFAULT,
    ];

    protected $appends = ['author'];

    public function __construct(array $attributes = [])
    {
        $this->userRepo = App::make('App\Repositories\UserRepositoryInterface');
        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFileName()
    {
        return basename($this->fileUrl);
    }

    public function getPreviewUrlSanitisedAttribute()
    {
        if ($this->previewURL === null) {
            return 'https://dummyimage.com/600x400/000/fff&text=Video';
        }
        return $this->previewURL;
    }

    public function getAuthorAttribute()
    {
        $id = $this->user_id;
        if(!$id) {
            return null;
        }
        $user = $this->userRepo->getWithProfile($this->user_id);
        $name = $user->name;
        $image = $user->profile ? $user->profile->avatar : '';

        return [
            'name' => $name,
            'id' => $id,
            'image' => $image
        ];
    }

    public function getCreatedAtAttribute()
    {
        return $this->updated_at->setTimezone('Europe/Berlin')->format(config('app')['date_format']);
    }

    public function annotations()
    {
        return $this->hasMany(App\Models\EP5\Annotation::class, 'video_nid');
    }
}
