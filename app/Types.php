<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Types extends Model
{
    use SoftDeletes;
	protected $table = 'types';
	protected $primary_key = 'id';
   	protected $dates = ['deleted_at'];
}
