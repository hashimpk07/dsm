String.prototype.trim=function(){
	return this.replace(/^\s+|\s+$/g, '');
};

String.prototype.ltrim=function(){
	return this.replace(/^\s+/,'');
};

String.prototype.rtrim=function(){
	return this.replace(/\s+$/,'');
};

String.prototype.fulltrim=function(){
	return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/g,'').replace(/\s+/g,' ');
};
function cloneSelect(from, to, cls, selValue, trig )
{
	var clone = jQuery(from).clone(true) ;
	var filtered = jQuery(clone).find(cls) ;
	jQuery(to).empty().prepend("<option value=''>--Select--</option>") ;
	jQuery(to).append(filtered) ;
	jQuery(to).val(selValue) ;

	var _trigger = false ;
	if( typeof trig != 'undefined' )
	{
		_trigger = trig ;
	}
	if( _trigger )
	{
		jQuery(to).trigger('change') ;
	}
}
/**
 * Refresh table.
 */
function htmlRefreshTable(tableId)
{
	var o = jQuery('#' + tableId ) ;
	if( o )
	{
		o.click() ;
	}
}
/**
 * Set inner html of an element.
 * @method setHtml
 * @param {string} targetId target html element
 * @param {string} html html contlent to put.
 * @param {int} [timeout=0] optional timeout in seconds. by default 0 means do not automatically close.
 * @return bool return true on success
 **/
function setHtml(targetId, html, timeout)
{
	var node = jQuery('#' + targetId) ;

	if( targetId == '__script' )
	{
		eval(html);
	}
	if( targetId == 'idContentAreaDetect' )
	{
		var idClicked = jQuery('#idContentAreaClicked').val() ;
		if( idClicked == 'idContentAreaMenu' )
		{
			targetId = 'idContentAreaSmall' ;
		}
		else if( idClicked == 'idContentAreaSmall' )
		{
			targetId = 'idContentAreaBig' ;
		}
		else if( idClicked == 'idContentAreaBig' )
		{
			targetId = 'idContentAreaBig' ;
		}
	}
	if( targetId == 'idSuccessMsg' || targetId == 'idFailureMsg' )
	{
		//clar both error and success
		jQuery('#idSuccessMsg').html('') ;
		jQuery('#idFailureMsg').html('') ;
		jQuery('#idSuccessMsg').hide() ;
		jQuery('#idFailureMsg').hide() ;
		node.fadeIn() ;

		if( timeout == undefined )
		{
			timeout = 10000 ;
		}
		setTimeout( function(){
			if( node.html() == html )
			{
				{
					node.fadeOut(1000) ;
				}
			}
		}, ((timeout == undefined) ? 10000 : timeout) ) ;
	}
	else if( targetId == 'idContentAreaSmall' || targetId == 'idContentAreaBig' )
	{
		if( targetId == 'idContentAreaSmall' )
		{
			$('#idContentAreaBig').html('') ;					
			$('#idContentAreaBig').hide() ;

			if( $(document).width() >= 960 )
			{
				$('#idContentAreaSmall').animate({ width: '100%' }, 150, function() {
					//do someting after animation..
				 });
				 bindActionButtons('show') ;
				 jQuery('#idContentAreaSmall').removeClass('minimized') ;
				 jQuery('#layout').removeClass('threeLayers') ;
			}
			else
			{
				$('#idContentAreaSmall').animate({ width: '100%' }, 150, function() {
					//do someting after animation..
				 });
				
				bindActionButtons('show') ;
				jQuery('#idContentAreaSmall').removeClass('minimized') ;
				jQuery('#layout').removeClass('threeLayers') ;
			}
			$('#idContentAreaSmall').html(html) ;
		}
		if( targetId == 'idContentAreaBig' )
		{
			if( $(document).width() >= 960 )
			{
				$('#idContentAreaBig').show() ;
				$('#idContentAreaSmall').animate({ width: '500px' }, 150, function() {
				 });
				 jQuery('#idContentAreaSmall').addClass('minimized') ;
				 jQuery('#layout').addClass('threeLayers') ;
				 bindActionButtons('hide') ;
			}
			else
			{
				$('#idContentAreaBig').show() ;
				$('#idContentAreaSmall').animate({ width: '500px' }, 150, function() {
				 });
				jQuery('#idContentAreaSmall').addClass('minimized') ;
				jQuery('#layout').addClass('threeLayers') ;
				bindActionButtons('hide') ;
			}
			jQuery('#idContentAreaBig').html('<span class="content_close flaticon-close" onclick="_onContentAreaClose();" ></span>' + html) ;
			return ;
		}
	}
	
	//TODO; check wheather node has propery named value then use value else html
	node.html(html) ;
	node.val(html) ;
}
/**
* Submit a form. Expected response format is json.
* 
* @method submitForm
* @param {string} formId id of the form.
* @param {function} before callback to be invoked before submit.  eg: validation, if this function returns false the form wont submit.
* @param {function} after callback to be invoked after receiving response.
* @param {string} targetId target element id, where the response should be place, only if the response is string.
* @param {bool} autoFill if the response is json the response data will be placed to appropriate fields.
* for autofill the response can be in the following format. All fields start with _ will be omitted.
* eg: 
* { eUsername : 'invalid username',
*	ePassword : 'password must be 6 charector or more' }
*	
* @return {bool} true on success
*/
function submitForm (formId, before, after, targetId, autoFill)
{
	var options = { 
		data : { ajaxquery : true },
		beforeSubmit : function() {
			var ret = true ;
			if( typeof before == 'function' )
			{
				ret = before() ;
			}
			if( ret )
			{
				var submit = jQuery('#' + formId).find(':submit') ;
				submit.attr('disabled', true) ;
				waitingMode(true) ;
			}
			return ret ;
		},
		error: function(jqXhr){ 
			
			var submit = jQuery('#' + formId).find(':submit') ;
			submit.attr('disabled', false) ;

			//waitingMode(false) ; ajaxFailed will do it..
			ajaxFailed(jqXhr) } ,
		success: function(data) {

			if( typeof data.__contentAreaClicked != 'undefined'  )
			{
				if( data.__contentAreaClicked == 'idPopupSubmit' )
				{
					if( typeof data.popupCallback != 'undefined'  )
					{
						var str = "" + data.popupCallback + "(" + JSON.stringify(data) + ");" ;
						eval(str) ;
					}
					if( typeof data.status != 'undefined' )
					{
						if( data.status == 'OK' )
						{
							popupSubmitHide() ;
						}
					}
				}
			}

			var submit = jQuery('#' + formId).find(':submit') ;
			submit.attr('disabled', false) ;

			waitingMode(false) ;
				//autofill errors
				var putFocus = false ;
				if( autoFill == undefined )
				{
					autoFill = true ;
				}
				
				//auto fill {
				if( autoFill != false )
				{
					if(typeof data =='object')
					{
						jQuery.each(data, function(name, val) {
							if( ! putFocus )
							{
								if( val )
								{
									jQuery("#" + name).focus() ;
									putFocus = true ;
								}
							}
							setHtml( name, val, 5000);
						}) ;
					}
				}
				//} auto fill
				if( targetId != null )
				{
					if( typeof data == 'string' )
					{
						setHtml(targetId, data) ;
					}
				}
				if( ! putFocus )
				{
					if( jQuery('.initfocusfield') )
					{
						jQuery('.initfocusfield:first').focus() ;
					}
					if( jQuery('.initscrollpos').length )
					{
						$('html, body').stop().animate({ scrollTop: jQuery('.initscrollpos').position().top });
					}
				}
				if( after != undefined )
				{
					if( typeof after == 'function' )
					{
						after(data) ;
					}
				}
			}
		}; 
	jQuery('#' + formId).ajaxForm(options);
}
/**
 * Enter response waiting state, a  waiting animation be shown. One should manually enter and leave waiting mode.
 * 
 * @method waitingMode
 * @param {bool} bshow true to enter waiting, false to leave waiting.
 */
function waitingMode(bshow)
{
	if( bshow )
	{
		jQuery('#idWaitArea').show() ;
	}
	else
	{
		jQuery('#idWaitArea').hide() ;
	}
}
/**
 * Fetch response in json format and fill the response to appropriate form fields.
 * 
 * @method fillForm
 * @param {string} url request url
 * @param {json} params additional params for url. all fields start with _ will be ommited.
 * @param {function} before a callback to be called before requesting.
 * @param {function}  after a callback to be called after receiving response from server. This method will receive response data as its argument.
 */
function fillForm (url, params, before, after)
{
	if( before != undefined )
	{
		before() ;
	}
	if( typeof params !='object' )
	{
		params = {} ;
	}
	params['ajaxquery'] = 1 ;

	var jObj = jQuery.post(url, params, function(data)
	{
		if( typeof data == 'object' )
		{
			jQuery.each(data, function(name, val) {
				jQuery("#" + name).val(val);
			}) ;
		}
		if( typeof after == 'function' )
		{
			after(data) ;
		}
	}) ;

	jObj.fail(function(jqXhr) { ajaxFailed(jqXhr) }) ;
}
/**
 * Call ajax using GET method.
 * 
 * @method ajaxGet
 * @param {string} url request url
 * @param {json} params addditional parameters
 * @param {function} before function to be called before sending request.
 * @param {function} after function to be called after receiving response.
 */
function ajaxGet(url, params, before, after)
{
	if( before != undefined )
	{
		before() ;
	}
	if( typeof params !='object' )
	{
		params = {} ;
	}
	params['ajaxquery'] = 1 ;

	var jObj = jQuery.get(url, params, function(data)
	{
		if( after != undefined )
		{
			after(data) ;
		}
	}) ;
	jObj.fail(function(jqXhr) { ajaxFailed(jqXhr) }) ;
}
/**
 * Call ajax using POST method.
 * 
 * @method ajaxGet
 * @param {string} url request url
 * @param {json} params addditional parameters
 * @param {function} before function to be called before sending request.
 * @param {function} after function to be called after receiving response.
 */
function ajaxPost(url, params, before, after)
{
	if( before != undefined )
	{
		before() ;
	}
	if( typeof params !='object' )
	{
		params = {} ;
	}
	params['ajaxquery'] = 1 ;

	var jObj = jQuery.post(url, params, function(data)
	{
		if( after != undefined )
		{
			after(data) ;
		}
	}) ;
	jObj.fail(function(jqXhr) { ajaxFailed(jqXhr) }) ;
}
/**
 * Action function to flag record.
 * 
 * @method actionFlag
 * @param {string} url request url
 * @param {json} params additional params
 * @param {string} msg confirmation message
 * @param {string} targetId target element where the html response should be place, only for string response
 * @param {function} before function to be called before making request
 * @param {function} after function to invoked after receiving response. the response data will be send to the after function as its first argument.
 * @param {bool} autoFill automatically fill html elements if response format is json.
 */
function actionFlag(url, params, msg, targetId, before, after, autoFill)
{
	if( msg )
	{
		if( ! confirm(msg) )
		{
			return false ;
		}
	}
	getData(url, params, targetId, before, after, autoFill) ;
	return true ;
}
/**
 * Action function to add record.
 * 
 * @method actionAdd
 * @param {string} url request url
 * @param {json} params additional params
 * @param {string} targetId target element where the html response should be place, only for string response
 * @param {function} before function to be called before making request
 * @param {function} after function to invoked after receiving response. the response data will be send to the after function as its first argument.
 * @param {bool} autoFill automatically fill html elements if response format is json.
 */
function actionAdd(url, params, targetId, before, after, autoFill)
{
	/* TODO; RESETCONFIRM { 
	if( jQuery('#idAddMode').length || jQuery('#idEditMode').length )
	{
		if( jQuery('#' + targetId).length )
		{
			jQuery('#' + targetId).show() ;
		}
		if( ! confirm('Do you want to reset form ? ') )
		{
			return ;
		}
	}
	RESET CONFRIM } */

	getData(url, params, targetId, before, function(data){
			
			if( jQuery('.initfocusfield') )
			{
				jQuery('.initfocusfield:first').focus() ;
			}

			if( jQuery('#' + targetId).length ){
				jQuery('#' + targetId).slideDown() ;
			}
		if( typeof after == 'function' ){
			after(data) ;
			}
	}, autoFill) ;
}
/**
 * Action function to edit a record.
 * 
 * @method actionEdit
 * @param {string} url request url
 * @param {json} params additional params
 * @param {string} targetId target element where the html response should be place, only for string response
 * @param {function} before function to be called before making request
 * @param {function} after function to invoked after receiving response. the response data will be send to the after function as its first argument.
 * @param {bool} autoFill automatically fill html elements if response format is json.
 */
function actionEdit(url, params, targetId, before, after, autoFill)
{
	getData(url, params, targetId, before, function(data){
		
			if( jQuery('.initfocusfield') )
			{
				jQuery('.initfocusfield:first').focus() ;
			}
		
			if( jQuery('#' + targetId).length ){
				jQuery('#' + targetId).slideDown() ;
			}
		if( typeof after == 'function' ){
			after(data) ;
			}
	}, autoFill) ;
}
/**
 * Action function to view a record.
 * 
 * @method actionView
 * @param {string} url request url
 * @param {json} params additional params
 * @param {string} targetId target element where the html response should be place, only for string response
 * @param {function} before function to be called before making request
 * @param {function} after function to invoked after receiving response. the response data will be send to the after function as its first argument.
 * @param {bool} autoFill automatically fill html elements if response format is json.
 */
function actionView(url, params, targetId, before, after, autoFill)
{
//	if( jQuery('#idAddMode').length || jQuery('#idEditMode').length )
//	{
//		if( jQuery('#' + targetId).length )
//		{
//			jQuery('#' + targetId).show() ;
//		}
//		if( ! confirm('Do you want to reset form ? ') )
//		{
//			return ;
//		}
//	}

	getData(url, params, targetId, before, function(data){
			if( jQuery('#' + targetId).length ){
				jQuery('#' + targetId).slideDown() ;
			}
		if( typeof after == 'function' ){
			after(data) ;
			}
	}, autoFill) ;
}
function _onContentAreaClose()
{
	$('#idContentAreaSmall').animate({ width: '100%' }, 100, function() {});
	bindActionButtons('showall') ;
	jQuery('#idContentAreaSmall').removeClass('minimized') ;
	jQuery('#layout').removeClass('threeLayers') ;
	$('#idContentAreaBig').hide(100) ;
}
/**
 * General function to get data from server.
 * 
 * @method getData
 * @param {string} url request url
 * @param {json} params additional params
 * @param {string} targetId target element where the html response should be place, only for string response
 * @param {function} before function to be called before making request
 * @param {function} after function to invoked after receiving response. the response data will be send to the after function as its first argument.
 * @param {bool} autoFill automatically fill html elements if response format is json.
 */
function getData(url, params, targetId, before, after, autoFill, silent)
{
	//swtich to waiting mode
	if( typeof before == 'function' )
	{
		before() ;
	}

	if( typeof silent == 'undefined' )
	{
		silent = false ;
	}

	if( ! silent )
	{
		waitingMode(true) ;
	}

	if( typeof params !='object' || params == null )
	{
		params = {} ;
	}
	if( typeof params['arg-gen-contentAreaClicked'] == 'undefined') {
		params['arg-gen-contentAreaClicked'] = jQuery('#idContentAreaClicked').val() ;
	}
	if( typeof params['ajaxquery'] == 'undefined') {
		params['ajaxquery'] = 1 ;
	}
	

	var jObj = jQuery.post(url, params, function(data)
	{
		try
		{
			//auto fill
			if( autoFill == undefined )
			{
				autoFill = true ;
			}
			if( autoFill != false )
			{
				if(typeof data =='object')
				{
					jQuery.each(data, function(name, val) {
						setHtml( name, val );
					}) ;
				}
			}
			// } autofill

			if( targetId != undefined )
			{
				if( targetId )
				{
					setHtml( targetId, data );
				}
			}
			if( typeof after == 'function' )
			{
				after(data) ;
			}
		}
		catch(err) {}

		if( ! silent )
		{
			waitingMode(false) ;
		}
	}) ;
	jObj.fail(function(jqXhr) { ajaxFailed(jqXhr) }) ;
}
function ajaxFailed(jqXhr)
{
	try
	{
		var errMsg = '' ;
		if(  jqXhr.responseText )
		{
			errMsg +=  jqXhr.responseText ;
		}
		else
		{
			errMsg = 'Request failed, Please check your network connection.' ;
		}
		setHtml('idFailureMsg', errMsg, 10000) ;
	}
	catch(err)
	{
	}
	waitingMode(false) ;
}
/**
* Validate a form.
* @method validateForm
* @param {json} fieldsAndRules a json objec contains fieds and validtion information. format is as following
*	{
*		inputFieldName : {
*						func : "functionNameWithArguments()",
*						errfield : "errorFieldName",
*						errmsg : "errorMessage"
*					},
*		anotherFieldName : { ... }
*	}
* @param {string} summaryField a summary of errors will be placed here, using <li> tag.
* @param {function} before function to be called before validtion
* @param {function} after function to be called called after validtion. This function will reive overall validation status as its first argument.
* @return {bool} overall status true if every validation succeeded.
*/
function validateForm(fieldsAndRules, summaryField, before, after, parent)
{
	var bstatus = true ;
	if( typeof before == 'function' )
	{
		before() ;
	}
	if( typeof fieldsAndRules == 'object' )
	{		
		var ret = '' ;
		var putFocus = false ;

		if( typeof parent !== 'undefined' )
		{
			if( parent.length > 0 )
			{
				parent = (parent + ' ') ;
			}
		}
		else
		{
			parent = '';
		}

		jQuery.each(fieldsAndRules, function(name, val) {
			if( typeof val.func == 'function' )
			{
				var result = val.func(val) ;
			}
			else
			{
				var ec = 'var result = jQuery(\'' + parent + name + '\').validation().' + val.func + ';' ;

				eval(ec) ;
			}
			if( ! result )
			{
				bstatus = false ;
			}
			//for indivitual result
			if( val.errfield != undefined )
			{
				var msg = ((result) ? '' : val.errmsg) ;
				ec = 'jQuery(\'' + parent + val.errfield + '\').html(\'' + msg + '\');' ;
				if( ! putFocus && msg.length > 0 )
				{
					ec += 'jQuery(\'' + parent + name + '\').focus();' ;
					putFocus = true ;
				}
				eval(ec) ;
			}
			//for summary
			if( summaryField != undefined )
			{
				ret += '<li>' + val.errmsg + '</li>' ;
			}
			
		}) ;
			  
		ret = '<ul class="errorSummary">' + ret + '</ul>' ;
		if( summaryField != undefined )
		{
			jQuery('' + parent + summaryField).html(ret) ;
		}
		if( typeof after == 'function' )
		{
			after(bstatus) ;
		}
	}
	return bstatus ;
}
/*
 * Validations
 */
function validation(els)
{
	//any one checked
	this.any = function()
	{
		var b = false ;
		els.each(function(){
			var ret = $(this).val() ;
			if( ret.length > 0 )
			{
				b = true ;
			}
		});
		return b ;
	};
	//any one checked
	this.ischecked = function()
	{
		var b = false ;
		els.each(function(){
			var ret = $(this).is(':checked') ;
			if( ret )
			{
				b = true ;
			}
		});
		return b ;
	};
	//not specified value
	this.notvalue = function(val)
	{
		var b = false ;
		els.each(function(){
			if( $(this).val() !== val )
			{
				b = true ;
			}
		});
		return b ;
	};
	//any has specified value
	this.anyvalue = function(val)
	{
		var b = true ;
		els.each(function(){
			if( $(this).val() == val )
			{
				b = false ;
			}
		});
		return b ;
	};
	//name
	//ip
	this.ip = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length > 0 || required == undefined )
			{
				var reg = /^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}
		})
		return b ;
	}
	//name
	this.aname = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 && required != undefined )
			{
				b = false ;
			}
			else if( val.length > 0 )
			{
				var reg = /^[a-zA-Z\x20\']+$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}
	
		})
		return b ;
	}
	//email
	this.email = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;

			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//numeric
	this.numeric = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;

			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^(?=.)\d*(?:[.,]\d+)?$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//url
	this.url = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;

			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^((https?|ftps?)+(:\/\/)+)?([\w-]+\.)?[\w-]+\.+([a-zA-Z]{2,4})+(\.+([a-zA-Z]{2,4})+)?(\:+([\d]{1,5})+)?(((\/)?)|(\/+[\/\?\w- .&=%]))*$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//match
	this.matchfield = function(to)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			var val2 = $('#' + to).val() ;
			if( val.length > 0 || val2.length > 0 )
			{
				if( val != jQuery('#' + to).val() )
				{
					b = false ;
				}
			}
		})
		return b ;
	}
	//match
	this.unmatchfield = function(to)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			var val2 = $('#' + to).val() ;
			if( val.length > 0 || val2.length > 0 )
			{
				if( val === jQuery('#' + to).val() )
				{
					b = false ;
				}
			}
		})
		return b ;
	}
	//zip code validation.
	this.zip = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^[0-9-]{6,10}$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//phone validation.
	this.phone = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				//var reg = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/ ;
				var reg = /^\+?[0-9]{10,15}$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
			if( val.length > 15 )
			{
				b = false ;
			}
		})
		return b ;
	}
	//alphabets only
	this.alpha = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^[a-zA-Z]+$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}
		})
		return b ;
	}
	//alpha numeric only
	this.alphanumeric = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^[a-zA-Z0-9]+$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//alpha numeric and underscore only
	this.alphanumericus = function(required)
	{
		var b = true ;
		els.each(function(){
			var val = $(this).val() ;
			if( val.length < 1 )
			{
				if( required != undefined )
				{
					b = false ;
				}
			}
			else
			{
				var reg = /^[a-zA-Z0-9_]$/ ;
				if( ! reg.test(val) )
				{
					b = false ;
				}
			}	
		})
		return b ;
	}
	//required validations
	this.required = function()
	{
		var b = true ;
		els.each(function(){
			if( $(this).val().trim().length < 1 )
			{
				b = false ;
			}
		})
		return b ;
	}
	//min lenght validation
	this.minlength = function(length)
	{
		var b = true ;
		els.each(function(){
			if( $(this).val().trim().length < length )
			{
				b = false ;
			}
		})
		return b ;
	}
	//min lenght validation
	this.lenbetween = function(min, max)
	{
		var b = true ;
		els.each(function(){
			var le = $(this).val().trim().length ;
			if( le < min || le > max )
			{
				b = false ;
			}
		})
		return b ;
	}
	//max lenght validation
	this.maxlength = function(length)
	{
		var b = true ;
		els.each(function(){
			if( $(this).val().trim().length > length )
			{
				b = false ;
			}
		})
		return b ;
	}
	//advanced email set..
	this.advemailset = function(required, excludestr)
	{
		var b = true ;
		els.each(function(){
			var valset = $(this).val() ;
			valset = valset.replace(/[\,\x20]+$/, '') ;
			var vala = valset.split(',') ;

			if( excludestr == undefined )
			{
				excludestr = '' ;
			}
			var excludea = excludestr.split('|') ;

			jQuery.each( vala, function(index,val2) {

				if( val2.length < 1 )
				{
					if( required != undefined )
					{
						b = false ;
					}
				}
				else
				{
					var reg = /^([A-Za-z0-9_\.\<\'\x20]+)?((([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))){1}(\>)?$/ ;
					if( ! reg.test(val2) )
					{
						var eskip = false ;
						jQuery.each( excludea, function (ei, ev) {
							if( val2 == ev )
							{
								eskip = true ;
							}
						}) ;
						if( ! eskip )
						{
							b = false ;
						}
					}
				}	
			}) ;
		})
		return b ;
	}
	//advanced sms set
	this.advsmsset = function(required, excludestr)
	{
		var b = true ;
		els.each(function(){
			var valset = $(this).val() ;
			valset = valset.replace(/[\,\x20]+$/, '') ;

			var vala = valset.split(',') ;
			if( excludestr == undefined )
			{
				excludestr = '' ;
			}
			var excludea = excludestr.split('|') ;

			jQuery.each( vala, function(index,val2) {

				if( val2.length < 1 )
				{
					if( required != undefined )
					{
						b = false ;
					}
				}
				else
				{
					var reg = /^([A-Za-z0-9_\.\<\'\x20]+)?(\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})){1}(\>)?$/ ;
					if( ! reg.test(val2) )
					{
						var eskip = false ;
						jQuery.each( excludea, function (ei, ev) {
							if( val2 == ev )
							{
								eskip = true ;
							}
						}) ;
						if( ! eskip )
						{
							b = false ;
						}
					}
				}	
			}) ;
		})
		return b ;
	}
	//value less than
	this.lt = function(val)
	{
		var b = true ;
		els.each(function(){
			if( $(this).val() >= val )
			{
				b = false ;
			}
		})
		return b ;
	}
	//value less than
	this.gt = function(val)
	{
		var b = true ;
		els.each(function(){
			if( $(this).val() <= val )
			{
				b = false ;
			}
		})
		return b ;
	}
	//value between
	this.between = function(val1, val2)
	{
		if( val2 < val1 )
		{
			var t = val1 ;
			val1 = val2 ;
			val2 = t ;
		}

		var b = true ;
		
		els.each(function(){
			if( $(this).val() < val1 || $(this).val() > val2 )
			{
				b = false ;
			}
		})
		return b ;
	}
	this.checkrequired = function(name)
	{
		var b = false ;
		els.each(function(){
			jQuery('[name="' + name + '"]').each(function(){
				if( $(this).is(':checked') )
				{
					b = true ;
				}
			})
		})
		return b ;
	}
	return this ;
}

(function($) {     // Compliant with jquery.noConflict()
	$.fn.validation = function()
	{
		return validation(this) ;
	}
}
)(jQuery);

function bindCheckLinked(one, set)
{
	$(set).click(function(event) {   
		$(set).each(function() {
			if( this.checked )
				{
					jQuery(one).prop('checked', true);
				}
		});
	}) ;
	$(one).click(function(event) {  
		if( ! this.checked )
		{
			var hasOne = false ;
			$(set).each(function() {
				if(this.checked)
				{
					hasOne = true ;
				}
			}) ;
			if( hasOne )
			{
				this.checked = true ;
			}
		}
	}) ;
}
/**
 * Tick all checkbox.
 * 
 * @method bindCheckAll
 * @param {string} eventElement on which element the tick all should fire ?
 * @param {string} parentNode parent element where checkbox contains
 **/
function bindCheckAll(eventElement, parentNode, after)
{
	var bret = false ;

	$(eventElement).change(function(event) {   
		var onechk = false ;

		if(this.checked) {
			// Iterate each checkbox
			$(parentNode).each(function() {
				$(this).prop('checked', true).triggerHandler('change') ;

				onechk = true ;
			});
			bret = true ;
			
		}
		else
		{			
			// Iterate each checkbox
			$(parentNode).each(function() {
				$(this).prop('checked', false).triggerHandler('change') ;
			});
			
			bret = false ;
		}

		if( onechk )
		{
			jQuery('.list-iconset').css('visibility', 'visible') ;
		}
		else
		{
			jQuery('.list-iconset').css('visibility', 'hidden') ;
		}

		if( typeof before == 'function' )
		{
			return before(bret) ;
		}
		
	});
	//clear check all if one is removed from check list..
	
	$(parentNode).change(function(event) {   
		var allchk = true ;
		var onechk = false ;

		$(parentNode).each(function() {
				if( ! this.checked ){
					allchk = false ;
				}
				else
				{
					onechk = true ;
				}
			});

			if( ! allchk )
			{
				$(eventElement).prop('checked', false) ;
			}
			else
			{
				$(eventElement).prop('checked', true) ;
			}
				
			if( onechk )
			{
				jQuery('.list-iconset').css('visibility', 'visible') ;
			}
			else
			{
				jQuery('.list-iconset').css('visibility', 'hidden') ;
			}
		}) ;
	
}
function setHightlightRow(cls, id)
{
	jQuery('#idListArea' + cls + ' .tab-grey tr').css('background-color', 'transparent') ;
	jQuery('#idListArea' + cls + ' #idTableRow' + id).css('background-color', '#EEE') ;
	
	var gHidId = 'idgrowhid' + cls ;
	var gHidJQId = '#' + gHidId ;

	var anyPrev = false ;
	if( jQuery(gHidJQId).length > 0 )
	{
		anyPrev = jQuery(gHidJQId).val('idTableRow' + id) ;
	}
	else
	{
		jQuery('body').append('<input class="clsHighlightRows" type="hidden" id="' + gHidId + '" value="idTableRow' + id + '" />') ;
	}
}
function bindHightlightRow(cls)
{
		var gHidId = 'idgrowhid' + cls ;
		var gHidJQId = '#' + gHidId ;
		
		var anyPrev = false ;
		if( jQuery(gHidJQId).length > 0 )
		{
			anyPrev = jQuery(gHidJQId).val() ;
			jQuery('#' + anyPrev).css('background-color', '#EEE') ;
		}
		else
		{
			jQuery('body').append('<input class="clsHighlightRows" type="hidden" id="' + gHidId + '" value="" />') ;
		}

		jQuery('.tab-grey tr td').on('click', function(){
			jQuery('.tab-grey tr').css('background-color', 'transparent') ;
			jQuery('.tab-grey tr td').css('background-color', 'transparent') ;
			jQuery(this).parent().css('background-color', '#EEE') ;

			//get curren id
			var vCurId = jQuery(this).parent().attr('id') ;
			jQuery(gHidJQId).val(vCurId) ;
		});
	}

function insertAtCursor(myField, myValue) {
	myField = document.getElementById(myField) ;

	if( ! myValue )
	{
		return ;
	}
    //IE support
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = myValue;
    }
    //MOZILLA and others
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        myField.value = myField.value.substring(0, startPos)
            + myValue
            + myField.value.substring(endPos, myField.value.length);
    } else {
        myField.value += myValue;
    }	
}
/*
 * Field Accept specified characters only.
 */
function acceptOnly(type, ele)
{
	jQuery(ele).keypress(function(evt) 
	{
		var charCode = evt.charCode;
		var charStr = String.fromCharCode(charCode);

		if( ! charStr.match(type) )
		{
			if( charCode >= 32 && charCode <= 126 )
			{
				evt.stopPropagation() ;
				return false ;
			}
		}
		return true ;
	}) ;
}
   
function bindActionButtons(defState)
{
	$('.minimized .tab-grey tr').mouseover(function(){
		var id = $(this).attr('id');
		if( $(this).find('th').length )
		{
			return ;
		}	
		//$( '#' + id + ' td.m.c').show() ; //show only current row
		//$( '#' + id + ' td:visible:first' ).attr('colspan', 1) ; //show only current row
		$( '.minimized .tab-grey tr td.m').show() ; //show all rows
		$( '.minimized .tab-grey tr th.m').show() ; //show all rows
		$( '.minimized .tab-grey tr td.m.s' ).attr('colspan', 1) ; //show only current row
		$( '.minimized .tab-grey tr th.m.s' ).attr('colspan', 1) ; //show only current row

		$( '.minimized #' + id + ' td.m.action-column' ).addClass('opshow') ; //show only current row
		
	}) ;
	$('.minimized .tab-grey tr').mouseout(function(){
		var id = $(this).attr('id');
		if( $(this).find('th').length )
		{
			return ;
		}	
		
		$( '.minimized .tab-grey td.m').hide() ; //show all rows
		$( '.minimized .tab-grey th.m').hide() ; //show all rows
		$( '.minimized .tab-grey td.m.s' ).attr('colspan', 2) ; //show only current row
		$( '.minimized .tab-grey th.m.s' ).attr('colspan', 2) ; //show only current row

		$( '.minimized #' + id + ' td.m.action-column' ).removeClass('opshow') ; //show only current row
	}) ;
	if( defState == 'hide' )
	{
		var id = $(this).attr('id');
		if( $(this).find('th').length )
		{
			return ;
		}	
		
		$( '.minimized .tab-grey td.m').hide() ; //show all rows
		$( '.minimized .tab-grey th.m').hide() ; //show all rows
		$( '.minimized .tab-grey td.m.s' ).attr('colspan', 2) ; //show only current row
		$( '.minimized .tab-grey th.m.s' ).attr('colspan', 2) ; //show only current row

		$( '.minimized #' + id + ' td.m.action-column' ).removeClass('opshow') ; //show only current row
	}
	else if( defState == 'show' )
	{
				var id = $(this).attr('id');
		if( $(this).find('th').length )
		{
			return ;
		}	
		//$( '#' + id + ' td.m.c').show() ; //show only current row
		//$( '#' + id + ' td:visible:first' ).attr('colspan', 1) ; //show only current row
		$( '.minimized .tab-grey tr td.m').show() ; //show all rows
		$( '.minimized .tab-grey tr th.m').show() ; //show all rows
		$( '.minimized .tab-grey tr td.m.s' ).attr('colspan', 1) ; //show only current row
		$( '.minimized .tab-grey tr th.m.s' ).attr('colspan', 1) ; //show only current row

		$( '.minimized #' + id + ' td.m.action-column' ).addClass('opshow') ; //show only current row
		

	}
	else if( defState == 'showall' )
	{
		$( '.minimized .tab-grey td.m').show() ; //show all rows
		$( '.minimized .tab-grey th.m').show() ; //show all rows
		$( '.minimized .tab-grey td.m.s' ).attr('colspan', 1) ; //show only current row
		$( '.minimized .tab-grey th.m.s' ).attr('colspan', 1) ; //show only current row
		$( '.minimized #' + id + ' td.m.action-column' ).removeClass('opshow') ; //show only current row
	}
}

function jConfirm(msg, okfunc, cancelfunc)
{
    $('#idModalDialogBox').html(msg) ;
    $('#idModalDialogBox').dialog(
		{
			resizable: false,
			modal: true,
			title : 'Confirm',
			buttons:
					{ 
						"OK": function(){
							jQuery(this).dialog("close");
							if( typeof okfunc === 'function' ){ okfunc();}
						},
						"Close": function(){
							jQuery(this).dialog("close");
							if( typeof cancelfunc === 'function' ){ cancelfunc(); }
						}
					}
		}); 
		return false ;
}
function jAlert(msg)
{
    $('#idModalDialogBox').html(msg) ;
    $('#idModalDialogBox').dialog(
		{
			resizable: false,
			modal: true,
			buttons:
					{ 
						"OK": function(){ jQuery(this).dialog("close");}
					}
		}); 
		return false ;
}
function onListIconClick(listId, url, params, targetId)
{
	if( jQuery('#idContentAreaSmall #' + listId).length )
	{
		_onContentAreaClose() ;
		return ;
	}
	else if( jQuery('#idContentAreaBig #' + listId).length )
	{
		//do nothing
		return ;
	}
	actionView(url, params, targetId) ;
}
function onHideitIconClick(node)
{
	if( jQuery(node).hasClass('flaticon-minus') )
	{
//		
//	}
//	
//	if( jQuery('.hideit').eq(0).is(':visible') )
//	{
		jQuery(node).removeClass('flaticon-minus') ;
		jQuery(node).addClass('flaticon-plus') ;		
		jQuery('.hideit').hide() ;
	}
	else
	{
		jQuery(node).removeClass('flaticon-plus') ;
		jQuery(node).addClass('flaticon-minus') ;
		jQuery('.hideit').show() ;
	}
}
function onSubmenuClick(id, obj)
{
	if( jQuery('#' + id).is(':visible') )
	{
		jQuery('#' + id).slideUp(100) ;
		jQuery(obj).find('.menuarrow').addClass('flaticon-downarrow') ;
		jQuery(obj).find('.menuarrow').removeClass('flaticon-uparrow') ;
	}
	else
	{
		jQuery('.pure-submenu').each(function(){
			$(this).slideUp(100) ;
			$('.menuarrow').addClass('flaticon-downarrow') ;
			$('.menuarrow').removeClass('flaticon-uparrow') ;
		}) ;

		jQuery('#' + id).slideDown(100) ;
		jQuery(obj).find('.menuarrow').addClass('flaticon-uparrow') ;
		jQuery(obj).find('.menuarrow').removeClass('flaticon-downarrow') ;
	}
}
function onBulkActionClick(fid, action, msg, showPrompt)
{
	var promptVal = '' ;
	if( showPrompt === true )
	{
		promptVal = prompt(msg) ;
		if( promptVal.length < 1 )
		{
			return false ;
		}
	}
	else
	{
		if( ! confirm(msg) )
		{
			return false ;
		}
	}
	
	jQuery('#hidbulkaction').val(action) ;
	jQuery('#hidbulkprompt').val(promptVal) ;
	jQuery('#' + fid).submit() ;
	return true ;
}
function onSaveAsTemplate(obj)
{
	var val = prompt('Enter new template name') ;
	if( val )
	{
		var newval = encodeURI(val) ;
		jQuery(obj).append('<option value="' + newval + '">' + val + '</option>') ;
		jQuery(obj).val( newval ) ;

		jQuery('#idTplButtonUpdate').click() ;
		return true ;
	}
	return false ;
}
var gTemplateLastSelected = null ;
function onTemplateChange(obj, url)
{
	if( jQuery(obj).val() == 'new' )
	{
		var val = prompt('Enter new template name') ;
		if( val )
		{
			
			//check already exists or not {
			fail = false ;
			jQuery('#idTplSelect option').each(function(x, y) {
				if( jQuery(this).text() === encodeURI(val) )
				{
					alert('Name already exists') ;
					fail = true ;
				}
			});
			if( fail )
			{
				jQuery(obj).val( '' ) ;
				return false;
			}
			//}
			
			jQuery(obj).append('<option value="' + encodeURI(val) + '">' + val + '</option>') ;
			jQuery(obj).val( val ) ;
		}
		else
		{
			jQuery(obj).val( gTemplateLastSelected ) ;
			return ;
		}
	}
	if( jQuery(obj).val() )
	{
		jQuery('#idTplButtonUpdate').hide();
		jQuery('#idTplButtonDelete').show();
	}
	else
	{
		jQuery('#idTplButtonUpdate').hide();
		jQuery('#idTplButtonDelete').hide();		
	}
	//store for next time use.
	gTemplateLastSelected = jQuery(obj).val() ;

	actionView(url, {}, 'idPrivContainer' ) ;
}
function siteUrl(path)
{
	return (QFS_SITE_URL + path) ;
}

function updateNotifications()
{
	getData( siteUrl('ajax/notifications'), {}, 'idNotificationArea', null, null, true, true) ;
}

var thisDay ;
var thisDay = new Date().getDate() ;
setInterval(function(){
	var newDay = new Date().getDate(); 
	if( thisDay != newDay )
	{
		updateNotifications() ;
	}
	thisDay = newDay ;
}, 1000*60) ;

//var o = new Date();
//var t1 = o.getTime() + (24*60*60*1000) ;
//var o2 = new Date() ;
//o2.setTime(t1) ;
//var d = new Date(o2.getFullYear(), o2.getMonth(), o2.getDate(), 0, 0, 0, 0) ;
//var diff = (d.getTime() - o.getTime()) + (60*1000);
//
//setTimeout('updateNotifications()', parseInt(diff)) ;

//DynaTable Start {
function dynaAdd(table, tplDataVar)
{
	var str  = 'var lDynaTemplateTmp = ' + tplDataVar + ';' ;
	eval(str) ;
	var tpl = decodeURIComponent(lDynaTemplateTmp.replace(/\+/g, ' ')) ;
	//replace
	var id = 'd' + Math.random().toString().replace('.', '') ;
	
	tpl = tpl.replace(/\{\%1\}/g, id) ;
	jQuery('#' + table).append( tpl );

	var str = " if( typeof " + table + "Update == 'function'){ " + table + "Update('add');}" ;
	eval(str); 
}
function dynaRemove(table, index)
{
	jQuery('#' + table + ' ' + '#idDynaTableTr' + index ).remove() ;

	var str = " if( typeof " + table + "Update == 'function'){ " + table + "Update('remove','" + index + "');}" ;

	eval(str); 
}
function dynaClear(table)
{
	jQuery('#' + table + ' .dyna-tr').remove() ;

	var str = " if( typeof " + table + "Update == 'function'){ " + table + "Update('clear');}" ;
	eval(str); 
}
// DynaTable End }

var popupSubmitCallback = null ;
/**
 * 
 * @param {type} url url to call
 * @param {type} params parameters to pass
 * @param {type} size size options. small, normal, full or custom percentage.
 * @param {type} before a function to call before ajax popup loading.
 * @param {type} after a function called on after finished a form. When it received a successful response after form submission.
 * @param {type} autoFill fill values automatically.
 * @param {type} silent silent call, do not show loading gif.
 * @return boolean true on successfully called.
 */
function popupSubmit(url, params, size, before, after, autoFill, silent)
{
	popupSubmitCallback = after ;
	if( typeof size != 'undefined' )
	{
		if( size == 'small' )
		{
			size = '50%' ;
		}
		else if( size == 'full' )
		{
			size = '100%' ;
		}
		else if( size == null)
		{
			size = '75%' ;
		}
	}
	else 
	{
		size = '75%' ;
	}
	jQuery('#idPopupSubmit').css('width', size) ;
	jQuery('#idPopupSubmit').css('height', size) ;

	jQuery('#idContentAreaClicked').val('idPopupSubmit') ;
	//Pass Response
	getData(url, params, null, before, function(data){

		jQuery('#idPopupSubmit').html(data) ;
		jQuery('#idPopupSubmit').slideDown('fast') ;
		jQuery('#idPopupSubmitContainer').slideDown('fast') ;
		if( typeof  after !== undefined )
		{
			after(data) ;
		}
	}, true, false);
}
function popupSubmitHide()
{	
	jQuery('#idPopupSubmit').slideUp('fast') ;
	jQuery('#idPopupSubmitContainer').slideUp('fast') ;
}

function number_format (number, decimals, dec_point, thousands_sep) {
    var n = number, prec = decimals;

    var toFixedFix = function (n,prec) {
        var k = Math.pow(10,prec);
        return (Math.round(n*k)/k).toString();
    };

    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep === 'undefined') ? '' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

    var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); 
    //fix for IE parseFloat(0.55).toFixed(0) = 0;

    var abs = toFixedFix(Math.abs(n), prec);
    var _, i;

    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;

        _[0] = s.slice(0,i + (n < 0)) +
               _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }

    var decPos = s.indexOf(dec);
    if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
        s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
    }
    else if (prec >= 1 && decPos === -1) {
        s += dec+new Array(prec).join(0)+'0';
    }
    return s; 
}

/* 
 * Field formattign library {
 */
function fieldFormatter(els)
{
	//any one checked
	this.number = function()
	{
		var b = false ;
		els.each(function(){
			jQuery(this).on( "blur", function() {
				var val = jQuery(this).val() ;
				
				val = number_format(val, 2) ;
				jQuery(this).val(val) ;
			});
		});
		return b ;
	};
	return this ;
};
(function($) {     // Compliant with jquery.noConflict()

	$.fn.fieldFormatter = function()
	{
		return fieldFormatter(this) ;
	};
}
)(jQuery);


/**
* Validate a form.
* @method validateForm
* @param {json} fieldsAndRules a json objec contains fieds and validtion information. format is as following
*	{
*		inputFieldName : {
*						func : "functionNameWithArguments()",
*						errfield : "errorFieldName",
*						errmsg : "errorMessage"
*					},
*		anotherFieldName : { ... }
*	}
* @param {string} summaryField a summary of errors will be placed here, using <li> tag.
* @param {function} before function to be called before validtion
* @param {function} after function to be called called after validtion. This function will reive overall validation status as its first argument.
* @return {bool} overall status true if every validation succeeded.
*/
function fieldFormat(fieldsAndRules, parent)
{
	var bstatus = true ;

	if( typeof fieldsAndRules == 'object' )
	{		
		if( typeof parent !== 'undefined' )
		{
			if( parent.length > 0 )
			{
				parent = (parent + ' ') ;
			}
		}
		else
		{
			parent = '';
		}

		jQuery.each(fieldsAndRules, function(name, val) {
			var ec = 'var result = jQuery(\'' + parent + name + '\').fieldFormatter().' + val.func + ';' ;

			eval(ec) ;
			
			if( ! result )
			{
				bstatus = false ;
			}
			
			
		}) ;
			  
	}
	return bstatus ;
}
/* 
 * }
 */

/**
 * Readonly field, bidnig..
 */
function readonlyBinding(fields)
{
	jQuery(fields).each( function (x, y){
		var tag = jQuery(this).prop('tagName') ;

		switch( tag )
		{
			case 'INPUT' :
				jQuery(this).attr('readonly', 'readonly') ;
				jQuery(this).on('mousedown', function(){return false;}) ;
				jQuery(this).on('keydown', function(){return false;}) ;
				jQuery(this).on('click', function(){return false;}) ;
				break ;
			case 'SELECT' :
				jQuery(this).attr('readonly', 'readonly') ;
				
				jQuery(this).on('mousedown', function(){return false;}) ;
				jQuery(this).on("focus", function(){this.defaultIndex = this.selectedIndex; });
				jQuery(this).on("change", function(){ this.selectedIndex = this.defaultIndex; });
				break ;
		}
	});
}



	var hexDigits = new Array
			("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "a", "b", "c", "d", "e", "f");
	function hex(x) {
		return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
	}

	function rgb2hex(rgb) {
		
		if ( rgb.length < 1 ) {
			return '#000000';
		}
		rgba = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		if( rgba != null )
		{
			return "#" + hex(rgba[1]) + hex(rgba[2]) + hex(rgba[3]);
		}
		return '#000000' ;
	}
	
	function getRandomColor() 
	{
		var letters = '0123456789ABCDEF'.split('');
		var color = '#';
		for (var i = 0; i < 6; i++) 
		{
			color += letters[Math.floor(Math.random() * 16)];
		}
		return color;
	}
	
	function updateBackgroundString(where, data)
	{
		var str = jQuery(where).val() || '' ;

		var a = str.split(' ') ;
		var ret = '' ;
		
		var index;
		if( a.length > 1 )
		{
			for (index = 0; index < a.length; ++index) {
				if( ret.length > 0 )
				{
					ret += " " ;
				}
				if( index === 1 )
				{
					ret += "url('" + data + "')" ;
				}
				else
				{
					ret += a[index];
				}
			}
		}
		else
		{
			ret += "url('" + data + "')" ;
		}
		
		jQuery(where).val(ret) ;
	}
	
function queryStringToJson(query) 
{
	
	var query_string = {};
	var vars = query.split("&");
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split("=");
		pair[0] = decodeURIComponent(pair[0]);
		pair[1] = decodeURIComponent(pair[1]);
		// If first entry with this name
		if (typeof query_string[pair[0]] === "undefined") {
			query_string[pair[0]] = pair[1];
			// If second entry with this name
		} else if (typeof query_string[pair[0]] === "string") {
			var arr = [query_string[pair[0]], pair[1]];
			query_string[pair[0]] = arr;
			// If third or later entry with this name
		} else {
			query_string[pair[0]].push(pair[1]);
		}
	}
	return query_string;
}