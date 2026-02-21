<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function __invoke(Request $request)
    {
        return response()->json($request->user());
    }
}
