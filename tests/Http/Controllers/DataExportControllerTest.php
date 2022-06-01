<?php

namespace Tests\Http\Controllers;

use App\Models\DataExport;
use App\Rules\PermissionSet;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DataExportControllerTest extends TestCase
{

    use RefreshDatabase;

    public function testDownload()
    {
        Storage::fake('data_export');
        $user = factory(User::class)->create();
        $test = factory(DataExport::class)->create([
            'filename' => 'testfile.json'
        ]);
        // Mock the getChildren Method
        Storage::disk('data_export')->put($test->filename , File::fake());

        $response = $this->actingAs($user)->get('rest/data/download');
        // User should only be able to download export with permission
        $response->assertStatus(403);

        $user->givePermissionTo(PermissionSet::CAN_EXPORT_DATA);
        $response = $this->actingAs($user)->get('rest/data/download');
        // Expecting redirect to download
        $response->assertStatus(200);

    }

    public function testLastDataExport() {
        $user = factory(User::class)->create();
        factory(DataExport::class)->create();
        $response = $this->actingAs($user)->get('rest/data/last');
        // User should only be able to get last data export with permission
        $response->assertStatus(403);
        $user->givePermissionTo(PermissionSet::CAN_EXPORT_DATA);
        $response = $this->actingAs($user)->get('rest/data/last');
        $response->assertStatus(200);
    }
}
