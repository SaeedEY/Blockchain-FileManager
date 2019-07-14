<!DOCTYPE html>
<html>
	<head>@yield('header')</head>
	<body onload="_onload()">

	<div class="container">
		<div class="page-header">
			<h1>{{$site_name}} <small>{{$site_description}}</small></h1>
		</div>
		<div class="container pb-filemng-template">
			@if ($errors->any())
				<div class="alert alert-danger login_alert" style="text-align: center;width: auto;margin-left: 10%;margin-right: 10%;margin-top: 2%;">
					<ul>
						@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
						@endforeach
					</ul>
				</div>
			@elseif(isset($success))
				<div class="alert alert-success" style="text-align: center;width: auto;margin-left: 10%;margin-right: 10%;margin-top: 2%;">
					<span>{{$success}}</span>
				</div>
			@endif
			<div class="row">
				<div class="col-md-8 col-md-offset-2">
					<nav class="navbar navbar-default pb-filemng-navbar">
						@if(isset($read_file) && $read_file)
							@yield('file-toolbar')
						@else
							@yield('main-toolbar')
						@endif
					</nav>
					<input style="visibility:hidden;height:1px;" type="file" id="files" name="files[]" multiple/>
					<nav class="breadcrumb" id="breadcrumb" style="background:white;margin-bottom:0px">
						
					</nav>
					<div class="panel panel-default" id="drag_place">
						<div class="panel-body pb-filemng-panel-body">
							<div class="row">
								<div class="col-sm-3 col-md-4 pb-filemng-template-treeview">
								    <div class="collapse navbar-collapse" id="treeview-toggle">
								        <div class="sui-treeview">
								            <div id="treeview" style="display: none;"></div>
								            <ul class="sui-treeview-list" tabindex="0" role="tree">
								                <li class="sui-treeview-item sui-unselectable" role="treeitem" aria-describedby="shielddx" aria-expanded="false">
								                    <div class="sui-treeview-item-content"><span class="sui-treeview-item-toggle"><span class="sui-treeview-item-toggle-collapsed"></span></span><span onclick="openDir(0)" class="sui-treeview-item-text" id="shielddx"><span class="sui-treeview-item-icon fa fa-home"></span>Home</span>
								                    </div>
								                    <ul class="sui-treeview-list sui-treeview-item-list" role="group" style="display: none;"></ul>
								                </li>
								            </ul>
								        </div>
								    </div>
								</div>
								@if(isset($read_file) && $read_file)
									@yield('file-container')
								@else
									@yield('nav-pan')
									@yield('main-container')
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div style="display:none" class="storage" id="storage"></div>
		<div class="hide menu" id="rmenu">
            <ul class="menu_ul" >
                <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="1">Open</a>
                </li>
               <!--  <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="2">Copy</a>
                </li> -->
                <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="3">Cut</a>
                </li>
                <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="4">Paste</a>
                </li>
                <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="5">Delete</a>
                </li>
                <li class="menu_li">
                    <a onclick="menuAction(this)" data-id="6">Rename</a>
                </li>
                <li>
                    <a onclick="menuAction(this)" data-id="7">Properties</a>
                </li>
            </ul>
        </div>
		<script>

			class PageManager{
				constructor(){ 
				    this.pages = []; 
				    this.names = ["Main"];
				    this.backward = document.getElementById('backward');
				    this.breadcrumb = document.getElementById("breadcrumb");
				}
				_findName(tag){
					var imgs = document.getElementsByClassName("img-responsive items");
					var names = document.getElementsByClassName("pb-filemng-paragraphs");
					for(var i=0;i<imgs.length;i++){
						if(imgs[i].getAttribute('data-tag') == tag){
							return names[i].innerHTML;
						}
					}
				}
				isEmptyPages(){
				    return this.pages.length == 0; 
				}
				next(folder_tag){
				    this.pages.push(folder_tag);
					this.backward.style.color = "#777";
					this.backward.disabled = false; 
					this.names.push(this._findName(folder_tag));
					this.generate();
				}
				back(){
				    if (this.pages.length > 0)
				    	return _openDir(this.pages.pop());
				    this.backward.style.color = "#d0d0d0";
					this.backward.disabled = true;
					this.generate();
				}
				generate(){
					var active = document.createElement("span");
					var elms = [];
					while(this.names.length != 0){
						var elm = document.createElement("a");
						elm.className = "breadcrumb-item";
						elm.innerHTML = this.names.pop();
						elms.push(elm);
					}
					active.className = "breadcrumb-item active";
					active.innerHTML = elms.pop().innerHTML;
					while(elms.length != 0)
						this.breadcrumb.appendChild(elms.pop());
					this.breadcrumb.appendChild(active);
				}
				show(){
					return this.pages;
				}
			}

			var page = new PageManager();
			var parent = null;
			var token = null;

			(function() {
				function $id(id) {
					return document.getElementById(id);
				}
				function FileDragHover(e) {
					e.stopPropagation();
					e.preventDefault();
				}
				function FileSelectHandler(e) {
					FileDragHover(e);
					var files = e.target.files || e.dataTransfer.files;
					Upload(files);
				}
				function Upload(Field){
					var xhr = new XMLHttpRequest();
					var formData = new FormData();
					for(var i=0;i<Field.length;i++)
						formData.append('files[]',Field[i]);
					formData.append('_token',token);
					formData.append('parent',parent);
					xhr.open('POST', "upload", true);
					xhr.addEventListener('readystatechange', function(e) {
						if (xhr.readyState == 4 && xhr.status == 200) {
							alert("فایل ها آپلود شدند !");
							openDir(parent);
						}
						else if (xhr.readyState == 4 && xhr.status != 200) {
							alert("آپلود ناموفق بود !");
						}
					});
					xhr.send(formData);
				}
				function Init() {
					var fileselect = $id("files"),
						filedrag = $id("drag_place");
					fileselect.addEventListener("change", FileSelectHandler, false);
					var xhr = new XMLHttpRequest();
					if (xhr.upload) {
						filedrag.addEventListener("dragover", FileDragHover, false);
						filedrag.addEventListener("dragleave", FileDragHover, false);
						filedrag.addEventListener("drop", FileSelectHandler, false);
						// filedrag.style.display = "block";
					}
				}
				if (window.File && window.FileList && window.FileReader) {
					Init();
				}
			})();

			/*
						<a class="breadcrumb-item" href="#">Main</a>
						<span class="breadcrumb-item active" id="active_bc">Sub</span>
			*/
			
			

			// function _addForward(tag){
			// 	console.log("add Backward");
			// 	pages.push(tag);
			// 	console.log("add Backward "+tag);
			// 	document.getElementById('forward').style.color = "#777";
			// 	document.getElementById('forward').disabled = false;
			// }
			// function forward(elm){
			// 	console.log("forward");
			// 	// var pages = JSON.parse(_getStorage("pages",pages));
			// 	// var backs = JSON.parse(_getStorage("backs",backs));
			// 	if(pages.isEmpty() || elm.disabled){
			// 		elm.style.color = "#d0d0d0";
			// 		return;
			// 	}
			// 	var i = pages.pop();
			// 	_openDir(i);
			// 	console.log("forward poped : "+i);
			// 	if(pages.isEmpty() || elm.disabled || i === false){
			// 		elm.style.color = "#d0d0d0";
			// 		elm.disabled = true;
			// 		return;
			// 	}
			// 	// backs.push(i);
			// 	// _setStorage("pages",JSON.stringify(pages));
			// 	// _setStorage("backs",JSON.stringify(backs));
			// }

			
			function _getStorage(param){
				var storage = document.getElementById('storage');
				var value = storage.getAttribute("data-"+param);
				return (value != "" && value != undefined) ? value : 0;
			}

			function _setStorage(param,value){
				var storage = document.getElementById('storage');
				value = (value != "" && value != undefined) ? value : 0;
				storage.setAttribute("data-"+param,value);
				return true;
			}

			function _onload(){
				token = '{{csrf_token()}}';
				parent = 0;
				_onStartNavbar();
				openDir(parent);
				$(document).bind("click", function(event) {
				    document.getElementById("rmenu").className = "hide";
				});
				// _setStorage("backs",JSON.stringify(backs));
				// _setStorage("pages",JSON.stringify(pages));
				document.getElementById('backward').disabled = true;
				document.getElementById('forward').disabled = true;
				document.getElementById('backward').style.color = "#d0d0d0";
				document.getElementById('forward').style.color = "#d0d0d0";
			}

			function _onStartNavbar(){
				//Need to work here for showing the opened directories in navbar as tree
				var dataSrc = [
					{
				        "text": "Recent", 
				        "iconCls": "fa fa-history"
				    },
				    {
				        "text": "Home", 
				        "iconCls": "fa fa-home",
				        "items" : [{
				        	 "text": "name", 
				        	"iconCls": "fa fa-folder"
				        }],
				        "id" : "alert"
				    },
				    {
				        "text": "Recycle bin",
				        "iconCls": "fa fa-trash-o"
				    }
				];
				_loadNavbar(dataSrc);
			}

			function _loadNavbar(navbarData){
				$("#treeview").empty();
				// $("#treeview").shieldTreeView({
				// 	dataSource: navbarData
				// });
			}

			function _loadDirectory(fileData){
				$(".pb-filemng-template-body").empty();
				for(var i=0;i<fileData.length;i++){
					for (var key in fileData[i]) {
						var el = fileData[i][key];
						// parent = el.parent;
						var div = document.createElement("div");
						div.className = "col-xs-6 col-sm-6 col-md-3 pb-filemng-body-folders";

						var img = document.createElement("img");
						img.className = "img-responsive items";
						img.src = "/images/"+el.type+".png";
						img.setAttribute("onclick" ,el.type=='folder'?'openDir("'+el.tag+'")':'openFile("'+el.tag+'")');
						img.setAttribute("data-p",parent);
						img.setAttribute("data-type",el.type);
						img.setAttribute("data-tag",el.tag);

						var p = document.createElement("p");
						p.className = "pb-filemng-paragraphs";
						p.innerHTML = el.name + (el.type !='folder' ? '.' + el.type : '');
						if(el.type == "folder")
							p.setAttribute("ondblclick","renameDir('"+el.tag+"','"+el.name+"')");
						else
							p.setAttribute("ondblclick","renameFile('"+el.tag+"','"+el.type+"','"+el.name+"')");

						div.appendChild(img);
						div.appendChild(document.createElement("br"));
						div.appendChild(p);
						div.setAttribute("oncontextmenu","listenToMenu(this)");
						$(".pb-filemng-template-body").append(div);

						// var el = fileData[i][key];
						// _setStorage('parent',parent);
						// $(".pb-filemng-template-body").append(
						// '<div 	class=\"col-xs-6 col-sm-6 col-md-3 pb-filemng-body-folders\">' +
						// '<img 	class=\"img-responsive items\"  src=\"/images/'+el.type+'.png\"'+
						// 		'onclick=\"'+(el.type == 'folder'
						// 				?'openDir('+el.id+')':'openFile(\''+el.tag+'\')')+
						// 		'\" data-p=\"'+el.parent+'\">' + '<br/>' + 
						// '<p class="pb-filemng-paragraphs">' + el.name + (el.type !='folder' ? '.' + el.type : '')+'</p>' + 
						// '</div>'
						// );
					}
				}
			}

			function _openDir(tag = false){
				// var tag = tag === false ? parent : tag;
				$.post({url: "/api/web/getDir",
					data : {
						tag : tag,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							_loadDirectory(result.data);
							parent = tag;
							// _setStorage('parent',tag);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function _createDir(new_name,parent){
				$.post({url: "/api/web/crtDir",
					data : {
						name : new_name,
						parent : parent,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							alert('فولدر با موفقیت ساخته شد');
							openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function _createFile(new_name,parent){
				var type = new_name.split('.').slice(-1);
				new_name = new_name.split('.').slice(0,-1);
				$.post({url: "/api/web/ctFile",
					data : {
						name : new_name.toString(),
						type : type.toString(),
						parent : parent,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							alert("فایل با موفقیت ساخته شد");
							_openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function _renameDir(tag,new_name){
				$.post({url: "/api/web/rnmDir",
					data : {
						tag : tag,
						new_name : new_name,
						parent : parent,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							_openDir(parent);
						}else{
							alert(result.msg);
						}
			  		}
			  	});
			}

			function _renameFile(file_tag,type,new_name){
				$.post({url: "/api/web/rnFile",
					data : {
						tag : file_tag,
						new_name : new_name,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							_openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function _removeDir(tag){
				var confirm = prompt("Write 'yes' to confirm deleting folder" , '');
				if(confirm.toUpperCase() != "YES")
					return ;
				$.post({url: "/api/web/remDir",
					data : {
						tag : tag,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							_openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			  alert('Folder successfuly has been deleted !');
			}

			function _deleteFile(tag){
				$.post({url: "/api/web/dlFile",
					data : {
						tag : tag,
						_token : token,
					},
					success: function(result){
						if(result.state == 1){
							alert("فایل با موفقیت حذف شد");
							_openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function listenToMenu(elm){
				elm.addEventListener('contextmenu', function(e) {
			        e.preventDefault();
			    }, false);
			    var name = elm.getElementsByTagName('p')[0].innerHTML;
				var type = elm.getElementsByTagName('img')[0].getAttribute("data-type");
				var tag = elm.getElementsByTagName('img')[0].getAttribute("data-tag");
				var menu = document.getElementById("rmenu");
				_setStorage("menu_item_type",type);
				_setStorage("menu_item_tag",tag);
				// menu.setAttribute("data-type",type);
				// menu.setAttribute("data-tag",tag);
			    menu.className = "show";  
			    menu.style.top =  mouseY(event) + 'px';
			    menu.style.left = mouseX(event) + 'px';
			    window.event.returnValue = false;
				return ;
			}

			function openDir(tag){
				_openDir(tag);
				page.next(tag);
				/**/
			}

			function renameDir(tag,new_name){
				new_name = prompt("Please Enter New Name : ",new_name);
				if(new_name == "" || new_name == false || new_name == null)
					return;
				_renameDir(tag,new_name);
			}

			function renameFile(file_tag,type,new_name){
				new_name = prompt("Please Enter New File Name : ",new_name);
				if(new_name == "" || new_name == false || new_name == null)
					return;
				_renameFile(file_tag,type,new_name);
			}

			function createFolder(elm){
				new_name = prompt("please enter the file name");
				if(new_name == "" || new_name == false || new_name == null)
					return;
				_createDir(new_name,parent);
			}

			function createFile(elm){
				new_name = prompt("please enter the file name");
				if(new_name == "" || new_name == false || new_name == null)
					return;
				_createFile(new_name,parent);
			}

			function openFile(tag){
				var win = window.open('/'+tag, '_blank', 'location=no,height=600,width=800,scrollbars=yes,status=yes');
				win.focus();
			}

			function mouseX(evt) {
			    if (evt.pageX) {
			        return evt.pageX;
			    } else if (evt.clientX) {
			       return evt.clientX + (document.documentElement.scrollLeft ?
			           document.documentElement.scrollLeft :
			           document.body.scrollLeft);
			    } else {
			        return null;
			    }
			}

			function mouseY(evt) {
			    if (evt.pageY) {
			        return evt.pageY;
			    } else if (evt.clientY) {
			       return evt.clientY + (document.documentElement.scrollTop ?
			       document.documentElement.scrollTop :
			       document.body.scrollTop);
			    } else {
			        return null;
			    }
			}

			function moveItem(source_tag,type,dest_parent_tag){
				var method = type == "folder" ? "movDir" : "mvFile";
				$.post({url: "/api/web/"+method,
					data : {
						tag : source_tag,
						parent : dest_parent_tag,
						_token : token
					},
					success: function(result){
						if(result.state == 1){
							alert("جابه جایی با موفقیت انجام شد");
							openDir(parent);
						}else{
							alert(result.msg);
						}
				    	
			  		}
			  	});
			}

			function menuAction(caller){
				id = caller.getAttribute('data-id');
				type = _getStorage("menu_item_type");
				tag = _getStorage("menu_item_tag");
				switch(+id){
					case 1:
						if(type == "folder")
							openDir(tag);
						else
							openFile(tag);
						break;
					case 2:

						break;
					case 3:
						_setStorage('cut_tag',tag);
						_setStorage('cut_type',type);
						break;
					case 4:
						moveItem(_getStorage("cut_tag"),_getStorage("cut_type"),tag);
						break;
					case 5:
						if(type == "folder")
							_removeDir(tag);
						else
							_deleteFile(tag);
						break;
					case 6:
						if(type == "folder")
							renameDir(tag);
						else
							renameFile(tag,type);
						break;
					case 7:
						showProperties(type,tag);
						break;
					default:
						alert(id+':/');
				}
			}

			function reload(navbar,fileData){
				_loadNavbar(navbar);
				_loadDirectory(fileData);
			}
		</script>
	</div>
	</body>
</html>