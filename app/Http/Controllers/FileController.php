<?php

namespace App\Http\Controllers;

use App\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileController extends Controller
{

    private function generateTag(){
        $tag = strtoupper(Str::random(8));
        while (Files::where('tag',$tag)->count() > 0) {
            $tag = strtoupper(Str::random(8));
        }
        return $tag;
    }

    private function respond($data,$state=null,$msg=""){
        $params = ['data' => $data,'state' => $state == null?boolval($data):true,'msg' => $msg];
        return app()->call('\App\Http\Controllers\APIResponseController@respond',$params);
    }

    private function encrypt($data,$salt=""){
        $params = ['text' => $data , 'salt' => $salt];
        return app()->call('\App\Http\Controllers\HomeController@encrypt',$params);
        // return $data;
    }

    // private function decrypt($data,$salt=""){
    //     // return $data;
    //     $params = ['text' => $data , 'salt' => $salt];
    //     return app()->call('\App\Http\Controllers\HomeController@decrypt',$params);
    // }

    private function fileExistTag($tag){
        return Files::where('tag',$tag)->count() > 0;
    }

    private function fileExist($file_name,$file_type,$parent_id){
        return Files::where(['name'=>$file_name,'parent'=>$parent_id,'type'=>$file_type])->count() > 0;
    }

    private function renameFile($tag,$new_name){
        ini_set('memory_limit','256M');
        if(!$this->fileExistTag($tag))
            return false;
        $name = substr($new_name, 0, strripos($new_name, "."));
        $type = explode($name.".", $new_name)[1];
        return Files::where('tag',$tag)
                ->update([
                        'name' => $this->encrypt($name,$tag),
                        'type' => $this->encrypt($type,$tag)
                    ]);
    }

    private function moveFile($tag,$parent){
        if(!$this->fileExistTag($tag))
            return false;
        $out = (object)Files::where('tag',$tag)->first()->only(['name','type']);
        if(Files::where(['name'=>$out->name,'parent'=>$parent,'type'=>$out->type])->count()>0)
            return false;
        return Files::where('tag',$tag)->update([
            'parent' => $parent
            ]);
    }

    private function deleteFile($tag){
        return Files::where('tag' , $tag)->delete();
    }

    private function createFile($file_name,$file_type,$parent_id,$data=""){
        if($this->fileExist($file_name,$file_type,$parent_id))
            return false;
        $tag = $this->generateTag();
        //
        $file = [
                    'name' => $this->encrypt($file_name,$tag),
                    'type' => $this->encrypt($file_type,$tag),
                    'parent' => $parent_id,
                    'tag' => $tag,
                    'data' => $this->encrypt($data,$tag)
                ];
        return Files::insert($file);
    }

    public function showReadFile($tag){
        $data = Files::where('tag',$tag)->first();
        $params = [
                'data' => $data->data,
                'read_file' => true
                ];
        return app()->call('\App\Http\Controllers\HomeController@index',['params'=>$params]);
    }

    public function updateFile(Request $req,$tag){
        // dd($this->encrypt($req->data,$tag));
        $file = Files::where('tag',$tag)->first();
        if($file == null)
            return $this->showReadFile($tag)->withErrors('file tag was wrong !');
        if($file->update(['data'=>$this->encrypt($req->data,$tag)])){
            return $this->showReadFile($tag)->withSuccess('file has been updated !');
        }
        return $this->showReadFile($tag)->withErrors('file update error !');
    }

    public function createFileAPI(Request $req){
        if($this->fileExist($req->name,$req->type,$req->parent))
            return $this->respond([],false,'file $req->file_name already exist !');
        return $this->respond($this->createFile($req->name,$req->type,$req->parent),true,'');
    }

    public function deleteFileAPI(Request $req){
        if(!$this->fileExistTag($req->tag))
            return $this->respond([],false,'file already deleted or not found !');
        return $this->respond($this->deleteFile($req->tag),true,'');
    }

    public function renameFileAPI(Request $req){
        if(!$this->fileExistTag($req->tag))
            return $this->respond([],false,'file not found to rename !');
        return $this->respond($this->renameFile($req->tag,$req->new_name),true,''); 
    }

    public function movingFileAPI(Request $req){
        if(!$this->fileExistTag($req->tag))
            return $this->respond([],false,'moving not work !');
        return $this->respond($this->moveFile($req->tag,$req->parent),true,''); 
    }

    public function uploadFileAPI(Request $req){
        $result = [];
        $parent = $req->parent;
        if($req->files != null){
            foreach ($req->files as $key => $files) {
                foreach ($files as $key => $file) {
                    $type = $file->getClientOriginalExtension();
                    $name = explode(".".$type, $file->getClientOriginalName())[0];
                    $data = \File::get($file->getRealPath());
                    array_push($result,$this->createFile($name,$type,$parent,$data));
                }
            }
        }
        return $result;
    }
    
}
