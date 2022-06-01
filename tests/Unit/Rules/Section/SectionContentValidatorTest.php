<?php

namespace Tests\Unit\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionContentValidator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionContentValidatorTest extends TestCase
{

    use RefreshDatabase;

    public function testPasses()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();
        $section->heading = 1;
        $section->title = 'Old title';
        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;

        $data = [
            'title' => 'New title',
            'content' => 'Test Content'
        ];

        // User with no permission is not entitled to change level 1 content
        $v = $this->app['validator']->make($data, ['title' => new SectionContentValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission is not entitled to change level 1 title');


        $v2 = $this->app['validator']->make($data, ['content' => new SectionContentValidator($section)]);
        $this->assertFalse($v2->passes(), 'User with no permission is not entitled to change level 1 content');

        // Give permission
        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_1_CONTENT);
        $user->givePermissionTo(PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT);
        $this->assertTrue($v->passes(), 'User with permission should be entitled to change level 1 title');
        $this->assertTrue($v2->passes(), 'User with permission should be entitled to change level 1 content');
    }

    public function testEntitledToChangeContent()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();
        $section->heading = 1;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContent($section),
            'User without permission is not entitled to change level 1 content'
        );
        $section->heading = 2;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContent($section),
            'User without permission is not entitled to change level 2 content'
        );
        for ($i = 3; $i < 6; $i++) {
            $section->heading = $i;
            $this->assertTrue(
                SectionContentValidator::entitledToChangeContent($section),
                'User is not entitled to change level 3 to 5 content'
            );
        }
        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_1_CONTENT);
        $section->heading = 1;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContent($section),
            'User with permission is entitled to change level 1 content'
        );
        $section->heading = 2;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContent($section),
            'User without permission is not entitled to change level 2 content'
        );

        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_2_CONTENT);
        $section->heading = 2;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContent($section),
            'User with permission is entitled to change level 2 content'
        );
    }

    public function testentitledToChangeContentByStatusByStatus() {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();

        $section->heading = 3;
        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status EDIT_NOT_POSSIBLE'
        );
        $section->status = SectionStatus::IN_PROGRESS;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status IN PROGRESS'
        );
        $section->status = SectionStatus::SUBMITTED;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status SUBMITTED'
        );
        $section->status = SectionStatus::IN_REVIEW;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status IN REVIEW'
        );
        $section->status = SectionStatus::COMPLETED;
        $this->assertFalse(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status COMPLETED'
        );

        $user->givePermissionTo(PermissionSet::EDIT_LOCKED_SECTIONS_CONTENT);
        $user->givePermissionTo(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_IN_REVIEW);
        $user->givePermissionTo(PermissionSet::EDIT_SECTIONS_CONTENT_WITH_STATUS_COMPLETED);

        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status EDIT_NOT_POSSIBLE'
        );
        $section->status = SectionStatus::IN_PROGRESS;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status IN PROGRESS'
        );
        $section->status = SectionStatus::SUBMITTED;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status SUBMITTED'
        );
        $section->status = SectionStatus::IN_REVIEW;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status IN_REVIEW'
        );
        $section->status = SectionStatus::COMPLETED;
        $this->assertTrue(
            SectionContentValidator::entitledToChangeContentByStatus($section->status),
            'User without permission is not entitled to change section with status COMPLETED'
        );
    }
}
