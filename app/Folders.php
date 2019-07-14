<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FoCrypto{
    public static function decrypt($data,$salt=""){
        $params = ['text' => $data , 'salt' => $salt];
        return app()->call('\App\Http\Controllers\HomeController@decrypt',$params);
    }
}

class Folders extends Model
{
	use SoftDeletes;
	protected $table = 'folders';
    protected $primary_key = 'id';
    protected $guarded = ['tag'];
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'name',
    						'parent',
    						'permission'
    					];
            
    public function getNameAttribute(){
        return FoCrypto::decrypt($this->attributes['name'],$this->attributes['tag']);
    }

    public function scopeOfParent($query,$tag){
        return $query->where('parent',strval($tag));
    }

    public function scopeOfId($query,$id){
        return $query->where('id',$id);
    }   
}
