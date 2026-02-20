<?php

namespace Modules\Organization;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'owner_id'];
}
