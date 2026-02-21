<?php

namespace App\Http\Controllers\Api\V1\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\OrganizationInvite;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function index(Organization $org)
    {
        //
    }

    public function store(Request $request, Organization $org)
    {
        //
    }

    public function accept(Organization $org, OrganizationInvite $invite)
    {
        //
    }

    public function destroy(Organization $org, OrganizationInvite $invite)
    {

    }
}
