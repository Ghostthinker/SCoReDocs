<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
class Link extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'ref_id',
        'type',
        'target',
        'origin',
        'section_id',
    ];
}
