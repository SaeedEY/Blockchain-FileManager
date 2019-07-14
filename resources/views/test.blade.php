<!DOCTYPE html>
<head>
<meta charset="UTF-8" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script type="text/javascript" src="http://127.0.0.1:8000/bootstrap/js/jquery-3.3.1.js"></script>
<style type="text/css">

	body
	{
		font-family: "Segoe UI", Tahoma, Helvetica, freesans, sans-serif;
		font-size: 90%;
		margin: 10px;
		color: #333;
		background-color: #fff;
	}

	h1, h2
	{
		font-size: 1.5em;
		font-weight: normal;
	}

	h2
	{
		font-size: 1.3em;
	}

	legend
	{
		font-weight: bold;
		color: #333;
	}

	#filedrag
	{
		display: none;
		font-weight: bold;
		text-align: center;
		padding: 1em 0;
		margin: 1em 0;
		color: #555;
		border: 2px dashed #555;
		border-radius: 7px;
		cursor: default;
	}

	#filedrag.hover
	{
		color: #f00;
		border-color: #f00;
		border-style: solid;
		box-shadow: inset 0 3px 4px #888;
	}

	img
	{
		max-width: 100%;
	}

	pre
	{
		width: 95%;
		height: 8em;
		font-family: monospace;
		font-size: 0.9em;
		padding: 1px 2px;
		margin: 0 0 1em auto;
		border: 1px inset #666;
		background-color: #eee;
		overflow: auto;
	}

	#messages
	{
		padding: 0 10px;
		margin: 1em 0;
		border: 1px solid #999;
	}

	#progress p
	{
		display: block;
		width: 240px;
		padding: 2px 5px;
		margin: 2px 0;
		border: 1px inset #446;
		border-radius: 5px;
		background: #eee url("progress.png") 100% 0 repeat-y;
	}

	#progress p.success
	{
		background: #0c0 none 0 0 no-repeat;
	}

	#progress p.failed
	{
		background: #c00 none 0 0 no-repeat;
	}
</style>
</head>
<body>


<form id="upload" action="/upload" method="POST" enctype="multipart/form-data">
	@csrf
	@method('PUT')
	<fieldset>
		<legend>HTML File Upload</legend>

		<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="300000" />

		<div>
			<label for="fileselect">Files to upload:</label>
			<input type="file" id="fileselect" name="fileselect[]" multiple/>
			<div id="filedrag">or drop files here</div>
		</div>

		<div id="submitbutton">
			<button type="submit">Upload Files</button>
		</div>

	</fieldset>

</form>

<script>
(function() {

	// // getElementById
	function $id(id) {
		return document.getElementById(id);
	}


	// // output information
	// function Output(msg) {
	// 	var m = $id("messages");
	// 	m.innerHTML = msg + m.innerHTML;
	// }


	// file drag hover
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}


	// file selection
	function FileSelectHandler(e) {

		// cancel event and hover styling
		FileDragHover(e);

		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;
		// console.log(e.dataTransfer);
		// console.log(e.dataTransfer.items);
		// console.log(e.dataTransfer.files);
		// console.log(e.target);
		// console.log(e.target.files);
		Upload(files);
		// process all File objects
		// for (var i = 0, f; f = files[i]; i++) {
		// 	ParseFile(f);
		// }

	}


	// output file information
	// function ParseFile(file) {

	// 	Output(
	// 		"<p>File information: <strong>" + file.name +
	// 		"</strong> type: <strong>" + file.type +
	// 		"</strong> size: <strong>" + file.size +
	// 		"</strong> bytes</p>"
	// 	);

	// }


	// initialize

	function Upload(Field){
		var xhr = new XMLHttpRequest();
		var formData = new FormData();
		console.log(Field);
		for(var i=0;i<Field.length;i++)
			formData.append('files[]',Field[i]);
		formData.append('_token','{{csrf_token()}}');
		xhr.open('POST', "upload", true);
		xhr.addEventListener('readystatechange', function(e) {
			if (xhr.readyState == 4 && xhr.status == 200) {
				alert("Uploaded");
			}
			else if (xhr.readyState == 4 && xhr.status != 200) {
				alert("Upload Failed");
			}
		});
		xhr.send(formData);
	}

	function Init() {

		var fileselect = $id("fileselect"),
			filedrag = $id("filedrag"),
			submitbutton = $id("submitbutton");

		fileselect.addEventListener("change", FileSelectHandler, false);

		var xhr = new XMLHttpRequest();
		if (xhr.upload) {
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";
			submitbutton.style.display = "none";
		}

	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}


})();
</script>
</body>
</html>