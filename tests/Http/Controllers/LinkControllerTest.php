<?php

namespace Tests\Http\Controllers;

use App\Models\Section;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testStoreSectionLinkFail()
    {
        $user = factory(User::class)->create();
        $section = factory(Section::class)->create();
        $uri = 'rest/projects/'.$section->project_id.'/sections/'.$section->id.'/link';

        $response = $this->actingAs($user)->post($uri,
            [
                'ref_id' => 'Section-'.$section->id,
                'target' => null,
                'section_id' => $section->id,
            ]
        );
        $response->assertStatus(500);

        $ref = '/localhost/projects/'.$section->project_id.'#Section-'.$section->id;
        $response = $this->actingAs($user)->post($uri,
            [
                'ref_id' => 'Section-'.$section->id,
                'target' => $ref,
                'section_id' => null,
            ]
        );
        $response->assertStatus(500);
    }
}
