<?php

namespace App\Http\Controllers;

use App\Folders;
use App\Files;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    private function _normalizeString($str = ''){
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

    private function _psSanitizePath($str,$pre=false){
        $str = str_replace("\"","",$str);
        $str = str_replace("`","'",$str);
        $str = str_replace("..","",$str);
        $str = str_replace("%2E%2E","",$str);
        $str = str_replace("./","",$str);
        $str = str_replace(":","",$str);
        $str = str_replace('"',"'",$str);
        $str = str_replace('//',"/",$str);
        if (substr($str,0,1) == "/" && $pre == false) { $str = substr($str,1); }
        return $str;
    }

    private function respond($data,$state=null,$msg=""){
        $params = ['data' => $data,'state' => $state == null ?boolval($data):boolval($state) ,'msg' => $msg];
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

    private function generateTag(){
        $tag = strtoupper(Str::random(8));
        while (Folders::where('tag',$tag)->count() > 0) {
            $tag = strtoupper(Str::random(8));
        }
        return $tag;
    }

    private function renamesPosibility($tag,$new_name){
        $parent = $this->getParentByTag($tag);
        $folders = Folders::where('id',$parent)->where('name',$new_name);
        return $folders->count() == 0;
    }

    private function moveDirectory($tag,$new_dest_parent){
        if(!$this->directoryTagExist($tag) || !$this->directoryTagExist($new_dest_parent))
            return false;
        if($this->getAllSubChildsByTag($tag)->contains(['tag'=>$new_dest_parent]))
            return false;   // The Destination Folder Nested the Source Folder
        return Folders::where('tag',$tag)->update([
            'parent' => $new_dest_parent
            ]);
    }

    private function renameDirectory($tag,$new_name){
        if(!$this->directoryTagExist($tag))
            return false;
        if(!$this->renamesPosibility($tag,$new_name))
            return false;
        return Folders::where('tag',$tag)
                ->update(['name' => $this->encrypt($new_name,$tag)]);
    }

    private function deleteDirectory($tag){
        /**/
        $subItems = $this->getAllSubChildsByTag($tag);
        foreach ($subItems as $key => $item) {
            Folders::where('tag' , $item['tag'])->delete();
        }
        foreach ($subItems as $key => $item) {
            Files::where('tag' , $item['tag'])->delete();
        }
        Folders::where('tag' , $tag)->delete();
        return true;
    }

    private function createDirectory($folder_name,$parent_id){
        if($this->directoryExist($folder_name,$parent_id))
            return false;
        $tag = $this->generateTag();
        $file = [
                    'name' => $this->encrypt($folder_name,$tag),
                    'parent' => $parent_id,
                    'tag' => $tag
                ];
        return Folders::insert($file);
    }

    private function getRoot(){
        $folders = Folders::where('parent',"0")->get();
        $files = Files::where('parent',"0")->get();
        $parts = collect();
        $new_folders = collect();
        $new_files = collect();
        foreach($folders as $folder){
            $new = $folder->only(['tag','parent','name','permission']);
            $new['type'] = "folder";
            $new_folders->push($new);
        }
        foreach($files as $files)
            $new_files->push($files->only(['tag','parent','name','type','permission']));
        if($new_folders->count() > 0)
            $parts->push($new_folders->toArray());
        if($new_files->count() > 0)
            $parts->push($new_files->toArray());
        return $parts;
    }

    private function getChildsByTag($tag){
        if($tag === strval(0))
            return $this->getRoot();
        $parts = collect();
        $new_folders = collect();
        $new_files = collect();
        $folders = Folders::ofParent($tag)->get();
        $files = Files::ofParent($tag)->get();
        foreach($folders as $folder){
            $new = $folder->only(['tag','parent','name','permission']);
            $new['type'] = "folder";
            $new_folders->push($new);
        }
        foreach($files as $files)
            $new_files->push($files->only(['tag','parent','name','type','permission']));
        if($new_folders->count() > 0)
            $parts->push($new_folders->toArray());
        if($new_files->count() > 0)
            $parts->push($new_files->toArray());
        return $parts;
    }

    /*
        Use this Function for deleting folder and his sub items
    */
    private function getAllSubChildsByTag($tag){
        if($tag === false)
            return false;
        $total = collect();
        $folders = Folders::ofParent($tag)->select(["tag"])->get();
        $files = Files::ofParent($tag)->select(["tag"])->get()->toArray();
        $total = $total->concat($folders->toArray())->concat($files);
        while ($folders->count() > 0) {
            foreach ($folders->toArray() as $key => $folder) {
                $folders = Folders::ofParent($folder['tag'])->select(["tag"])->get();
                $files = Files::ofParent($folder['tag'])->select(["tag"])->get()->toArray();
                $total = $total->concat($folders->toArray())->concat($files);
            }
        }
        return $total;
    }

    private function getParentByTag($tag){
        $folder = Folders::where('tag',$tag)->first();
        return $folder->parent;
    }

    public function getDirectoryAPI(Request $req){
        // return dd($this->getAllSubChildsByTag($req->tag));
        // return dd($this->getChildsById($req->id));
        if($req->tag != null)
            return $this->respond($this->getChildsByTag($req->tag),true);
        else
            return $this->respond([],false);
    }

    private function directoryTagExist($tag){
        return Folders::where('tag',$tag)->count() > 0 ;
    }

    private function directoryExist($folder_name,$parent_id){
        return Folders::where(['name'=>$folder_name,'parent'=>$parent_id])->count() > 0;
    }

    public function createDirectoryAPI(Request $req){
        if($this->directoryExist($req->name,$req->parent))
            return $this->respond([],false,'folder '.$req->folder_name.' already exist !');
        return $this->respond($this->createDirectory($req->name,$req->parent));
    }

    public function movingDirectoryAPI(Request $req){
        if(!$this->directoryTagExist($req->tag))
            return $this->respond([],false,'moving not work !');
        return $this->respond($this->moveDirectory($req->tag,$req->parent)); 
    }

    public function removeDirectoryAPI(Request $req){
        if(!$this->directoryTagExist($req->tag))
            return $this->respond([],false,'folder already deleted or not found !');
        return $this->respond($this->deleteDirectory($req->tag));
    }

    public function renameDirectoryAPI(Request $req){
        if(!$this->directoryTagExist($req->tag))
            return $this->respond([],false,'folder not found to rename !');
        return $this->respond($this->renameDirectory($req->tag,$req->new_name)); 
    }
    public function setDirectoryPermissionAPI(){

    }
    public function getDirectoryPropertiesAPI(){

    }
}
