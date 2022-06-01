<?php

namespace Tests\Feature;

use App\Models\Section;
use App\Repositories\LinkRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LinkRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic CRUD test
     */
    public function testSaveLink()
    {
        $repository = new LinkRepository();
        $section = factory(Section::class)->create();
        $ref = '/localhost/projects/' . $section->project_id . '#Section-' . $section->id;
        $obj = $repository->create([
            'ref_id' => 'Section-' . $section->id,
            'type' => Section::class,
            'target' => $ref,
            'section_id' => $section->id
        ]);

        $this->assertNotEmpty($obj->id);
        $this->assertNotEmpty($obj->ref_id);
        $this->assertNotEmpty($obj->target);
        $this->assertNotEmpty($obj->section_id);

        $obj = $repository->get($obj->id);
        $this->assertEquals($obj->section_id, $section->id);
    }
}
