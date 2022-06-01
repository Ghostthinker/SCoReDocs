<?php

namespace App\Http\Controllers;

use App\Repositories\LinkRepositoryInterface;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function getCount(Request $request, $refId, LinkRepositoryInterface $repository)
    {
        return $repository->getByRefId($refId)->count();
    }
}
