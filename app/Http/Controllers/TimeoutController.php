<?php

namespace App\Http\Controllers;

use App\Repositories\TimeoutRepositoryInterface;
use Illuminate\Http\Request;

class TimeoutController extends Controller
{
    /**
     * Request is used to reset the timeout
     *
     * @param  Request  $request  The request sent by the user
     * @param  int  $project_id The project id of the section
     * @param  int  $section_id  The section id to reset the timeout
     * @param  TimeoutRepositoryInterface  $repository  The timeout repository
     * @return mixed Just an indicator if update worked
     */
    public function resetTimeout(Request $request, int $project_id, int $section_id, TimeoutRepositoryInterface $repository)
    {
        return $repository->updateTimeout($section_id);
    }
}
