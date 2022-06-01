<?php

namespace Tests\Unit\Models;

use App\Models\Project;
use App\Models\Section;
use App\Repositories\SectionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionTest extends TestCase
{
    use RefreshDatabase;

    public function testGetChildSections() {
        $sectionRepository = new SectionRepository();
        $project =factory(Project::class)->create();
        $section1 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section2 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 2,
            'index' => 2,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section3 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 2,
            'index' => 3,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section4 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 3,
            'index' => 4,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section5 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 5,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);
        $this->assertCount(3, $section1->getChildren());
        $this->assertCount(1, $section3->getChildren());
        $this->assertCount(0, $section4->getChildren());

        $this->assertContains(2, array_column($section1->getChildren(), 'id'));
        $this->assertContains(3, array_column($section1->getChildren(), 'id'));
        $this->assertContains(4, array_column($section1->getChildren(), 'id'));

        $sectionRepository->delete($section3->id);
        $this->assertCount(2, $section1->getChildren());
    }

    public function testGetParentSections() {
        $sectionRepository = new SectionRepository();
        $project =factory(Project::class)->create();
        $section1 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 1,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section2 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 2,
            'index' => 2,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section3 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 2,
            'index' => 3,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section4 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 3,
            'index' => 4,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);

        $section5 = $sectionRepository->create([
            'title' => 'Title',
            'content' => 'Descrption',
            'heading' => 1,
            'index' => 5,
            'project_id' => $project->id,
            'locked' => false,
            'status' => 0
        ]);
        $this->assertCount(2, $section4->getParents());
        $this->assertCount(1, $section2->getParents());
        $this->assertCount(0, $section1->getParents());

        $this->assertContains(1, array_column($section4->getParents(), 'id'));
        $this->assertContains(3, array_column($section4->getParents(), 'id'));

        $this->assertNotContains(2, $section4->getParents());
        $sectionRepository->delete($section3->id);
        $this->assertCount(2, $section4->getParents());
    }
}
