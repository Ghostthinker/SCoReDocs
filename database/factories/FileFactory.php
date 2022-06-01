<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\File;
use Faker\Generator as Faker;
use Illuminate\Http\UploadedFile;

$factory->define(File::class, function(Faker $faker) {
    $filePath = storage_path('app/uploads');
    $image = $faker->image($filePath, 5, 5, null, false);
    return [
        'filename' => $image,
        'uid' => 1,
        'storage' => 'uploads',
        'path' => 'file/' . $image,
        'caption' => "factoryCaption",
        'filesize' => filesize($filePath . '/' . $image),
        'status' => '1',
        'meta' => "factoryMeta",
        'created_at' => now(),
        'updated_at' => now()
    ];
});
