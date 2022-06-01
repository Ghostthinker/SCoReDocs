<?php

namespace Tests\Http\Controllers\Auth;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Project;
use App\Models\Section;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCreate()
    {
        $project = factory(Project::class)->create([
            'type' => ProjectType::TEMPLATE
        ]);
        $section = factory(Section::class)->create([
            'project_id' => $project->id
        ]);
        $response = $this->post("/register", [
            'surname' => 'Testsurname',
            'firstname' => 'Testfirstname',
            'email' => 'Test@mail.com',
            'password' => 'HollaBolla1!',
            'password_confirmation' => 'HollaBolla1!',
            'privacy' => true,
            'terms' => true,
            'student' => true,
            'matriculation_number' => 123,
            'university' => 'Uni Augsburg',
            'upload_privacy_agreement' => UploadedFile::fake()->image('upload1.jpg'),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $user = User::with('roles')->where('surname', 'Testsurname')->get()->first();
        $this->assertTrue($user->hasRole('Studentin'));

        $doc = Project::ofType(new ProjectType(ProjectType::ASSESSMENT_DOC))->first();
        $copiedSection = Section::where('project_id', '=', $doc->id)->first();
        $this->assertEquals($project->title, $doc->title, 'Title of cloned project should match the original project');
        $this->assertEquals(ProjectStatus::IN_PROGRESS, $doc->status, 'Status of cloned project should be "In Progress"');
        $this->assertEquals($user->id, $doc->assessment_doc_owner_id, 'Assessment Doc Owner Id should be like the id of the user');
        $this->assertEquals(1, $user->accepted_terms_of_usage, 'User should have accepted terms of usage');
        $this->assertEquals(1, $user->accepted_privacy_policy, 'User should have accepted privacy police');
        $this->assertEquals(1, $user->accepted_student_in_germany, 'User should have accepted student in germany');
        $this->assertEquals(1, $user->uploaded_privacy_agreement, 'User should have uploaded privacy agreement');
        $this->assertEquals('uploads/user-' . $user->id . '.jpg', $user->uploaded_privacy_filepath, 'User should have uploaded a privacy agreement');
        $this->assertEquals($section->title, $copiedSection->title, 'Title of cloned project should match the original project');
        $this->assertEquals($user->assessment_doc_id, $doc->id, 'User should have a assessment doc');
        $this->assertEquals('Uni Augsburg', $user->profile->university, 'University should be "Uni Augsburg"');
        $this->assertEquals(123, $user->profile->matriculation_number, 'matriculation_number should be "123"');
    }
}
