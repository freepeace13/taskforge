<?php

namespace Domains\Organization\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = ['name', 'owner_id'];
}
