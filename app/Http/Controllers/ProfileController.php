<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Repositories\ProfileRepositoryInterface;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * @param ProfileRepositoryInterface $repository
     *
     * @return ProfileResource
     */
    public function show(ProfileRepositoryInterface $repository)
    {
        $profile = $repository->getByUser(Auth::id());
        return new ProfileResource($profile);
    }


    /**
     *
     * returns the profile for a specific userid
     *
     * @param  Request  $request
     * @param  ProfileRepositoryInterface  $repository
     * @return mixed
     */
    public function getById(Request $request, ProfileRepositoryInterface $repository) {
        return $repository->getByUser($request->get('profile_id'));
    }

    /**
     *
     * returns all profiles for all users
     *
     * @param  ProfileRepositoryInterface  $repository
     * @return mixed
     */
    public function getProfiles(ProfileRepositoryInterface $repository) {
        return $repository->getProfiles();
    }

    /**
     * @param Request                    $request
     * @param ProfileRepositoryInterface $repository
     *
     * @return bool
     */
    public function update(Request $request, ProfileRepositoryInterface $repository)
    {
        $profile = $repository->getByUser(Auth::id());
        if (!$profile) {
            Log::error('User Profile not found for id: '. Auth::id());
            abort(404, 'User Profile not found!');
        }

        $request->validate(
            [
                'avatarFile' => 'image|mimes:jpeg,png,jpg|max:4000',
                'matriculationNumber' => 'numeric|digits_between:1,11|nullable',
            ],
            [
                'avatarFile.image' => 'Es können nur Bilder hochgeladen werden',
                'avatarFile.mimes' => 'Das Profilbild muss vom Typ png,jpeg,jpg sein',
                'avatarFile.max' => 'Das Profilbild darf nicht größer als 4MB sein',
                'matriculationNumber.numeric' => 'Die Matrikelnummer darf nur aus Zahlen bestehen',
                'matriculationNumber.digits_between' => 'Die Matrikelnummer darf maximal 11 Zeichen lang sein'
            ]
        );

        $file = $request->file('avatarFile');
        if ($file != null) {
            $filepath = $file->store('uploads');


            $filename = basename($filepath);

            $store_path = storage_path('app/uploads') . DIRECTORY_SEPARATOR . $filename;
            $img = Image::make($store_path);

            $width = 100;
            $height = 100;

            // we need to resize image, otherwise it will be cropped
            if ($img->width() > $width) {
                $img->resize($width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }

            if ($img->height() > $height) {
                $img->resize(null, $height, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $img->resizeCanvas($width, $height, 'center', false, '#ffffff');

            $img->save();

            $url = route('file.deliver', $filename, false);

            //remove old image
            if ($profile->avatar) {
                $oldImage = str_replace('/file/uploads/', '', $profile->avatar);
                Storage::disk('local')->delete('uploads/'.$oldImage);
            }
        }

        if (isset($profile)) {
            $data = [
                'university' => $request->input('university'),
                'course' => $request->input('course'),
                'matriculation_number' => $request->input('matriculationNumber'),
                'knowledge' => $request->input('knowledge'),
                'personal_resources' => $request->input('personalResources'),
                'about_me' => $request->input('aboutMe'),
            ];
            if (isset($url)) {
                $data['avatar'] = $url;
            }
            return $repository->update($profile->id, $data);
        }
        return false;
    }
}
