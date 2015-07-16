var page = {
	requestSingle : null,
	requestUrl : null,

	load : function(url,callback){
		if( this.requestSingle != null ){
			var requestObject = this.requestSingle;
			this.requestSingle = null;
			requestObject.abort();
			return false;
		}
		this.requestUrl = url;
		this.requestSingle = $.ajax({
			type:'GET',
			url: this.requestUrl,
			dataType:'html',
			data: "_art="+String(new Date().getTime()),
			beforeSend: function(XMLHttpRequest){
			},
			success: function(data){
				if( page.requestSingle != null ){
					if( typeof(callback) != 'undefined' && callback != '' ){
						var callbackArray = Array();
						callbackArray = callback.split('|');
						for(var i in callbackArray){
							eval(callbackArray[i]+"(data);");
						}
					}else{
						page.writehtml(data);
					}
					page.init();
				}
			},
			complete: function(XMLHttpRequest,textStatus){
			},
			error: function(){
			}
		});
	},

	request : function(href,callback){
		if(href == '#'){
			return false;
		}
		this.load(href,callback);
		return false;
	},

	iframe : function(href){
		if(href == '#'){
			return false;
		}
		var result = '<iframe frameBorder="0" name="" id="" src="'+href+'" width=100% height=100%></iframe>';
		this.writehtml(result)
		return false;
	},

	setLinks : function(){
		var p = "a[href!='#'][href!='javascript:;'][href!='javascript:void(0);']";
		var a = $(p);
		var alength = a.length;

		for(var i=0;i<alength;i++){
			var href = $(a[i]).attr('href');
			var rel = $(a[i]).attr('rel');
			if(href == '#') continue;
			
			var bindfun = '';
			switch(rel){
				case 'blank':
					$(a[i]).attr('target','_blank');
				break;
				case 'ajax':
					bindfun = function(){
						href = $(this).attr('href');
						return page.request(href);
					};
				break;
				case 'iframe':
				default:
					bindfun = function(){
						href = $(this).attr('href');
						return page.iframe(href);
					};
				break;
			}
			$(a[i]).bind('click',bindfun);
		}
	},

	writehtml : function(result){
		$('#'+this.boxId).html(result);
	},

	init : function(){
		this.requestSingle = null;
		this.setLinks();
	},

	refresh : function(){
		
	},

	redirect : function(href,msg){
		if(typeof(msg) != 'undefined'){
			alert(msg);
		}
		if(typeof(href) == 'undefined'){
			window.history.back();
		}else{
			window.location.href = href;
		}
	},

	msg : function(status,msg){
		var msgHtml = '<div class="prompt ';
		var jsHtml = '';
		if(status == 'succ'){
			msgHtml += 'prompt-succ';
			jsHtml = '<script language="javascript" type="text/javascript">setTimeout("$(\'#msgresult\').empty();",3000);</script>';
		}else if(status == 'fail'){
			msgHtml += 'prompt-warn';
		}
		msgHtml += '">'+msg+'</div>';
		msgHtml += jsHtml;
		$('#msgresult').html(msgHtml);
		$('#msgresult').show();
	},
};


