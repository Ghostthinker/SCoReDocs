<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\DataExport
 *
 * @property int $id
 * @property string $filename
 * @property string $path
 * @property string $last_updated
 * @property int $statement_count
 * @property int $downloaded_count
 * @property int $filesize
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport newQuery()
 * @method static \Illuminate\Database\Query\Builder|DataExport onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport query()
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereDownloadedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereFilesize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereLastUpdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereStatementCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DataExport whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|DataExport withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DataExport withoutTrashed()
 * @mixin \Eloquent
 */
class DataExport extends Model
{
    use SoftDeletes;

    /**
     * @var string[]
     */
    protected $fillable = [

    ];
}
