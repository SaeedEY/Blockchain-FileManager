@section('main-toolbar')
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="pull-left navbar-toggle collapsed treeview-toggle-btn" data-toggle="collapse" data-target="#treeview-toggle" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>

			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#options" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="fa fa-gears"></span>
			</button>

			<!-- Search button -->
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#pb-filemng-navigation" aria-expanded="false">
				<span class="sr-only">Toggle navigation</span>
				<span class="fa fa-share"></span>
			</button>
		</div>

		<ul class="collapse navbar-collapse nav navbar-nav navbar-right" id="options">

			<!-- <li><a href="#"><span class="fa fa-crosshairs fa-lg"></span></a></li> -->
			<li><a href="#"><span class="fa fa-ellipsis-v fa-lg"></span></a></li>
			<!-- <li><a href="#"><span class="fa fa-lg fa-server"></span></a></li> -->
			<!-- <li><a href="#"><span class="fa fa-lg fa-minus"></span></a></li> -->
			<li><a href="#"><span class="fa fa-lg fa-window-maximize"></span></a></li>
			<li><a href="#"><span class="fa fa-lg fa-times"></span></a></li>
		</ul>

		<div class="collapse navbar-collapse" id="pb-filemng-navigation">
			<ul class="nav navbar-nav">
				<li><a><span class="fa fa-chevron-left fa-lg" onclick="back(this)" id="backward"></span></a></li>
				<li><a><span class="fa fa-chevron-right fa-lg" onclick="forward(this)" id="forward"></span></a></li>
				<!-- <li class="pb-filemng-active"><a href="#"><span class="fa fa-file fa-lg"></span> -->
				<li><a href="#"><img src="/images/new-folder.png" style="width:25px" onclick="createFolder(this)"></span></a></li>
			<li><a href="#"><img src="/images/new-file.png" style="width:20px" onclick="createFile(this)"></span></a></li></a></li>
			</ul>
		</div>
	</div>
@endsection