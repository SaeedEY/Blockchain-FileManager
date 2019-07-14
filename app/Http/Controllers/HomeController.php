<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
	private $NAME = "Sedo";
	private function view($frame){
		return view($frame)
			->with('site_name',"The ".$this->NAME." File Manager")
			->with('site_description',"Simple & Secure File Manager")
			->with('title',$this->NAME." File Manager");
	}
    public function index(array $params=[]){
    	session()->put('p',"Test Pass");
    	// dd($params);
    	$view = $this->view("index");
    	foreach ($params as $name => $value) {
    		$view = $view->with($name,$value);
    	}
    	return $view;
    }
	
	public function encrypt($text,$salt) {
		$password = md5(session()->get('p').$salt);
	    $method = "AES-256-CBC";
	    $key = hash('sha256', $password, true);
	    $iv = openssl_random_pseudo_bytes(16);
	    $ciphertext = openssl_encrypt($text, $method, $key, OPENSSL_RAW_DATA, $iv);
	    $hash = hash_hmac('sha256', $ciphertext, $key, true);
	    $out = $iv . $hash . $ciphertext;
	    return base64_encode($out);
	}
	
	public function decrypt($text,$salt) {
		$text = base64_decode($text);
		$password = md5(session()->get('p').$salt);
	    $method = "AES-256-CBC";
	    $iv = substr($text, 0, 16);
	    $hash = substr($text, 16, 32);
	    $ciphertext = substr($text, 48);
	    $key = hash('sha256', $password, true);
	    if (hash_hmac('sha256', $ciphertext, $key, true) !== $hash) return null;
	    return openssl_decrypt($ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv);
	}
}
