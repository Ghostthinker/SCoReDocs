<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\Section
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string|null $content
 * @property int $heading
 * @property int $index
 * @property int $locked
 * @property \Illuminate\Support\Carbon|null $locked_at
 * @property int|null $locking_user
 * @property int $project_id
 * @property int $author_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereHeading($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereLockedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereLockingUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Section whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $status
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereStatus($value)
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Query\Builder|Section onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Section whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|Section withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Section withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SectionMedia[] $section_media
 * @property-read int|null $section_media_count
 */
class Section extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $changeLog;
    protected $isMinorUpdate;

    /**
     * The attributes that are not mass assignable
     *
     * @var array
     */
    protected $guarded = [];

    protected $dates = ['locked_at', 'deleted_at'];

    /**
     * Attributes to include in the Audit.
     *
     * @var array
     */
    protected $auditInclude = [
        'title',
        'content',
        'heading',
        'status',
    ];

    public function setChangeLog($value)
    {
        $this->changeLog = $value;
        return $this;
    }

    /**
     * @param $value
     *
     * @return $this
     */
    public function withMinorUpdate($value)
    {
        $this->isMinorUpdate = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isMinorUpdate()
    {
        return $this->isMinorUpdate;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function transformAudit(array $data): array
    {
        Arr::set($data, 'change_log', $this->changeLog);
        Arr::set($data, 'is_minor_update', $this->isMinorUpdate);
        Arr::set($data, 'state', json_encode([
            'title' => $this->title,
            'content' => $this->content,
        ]));

        return $data;
    }

    /**
     * Returns the child section ids of a section
     *
     * @return array
     */
    public function getChildren(): array
    {
        $childSectionIdArray = [];
        $sections = Section::where([
            ['index', '>', $this->index], ['project_id', '=', $this->project_id],
        ])->get()->sortBy('index');
        foreach ($sections as $section) {
            if ($section->heading > $this->heading) {
                array_push($childSectionIdArray, $section);
            } else {
                return $childSectionIdArray;
            }
        }
        return $childSectionIdArray;
    }

    /**
     * Returns the parent section ids of a section
     *
     * @return array
     */
    public function getParents(): array
    {
        $parentSectionIdArray = [];
        $sections = Section::where([
            ['index', '<', $this->index], ['project_id', '=', $this->project_id],
        ])->get()->sortByDesc('index');
        $currentHeading = $this->heading;
        foreach ($sections as $section) {
            if ($section->heading < $currentHeading) {
                array_push($parentSectionIdArray, $section);
                $currentHeading = $section->heading;
                continue;
            }
            if ($section->heading === $currentHeading) {
                continue;
            }
            return $parentSectionIdArray;
        }
        return $parentSectionIdArray;
    }

    public function section_media()
    {
        return $this->hasMany(SectionMedia::class);
    }

    public function user_collapse()
    {
        return $this->belongsToMany(User::class, 'user_collapse_sections', 'section_id', 'user_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
