/*
Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.toolbar = 'Full';
	config.toolbar_Full =
	[
		{ name: 'document', items : [ 'Source',] },
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
		{ name: 'editing', items : [ 'Find','Replace','-','SelectAll'] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'] },
		{ name: 'links', items : [ 'Link','Unlink','Anchor' ] },
		{ name: 'insert', items : [ 'Image','Flash','Table'] },
		'/',
		{ name: 'styles', items : [ 'Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] },
		
	];
	
	config.filebrowserBrowseUrl 		= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/browse.php?type=files';
    config.filebrowserImageBrowseUrl 	= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/browse.php?type=images';
    config.filebrowserFlashBrowseUrl 	= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/browse.php?type=flash';
    config.filebrowserUploadUrl 		= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/upload.php?type=files';
    config.filebrowserImageUploadUrl 	= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/upload.php?type=images';
    config.filebrowserFlashUploadUrl 	= GCore.DESIGN_PATH + '_js_libs/ckeditor/kcfinder/upload.php?type=flash';
};
