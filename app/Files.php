<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiCrypto{
    public static function decrypt($data,$salt=""){
        $params = ['text' => $data , 'salt' => $salt];
        return app()->call('\App\Http\Controllers\HomeController@decrypt',$params);
    }
}

class Files extends Model
{
	use SoftDeletes;
    protected $table = 'files';
    protected $primary_key = 'id';
    protected $guarded = ['tag'];
    protected $dates = ['deleted_at'];
    protected $fillable = [
    						'data',
    						'name',
                            'type',
                            'parent',
    						'permission'
    					];

    public function getDataAttribute($value){
        return FiCrypto::decrypt($this->attributes['data'],$this->attributes['tag']);
    }

    public function getNameAttribute($value){
        return FiCrypto::decrypt($this->attributes['name'],$this->attributes['tag']);
    }

    public function getTypeAttribute($value){
        return FiCrypto::decrypt($this->attributes['type'],$this->attributes['tag']);
    }
    
    public function scopeOfParent($query,$tag){
        return $query->where('parent',$tag);
    }

    public function scopeOfId($query,$id){
        return $query->where('id',$id);
    }	
}
