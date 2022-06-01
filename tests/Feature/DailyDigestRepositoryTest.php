<?php

namespace Tests\Feature;

use App\Models\EP5\Annotation;
use App\Models\Media;
use App\Models\Project;
use App\Models\Section;
use App\Models\SectionMedia;
use App\Repositories\DailyDigestRepository;
use App\Rules\Roles;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyDigestRepositoryTest extends TestCase
{

    use RefreshDatabase;

    private $date;
    private $startDate;
    private $endDate;
    private $repository;
    private $userAdmin;


    protected function setUp(): void
    {
        parent::setUp();

        $this->date = Carbon::now();
        $this->startDate = Carbon::now()->startOfDay();
        $this->endDate = Carbon::now()->endOfDay();
        $this->repository = new DailyDigestRepository();

        $this->userAdmin = factory(User::class)->create();
        $this->userAdmin->assignRole(Roles::ADMIN);

        $this->be($this->userAdmin);
    }

    public function testGetNewCreatedSectionAmount()
    {
        $project = factory(Project::class)->create();
        factory(Section::class)->create([
            'project_id' => $project->id
        ]);

        $sectionAmount = $this->repository->getNewCreatedSectionAmount($project, $this->startDate, $this->endDate);
        $this->assertEquals(1, $sectionAmount);
    }

    public function testGetChangedSectionsAmount()
    {

        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create([
            'project_id' => $project->id,
        ]);

        $sectionAmount = $this->repository->getChangedSectionsAmount($project, $this->startDate, $this->endDate);
        $this->assertEquals(0, $sectionAmount);

        $section->title = 'new title';
        $section->updated_at = Carbon::now()->addMinutes(2);
        $section->withMinorUpdate(false);
        $section->save();
        $sectionAmount = $this->repository->getChangedSectionsAmount($project, $this->startDate, $this->endDate);
        $this->assertEquals(1, $sectionAmount);
    }

    public function testGetNewCreatedVideoAmount()
    {
        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);

        $video = factory(Media::class)->create();
        factory(SectionMedia::class)->create([
            'section_id' => $section->id,
            'mediable_id' => $video->id,
            'type' => 'video'
        ]);

        $videoAmount = $this->repository->getNewCreatedVideoAmount($project, $this->startDate, $this->endDate);
        $this->assertEquals(1, $videoAmount);
    }

    public function testGetNewCreatedAnnotationAmount()
    {
        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);

        $video = factory(Media::class)->create();
        factory(SectionMedia::class)->create([
            'section_id' => $section->id,
            'mediable_id' => $video->id,
            'type' => 'video'
        ]);
        factory(Annotation::class)->create([
            'video_nid' => $video->id
        ]);

        $annotationAmount = $this->repository->getNewCreatedAnnotationAmount($project, $this->startDate,
            $this->endDate);
        $this->assertEquals(1, $annotationAmount);

    }

    public function testGetInvolvedSectionChangesAmount()
    {
        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);
        $section->setChangeLog('Wilde Anpassungen');
        $section->withMinorUpdate(false);
        $section->update([
            'title' => 'Neuer Test',
            'content' => 'Content',
        ]);


        $result = $this->repository->getInvolvedSectionChangesAmount($project, $this->userAdmin->id, $this->startDate,
            $this->endDate);

        $this->assertEquals(1, sizeof($result), 'There should be one entry in the result array');
        $this->assertEquals(1, $result[0]['count'], 'There should be one change on the section');
        $this->assertEquals($section->id, $result[0]['section']->id, 'The section should be the same as the given one');
    }

    public function testGetChangedSectionAmount()
    {
        $project = factory(Project::class)->create();
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);

        $changes = $this->repository->getChangedSectionAmount($section, $this->startDate, $this->endDate);

        $this->assertEquals(0, $changes, 'There should be no changes in the section');

        $section->setChangeLog('Wilde Anpassungen');
        $section->withMinorUpdate(false);
        $section->update([
            'title' => 'Neuer Test',
            'content' => 'Content',
        ]);

        $changes = $this->repository->getChangedSectionAmount($section, $this->startDate, $this->endDate);

        $this->assertEquals(1, $changes, 'There should be one change in the section');
    }


}
