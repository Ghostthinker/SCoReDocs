<?php

namespace App\Models;

use App\Enums\ProjectType;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $title
 * @property string $description
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $type
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Section[] $sections
 * @property-read int|null $sections_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project ofType(\App\Enums\ProjectType $type)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereType($value)
 * @property int|null $status
 * @property int|null $assessment_doc_owner_id
 * @property-read User|null $assessmentDocOwner
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $user_involve
 * @property-read int|null $user_involve_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $user_watch
 * @property-read int|null $user_watch_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereAssessmentDocOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereStatus($value)
 */
class Project extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use \Bkwld\Cloner\Cloneable;

    protected $cloneable_relations = ['sections'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'type', 'status', 'basic_course'];

    /**
     * Scope a query to only include projects of a given type
     *
     * @param $query
     * @param ProjectType $type
     * @return mixed
     */
    public function scopeOfType($query, ProjectType $type)
    {
        return $query->where('type', $type);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function assessmentDocOwner()
    {
        return $this->belongsTo(User::class, 'assessment_doc_owner_id');
    }

    public function user_watch()
    {
        return $this->belongsToMany(User::class, 'user_watch_projects', 'project_id', 'user_id')->withTimestamps();
    }

    public function user_involve()
    {
        return $this->belongsToMany(User::class, 'user_involved_projects', 'project_id', 'user_id');
    }

    public function isAuthUserWatching()
    {
        return $this->user_watch()->where('user_id', Auth::id())->get()->count() > 0;
    }
}
