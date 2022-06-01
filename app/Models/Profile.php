<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Profile
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $avatar
 * @property int|null $data_university_id
 * @property string|null $course
 * @property int|null $matriculation_number
 * @property string|null $knowledge
 * @property string|null $personal_resources
 * @property string|null $about_me
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DataUniversities|null $data_university
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereAboutMe($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCourse($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereDataUniversityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereKnowledge($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereMatriculationNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile wherePersonalResources($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $university
 * @method static \Illuminate\Database\Eloquent\Builder|Profile whereUniversity($value)
 */
class Profile extends Model
{
    protected $fillable = [
        'user_id', 'avatar',
        'university', 'course', 'matriculation_number',
        'knowledge', 'personal_resources', 'about_me',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
