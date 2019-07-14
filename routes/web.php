<?php













Route::get('/', "HomeController@index");
Route::get('/{tag}', "FileController@showReadFile")
	->where('tag','[\d\w]{8}');
Route::put('/{tag}', "FileController@updateFile")
	->where('tag','[\d\w]{8}');
Route::get('/test',function(){
	return view('test');
});
Route::any('/upload', "FileController@uploadFileAPI");


Route::prefix('api')->group(function(){
	Route::prefix('web')->group(function(){
		Route::post('getDir',"FolderController@getDirectoryAPI");
		Route::post('crtDir',['uses'=>"FolderController@createDirectoryAPI"]);
		Route::post('rnmDir',['uses'=>"FolderController@renameDirectoryAPI"]);
		Route::post('remDir',['uses'=>"FolderController@removeDirectoryAPI"]);
		Route::post('movDir',['uses'=>"FolderController@movingDirectoryAPI"]);
		Route::post('prmDir',['uses'=>"FolderController@setDirectoryPermissionAPI"]);
		Route::post('prpDir',['uses'=>"FolderController@getDirectoryPropertiesAPI"]);
		// Route::post('rdFile',['uses'=>"FileController@readFileAPI"]);
		Route::post('rnFile',['uses'=>"FileController@renameFileAPI"]);
		Route::post('mvFile',['uses'=>"FileController@movingFileAPI"]);
		Route::post('ctFile',['uses'=>"FileController@createFileAPI"]);
		Route::post('dlFile',['uses'=>"FileController@deleteFileAPI"]);
		Route::post('prFile',['uses'=>"FileController@setFilePermissionAPI"]);
		Route::post('ppFile',['uses'=>"FileController@getFilePropertiesAPI"]);
	});
});