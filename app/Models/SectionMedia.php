<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
 * @property int|null $mediable_id
 * @method static \Illuminate\Database\Eloquent\Builder|SectionMedia whereMediableId($value)
 */
class SectionMedia extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'ref_id',
        'type',
        'section_id',
        'mediable_id'
    ];
}
