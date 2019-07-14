@section('file-container')
	<form method="POST" action="" id="edit_file">
		@csrf
		@method('PUT')
		<textarea name="data" style="width: 100%;height: 100%;padding: 5px;border: 1px outset white;border-radius: 5px;margin: 0px;max-width: 100%;min-width: 100%;margin:auto">{{isset($data)?$data:""}}</textarea>
	</form>
@endsection