<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permissions extends Model
{
	use SoftDeletes;
	protected $table = 'permissions';
	protected $primary_key = 'id';
   	protected $dates = ['deleted_at'];
}
