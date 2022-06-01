<?php

namespace Tests\Unit\Rules\Section;

use App\Enums\SectionStatus;
use App\Models\Section;
use App\Rules\PermissionSet;
use App\Rules\Section\SectionStatusValidator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SectionStatusValidatorTest extends TestCase
{

    use RefreshDatabase;

    public function testPasses()
    {
        $user = factory(User::class)->create();
        $this->be($user);
        $section = factory(Section::class)->create();
        $section->heading = 3;
        $section->status = SectionStatus::EDIT_NOT_POSSIBLE;

        $data = [
            'status' => SectionStatus::IN_PROGRESS
        ];

        // User with no permission is not entitled to set status
        $v = $this->app['validator']->make($data, ['status' => new SectionStatusValidator($section)]);
        $this->assertFalse($v->passes(), 'User with no permission is not entitled to set status');

        // Give permission
        $user->givePermissionTo(PermissionSet::SET_STATUS_IN_PROGRESS);
        $user->givePermissionTo(PermissionSet::CHANGE_STATUS_EDIT_NOT_POSSIBLE);
        $this->assertTrue($v->passes(), 'User with permission should be entitled to set status');
    }

    public function testUserIsEntitledToSetStatus()
    {
        $user = factory(User::class)->create();
        $this->be($user);

        // User with no permission is not entitled to set status
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::EDIT_NOT_POSSIBLE),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_PROGRESS),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::SUBMITTED),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_REVIEW),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::COMPLETED),
            "User should not be able to change status without permission");

        // Give user permission to set EditNotPossible
        $user->givePermissionTo(PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE);
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::EDIT_NOT_POSSIBLE),
            "User should be able to set SetEditNotPossible status with permission");

        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_PROGRESS),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::SUBMITTED),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_REVIEW),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::COMPLETED),
            "User should not be able to change status without permission");

        // Give user permission to set EditNotPossible and InReview
        $user->givePermissionTo(PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE);
        $user->givePermissionTo(PermissionSet::SET_STATUS_IN_REVIEW);
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::EDIT_NOT_POSSIBLE),
            "User should be able to set status with the rights to do so");
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_REVIEW),
            "User should be able to set status with the rights to do so");

        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_PROGRESS),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::SUBMITTED),
            "User should not be able to change status without permission");
        $this->assertFalse(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::COMPLETED),
            "User should not be able to change status without permission");

        // Give user all permissions
        $user->givePermissionTo(PermissionSet::SET_STATUS_EDIT_NOT_POSSIBLE);
        $user->givePermissionTo(PermissionSet::SET_STATUS_IN_PROGRESS);
        $user->givePermissionTo(PermissionSet::SET_STATUS_SUBMITTED);
        $user->givePermissionTo(PermissionSet::SET_STATUS_IN_REVIEW);
        $user->givePermissionTo(PermissionSet::SET_STATUS_COMPLETED);
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::EDIT_NOT_POSSIBLE),
            "User should be able to set status with the rights to do so");
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_PROGRESS),
            "User should be able to set status with the rights to do so");
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::SUBMITTED),
            "User should be able to set status with the rights to do so");
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::IN_REVIEW),
            "User should be able to set status with the rights to do so");
        $this->assertTrue(SectionStatusValidator::userIsEntitledToSetStatus(SectionStatus::COMPLETED),
            "User should be able to set status with the rights to do so");
    }

    public function testIsAValidWorkflow()
    {
        $oldStatus = SectionStatus::EDIT_NOT_POSSIBLE;
        $newStatus = SectionStatus::IN_PROGRESS;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change from former status to next status should work");

        $oldStatus = SectionStatus::IN_PROGRESS;
        $newStatus = SectionStatus::SUBMITTED;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change from former status to next status should work");

        $oldStatus = SectionStatus::SUBMITTED;
        $newStatus = SectionStatus::IN_REVIEW;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change from former status to next status should work");

        $oldStatus = SectionStatus::IN_REVIEW;
        $newStatus = SectionStatus::COMPLETED;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change from former status to next status should work");

        $oldStatus = SectionStatus::IN_REVIEW;
        $newStatus = SectionStatus::IN_PROGRESS;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change from InReview status to InProgress status should work");

        $oldStatus = SectionStatus::EDIT_NOT_POSSIBLE;
        $newStatus = SectionStatus::COMPLETED;
        $this->assertFalse(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "Status change jumps should be prohibited");

        $oldStatus = SectionStatus::COMPLETED;
        $newStatus = SectionStatus::IN_REVIEW;
        $this->assertFalse(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "The direction of the change should not have an impact to the validation");

        $oldStatus = SectionStatus::SUBMITTED;
        $newStatus = SectionStatus::IN_PROGRESS;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "InProgress and Submitted can always be changed in any direction");

        $oldStatus = SectionStatus::IN_PROGRESS;
        $newStatus = SectionStatus::SUBMITTED;
        $this->assertTrue(SectionStatusValidator::isAValidWorkflow($oldStatus, $newStatus),
            "InProgress and Submitted can always be changed in any direction");
    }

    public function testIsExistingStatus()
    {
        $rightStatus = SectionStatus::EDIT_NOT_POSSIBLE;
        $wrongStatus = 14843;

        $this->assertTrue(SectionStatusValidator::isExistingStatus($rightStatus),
            "Status 0 is part of the status enum");

        $this->assertFalse(SectionStatusValidator::isExistingStatus($wrongStatus),
            "Status 14843 is no part of the status enum");
    }

    public function testChildrenAreSubmitted()
    {
        $section2 = factory(Section::class)->create();
        $section2->heading = 2;
        $section2->status = SectionStatus::SUBMITTED;
        $section3 = factory(Section::class)->create();
        $section3->heading = 3;
        $section3->status = SectionStatus::SUBMITTED;

        // Mock the getChildren Method
        $sectionMock = Mockery::mock(Section::class)->makePartial();
        $sectionMock->shouldReceive('getChildren')
            ->andReturn([$section2, $section3]);
        $sectionMock->heading = 1;

        $children = $sectionMock->getChildren();
        $this->assertEquals([$section2, $section3], $children);

        $this->assertTrue(SectionStatusValidator::childrenAreSubmitted($sectionMock),
            "Validation should work because children have the status submitted");

        $section3->status = SectionStatus::IN_PROGRESS;
        $this->assertFalse(SectionStatusValidator::childrenAreSubmitted($sectionMock),
            "Children must be submitted before changing the parent");
    }
}
