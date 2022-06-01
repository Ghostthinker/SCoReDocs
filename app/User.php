<?php

namespace App;

use App\Models\Activity;
use App\Models\Profile;
use App\Models\Project;
use App\Models\Section;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|User permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|User role($roles, $guard = null)
 * @property int|null $assessment_doc_id
 * @property int $accepted_terms_of_usage
 * @property int $accepted_privacy_policy
 * @property int $uploaded_privacy_agreement
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedPrivacyPolicy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedTermsOfUsage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAssessmentDocId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUploadedPrivacyAgreement($value)
 * @property string $surname
 * @property int $accepted_student_in_germany
 * @property string|null $uploaded_privacy_filepath
 * @property string|null $firstname
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int $has_seen_intro_video
 * @property-read mixed $has_seen_intro
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $project_involve
 * @property-read int|null $project_involve_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $project_watch
 * @property-read int|null $project_watch_count
 * @property-read \Illuminate\Database\Eloquent\Collection|Project[] $projects
 * @property-read int|null $projects_count
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAcceptedStudentInGermany($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereHasSeenIntroVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUploadedPrivacyFilepath($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 */
class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'surname', 'firstname', 'email', 'password', 'assessment_doc_id', 'accepted_terms_of_usage',
        'accepted_privacy_policy', 'uploaded_privacy_agreement', 'uploaded_privacy_filepath',
        'accepted_student_in_germany', 'has_seen_intro_video', 'left_menu_collapsed', 'right_menu_collapsed'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getNameAttribute()
    {
        if ($this->trashed()) {
            return 'Gelöschter Benutzer';
        }
        return $this->firstname ? $this->firstname.' '.$this->surname : $this->surname;
    }

    public function getHasSeenIntroAttribute()
    {
        $isIntroVideoExists = Storage::exists('files/IntroVideo.mp4');
        if($isIntroVideoExists){
            return $this->has_seen_intro_video;
        }else{
            return 1;
        }
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function project_watch()
    {
        return $this->belongsToMany(Project::class, 'user_watch_projects', 'user_id', 'project_id')->withTimestamps();
    }

    public function project_involve()
    {
        return $this->belongsToMany(Project::class, 'user_involved_projects', 'user_id', 'project_id');
    }

    public function section_collapse()
    {
        return $this->belongsToMany(Section::class, 'user_collapse_sections', 'user_id', 'section_id');
    }

    public function activity_read()
    {
        return $this->belongsToMany(Activity::class, 'user_read_activities', 'user_id', 'activity_id')->withTimestamps();
    }
}
