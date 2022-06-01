<?php

namespace Tests\Unit\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionCreateValidator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SectionCreateValidatorTest extends TestCase
{
    use RefreshDatabase;

    public function testPasses()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create([

        ]);
        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;

        // Mock the getSection Method
        $sectionMock = Mockery::mock(SectionCreateValidator::class)->makePartial();
        $sectionMock->shouldReceive('getSection')
            ->andReturn($section);

        $data = [
            'topSectionId' => $section->id,
        ];

        // User with no permission is not entitled to change level 1 content
        $v = $this->app['validator']->make($data, ['topSectionId' => new SectionCreateValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission is not entitled to add a section for a section with such a status');

        $section->status = SectionStatus::IN_REVIEW;
        $v = $this->app['validator']->make($data, ['topSectionId' => new SectionCreateValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission is not entitled to add a section for a section with such a status');

        $section->status = SectionStatus::COMPLETED;
        $v = $this->app['validator']->make($data, ['topSectionId' => new SectionCreateValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission is not entitled to add a section for a section with such a status');

        $section->status = SectionStatus::IN_PROGRESS;
        $v = $this->app['validator']->make($data, ['topSectionId' => new SectionCreateValidator($section)]);
        $this->assertTrue($v->passes(), 'User with no permission is not entitled add a section for a section with status in progress');

        // Give permission
        $section->status = SectionStatus::COMPLETED;
        $user->givePermissionTo(PermissionSet::CAN_ADD_SECTION_TO_LOCKED_SECTION);
        $this->assertTrue($v->passes(), 'User with permission should be entitled to add a section for a section with all status');
    }
}
