<?php

namespace Domains\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationMember extends Model
{
    protected $table = 'organization_user';

    protected $fillable = [
        'organization_id',
        'user_id',
        'role'
    ];
}
