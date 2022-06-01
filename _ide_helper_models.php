<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\DataUniversities
 *
 * @property int $id
 * @property string $university
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities whereUniversity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataUniversities whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class DataUniversities extends \Eloquent {}
}

namespace App\Models\EP5{
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
 */
	class Annotation extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\EP5{
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
 */
	class PlaybackCommand extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\File
 *
 * @property int $id
 * @property string $filename
 * @property int|null $uid
 * @property string|null $storage
 * @property string|null $path
 * @property string|null $caption
 * @property int|null $filesize
 * @property string|null $status
 * @property string|null $meta
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereMeta($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereStorage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\File whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Link
 *
 * @method static find($id)
 * @mixin \Eloquent
 * @property int $id
 * @property string $ref_id
 * @property string $target Target link
 * @property string|null $origin
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $section_id Used in section
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereOrigin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUpdatedAt($value)
 */
	class Link extends \Eloquent {}
}

namespace App\Models{
/**
 * App\LockTimeouts
 *
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $section_id Used in section
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LockTimeout whereUpdatedAt($value)
 */
	class LockTimeout extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Message extends \Eloquent {}
}

namespace App\Models{
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
 * @property-read \App\Models\DataUniversities|null $dataUniversity
 */
	class Profile extends \Eloquent {}
}

namespace App\Models{
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
 */
	class Project extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
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
 */
	class Section extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * App\Models\SectionMedia
 *
 * @property int $id
 * @property string $ref_id
 * @property string|null $type
 * @property int $section_id Used in section
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia query()
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereRefId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class SectionMedia extends \Eloquent {}
}

namespace App{
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
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAssessmentDocId($value)
 */
	class User extends \Eloquent {}
}

