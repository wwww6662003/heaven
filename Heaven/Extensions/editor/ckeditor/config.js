/*
Copyright (c) 2003-2010, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

CKEDITOR.editorConfig = function( config )
{
	config.language = 'zh-cn';
	config.toolbar = [
	['Source','-','Save','Preview','-','Cut','Copy','Paste'],
	['PasteText','PasteFromWord','-','TextColor','BGColor'],
	['Image','SpecialChar','Smiley','Flash'],['PageBreak','Table','-','Undo','Redo'],
	'/',
	['Font','FontSize','Bold','Italic','Underline','StrikeThrough','-','Subscript','Superscript','-','JustifyLeft','JustifyCenter','JustifyRight'],
	['NumberedList','BulletedList','Blockquote','-','Outdent','Indent'],
	['Link','Unlink']
] ;
	config.font_names = '宋体;黑体;隶书;楷体_CB2312;Arial;Tahoma;Verdana';
	// config.uiColor = '#AADC6E';

	
};
