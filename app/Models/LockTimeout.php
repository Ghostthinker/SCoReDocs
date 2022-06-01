<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
class LockTimeout extends Model
{
    protected $fillable = [
        'section_id', 'user',
    ];
}
