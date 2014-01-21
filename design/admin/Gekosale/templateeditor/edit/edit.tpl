{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}
<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/jqueryFileTree/jqueryFileTree.css">
{% endblock %}
{% block javascript %}
{{ parent() }}
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/ace.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/theme-chrome.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-liquid.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-javascript.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-css.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-less.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-xml.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/ace/src/mode-json.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jqueryFileTree/jqueryFileTree.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/plupload/plupload.full.js"></script>
<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/plupload/i18n/pl.js"></script>

{% endblock %}
{% block content %}
<h2 id="path">{{ theme }}</h2>
{% if disableNavigation is not defined %}
<ul class="possibilities">
	<li><a href="{{ URL }}templateeditor" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_TEMPLATE_EDITOR{% endtrans %}" alt="{% trans %}TXT_TEMPLATE_EDITOR{% endtrans %}"/></span></a></li>
	<li id="delete" style="display: none;"><a href="#delete" class="button delete"><span>{% trans %}TXT_DELETE{% endtrans %}</span></a></li>
	<li id="save" style="display: none;"><a href="#save" class="button"><span>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
{% endif %}

<div class="layout-two-columns">

	<div class="column narrow-collapsed">
		<div class="block">
			<div><h4>Pliki:</h4></div>
			<div id="tree"></div>
			<div id="keys"><h4>Skróty klawiaturowe:</h4>
				<ul>
					<li>Ctrl+S - zapisanie edytowanego pliku</li>
					<li>Ctrl+F - znajdź</li>
					<li>Ctrl+R - znajdź / zamień</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="column wide-collapsed">
		<div class="block" id="edit">
			<div id="help"><p style="text-align: center;">Wybierz plik do edycji po lewej stronie.</p></div>
			<div>
				<div id="editor"></div>
			</div>
			<div id="file-preview" style="display: none;"></div>
			<div id="container" style="margin-bottom: 20px;display: none;">
				<p style="text-align: center;">Jeżeli chcesz wgrać pliki do folderu <span id="pathinfo" style="font-weight: 700;"></span></p>
			    <div id="drag-drop-area">
					<div class="drag-drop-inside">
						<p class="drag-drop-info">Upuść pliki nad tym polem</p>
						<p style="text-align: center;">lub</p>
						<p class="drag-drop-buttons">
							<a href="#choose" id="choose" class="button"><span>Wybierz pliki</span></a>
						</p>
						<div id="filelist"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
var editor = ace.edit("editor");
var currentFile;
var currentDir;
var currentPath;

function saveFile(file, content){

	if(currentFile == undefined || currentFile == ''){
		return GWarning('Musisz wybrać plik do edycji.');
	}
	
	xajax_SaveFileContent({
		file: currentFile,
		content: editor.getValue()
	}, GCallback(function(oResponse) {
		GWarning(oResponse.msg,'', {});
		setTimeout(function() { 
			GAlert.DestroyAll();
		}, 1500);
	}));
}



$(document).ready(function(){

	editor.setTheme("ace/theme/chrome");
	editor.setShowPrintMargin(false);
	var TplMode = require("ace/mode/liquid").Mode;
	var JsMode = require("ace/mode/javascript").Mode;
	var CssMode = require("ace/mode/css").Mode;
	var LessMode = require("ace/mode/less").Mode;
	var XmlMode = require("ace/mode/xml").Mode;
	var JsonMode = require("ace/mode/json").Mode;
	editor.getSession().setMode(new TplMode());
	editor.getSession().setUseWrapMode(true);
	var commands = editor.commands;
	commands.addCommand({
	    name: "save",
	    bindKey: {win: "Ctrl-S", mac: "Command-S"},
	    exec: function() {
	    	saveFile();
	    }
	});

	$('#save a').click(function(e){
		e.preventDefault();
		saveFile();
	});
	
	$('#delete a').click(function(e){
		e.preventDefault();
		if(currentFile == undefined || currentFile == ''){
			return GWarning('Musisz wybrać plik do skasowania.');
		}
		xajax_DeleteFile({
			file: currentFile,
		}, GCallback(function(oResponse) {
			GWarning(oResponse.msg,'', {});
			setTimeout(function() { 
				GAlert.DestroyAll();
				$('a[rel='+ currentPath +']').click().click();
			}, 1500);
		}));	
	});

	$('#tree').fileTree({ 
		root: '/',
		multiFolder: true,
		expandSpeed: -1,
		collapseSpeed: -1,
	    script: '{{ URL }}templateeditor/confirm/{{ theme }}',
	}, function(file, type) {
		if (type == 1){
			currentFile = file;
			currentDir = '';
			$('#path').html('{{ theme }}' + file);
			xajax_GetFileContent({
				file: file
			}, GCallback(function(oResponse) {
				editable = false;
				preview = false;
				switch(oResponse.mode){
					case 'tpl':
						editor.getSession().setMode(new TplMode());
						editable = true;
						break;
					case 'js':
						editor.getSession().setMode(new JsMode());
						editable = true;
						break;
					case 'css':
						editor.getSession().setMode(new CssMode());
						editable = true;
						break;
					case 'less':
						editor.getSession().setMode(new LessMode());
						editable = true;
						break;
					case 'xml':
						editor.getSession().setMode(new XmlMode());
						editable = true;
						break;
					case 'json':
						editor.getSession().setMode(new JsonMode());
						editable = true;
						break;
					case 'jpg':
					case 'jpeg':
					case 'png':
					case 'gif':
						editable = false;
						preview = true;
					break;
					default:
						editable = false;
						break;
				}
				if(editable){
					$('#help').hide();
					$('#container').hide();
					$('#editor').show();
					$('#file-preview').hide();
					$('#delete').show();
					$('#save').show();
				}else{
					if(preview){
						$('#delete').show();
						$('#save').hide();
						$('#file-preview').show();
						$('#file-preview').html('<img style="max-width: 890px;" src="{{ THEMESPATH }}{{ theme }}' + file + '" />');
					}else{
						$('#delete').show();
						$('#save').hide();
						$('#file-preview').show();
						$('#file-preview').html('<p>Podgląd pliku <strong>' + file + '</strong> nie jest dostępny.</p>');
					}
					$('#help').hide();
					$('#container').hide();
					$('#editor').hide();
				}
				editor.setValue(oResponse.content);
				editor.clearSelection();
				editor.focus();
				editor.scrollToRow(0);
				$.scrollTo($('#header'));
			}));
		}else{
			$('#help').hide();
			$('#container').show();
			$('#editor').hide();
			$('#file-preview').hide();
			$('#delete').hide();
			$('#save').hide();
			currentFile = '';
			currentPath = file;
			currentDir = Base64.encode('{{ theme }}' + file);
			$('#path').html('{{ theme }}' + file);
			$('#filelist').html('');
			$('#pathinfo').html('{{ theme }}' + file);
		}
	});

	var uploader = new plupload.Uploader({
		runtimes : 'html5,flash',
		browse_button : 'choose',
		button_browse_hover : true,  
        drop_element : "drag-drop-area",  
		container : 'container',
		max_file_size : '10mb',
		url : '{{ URL }}files/confirm/' + currentDir,
		flash_swf_url : '{{ DESIGNPATH }}_js_libs/plupload/plupload.flash.swf',  
	    silverlight_xap_url : '{{ DESIGNPATH }}_js_libs/plupload/plupload.silverlight.xap',
	    filters : [
            {title : "Pliki", extensions : "less,css,tpl,js,xml,json,jpg,jpeg,png,gif,ico,otf,pdf"},
        ],
	    
	});

	uploader.init(); 
	
	uploader.bind('BeforeUpload', function(up, file) {
	    up.settings.url = '{{ URL }}files/confirm/' + currentDir;
	});
	
	uploader.bind('Init', function(up, params) {
	});

	uploader.bind('FilesAdded', function(up, files) {
        $.each(files, function(i, file) {
            $('#filelist').append(
                '<div id="' + file.id + '">' +
	                file.name + ' (' + plupload.formatSize(file.size) + ') <b></b>' +
	            '</div>');
	    });
        up.start();
	});

	uploader.bind('UploadProgress', function(up, file) {
		$('#' + file.id + " b").html(file.percent + "%");
	});

	uploader.bind('UploadComplete', function(up, file) {
		$('a[rel='+ currentPath +']').click().click();
		GWarning('Upload plików zakończony sukcesem','', {});
		setTimeout(function() { 
			GAlert.DestroyAll();
		}, 1500);
    });

	uploader.bind('Error', function(up, err) {
		GWarning(err.message,err.file.name, {});
		up.refresh(); // Reposition Flash/Silverlight
	});
});
</script>
    
<style>

#keys ul {
	list-style: none;
	margin-right: 0;
	margin-left: 0;
}

#keys ul li {
	margin-bottom: 5px;
}
#content > h2 {
	max-width: 945px;
}

#tree {
	padding-bottom: 15px;
}

#editor {
	margin-bottom: -19px;
	top: -17px;
	left: -20px;
	position: relative;
	width: 933px;
	height: 700px;
	display: none;
}
.layout-two-columns > .column.narrow-collapsed {
	width: 247px;
	margin-right: -1px;
}
.layout-two-columns > .column.wide-collapsed {
width: 934px;
}

#drag-drop-area {
	border: 4px dashed #DDD;
	min-height: 200px;
	padding-bottom: 10px;
}

.drag-drop-info {
	font-weight: 400;
	font-size: 22px;
	text-align: center;
	margin-top: 50px!important;
}
.drag-drop-buttons {
	text-align: center;
}

#filelist {
	text-align: center;
}
</style>
{% endblock %}