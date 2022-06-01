<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Repositories\ProjectRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Basic CRUD test
     */
    public function testCRUD()
    {
        $projectRepository = new ProjectRepository();
        $project = $projectRepository->create([
            'title' => 'Title',
            'description' => 'Descrption',
        ]);

        $this->assertNotEmpty($project->id);
        $this->assertNotEmpty($project->title);
        $this->assertNotEmpty($project->description);

        //retrieve all
        $all = $projectRepository->all();
        $this->assertCount(1, $all);

        $one = $projectRepository->get($project->id);
        $this->assertEquals($project->id, $one->id);

        $newTitle = ['title' => 'Neuer Title'];

        $status = $projectRepository->update($project->id, $newTitle);
        $this->assertTrue($status);

        $updatedProject = $projectRepository->get($project->id);

        $this->assertEquals($newTitle['title'], $updatedProject->title);

        $projectRepository->delete($project->id);
        $this->assertCount(0, $projectRepository->all());
    }

    public function testRevisioning()
    {
        $projectRepository = new ProjectRepository();

        $project = factory(Project::class)->create([
            'title' => 'Test',
            'description' => 'Desc'
        ]);

        $this->assertNotEmpty($project->id);

        $projectLoaded = $projectRepository->get($project->id);

        $this->assertNotEmpty($projectLoaded->id);

        $this->assertCount(1, $projectLoaded->audits);

        $projectLoaded->update([
            'title' => 'Neuer Test'
        ]);

        $projectLoaded = $projectRepository->get($project->id);
        $this->assertCount(2, $projectLoaded->audits);


        $revision2 = $projectLoaded->audits()->find(2);
        $modified = $revision2->getModified();

        $this->assertEquals('Neuer Test', $modified['title']['new']);
        $this->assertEquals('Test', $modified['title']['old']);
    }
}
