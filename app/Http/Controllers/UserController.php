<?php

namespace App\Http\Controllers;

use App\Enums\MenuType;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepositoryInterface;
use App\Services\UserService;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param UserRepositoryInterface $repository
     *
     * @return User[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getUsers(UserRepositoryInterface $repository)
    {
        return $repository->getAllWithRoles();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Role[]
     */
    public function getRoles()
    {
        return Role::all();
    }

    /**
     * @param Request $request
     * @param UserService $userService
     */
    public function updateRoles(Request $request, UserService $userService)
    {
        $userService->updateRoles($request->all());
    }


    /**
     * Returns current user
     *
     * @param Request $request
     * @return UserResource
     */
    public function getCurrentUser(Request $request)
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function downloadAgreementsDataProcessing(UserRepositoryInterface $repository)
    {
        $path = storage_path('app/uploads');

        $zip_file = 'score_'.date("Y").'_'.date("m").'_'.date("d").'.zip';
        $zip = new \ZipArchive();
        $zip->open($path.'/'.$zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);



        $students = $repository->getUsersWithRoleStudent();
        foreach($students as $student) {
            if ($student->uploaded_privacy_filepath) {
                $filePath = Storage::path($student->uploaded_privacy_filepath);
                $relativePath = substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $numFiles = $zip->numFiles;
        $zip->close();

        if($numFiles > 0){
            return response()->download($path.'/'.$zip_file);
        }
        abort('404');
    }

    public function markIntroVideoAsSeen(UserRepositoryInterface $repository)
    {
        return $repository->markIntroVideoAsSeen();
    }

    public function setMenuCollapseState(
        Request $request,
        $menu,
        UserRepositoryInterface $repository
    ) {
        if($menu === MenuType::LEFTMENU) {
            return  $repository->toggleLeftMenuCollapseState();
        } else if ($menu === MenuType::RIGHTMENU) {
            return  $repository->toggleRightMenuCollapseState();
        }
    }
}
