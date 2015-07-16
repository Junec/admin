// JavaScript Document
//页面自适应宽高
function autowh(){
	//main
	$('.main').hide();
	$('.main').width( $(window).width() );
	$('.main').height( $(window).height() );
	$('.main-body').width( $('.main').width() );
	$('.main-body').height( $('.main').height() - $('.main-top').outerHeight(true) );
	$('.main-body-right').width( $('.main-body').width() - $('.main-body-left').outerWidth(true) );
	$('.main-body-right').height( $('.main-body').height());
	$('.main').show();
}

function autofinderwh(){
	
	$('#finder-content').height( 300 );

	//var finderListHeight = $('#mainContent').height() - $('.finderTitle').outerHeight(true) - $('.finderTab').outerHeight(true) - $('.finderCustomHtml').outerHeight(true) - $('.finderTop').outerHeight(true) - $('.finderHeader').outerHeight(true) - $('.finderFooter').outerHeight(true) - 23;
	//$('.finderList').height( finderListHeight );
	//$('#finderHeaderTable').width( $('#finderList').width() );
}


function uploadfile(o){
	$('#uploadfile').attr('inputId',$(o).attr('inputId'));
	$('#uploadfile').click();
}

function reviewfile(o){
	var filepath = $('#'+$(o).attr('inputId')).val();
	window.open(filepath);
}

function ajaxFileUpload(o){
	var inputId = $(o).attr('inputId');
	var inputUploadButtonId = inputId+'-uploadbutton';
	var fileType = $('#'+inputUploadButtonId).attr('fileType');
	var maxsize = $('#'+inputUploadButtonId).attr('maxsize');
	var uploadPath = $('#'+inputUploadButtonId).attr('uploadPath');
	var isRename = $('#'+inputUploadButtonId).attr('isRename');

	var url = 'index.php?app=Admin&ctl=Content&act=Upload&fileType='+fileType+'&maxsize='+maxsize+'&uploadPath='+uploadPath+'&isRename='+isRename;

	$('#'+inputUploadButtonId).html('正在上传...');
	$.ajaxFileUpload({
		url:url,
        secureuri:false,
        fileElementId:'uploadfile',
        dataType: 'json',

        success: function (data){
			page.alert(data.msg,data.status);
			if(data.status == 'succ'){
				$('#'+inputId).val( data.path );
			}
			$('#'+inputUploadButtonId).html('上传');
		}
	});
}

$(document).ready(function(){

	$('.contentEditGroup a').bind('click',function(){
		$(this).parent().find('a').removeClass('active');
		$(this).addClass('active');
		var christmaslistcontentbox = $('.contentEditBox');
		var index = $(this).parent().find('a').index($(this));
		christmaslistcontentbox.hide();
		$(christmaslistcontentbox[index]).show();
	});
	$('.contentEditGroup a').first().addClass('active');
	$('.contentEditBox').first().show();
});

