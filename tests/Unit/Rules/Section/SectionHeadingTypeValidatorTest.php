<?php

namespace Tests\Unit\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionHeadingTypeValidator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectionHeadingTypeValidatorTest extends TestCase
{

    use RefreshDatabase;

    public function testPasses()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();
        $section->heading = 1;

        $data = [
            'heading' => '3'
        ];

        // User with no permission is not entitled to change level 1 content
        $v = $this->app['validator']->make($data, ['heading' => new SectionHeadingTypeValidator($section)]);
        $this->assertFalse(
            $v->passes(),
            'User with no permission should not be entitled to change level 1 heading type'
        );

        // Give permission
        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_1_TYPE);
        $this->assertTrue(
            $v->passes(),
            'User with permission should be entitled to change level 1 heading type'
        );
    }

    public function testEntitledToChangeHeading()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();

        $section->heading = 1;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeading($section),
            'User without permission is not entitled to change level 1 type'
        );
        $section->heading = 2;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeading($section),
            'User without permission is not entitled to change level 2 type'
        );
        for ($i = 3; $i < 6; $i++) {
            $section->heading = $i;
            $this->assertTrue(
                SectionHeadingTypeValidator::entitledToChangeHeading($section),
                'User is not entitled to change level 3 to 5 type'
            );
        }
        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_1_TYPE);
        $section->heading = 1;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeading($section),
            'User with permission is entitled to change level 1 type'
        );
        $section->heading = 2;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeading($section),
            'User without permission is not entitled to change level 2 type'
        );

        $user->givePermissionTo(PermissionSet::CHANGE_HEADING_2_TYPE);
        $section->heading = 2;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeading($section),
            'User with permission is entitled to change level 2 type'
        );
    }

    public function testEntitledToSetHeading()
    {
        $user = factory(User::class)->create();
        $this->be($user);

        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToSetHeading(1),
            'User without permission is not entitled to set level 1 type'
        );
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToSetHeading(2),
            'User without permission is not entitled to set level 2 type'
        );
        for ($i = 3; $i < 6; $i++) {
            $this->assertTrue(
                SectionHeadingTypeValidator::entitledToSetHeading($i),
                'User is not entitled to set level 3 to 5 type'
            );
        }
        $user->givePermissionTo(PermissionSet::SET_HEADING_1_TYPE);
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToSetHeading(1),
            'User with permission is entitled to set level 1 type'
        );
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToSetHeading(2),
            'User without permission is not entitled to set level 2 type'
        );

        $user->givePermissionTo(PermissionSet::SET_HEADING_2_TYPE);
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToSetHeading(2),
            'User with permission is entitled to set level 2 type'
        );
    }

    public function testEntitledToChangeHeadingByStatus() {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();

        $section->heading = 3;
        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status EDIT_NOT_POSSIBLE'
        );
        $section->status = SectionStatus::IN_PROGRESS;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status IN PROGRESS'
        );
        $section->status = SectionStatus::SUBMITTED;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status SUBMITTED'
        );
        $section->status = SectionStatus::IN_REVIEW;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status IN REVIEW'
        );
        $section->status = SectionStatus::COMPLETED;
        $this->assertFalse(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status COMPLETED'
        );

        $user->givePermissionTo(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_EDIT_NOT_POSSIBLE);
        $user->givePermissionTo(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_IN_REVIEW);
        $user->givePermissionTo(PermissionSet::EDIT_SECTIONS_HEADING_WITH_STATUS_COMPLETED);

        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status EDIT_NOT_POSSIBLE'
        );
        $section->status = SectionStatus::IN_PROGRESS;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status IN_PROGRESS'
        );
        $section->status = SectionStatus::SUBMITTED;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status SUBMITTED'
        );
        $section->status = SectionStatus::IN_REVIEW;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status IN REVIEW'
        );
        $section->status = SectionStatus::COMPLETED;
        $this->assertTrue(
            SectionHeadingTypeValidator::entitledToChangeHeadingByStatus($section->status),
            'User without permission is not entitled to change section with status COMPLETED'
        );
    }
}
