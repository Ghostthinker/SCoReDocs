<?php

namespace App\Http\Controllers\Auth;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Project;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'surname' => ['required', 'string', 'max:255'],
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'matriculation_number' => ['required', 'numeric', 'digits_between:1,11'],
            'university' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'privacy' => ['required'],
            'terms' => ['required'],
            'student' => ['required'],
            'upload_privacy_agreement' => ['required', 'mimes:jpeg,jpg,pdf', 'max:2048'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     *
     * @return \App\User
     */
    protected function create(array $data)
    {
        // Creating a assessement doc for the new user
        $template = Project::ofType(new ProjectType(ProjectType::TEMPLATE))->first();
        $assessmentDoc = null;
        if ($template != null) {
            $assessmentDoc = $template->duplicate();
            $assessmentDoc->type = ProjectType::ASSESSMENT_DOC;
            $assessmentDoc->status = ProjectStatus::IN_PROGRESS;
            $assessmentDoc->save();
        }
        // Creating user
        $user = User::create([
            'surname' => $data['surname'],
            'firstname' => $data['firstname'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'assessment_doc_id' => $assessmentDoc ? $assessmentDoc->id : null,
            'accepted_terms_of_usage' => true,
            'accepted_privacy_policy' => true,
            'uploaded_privacy_agreement' => true,
            'accepted_student_in_germany' => true,
        ]);
        $user->assignRole('Studentin');
        if ($user->id) {
            Profile::create([
                'user_id' => $user->id,
                'matriculation_number' => $data['matriculation_number'],
                'university' => $data['university'],
            ]);

            $file = $data['upload_privacy_agreement'];

            if ($file != null) {
                $filename = 'user-'.$user->id.'.'.$file->getClientOriginalExtension();
                $filepath = $file->storeAs('uploads', $filename);
                $user->uploaded_privacy_filepath = $filepath;
                $user->save();
            }
        }
        if ($assessmentDoc != null) {
            $assessmentDoc->assessment_doc_owner_id = $user->id;
            $assessmentDoc->save();
        }
        return $user;
    }
}
