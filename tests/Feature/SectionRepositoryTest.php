<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Repositories\SectionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic CRUD test
     */
    public function testCRUD()
    {
        $sectionRepository = new SectionRepository();
        $project =factory(Project::class)->create();
        $section = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'project_id' => $project->id,
            'locked' => false
        ]);

        $this->assertNotEmpty($section->id);
        $this->assertNotEmpty($section->title);
        $this->assertNotEmpty($section->content);
        $this->assertNotEmpty($section->heading);
        $this->assertNotEmpty($section->index);
        $this->assertFalse($section->locked);
        $this->assertNull($section->locking_user);
        $this->assertNotEmpty($section->project_id);

        //retrieve all
        $all = $sectionRepository->all();
        $this->assertCount(1, $all);

        $one = $sectionRepository->get($section->id);
        $this->assertEquals($section->id, $one->id);

        $newTitle = ['title' => 'Neuer Title'];

        $status = $sectionRepository->update($section, $newTitle);
        $this->assertTrue($status);

        $updatedProject = $sectionRepository->get($section->id);

        $this->assertEquals($newTitle['title'], $updatedProject->title);

        $sectionRepository->delete($section->id);
        $this->assertCount(0, $sectionRepository->all());
    }

    public function testGetAllByProjectId() {
        $sectionRepository = new SectionRepository();
        $project =factory(Project::class)->create();
        $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'project_id' => $project->id,
            'locked' => false
        ]);
        $sections = $sectionRepository->getAllByProjectId($project->id)->toArray();
        $this->assertCount(1, $sections);
    }

    public function testRevisioning()
    {
        $sectionRepository = new SectionRepository();
        $project =factory(Project::class)->create();
        $section = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'project_id' => $project->id,
            'locked' => false
        ]);

        $this->assertNotEmpty($section->id);

        $sectionLoaded = $sectionRepository->get($section->id);

        $this->assertNotEmpty($sectionLoaded->id);

        $this->assertCount(1, $sectionLoaded->audits);
        $sectionLoaded->setChangeLog('Wilde Anpassungen');
        $sectionRepository->update($sectionLoaded,[
            'title' => 'Neuer Test',
            'content' => 'Content',
        ]);

        $sectionLoaded = $sectionRepository->get($section->id);
        $this->assertCount(2, $sectionLoaded->audits);


        $revision2 = $sectionLoaded->audits->last();
        $modified2 = $revision2->getModified();

        $this->assertEquals('Neuer Test', $modified2['title']['new']);
        $this->assertEquals('Wilde Anpassungen', $revision2->change_log);
        $this->assertEquals('Title', $modified2['title']['old']);
    }
}
