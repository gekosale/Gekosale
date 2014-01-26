/*
* ALERT
*/

var GAlert = function(sTitle, sMessage, oParams) {
	if (sMessage == undefined) {
		sMessage = '';
	}
	var iAlertId = GAlert.Register();
	if (GAlert.sp_dHandler != undefined) {
		GAlert.sp_dHandler.Alert(sTitle, sMessage, oParams, iAlertId);
	}
	else {
		alert(sTitle + '\n' + sMessage);
	}
	return iAlertId;
};

var GWarning = function(sTitle, sMessage, oParams) {
	if (oParams == undefined) {
		oParams = {};
	}
	oParams.iType = GAlert.TYPE_WARNING;
	return GAlert(sTitle, sMessage, oParams);
};

var GError = function(sTitle, sMessage, oParams) {
	if (oParams == undefined) {
		oParams = {};
	}
	oParams.iType = GAlert.TYPE_ERROR;
	return GAlert(sTitle, sMessage, oParams);
};

var GMessage = function(sTitle, sMessage, oParams) {
	if (oParams == undefined) {
		oParams = {};
	}
	oParams.iType = GAlert.TYPE_MESSAGE;
	return GAlert(sTitle, sMessage, oParams);
};

var GPrompt = function(sTitle, fOnConfirm, oParams) {
	if (oParams == undefined) {
		oParams = {};
	}
	var sMessage = '<span class="field-text"><input type="text" class="prompt-value" value="' + ((oParams.sDefault == undefined) ? '' : oParams.sDefault) + '"/></span>';
	oParams = $.extend(true, {
		bAutoExpand: true,
		bNotRetractable: true,
		aoPossibilities: [
			{
				mLink: GEventHandler(function(eEvent) {
					var sValue = $(this).closest('.message').find('input.prompt-value').val();
					fOnConfirm.apply(this, [sValue]);
				}),
				sCaption: GMessageBar.Language.add
			},
			{
				mLink: GAlert.DestroyThis,
				sCaption: GMessageBar.Language.cancel
			}
		]
	}, oParams);
	oParams.iType = GAlert.TYPE_PROMPT;
	return GAlert(sTitle, sMessage, oParams);
};

GAlert.Destroy = function(iAlertId) {
	if (GAlert.sp_dHandler != undefined) {
		GAlert.sp_dHandler.Destroy(iAlertId);
	}
};

GAlert.DestroyThis = function(eEvent) {
	GAlert.Destroy($(this));
};
	
GAlert.DestroyAll = function() {
	if (GAlert.sp_dHandler != undefined) {
		GAlert.sp_dHandler.DestroyAll();
	}
};

GAlert.Register = function() {
	return GAlert.s_iCounter++;
};

GAlert.sp_dHandler;
GAlert.s_iCounter = 0;

GAlert.TYPE_WARNING = 0;
GAlert.TYPE_ERROR = 1;
GAlert.TYPE_MESSAGE = 2;
GAlert.TYPE_PROMPT = 3;


/*
* CORE
*/

var oDefaults = {
	iCookieLifetime: 30,
	sDesignPath: '',
	iActiveView: null,
	aoViews:'',
	iActiveLanguage:1,
	aoLanguages:'',
	aoVatValues:'',
	sCurrentController: '',
	sCurrentAction: '',
};

GCore = function(oParams) {
	GCore.p_oParams = oParams;
	GCore.DESIGN_PATH = GCore.p_oParams.sDesignPath;
	GCore.iActiveView = GCore.p_oParams.iActiveView;
	GCore.aoViews = GCore.p_oParams.aoViews;
	GCore.iActiveLanguage = GCore.p_oParams.iActiveLanguage;
	GCore.aoLanguages = GCore.p_oParams.aoLanguages;
	GCore.aoVatValues = GCore.p_oParams.aoVatValues;
	GCore.sCurrentController = GCore.p_oParams.sCurrentController;
	GCore.sAdminUrl = GCore.p_oParams.sUrl;
	GCore.sCurrentAction = GCore.p_oParams.sCurrentAction;
	GXajaxInterface.Initialize();
	GCore.StartWaiting();
};


GCore.NULL = 'null';

GCore.s_afOnLoad = [];

GCore.GetArgumentsArray = function(oArguments) {
	var amArguments = [];
	for (var i = 0; i < oArguments.length; i++) {
		amArguments[i] = oArguments[i];
	}
	return amArguments;
};

GCore.Duplicate = function(oA, bDeep) {
	var oB = $.extend((bDeep == true), {}, oA);
	return oB;
};

GCore.OnLoad = function(fTarget) {
	GCore.s_afOnLoad.push(fTarget);
};

GCore.Init = function() {
	for (var i = 0; i < GCore.s_afOnLoad.length; i++) {
		GCore.s_afOnLoad[i]();
	}
	$('#content').css('visibility', 'visible').children('.preloader').remove();
	GCore.StopWaiting();
};

GCore.ExtendClass = function(fBase, fChild, oDefaults) {
	var fExtended = function() {
		var aBaseArguments = [];
		for (var i = 0; i < arguments.length; i++) {
			aBaseArguments.push(arguments[i]);
		}
		var result = fBase.apply(this, aBaseArguments);
		if (result === false) {
			return result;
		}
		fChild.apply(this, arguments);
		this.m_oOptions = $.extend(true, GCore.Duplicate(oDefaults, true), arguments[0]);
		return this;
	};
	for(var i in fBase.prototype) {
		fExtended.prototype[i] = fBase.prototype[i];
	}
	return fExtended;
};

GCore.ObjectLength = function(oObject) {
	var iLength = 0;
	for (var i in oObject) {
		iLength++;
	}
	return iLength;
};

GCore.FilterObject = function(oSource, fTest) {
	var oResult = {};
	for (var i in oSource) {
		if (fTest(oSource[i])) {
			oResult[i] = GCore.Duplicate(oSource[i], true);
		}
	}
	return oResult;
};

GCore.GetIterationArray = function(oSource, fCompare) {
	var oSource = $.extend(true, {}, oSource);
	var aSource = [];
	for (var i in oSource) {
		aSource.push($.extend(true, {$$key: i}, oSource[i]));
	}
	aSource.sort(fCompare);
	var asIterationArray = [];
	for (var i = 0; i < aSource.length; i++) {
		asIterationArray.push(aSource[i]['$$key']);
	}
	return asIterationArray;
};

GCore.StartWaiting = function() {
	$('body').css({
		cursor: 'wait'
	});
};

GCore.StopWaiting = function() {
	$('body').css({
		cursor: 'auto'
	});
};


var GEventHandler = function(fHandler) {
	var fSafeHandler = function(eEvent) {
		try {
			if (eEvent.data) {
				for (var i in eEvent.data) {
					this[i] = eEvent.data[i];
				}
			}
			return fHandler.apply(this, arguments);
		}
		catch (xException) {
			GException.Handle(xException);
			return false;
		}
	};
	return fSafeHandler;
};

/*
 * GCookie 
 */

GCookie = function(name, value, options) {
    if (typeof value != 'undefined') {
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString();
        }
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { 
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

/*
* CACHE
* For caching xajax requests.
*/

var GCache = function() {
	
	var gThis = this;
	
	this.m_oResponses;
	
	this._Constructor = function() {
		this.m_oResponses = {};
	};
	
	this.Execute = function(fHandler, oRequest, sCallback) {
		var sRequest = JSON.stringify(oRequest);
		if (this.m_oResponses[sRequest] != undefined) {
			gThis.ReturnResponse(sCallback, this.m_oResponses[sRequest]);
			return;
		}
		fHandler(oRequest, GCallback(this.SaveResponse, {
			sCallback: sCallback,
			sRequest: sRequest
		}));
	};
	
	this._CompareRequests = function(oA, oB) {
		for (var i in oA) {
			if (oA[i] != oB[i]) {
				return false;
			}
		}
		for (var j in oB) {
			if (oA[j] != oB[j]) {
				return false;
			}
		}
		return true;
	};
	
	this.ReturnResponse = function(sFunction, oResponse) {
		eval(sFunction + '(oResponse);');
	};
	
	this.SaveResponse = new GEventHandler(function(eEvent) {
		var sCallback = eEvent.sCallback;
		var sRequest = eEvent.sRequest;
		delete eEvent.sCallback;
		delete eEvent.sRequest;
		gThis.m_oResponses[sRequest] = eEvent;
		gThis.ReturnResponse(sCallback, eEvent);
	});
	
	this._Constructor();
	
};


/*
* CALLBACK
*/

var GCallback = function(fHandler, oParams) {
	if (oParams == undefined) {
		oParams = {};
	}
	var i = GCallback.s_iReferenceCounter++;
	GCallback.s_aoReferences[i] = {
		fHandler: fHandler,
		oParams: oParams
	};
	GCallback['Trigger_' + i] = function() {
		GCallback.Invoke(i, GCore.GetArgumentsArray(arguments));
	};
	return 'GCallback.Trigger_' + i + '';
};

GCallback.s_iReferenceCounter = 0;
GCallback.s_aoReferences = {};

GCallback.Invoke = function(iReference, amArguments) {
	if (amArguments[0] == undefined) {
		amArguments[0] = {};
	}
	var oReference = GCallback.s_aoReferences[iReference];
	if (oReference != undefined) {
		oReference.fHandler.call(this, $.extend(oReference.oParams, amArguments[0]));
	}
	delete GCallback.s_aoReferences[iReference];
};

/*
* EXCEPTION
*/

var GException = function(sMessage) {
	this.m_sMessage = sMessage;
	this.toString = function() {
		return this.m_sMessage;
	};
};

GException.Handle = function(xException) {
	new GAlert(GException.Language['exception_has_occured'], xException);
	throw xException; // for debugging
};


/*
* PLUGIN
*/

var GPlugin = function(sPluginName, oDefaults, fPlugin) {
	
	(function($) {

		var oExtension = {};
		oExtension[sPluginName] = function(oOptions) {
			if ($(this).hasClass(sPluginName)) {
				return;
			}
			oOptions = $.extend(GCore.Duplicate(oDefaults), oOptions);
			return this.each(function() {
				this.m_oOptions = oOptions;
				this.m_iId = GPlugin.s_iCounter++;
				GPlugin.s_oInstances[this.m_iId] = this;
				this.m_oParams = {};
				this._GetClass = function(sClassName) {
					var sClass = this.m_oOptions.oClasses['s' + sClassName + 'Class'];
					if (sClass == undefined) {
						return '';
					}
					else {
						return sClass;
					}
				};
				this._GetImage = function(sImageName) {
					var sImage = this.m_oOptions.oImages['s' + sImageName];
					if (sImage == undefined) {
						return '';
					}
					else {
						return GCore.DESIGN_PATH + sImage;
					}
				};
				try {
					if($(this).attr('class') != undefined){
						var asParams = $(this).attr('class').match(/G\:\w+\=\S+/g);
						if (asParams != undefined) {
							for (var i = 0; i < asParams.length; i++) {
								var asParamData = asParams[i].match(/G:(\w+)\=(\S+)/);
								this.m_oParams[asParamData[1]] = asParamData[2];
							}
						}
					}
					$(this).addClass(sPluginName);
					fPlugin.apply(this, [this.m_oOptions]);
				}
				catch(xException) {
					throw xException;
					GException.Handle(xException);
				}
			});
		};
		$.fn.extend(oExtension);
		fPlugin.GetInstance = GPlugin.GetInstance;
	
	})(jQuery);
	
};

GPlugin.s_iCounter = 0;
GPlugin.s_oInstances = {};

GPlugin.GetInstance = function(iId) {
	if (GPlugin.s_oInstances[iId] != undefined) {
		return GPlugin.s_oInstances[iId];
	}
	throw new GException('Requested instance (' + iId + ') not found.');
	return false;
};


/*
* BLOCK
* Adds rounded corners to a block.
*/

var oDefaults = {
	oClasses: {
		sNE: 'beginning-r',
		sNW: 'beginning-l',
		sSE: 'ending-r',
		sSW: 'ending-l'
	}
};

var GBlock = function() {
	
	var gThis = this;
	
	this._Constructor = function() {
		$(gThis).prepend('<div class="' + gThis.m_oOptions.oClasses.sNE + '"/>');
		$(gThis).prepend('<div class="' + gThis.m_oOptions.oClasses.sNW + '"/>');
		$(gThis).append('<div class="' + gThis.m_oOptions.oClasses.sSE + '"/>');
		$(gThis).append('<div class="' + gThis.m_oOptions.oClasses.sSW + '"/>');
	};
	
	gThis._Constructor();
	
};

new GPlugin('GBlock', oDefaults, GBlock);



/*
* BOX
* Adds a subtle shadow to a block.
*/

var oDefaults = {
	oClasses: {
		sN: 'beginning',
		sS: 'ending'
	}
};

var GBox = function() {
	
	var gThis = this;
	
	gThis._Constructor = function() {
		$(gThis).prepend('<div class="' + gThis.m_oOptions.oClasses.sN + '"/>');
		$(gThis).append('<div class="' + gThis.m_oOptions.oClasses.sS + '"/>');
	};
	
	gThis._Constructor();
	
};

new GPlugin('GBox', oDefaults, GBox);

/*
* CHART
* Chart
*/

var oDefaults = {
	oDefaultData: {
    	areaOpacity: 0.6,
    	colors: ['#33cccc', '#CC0000', '#FF7400', '#FF0084', '#4096EE', '#B02B2C', '#D15600', '#C3D9FF', '#CDEB8B', '#36393D'],
    	chartArea: {
        	left: 50,
        	top: 50,
        	height: 300,
        	width: 700,
        	backgroundColor: '#fbfbfb',
        },
        backgroundColor: {
            fill: '#fbfbfb',
        	stroke: '#ececec',
            strokeWidth: 3,
        },
    	width: 850, 
    	height: 400,
    	lineWidth: 3,
    	pointSize: 10,
    	vAxis: {
        	gridlines:{
        		color: '#ececec', 
        	}
        },
    	hAxis: {
        	gridlines:{
        		color: '#ececec', 
        	}
        },
        legend: {
        	position: 'top'
        },
	},
	sType: 'area',
	oParams: {},
	fSource: GCore.NULL,
};

var GChart = function() {
	
	var gThis = this;
	
	gThis._Constructor = function() {
		gThis.oOptions = $.extend(true, GCore.Duplicate(gThis.m_oOptions.oDefaultData, true), gThis.m_oOptions.oParams);
		gThis.Update();
		$(window).bind('hashchange', function() {
			gThis.Update();
		});
	};
	
	gThis.Update = function() {
		
		if(location.hash.length){
			var url = gThis.m_oOptions.fSource + ',' + location.hash.substr(1);
		}else{
			var url = gThis.m_oOptions.fSource;
		}
		var jsonData = $.ajax({
        	url: url,
          	dataType:"json",
          	async: false
		}).responseText;
		
		var data = new google.visualization.DataTable(jsonData);
		var sId = $(gThis).attr('id');
		if(gThis.m_oOptions.sType == 'area'){
			gThis.oChart = new google.visualization.AreaChart(document.getElementById(sId));
		}
		if(gThis.m_oOptions.sType == 'pie'){
			gThis.oChart = new google.visualization.PieChart(document.getElementById(sId));
		}
		gThis.oChart.draw(data, gThis.oOptions);
	};
	
	gThis._Constructor();
	
};

new GPlugin('GChart', oDefaults, GChart);


/*
* CLIENT ACTIVITY
*/

var oDefaults = {
	oClasses: {
		sButtonClass: 'button'
	},
	jClientId: $(),
	fSource: function() {},
	gProducts: GCore.NULL
};

var GClientActivity = function() {
	
	var gThis = this;
	
	gThis._Constructor = function() {
		gThis.m_oOptions.jClientId.change(GEventHandler(function(eEvent) {
			gThis.LoadActivities();
		}));
		gThis.LoadActivities();
	};
	
	gThis.LoadActivities = function() {
		gThis.m_oOptions.fSource({
			client: gThis.m_oOptions.jClientId.val()
		}, GCallback(gThis.OnActivitiesLoaded));
	};
	
	gThis.OnActivitiesLoaded = function(oData) {
		var aoActivities = oData.clientActivity;
		$(gThis).children('h3').nextAll().remove();
		for (var i = 0; i < aoActivities.length; i++) {
			if ((aoActivities[i].products == undefined) || !(aoActivities[i].products instanceof Array)) {
				aoActivities[i].products = [];
			}
			var jForm = $('<form action="" method="post"/>');
			var iProductsCount = aoActivities[i].products.length;
			jForm.append('<h4>' + aoActivities[i].name + ' <small>(' + iProductsCount + ')</small></h4>');
			var jUl = $('<ul/>');
			for (var j = 0; j < iProductsCount; j++) {
				jUl.append('<li><label><input type="checkbox" name="product[]" value="' + aoActivities[i].products[j].id + '"/> ' + aoActivities[i].products[j].name + '</label></li>');
			}
			jForm.append(jUl);
			jForm.append('<div class="' + gThis._GetClass('Button') + '"><input type="submit" value="' + GClientActivity.Language.add_to_order + '"/></div>');
			$(gThis).append(jForm);
			jForm.submit(GEventHandler(function(eEvent) {
				var jChecked = $(this).find('input:checked');
				var aIds = [];
				for (var i = 0; i < jChecked.length; i++) {
					aIds.push(jChecked.eq(i).attr('value'));
				}
				gThis.m_oOptions.gProducts.AddProducts(aIds);
				return false;
			}));
		}
	};
	
	gThis._Constructor();
	
};

new GPlugin('GClientActivity', oDefaults, GClientActivity);



/*
* COMBO BOX
* Select field with the ability to define custom values.
*/

var oDefaults = {
	oClasses: {
	},
	oFiles: {
		sIconExpand: '_images_frontend/icons/arrows/triangle-gray-down.png',
		sIconRetract: '_images_frontend/icons/arrows/triangle-gray-up.png'
	}
};

var GComboBox = function() {
	
	var gThis = this;
	
	gThis.m_jField;
	gThis.m_sDefaultValue;
	
	gThis.Update = function() {
		$(gThis).parent().find('.faux span').text($(gThis).find('option:selected').text()).attr('class', $(gThis).find('option:selected').attr('class') + ' ');
		gThis.m_jField.val($(gThis).find('option:selected').text()).attr('class', $(gThis).find('option:selected').attr('class') + ' ');
		return true;
	};
	
	this._Constructor = function() {
		$(gThis).parent().addClass('GComboBox');
		gThis.m_sDefaultValue = $(gThis).find('option:eq(0)').text();
		gThis.m_jField = $('<input type="text" value="' + $(gThis).find('option:selected').text() + '"/>');
		$(gThis).parent().find('select').css('opacity', 0);
		$(gThis).parent().append('<span class="faux"><span style="visibility: hidden;">' + $(gThis).find('option:selected').text() + '</span></span>');
		$(gThis).parent().append(gThis.m_jField);
		$(gThis).change(gThis.Update).change(function() {
			$(gThis).trigger('GChange');
		});
		$(gThis).add(gThis.m_jField).focus(function() {
			$(gThis).closest('.field').addClass('focus');
			return true;
		});
		$(gThis).add(gThis.m_jField).blur(function() {
			$(gThis).closest('.field').removeClass('focus');
			if ($(gThis).val() != '_new_') {
				$(gThis).find('option[value="_new_"]').remove();
			}
			return true;
		});
		gThis.m_jField.focus(function() {
			if ($(this).val() == gThis.m_sDefaultValue) {
				$(this).val('');
			}
		}).blur(function() {
			if (!$(this).val().length) {
				$(this).val(gThis.m_sDefaultValue);
			}
			$(gThis).find('option[value="_new_"]').remove();
			if ($(this).val() != gThis.m_sDefaultValue) {
				var jOption = $(gThis).find('option');
				var bFound = false;
				if($('.field-attribute-editor').length == 0){
					for (var i = 0; i < jOption.length; i++) {
						if (jOption.eq(i).text() == $(this).val()) {
							$(gThis).val(jOption.eq(i).attr('value'));
							bFound = true;
							break;
						}
					}
				}
				if (!bFound) {
					$(gThis).append('<option value="_new_">' + $(this).val() + '</option>').val('_new_');
				}
			}
			$(gThis).trigger('GChange');
		}).keydown(gThis.PreventEnter);
		gThis.Update();
	};
	
	gThis.PreventEnter = function(eEvent) {
		if (eEvent.keyCode == 13) {
			eEvent.preventDefault();
			eEvent.stopImmediatePropagation();
			$(this).blur().change();
			$(gThis).change();
		}
	};
	
	gThis._Constructor();
	
};

new GPlugin('GComboBox', oDefaults, GComboBox);


/*
* CONTENT LOADER
* AJAX-based remote content loader.
*/

var oDefaults = {
	oParams: {},
	sSource: '',
	oClasses: {
		sLoadingClass: 'preloader'
	}
};

var GContentLoader = function() {
	
	var gThis = this;
	
	gThis.m_jHolder;
	
	gThis._Constructor = function() {
		gThis.m_jHolder = $('<div/>');
		$(gThis).append($('<div class="' + gThis._GetClass('Loading') + '"/>').css('display', 'none').fadeIn(150));
		gThis.m_jHolder.load(gThis.m_oOptions.sSource, gThis.m_oOptions.oParams, gThis.Loaded);
	};
	
	gThis.Loaded = GEventHandler(function(eEvent) {	
		$(gThis).find('.' + gThis._GetClass('Loading')).stop(true, false).fadeOut(150, function() {
			$(gThis).empty().html(gThis.m_jHolder.html()).css('opacity', 0).animate({
				opacity: 1
			}, 150);
			$(gThis).find('.block').GBlock();
			$(gThis).find('.box').GBox();
			$(gThis).find('select').GSelect();
		});
	});
	
	gThis._Constructor();
	
};

new GPlugin('GContentLoader', oDefaults, GContentLoader);


/*
* DO NOTHING
* Event handler that prevents anything else from happening.
*/

var GDoNothing = function(eEvent) {
	eEvent.preventDefault();
	eEvent.stopImmediatePropagation();
	return false;
};


/*
* LINK
*/

var GLink = function(jA, mLink) {
	if (mLink instanceof Function) {
		jA.attr('href', '#');
		jA.click(function(eEvent) {
			mLink.apply(jA, [eEvent]);
			return false;
		});
	}
	else {
		jA.attr('href', mLink);
	}
};


/*
* MENU
*/

var GMENU_MODE_CLICK = 0;
var GMENU_MODE_DELAY = 1;
var GMENU_MODE_HOVER = 2;

var GMENU_FOLLOW = 1;
var GMENU_NOFOLLOW = 0;

var oDefaults = {
	oClasses: {
		sCustomizationClass: 'customization',
		sCustomizeClass: 'customize',
		sExpandableClass: 'expandable',
		sButtonClass: 'button',
		sCancelClass: 'cancel',
		sSaveClass: 'save',
		sActiveClass: 'active'
	},
	iDefaultMode: GMENU_MODE_HOVER,
	iDefaultDelay: 500,
	iDefaultFollow: GMENU_NOFOLLOW,
	sModeCookieName: 'gekosale-panel-menu-mode',
	sDelayCookieName: 'gekosale-panel-menu-delay',
	sFollowCookieName: 'gekosale-panel-menu-follow',
};

var GMenu = function() {
	
	var gThis = this;
	
	gThis.m_jCustomizationTrigger;
	gThis.m_jCustomization;
	
	gThis.m_iMode;
	gThis.m_iDelay;
	gThis.m_bCustomization;
	
	gThis._Constructor = function() {
		gThis.m_bCustomization = false;
		gThis._UpdateParams();
		$(gThis).find('li:has(ul)').mouseenter(gThis.OnMouseEnter).mouseleave(gThis.OnMouseLeave).children('ul').hide();
		$(gThis).find('li:has(ul)').children('a').click(gThis.OnMouseClick);
		$(gThis).find('li li:has(ul)').addClass(gThis.m_oOptions.oClasses.sExpandableClass);
		gThis._PrepareCustomization();
	};
	
	gThis._PrepareCustomization = function() {
		gThis.m_jCustomizationTrigger = $('<span class="' + gThis.m_oOptions.oClasses.sCustomizeClass + '" title="' + GMenu.Language.customize + '"/>');
		gThis.m_jCustomizationTrigger.click(gThis.OnExpandCustomization);
		gThis.m_jCustomization = $('<div class="' + gThis.m_oOptions.oClasses.sCustomizationClass + '"/>');
		gThis.m_jCustomization.append('<h3>' + GMenu.Language.choose_mode + '</h3>');
		gThis.m_jCustomization.append('<label><input type="radio" name="GMenu-' + gThis.m_iId + '-mode" value="' + GMENU_MODE_CLICK + '"/> ' + GMenu.Language.mode_click + '</label>');
		gThis.m_jCustomization.append('<label><input type="radio" name="GMenu-' + gThis.m_iId + '-mode" value="' + GMENU_MODE_HOVER + '"/> ' + GMenu.Language.mode_hover + '</label>');
		var jSaveTrigger = $('<a href="#" class="' + gThis.m_oOptions.oClasses.sButtonClass + '" title="' + GMenu.Language.save_desc + '"><span>' + GMenu.Language.save + '</span></a>');
		jSaveTrigger.click(gThis.OnSave);
		var jCancelTrigger = $('<a href="#" title="' + GMenu.Language.restore_default_desc + '"><span>' + GMenu.Language.restore_default + '</span></a>');
		jCancelTrigger.click(gThis.OnCancel);
		gThis.m_jCustomization.append($('<p class="' + gThis.m_oOptions.oClasses.sSaveClass + '"/>').append(jSaveTrigger));
		gThis.m_jCustomization.append($('<p class="' + gThis.m_oOptions.oClasses.sCancelClass + '"/>').append(jCancelTrigger));
		gThis.m_jCustomization.hide();
		$(gThis).after(gThis.m_jCustomization);
		$(gThis).after(gThis.m_jCustomizationTrigger);
	};
	
	gThis.OnMouseClick = new GEventHandler(function(eEvent) {
		if ((gThis.m_iMode == GMENU_MODE_DELAY) || (gThis.m_iMode == GMENU_MODE_CLICK)) {
			var jLi = $(this).closest('li');
			jLi.stop(true, false);
			gThis._ShowMenu(jLi);
		}
		return false;
	});
	
	gThis.OnMouseEnter = new GEventHandler(function(eEvent) {
		if (gThis.m_iMode == GMENU_MODE_HOVER) {
			gThis._ShowMenu($(this));
		}
		else if (gThis.m_iMode == GMENU_MODE_DELAY) {
			$(this).delay(gThis.m_iDelay, function() {
				gThis._ShowMenu($(this));
			});
		}
	});
	
	gThis.OnMouseLeave = new GEventHandler(function(eEvent) {
		$(this).stop(true, false);
		gThis._HideMenu($(this));
	});
	
	gThis._ShowMenu = function(jParent) {
		if (gThis.m_bCustomization) {
			return;
		}
		var jUl = jParent.find('ul:first');
		jUl.hide().stop(true, true);
		if (jParent.closest('ul').hasClass('GMenu')) {
			jUl.slideDown(150);
		}
		else {
			jUl.show('slide', {}, 150);
		}
	};
	
	gThis._HideMenu = function(jParent) {
		var jUl = jParent.find('ul:first');
		jUl.stop(true, true).fadeOut(50);
	};
	
	gThis._UpdateParams = function() {
		var sCookie;
		var bSave = false;
		sCookie = GCookie(gThis.m_oOptions.sModeCookieName);
		if ((sCookie == undefined) || (sCookie == '')) {
			gThis.m_iMode = parseInt(gThis.m_oOptions.iDefaultMode);
			bSave = true;
		}
		else {
			gThis.m_iMode = parseInt(sCookie);
		}
		sCookie = GCookie(gThis.m_oOptions.sDelayCookieName);
		if ((sCookie == undefined) || (sCookie == '')) {
			gThis.m_iDelay = parseInt(gThis.m_oOptions.iDefaultDelay);
			bSave = true;
		}
		else {
			gThis.m_iDelay = parseInt(sCookie);
		}
		if (bSave) {
			gThis._SaveParams();
		}
	};
	
	gThis._SaveParams = function() {
		GCookie(gThis.m_oOptions.sDelayCookieName, gThis.m_iDelay, {
			expires: GCore.p_oParams.iCookieLifetime
		});
		GCookie(gThis.m_oOptions.sModeCookieName, gThis.m_iMode, {
			expires: GCore.p_oParams.iCookieLifetime
		});
	};
	
	gThis.OnExpandCustomization = new GEventHandler(function(eEvent) {
		if (gThis.m_bCustomization) {
			gThis.OnRetractCustomization({});
			return false;
		}
		gThis.m_jCustomizationTrigger.addClass(gThis.m_oOptions.oClasses.sActiveClass);
		gThis.m_bCustomization = true;
		gThis.m_jCustomization.css('left', - gThis.m_jCustomization.width() + gThis.m_jCustomizationTrigger.offset().left - gThis.m_jCustomizationTrigger.parent().offset().left).slideDown(150);
		gThis.m_jCustomization.find('input[value="' + gThis.m_iMode + '"]').click();
		gThis.m_jCustomization.find('input[name="GMenu-' + gThis.m_iId + '-delay"]').val(gThis.m_iDelay);
	});
	
	gThis.OnRetractCustomization = new GEventHandler(function(eEvent) {
		if (!gThis.m_bCustomization) {
			return false;
		}
		gThis.m_jCustomizationTrigger.removeClass(gThis.m_oOptions.oClasses.sActiveClass);
		gThis.m_bCustomization = false;
		gThis.m_jCustomization.slideUp(100);
	});
	
	gThis.OnSave = new GEventHandler(function(eEvent) {
		gThis.m_iMode = gThis.m_jCustomization.find('input[type="radio"]:checked').val();
		gThis.m_iDelay = parseInt(gThis.m_jCustomization.find('input[name="GMenu-' + gThis.m_iId + '-delay"]').val());
		if (isNaN(gThis.m_iDelay)) {
			gThis.m_iDelay = gThis.m_oOptions.iDefaultDelay;
		}
		gThis._SaveParams();
		gThis.OnRetractCustomization({});
		return false;
	});
	
	gThis.OnCancel = new GEventHandler(function(eEvent) {
		GCookie(gThis.m_oOptions.sDelayCookieName, null);
		GCookie(gThis.m_oOptions.sModeCookieName, null);
		gThis.OnRetractCustomization({});
		gThis._UpdateParams();
		return false;
	});
	
	gThis._Constructor();
	
};

new GPlugin('GMenu', oDefaults, GMenu);

/*
* STICKY
*/

var oDefaults = {
};

var GSticky = function() {
	
	var gThis = this;
	
	gThis._Constructor = function() {
		gThis.m_jSticky = $(gThis);
		gThis.sStickyId = gThis.m_jSticky.attr('id');
		sCookie = GCookie(gThis.sStickyId);
		if(sCookie != undefined && sCookie){
			gThis.m_jSticky.hide();
		}
		
		if(GCore.sCurrentController == 'mainside'){
			setTimeout(function() {
				for(var i = 0; i < 2; i++) {
					gThis.m_jSticky.animate({opacity: 0.2}, 250, 'linear').animate({opacity: 1}, 250, 'linear');
				}
			}, 1500);
		}
		
		gThis.m_jSticky.find('.task-completed a').click(function(){
			gThis.m_jSticky.fadeOut('slow');
			GCookie(gThis.sStickyId, true, {
				expires: GCore.p_oParams.iCookieLifetime
			});
		});
	};
	
	gThis._Constructor();
	
};

new GPlugin('GSticky', oDefaults, GSticky);




/*
* MESSAGE BAR
*/

var oDefaults = {
	iMessagesToShow: 1,
	oClasses: {
		sMessageClass: 'message',
		sCaptionClass: 'caption',
		sContentClass: 'content',
		sContainerClass: 'layout-container',
		sOptionsClass: 'options',
		sRetractableClass: 'retractable',
		sExpandedClass: 'expanded',
		sTypeWarningClass: 'warning',
		sTypeErrorClass: 'error',
		sTypeMessageClass: 'message',
		sTypePromptClass: 'prompt'
	}
};

var GMessageBar = function() {

	var gThis = this;

	gThis.m_jBox;
	gThis.m_jHoax;
	gThis.m_ojMessages;
	gThis.m_iMargin;

	gThis._Constructor = function() {
		GAlert.sp_dHandler = gThis;
		gThis.m_jBox = $(gThis);
		gThis.m_ojMessages = {};
		gThis.m_jHoax = $('<div/>');
		gThis.m_jHoax.css({
			height: 0
		});
		gThis.m_jBox.before(gThis.m_jHoax);
		gThis.m_jBox.css({
			width: '100%',
			position: 'fixed',
			left: 0,
			top: 0,
			zIndex: 100,
			opacity: 1
		});
		gThis.m_iMargin = gThis.m_jHoax.offset().top;
		$(window).scroll(gThis._UpdateScroll);
		gThis._UpdateScroll();
		gThis._InitExistingMessages();
	};
	
	gThis._UpdateScroll = function(eEvent) {
		if (gThis.m_jHoax.css('display') != 'none') {
			gThis.m_iMargin = gThis.m_jHoax.offset().top;
		}
		if (($(document).scrollTop() < gThis.m_iMargin) || !$(gThis).find('.' + gThis._GetClass('Message')).length) {
			gThis.m_jBox.css({
				position: 'fixed',
				zIndex: 100
			});
			gThis.m_jHoax.css('display', 'none');
		}
		else {
			gThis.m_jBox.css({
				position: 'fixed',
				zIndex: 100
			});
			gThis.m_jHoax.css('display', 'block');
		}
	};
	
	gThis._UpdateHeight = function() {
		gThis.m_jHoax.css('height', gThis.m_jBox.height());
	};

	gThis._InitExistingMessages = function() {
		var jMessages = $(gThis).find('.' + gThis._GetClass('Message'));
		for (var i = 0; i < jMessages.length; i++) {
			var iAlertId = GAlert.Register();
			gThis.m_ojMessages[iAlertId] = jMessages.eq(i);
			gThis._InitMessage(jMessages.eq(i), iAlertId);
			gThis.ShowMessage(jMessages.eq(i));
		}
	};

	gThis._PrepareMessageDOM = function(sTitle, sMessage, oParams) {
		gThis.bAutoFocus = true;
		if (oParams.bAutoFocus == false) {
			gThis.bAutoFocus = false;
		}
		
		if (!oParams.bNoAutoFormatting) {
			sMessage = '<p>' + sMessage + '</p>';
		}
		var jMessage = $('<div class="' + gThis._GetClass('Message') + '"/>');
		var jContainer = $('<div class="' + gThis._GetClass('Container') + '"/>');
		switch (oParams.iType) {
			case GAlert.TYPE_MESSAGE:
				jMessage.addClass(gThis._GetClass('TypeMessage'));
				break;
			case GAlert.TYPE_ERROR:
				jMessage.addClass(gThis._GetClass('TypeError'));
				break;
			case GAlert.TYPE_PROMPT:
				jMessage.addClass(gThis._GetClass('TypePrompt'));
				break;
			default:
				jMessage.addClass(gThis._GetClass('TypeWarning'));
		}
		jContainer.append('<h3>' + sTitle + '</h3>');
		if (!oParams.aoPossibilities || !oParams.aoPossibilities.length) {
			oParams.aoPossibilities = [];
			oParams.aoPossibilities[0] = {
				mLink: GAlert.DestroyThis,
				sCaption: GMessageBar.Language.close_alert
			};
		}
		var jUl = $('<ul class="' + gThis._GetClass('Options') + '"/>');
		for (var i = 0; i < oParams.aoPossibilities.length; i++) {
			var jA = $('<a/>');
			jA.append(oParams.aoPossibilities[i].sCaption);
			GLink(jA, oParams.aoPossibilities[i].mLink);
			if (oParams.aoPossibilities[i].bHidden) {
				jA.css('display', 'none');
			}
			jUl.append($('<li/>').append(jA));
		}
		jContainer.append(jUl);
		jContainer.append('<div class="' + gThis._GetClass('Content') + '">' + sMessage + '</div>');
		jMessage.append(jContainer);
		if (!oParams.bNotRetractable) {
			jMessage.addClass(gThis._GetClass('Retractable'));
			oParams.bAutoExpand = true;
		}
		return jMessage;
	};

	gThis.RetractMessage = function(jMessage) {
		if (!jMessage.hasClass(gThis._GetClass('Retractable'))) {
			return;
		}
		jMessage.get(0).g_bExpanded = false;
		jMessage.removeClass(gThis._GetClass('Expanded'));
		jMessage.stop(true, false).find('.' + gThis._GetClass('Content') + ', .' + gThis._GetClass('Options') + ' li:not(:first-child)').stop(true, false).fadeOut(100, function() {
			jMessage.animate({
				height: jMessage.get(0).g_iRetractedHeight
			}, 150, function() {
				gThis._UpdateHeight();
			});
		});
	};

	gThis._InitMessage = function(jMessage, iAlertId) {
		jMessage.get(0).g_iAlertId = iAlertId;
		jMessage.get(0).g_iExpandedHeight = jMessage.height();
		jMessage.find('.' + gThis._GetClass('Content')).hide();
		jMessage.find('.' + gThis._GetClass('Options') + ' li:not(:first-child)').hide();
		jMessage.get(0).g_iRetractedHeight = jMessage.height();
		jMessage.get(0).g_bExpanded = false;
		jMessage.click(gThis.OnExpandMessage);
		jMessage.find('a').click(new GEventHandler(function(eEvent) {
			eEvent.stopPropagation();
			return true;
		}));
	};

	gThis.OnExpandMessage = new GEventHandler(function(eEvent) {
		if (this.g_bExpanded) {
			gThis.RetractMessage($(this));
		}
		else {
			gThis.ExpandMessage($(this));
		}
		return true;
	});

	gThis.ExpandMessage = function(jMessage) {
		jMessage.get(0).g_bExpanded = true;
		jMessage.addClass(gThis._GetClass('Expanded'));
		jMessage.stop(true, false).animate({
			height: jMessage.get(0).g_iExpandedHeight
		}, 150, function() {
			$(this).find('.' + gThis._GetClass('Content') + ', .' + gThis._GetClass('Options') + ' li:not(:first-child)').stop(true, false).fadeIn(100);
			gThis._UpdateHeight();
			if ($(this).find('input:text').length) {
				$(this).find('input:text:eq(0)').focus().keydown(GEventHandler(function(eEvent) {
					if (eEvent.keyCode == 13) {
						eEvent.preventDefault();
						eEvent.stopImmediatePropagation();
						jMessage.find('.' + gThis._GetClass('Options')).find('a:first').click();
					}
					if (eEvent.keyCode == 27) {
						eEvent.preventDefault();
						eEvent.stopImmediatePropagation();
						jMessage.find('.' + gThis._GetClass('Options')).find('a:last').click();
					}
				}));
			}else{
				if(gThis.bAutoFocus){
					$(this).find('a:first').focus().keydown(GEventHandler(function(eEvent) {
						if (eEvent.keyCode == 13) {
							eEvent.preventDefault();
							eEvent.stopImmediatePropagation();
							jMessage.find('.' + gThis._GetClass('Options')).find('a:first').click();
						}
						if (eEvent.keyCode == 27) {
							eEvent.preventDefault();
							eEvent.stopImmediatePropagation();
							jMessage.find('.' + gThis._GetClass('Options')).find('a:last').click();
						}
					}));
				}
				else{
					$(this).find('a:first').keydown(GEventHandler(function(eEvent) {
						if (eEvent.keyCode == 13) {
							eEvent.preventDefault();
							eEvent.stopImmediatePropagation();
							jMessage.find('.' + gThis._GetClass('Options')).find('a:first').click();
						}
						if (eEvent.keyCode == 27) {
							eEvent.preventDefault();
							eEvent.stopImmediatePropagation();
							jMessage.find('.' + gThis._GetClass('Options')).find('a:last').click();
						}
					}));
				}
				
			}
		});
	};

	gThis.ShowMessage = function(jMessage, bAutoExpand) {
		gThis._UpdateScroll();
		var iTime = 200;
		var nOpacity = .1;
		var jContainer = jMessage.children('.' + gThis._GetClass('Container'));
		jContainer.css('opacity', (document.documentMode == 8) ? '' : 0);
		jMessage.css('height', 0).animate({
			height: jMessage.get(0).g_iRetractedHeight
		}, iTime, function() {
			gThis._UpdateHeight();
			jContainer.animate({
				opacity: (document.documentMode == 8) ? '' : 1
			}, iTime, function() {
				if (bAutoExpand) {
					gThis.ExpandMessage($(this).closest('.' + gThis._GetClass('Message')));
					return;
				}
				gThis.UpdateStack();
				jContainer.animate({
					opacity: (document.documentMode == 8) ? '' : nOpacity
				}, iTime, function() {
					jContainer.animate({
						opacity: (document.documentMode == 8) ? '' : 1
					}, iTime, function() {
						jContainer.animate({
							opacity: (document.documentMode == 8) ? '' : nOpacity
						}, iTime, function() {
							jContainer.animate({
								opacity: (document.documentMode == 8) ? '' : 1
							}, iTime);
						});
					});
				});
			});
		});
	};

	gThis.Destroy = function(mAlert) {
		var iAlertId;
		var jMessage;
		if (!isNaN(mAlert)) {
			iAlertId = mAlert;
			jMessage = gThis.m_ojMessages[iAlertId];
		}
		else {
			if ((mAlert == undefined) || !(mAlert instanceof $)) {
				return;
			}
			jMessage = mAlert.closest('.' + gThis._GetClass('Message'));
			if (!jMessage.length) {
				return;
			}
			iAlertId = jMessage.get(0).g_iAlertId;
		}
		if ((jMessage == undefined) || !(jMessage instanceof $)) {
			return;
		}
		jMessage.stop(true, false).children('.' + gThis._GetClass('Container')).animate({
			opacity: (document.documentMode == 8) ? '' : 0
		}, 100, function() {
			jMessage.animate({
				height: 0
			}, 150, function() {
				jMessage.remove();
				delete gThis.m_ojMessages[iAlertId];
				gThis.UpdateStack();
				gThis._UpdateHeight();
			});
		});
	};
	
	gThis.DestroyAll = function() {
		for (var i in gThis.m_ojMessages) {
			gThis.Destroy(gThis.m_ojMessages[i]);
		}
	};

	gThis.Alert = function(sTitle, sMessage, oParams, iAlertId) {
		oParams = $.extend({}, oParams);
		var jMessage = gThis._PrepareMessageDOM(sTitle, sMessage, oParams);
		gThis.m_jBox.append(jMessage);
		gThis.m_ojMessages[iAlertId] = jMessage;
		gThis._InitMessage(jMessage, iAlertId);
		gThis.ShowMessage(jMessage, (oParams.bAutoExpand == true));
	};
	
	gThis.UpdateStack = function() {
		var jMessages = gThis.m_jBox.find('.' + gThis._GetClass('Message'));
		for (var i = 0; i < jMessages.length; i++) {
			if (jMessages.length - i > gThis.m_oOptions.iMessagesToShow) {
				jMessages.eq(i).css('display', 'none');
			}
			else {
				jMessages.eq(i).css('display', 'block');
			}
		}
	};

	gThis._Constructor();
};

new GPlugin('GMessageBar', oDefaults, GMessageBar);


/*
* OVERLAY
* Adds a customizable overlay that covers everything except the element that it's invoked for.
*/

var oDefaults = {
	oClasses: {
	},
	iZIndex: 1000,
	fClick: GCore.NULL,
	fOpacity: 0.0
};

var GOverlay = function() {
	
	var gThis = this;
	
	gThis.m_jOverlay;
	
	this._Constructor = function() {
		gThis.m_jOverlay = $('<div class="GOverlay"/>').css({
			display: 'block',
			position: 'absolute',
			left: 0,
			top: 0,
			width: $(document).width(),
			height: $(document).height(),
			zIndex: gThis.m_oOptions.iZIndex,
			opacity: gThis.m_oOptions.fOpacity,
			background: '#000'
		});
		$('body').append(gThis.m_jOverlay);
		$(gThis).css({
			zIndex: gThis.m_oOptions.iZIndex + 1
		});
		gThis.m_jOverlay.click(GEventHandler(function(eEvent) {
			var bResult = false;
			if (gThis.m_oOptions.fClick instanceof Function) {
				bResult = gThis.m_oOptions.fClick.apply(this, [eEvent]);
			}
			if (!bResult) {
				gThis.m_jOverlay.remove();
			}
			return false;
		}));
	};
	
	gThis._Constructor();
	
};

GOverlay.RemoveAll = function() {
	$('.GOverlay').remove();
};

new GPlugin('GOverlay', oDefaults, GOverlay);



/*
* QUICK ACCESS
*/

var oDefaults = {
	aoPossibilities: [],
	oClasses: {
		sExpandClass: 'expand',
		sExpandedClass: 'expanded',
		sActiveClass: 'active',
		sListClass: 'list',
		sPossibilitiesListClass: 'possibilities-list',
		sCustomizeClass: 'customize',
		sCustomizationClass: 'customization',
		sButtonClass: 'button',
		sCancelClass: 'cancel',
		sSaveClass: 'save',
		sAccessKeyClass: 'accesskey',
		sCaptionClass: 'caption',
		sHiddenClass: 'aural'
	},
	sCookieName: 'gekosale-panel-quick-access'
};

var GQuickAccess = function() {
	
	var gThis = this;
	
	gThis.m_jMainOption;
	gThis.m_jListTrigger;
	gThis.m_jCustomizationTrigger;
	gThis.m_jList;
	gThis.m_jCustomization;
	gThis.m_jPossibilitiesList;
	gThis.m_aList;
	gThis.m_aPossibilitiesList;
	
	gThis.m_bExpanded;
	gThis.m_bCustomization;
	
	gThis._Constructor = function() {
		gThis.m_bExpanded = false;
		gThis.m_bCustomization = false;
		gThis.m_aList = [];
		gThis.m_aPossibilitiesList = gThis.m_oOptions.aoPossibilities;
		gThis._PrepareDOM();
	};
	
	gThis._PrepareDOM = function() {
		gThis._PrepareList();
		gThis._PreparePossibilitiesList();
		gThis.m_jMainOption = $('<p/>');
		gThis.m_jList.before(gThis.m_jMainOption);
		gThis._UpdateMainOption();
		gThis.m_jListTrigger = $('<span class="' + gThis.m_oOptions.oClasses.sExpandClass + '" title="' + GQuickAccess.Language.show_list + '"/>');
		var fToggleClass = 
		gThis.m_jListTrigger.hover(function() {
			gThis.m_jMainOption.addClass(gThis.m_oOptions.oClasses.sActiveClass);
		}, function() {
			if (!gThis.m_bExpanded) {
				gThis.m_jMainOption.removeClass(gThis.m_oOptions.oClasses.sActiveClass);
			}
		}).mousedown(gThis.OnExpandList);
		$(gThis).mouseleave(gThis.OnRetractList);
		gThis.m_jMainOption.append(gThis.m_jListTrigger);
		gThis._RefreshAccessKeys();
	};
	
	gThis._SaveList = function() {
		GCookie(gThis.m_oOptions.sCookieName, gThis.m_aList.join(','), {
			expires: GCore.p_oParams.iCookieLifetime
		});
	};
	
	gThis._PrepareList = function() {
		var i;
		var sCookie = GCookie(gThis.m_oOptions.sCookieName);
		if ((sCookie == undefined) || (sCookie == '')) {
			gThis.m_aList = [];
			for (i = 0; i < gThis.m_aPossibilitiesList.length; i++) {
				if ((gThis.m_aPossibilitiesList[i] != undefined) && gThis.m_aPossibilitiesList[i].bDefault) {
					gThis.m_aList.push(i);
				}
			}
			gThis._SaveList();
		}
		else {
			gThis.m_aList = sCookie.split(',');
		}
		if (gThis.m_jList == undefined) {
			gThis.m_jList = $(gThis).find('ul:first').wrap('<div class="' + gThis.m_oOptions.oClasses.sListClass + '"/>').closest('div');
			gThis.m_jList.GShadow();
			gThis.m_jCustomizationTrigger = $('<span class="' + gThis.m_oOptions.oClasses.sCustomizeClass + '" title="' + GQuickAccess.Language.customize + '"/>');
			gThis.m_jCustomizationTrigger.click(gThis.OnExpandCustomization);
			gThis.m_jList.append(gThis.m_jCustomizationTrigger);
		}
		var jUl = gThis.m_jList.find('ul:first').empty();
		var aList = [];
		for (i = 0; i < gThis.m_aList.length; i++) {
			var oPossibility = gThis.m_aPossibilitiesList[gThis.m_aList[i]];
			if (oPossibility != undefined) {
				aList.push(gThis.m_aList[i]);
				var jA = $('<a rel="' + gThis.m_aList[i] + '"/>');
				GLink(jA, oPossibility.mLink);
				jA.append('<span class="' + gThis.m_oOptions.oClasses.sCaptionClass + '">' + oPossibility.sCaption + '</span>');
				jUl.append($('<li/>').append(jA));
			}
		}
	};
	
	gThis._PreparePossibilitiesList = function() {
		gThis.m_jPossibilitiesList = $('<div class="' + gThis.m_oOptions.oClasses.sPossibilitiesListClass + '"/>');
		var jUl = $('<ul/>');
		for (var i = 0; i < gThis.m_aPossibilitiesList.length; i++) {
			if (($.inArray(i, gThis.m_aList) == -1) && ($.inArray('' + i, gThis.m_aList) == -1)) {
				var oPossibility = gThis.m_aPossibilitiesList[i];
				var jA = $('<a rel="' + i + '"/>');
				GLink(jA, oPossibility.mLink);
				jA.append('<span class="' + gThis.m_oOptions.oClasses.sCaptionClass + '">' + oPossibility.sCaption + '</span>');
				jUl.append($('<li/>').append(jA));
			}
		}
		gThis.m_jPossibilitiesList.append(jUl);
		var jSaveTrigger = $('<a href="#" class="' + gThis.m_oOptions.oClasses.sButtonClass + '" title="' + GQuickAccess.Language.save_desc + '"><span>' + GQuickAccess.Language.save + '</span></a>');
		jSaveTrigger.click(gThis.OnSave);
		var jCancelTrigger = $('<a href="#" title="' + GQuickAccess.Language.restore_default_desc + '"><span>' + GQuickAccess.Language.restore_default + '</span></a>');
		jCancelTrigger.click(gThis.OnCancel);
		gThis.m_jPossibilitiesList.append($('<p class="' + gThis.m_oOptions.oClasses.sSaveClass + '"/>').append(jSaveTrigger));
		gThis.m_jPossibilitiesList.append($('<p class="' + gThis.m_oOptions.oClasses.sCancelClass + '"/>').append(jCancelTrigger));
		gThis.m_jPossibilitiesList.GShadow();
		$(gThis).append(gThis.m_jPossibilitiesList);
	};
	
	gThis.OnExpandList = new GEventHandler(function(eEvent) {
		gThis.m_bExpanded = true;
		gThis.m_jMainOption.addClass(gThis.m_oOptions.oClasses.sActiveClass);
		gThis.m_jList.css('display', 'none').removeClass(gThis.m_oOptions.oClasses.sHiddenClass).css('left', gThis.m_jMainOption.offset().left - gThis.m_jMainOption.parent().offset().left);
		gThis.m_jList.stop(true, true).slideDown(150);
		return false;
	});
	
	gThis.OnRetractList = new GEventHandler(function(eEvent) {
		if (gThis.m_bCustomization) {
			return;
		}
		gThis.m_bExpanded = false;
		gThis.m_jMainOption.removeClass(gThis.m_oOptions.oClasses.sActiveClass);
		gThis.m_jList.stop(true, true).slideUp(50, function() {
			$(this).css('display', 'block').addClass(gThis.m_oOptions.oClasses.sHiddenClass);
		});
	});
	
	gThis.OnExpandCustomization = new GEventHandler(function(eEvent) {
		if (gThis.m_bCustomization) {
			gThis.OnRetractCustomization({});
			return false;
		}
		gThis.m_bCustomization = true;
		$(gThis).addClass(gThis.m_oOptions.oClasses.sCustomizationClass);
		gThis.m_jList.find('ul:first').sortable({
			placeholder: 'placeholder',
			connectWith: '.' + gThis.m_oOptions.oClasses.sPossibilitiesListClass + ' ul',
			update: gThis.OnChange,
			start: gThis.OnDragStart
		});
		gThis.m_jPossibilitiesList.find('ul:first').sortable({
			placeholder: 'placeholder',
			connectWith: '.' + gThis.m_oOptions.oClasses.sListClass + ' ul',
			update: gThis.OnChange,
			start: gThis.OnDragStart
		});
		gThis.m_jList.find('li a').bind('click', GDoNothing);
		gThis.m_jPossibilitiesList.find('li a').bind('click', GDoNothing);
		gThis.m_jPossibilitiesList.css('left', gThis.m_jMainOption.offset().left - gThis.m_jMainOption.parent().offset().left + gThis.m_jList.width() + 1);
		gThis.m_jPossibilitiesList.slideDown(150);
	});
	
	gThis.OnRetractCustomization = new GEventHandler(function(eEvent) {
		if (!gThis.m_bCustomization) {
			return false;
		}
		gThis.m_bCustomization = false;
		gThis.m_jList.find('ul:first').sortable('destroy');
		gThis.m_jPossibilitiesList.find('ul:first').sortable('destroy');
		gThis.m_jPossibilitiesList.slideUp(50);
		$(gThis).removeClass(gThis.m_oOptions.oClasses.sCustomizationClass);
		gThis.m_jList.find('li a').unbind('click', GDoNothing);
		gThis.m_jPossibilitiesList.find('li a').unbind('click', GDoNothing);
	});
	
	gThis.OnChange = new GEventHandler(function(eEvent, oUi) {
		oUi.item.find('.' + gThis.m_oOptions.oClasses.sAccessKeyClass).remove();
		gThis._UpdateMainOption();
		gThis.m_aList = [];
		var jAs = gThis.m_jList.find('li a');
		for (var i = 0; i < jAs.length; i++) {
			gThis.m_aList.push(jAs.eq(i).attr('rel'));
		}
		gThis._RefreshAccessKeys();
	});
	
	gThis.OnSave = new GEventHandler(function(eEvent) {
		gThis._SaveList();
		gThis.OnRetractCustomization({});
		return false;
	});
	
	gThis.OnCancel = new GEventHandler(function(eEvent) {
		GCookie(gThis.m_oOptions.sCookieName, null);
		gThis.OnRetractCustomization({});
		gThis._PrepareList();
		gThis._PreparePossibilitiesList();
		gThis._UpdateMainOption();
		gThis._RefreshAccessKeys();
		return false;
	});
	
	gThis.OnDragStart = new GEventHandler(function(eEvent, oUi) {
		oUi.helper.find('.' + gThis.m_oOptions.oClasses.sAccessKeyClass).remove();
	});
	
	gThis._UpdateMainOption = function() {
		gThis.m_jMainOption.find('a').remove();
		var jA = gThis.m_jList.find('li:first a:first').clone().attr('accesskey', '');
		jA.find('.' + gThis.m_oOptions.oClasses.sAccessKeyClass).remove();
		gThis.m_jMainOption.prepend(jA);
	};
	
	gThis._RefreshAccessKeys = function() {
		gThis.m_jList.find('a').each(function(i) {
			$(this).find('.' + gThis.m_oOptions.oClasses.sAccessKeyClass).remove();
			if (i < 10) {
				$(this).attr('accesskey', i + 1).prepend('<span class="' + gThis.m_oOptions.oClasses.sAccessKeyClass + '" title="' + GQuickAccess.Language.accesskey + ': ' + (i + 1) + '">' + (i + 1) + '</span>');
			}
		});
	};
	
	gThis._Constructor();
	
};

new GPlugin('GQuickAccess', oDefaults, GQuickAccess);



/*
* SCROLLABLE TABS
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sContainerClass: 'container',
		sControlsClass: 'controls',
		sActiveClass: 'active'
	},
	oImages: {
		sLeft: '_images_panel/icons/buttons/arrow-left-gray.png',
		sRight: '_images_panel/icons/buttons/arrow-right-gray.png'
	}
};

var GScrollableTabs = function() {
	
	var gThis = this;
	
	gThis.m_jUl;
	gThis.m_jContainier;
	gThis.m_jControls;
	gThis.m_jNext;
	gThis.m_jPrevious;
	
	gThis.m_iContainerWidth;
	gThis.m_iUlWidth;
	
	gThis.Constructor = function() {
		gThis.m_jUl = $(gThis).find('ul');
		gThis.m_jUl.wrap('<div class="' + gThis._GetClass('Container') + '"/>');
		gThis.m_jContainier = gThis.m_jUl.parent();
		gThis.m_iContainerWidth = gThis.m_jContainier.width();
		gThis._UpdateWidth();
		if (gThis.m_iUlWidth > gThis.m_iContainerWidth) {
			gThis.m_jControls = $('<p class="' + gThis._GetClass('Controls') + '"/>');
			gThis.m_jNext = $('<a href="#"/>').append('<img src="' + gThis._GetImage('Right') + '" alt="' + GScrollableTabs.Language.next + '" title="' + GScrollableTabs.Language.next + '"/>');
			gThis.m_jPrevious = $('<a href="#"/>').append('<img src="' + gThis._GetImage('Left') + '" alt="' + GScrollableTabs.Language.previous + '" title="' + GScrollableTabs.Language.previous + '"/>');
			gThis.m_jControls.append(gThis.m_jPrevious).append(gThis.m_jNext);
			$(gThis).append(gThis.m_jControls);
		}
		gThis._InitializeEvents();
	};
	
	gThis._UpdateWidth = function() {
		gThis.m_jUl.css('width', 19000);
		var jLis = gThis.m_jUl.children('li');
		var iLisLength = jLis.length;
		var iWidth = 0;
		for (var i = 0; i < iLisLength; i++) {
			iWidth += jLis.eq(i).width() + 4;
		}
		gThis.m_jUl.css('width', iWidth);
		gThis.m_iUlWidth = iWidth;
	};
	
	gThis._InitializeEvents = function() {
		if (gThis.m_jNext != undefined) {
			gThis.m_jNext.click(function() {
				gThis.Right();
				return false;
			});
			gThis.m_jPrevious.click(function() {
				gThis.Left();
				return false;
			});
		}
		gThis.m_jUl.find('a').click(function() {
			GCore.StartWaiting();
			gThis.m_jUl.find('li').removeClass(gThis._GetClass('Active'));
			$(this).closest('li').addClass(gThis._GetClass('Active'));
			gThis._UpdateWidth();
			return true;
		});
	};
	
	gThis.Right = function() {
		
		var left = isNaN(parseInt(gThis.m_jUl.css('left'))) ? 0 : parseInt(gThis.m_jUl.css('left'));
		gThis.m_jUl.stop(true, true).animate({
			left: Math.max(- (gThis.m_iUlWidth - gThis.m_iContainerWidth), left - 250)
		}, 150);
	};
	
	gThis.Left = function() {
		gThis.m_jUl.stop(true, true).animate({
			left: Math.min(0, parseInt(gThis.m_jUl.css('left')) + 250)
		}, 150);
	};
	
	gThis.Constructor();
	
};

new GPlugin('GScrollableTabs', oDefaults, GScrollableTabs);



/*
* SELECT
* Beautiful select-field replacement.
*/

var oDefaults = {
	oClasses: {
		sFauxClass: 'faux'
	}
};

var GSelect = function() {

	var gThis = this;
	
	this._Constructor = function() {
		var dThis = this;
		
		if (this.bBeautifulized) {
			return;
		}
		this.bBeautifulized = true;

		this.Update = function() {
			$(dThis).parent().find('.faux span').text($(dThis).find('option:selected').text()).attr('class', $(dThis).find('option:selected').attr('class') + ' ');
			return true;
		};
		
		$(this).parent().find('select').css('opacity', 0);
		$(this).parent().append('<span class="faux"><span>' + $(this).find('option:selected').text() + '</span></span>');
		$(this).change(this.Update);
		$(this).focus(function() {
			$(this).closest('.field').addClass('focus');
			return true;
		});
		$(this).blur(function() {
			$(this).closest('.field').removeClass('focus');
			return true;
		});
		this.Update();
	};
	
	gThis._Constructor();
	
};

new GPlugin('GSelect', oDefaults, GSelect);


/*
* SHADOW
* Adds a nice shadow to the given element.
*/

var oDefaults = {
	oClasses: {
		sNE: 'shadow-ne',
		sSE: 'shadow-se',
		sSW: 'shadow-sw',
		sS: 'shadow-s',
		sE: 'shadow-e'
	}
};

var GShadow = function() {
	
	var gThis = this;
	
	this._Constructor = function() {
		$(gThis).append('<span class="' + gThis.m_oOptions.oClasses.sNE + '"/>');
		$(gThis).append('<span class="' + gThis.m_oOptions.oClasses.sSE + '"/>');
		$(gThis).append('<span class="' + gThis.m_oOptions.oClasses.sSW + '"/>');
		$(gThis).append('<span class="' + gThis.m_oOptions.oClasses.sS + '"/>');
		$(gThis).append('<span class="' + gThis.m_oOptions.oClasses.sE + '"/>');
	};
	
	gThis._Constructor();
	
};

new GPlugin('GShadow', oDefaults, GShadow);



/*
* TABS
*/

var oDefaults = {
	oClasses: {
		sBlockClass: 'block',
		sButtonClass: 'button',
		sButtonImageRightClass: 'right',
		sNavigationClass: 'navigation',
		sPreviousClass: 'previous',
		sNextClass: 'next',
		sInputWithImageClass: 'with-image',
		sActionsClass: 'actions',
		sTabbedClass: 'tabbed',
		sTabbedHorizontalClass: 'tabbed-horizontal'
	},
	oImages: {
		sArrowLeftGray: '_images_panel/icons/buttons/arrow-left-gray.png',
		sArrowRightGreen: '_images_panel/icons/buttons/arrow-right-green.png',
		sSave: '_images_panel/icons/buttons/check.png'
	},
	iType: 0
};

var GTabs = function() {
	
	var gThis = this;
	
	this._Constructor = function() {
		gThis._PrepareDOM();
	};
	
	gThis._PrepareDOM = function() {
		var jPanels = $(gThis).children('fieldset');
		if (!jPanels.length) {
			return;
		}
		if (gThis.m_oOptions.iType == GTabs.TABS_HORIZONTAL) {
			$(gThis).addClass(gThis._GetClass('TabbedHorizontal'));
		}
		else {
			$(gThis).addClass(gThis._GetClass('Tabbed'));
		}
		var jTabs = $('<ul class="form-navigation"/>');
		var sLastId = '';
		for (var i = 0; i < jPanels.length; i++) {
			var jPanel = jPanels.eq(i);
			var sId = jPanel.attr('id');
			if (!sId.length) {
				sId = 'GTabs-auto-panel-' + GTabs.s_iId++;
			}
			jPanel.attr('id', '');
			var jWrapper = $('<div/>').attr('id', sId).addClass(gThis._GetClass('Block'));
			jPanel.replaceWith(jWrapper);
			jWrapper.append(jPanel);
			jWrapper.GBlock();
			jTabs.append('<li><a href="#' + sId + '">' + jPanel.find('legend span').eq(0).text() + '</a></li>');
			var jNavigation = $('<ul class="' + gThis._GetClass('Navigation') + '"/>');
			if (i > 0) {
				jNavigation.append('<li class="' + gThis._GetClass('Previous') + '"><a tabindex="-1" class="' + gThis._GetClass('Button') + '" href="#previous-tab"><span><img src="' + gThis._GetImage('ArrowLeftGray') + '" alt=""/>' + GForm.Language.previous + '</span></a></li>');
			}
			if (i < jPanels.length - 1) {
				var sNextId = jPanels.eq(i + 1).attr('id');
				if (!sNextId.length) {
					sNextId = 'GTabs-auto-panel-' + GTabs.s_iId;
				}
				jNavigation.append('<li class="' + gThis._GetClass('Next') + '"><a tabindex="-1" class="' + gThis._GetClass('Button') + ' next" href="#next-tab"><span><img class="' + gThis._GetClass('ButtonImageRight') + '" src="' + gThis._GetImage('ArrowRightGreen') + '" alt=""/>' + GForm.Language.next + '</span></a></li>');
			}
			else if ($(gThis).is('form')) {
				jNavigation.append('<li class="' + gThis._GetClass('Next') + '"><span class="' + gThis._GetClass('Button') + '"><span><img class="' + gThis._GetClass('ButtonImageRight') + '" src="' + gThis._GetImage('Save') + '" alt=""/><input type="submit" class="' + gThis._GetClass('InputWithImage') + '" value="' + GForm.Language.save + '"/></span></span></li>');
			}
			jPanel.append(jNavigation);
			sLastId = sId;
		}
		$(gThis).prepend(jTabs).tabs();
		var jAs = $(gThis).find('.navigation a');
		for (i = 0; i < jAs.length; i++) {
			jAs.eq(i).click(function() {
				$(gThis).tabs('select', $(this).attr('href'));
				return false;
			});
		}
		$(gThis).bind('tabsshow', function(eEvent, oUI) {
			$(oUI.panel).children('fieldset').triggerHandler('GFormShow');
		});
		gThis._SolveAllProblems();
		gThis._InitializeTabsEvents();
	};
	
	gThis._InitializeTabsEvents = function() {
		$('a[href="#previous-tab"]').bind('click', GEventHandler(function(eEvent) {
			var jPanel = $(eEvent.currentTarget).closest('.ui-tabs-panel');
			do {
				jPanel = jPanel.prev();
				if (!jPanel.length) {
					return false;
				}
			} while (jPanel.children('fieldset').css('display') == 'none');
			$(gThis).tabs('select', '#' + jPanel.attr('id'));
			eEvent.stopImmediatePropagation();
			return false;
		}));
		
		$('a[href="#next-tab"]').bind('click', GEventHandler(function(eEvent) {
			var jPanel = $(eEvent.currentTarget).closest('.ui-tabs-panel');
			do {
				jPanel = jPanel.next();
				if (!jPanel.length) {
					return false;
				}
			} while (jPanel.children('fieldset').css('display') == 'none');
			$(gThis).tabs('select', '#' + jPanel.attr('id'));
			eEvent.stopImmediatePropagation();
			return false;
		}));
	};
	
	gThis._SolveAllProblems = function() {
		$(gThis).css({
			opacity: 0,
			height: 0,
			overflow: 'hidden'
		}).tabs('add', '#a', '', 1).tabs('select', 1);
		
		setTimeout(function() {
			setTimeout(function() {
				setTimeout(function() {
					$(gThis).tabs('select', 0).tabs('remove', 1).wrap('<div style="clear: both;"/>').css('height', 'auto');
					$(gThis).parent().css('display', 'none').slideDown(350);
					$(gThis).css({
						opacity: 1,
						overflow: 'visible'
					});
				}, 10);
			}, 10);
		}, 10);
		
//		if(window.location.hash.length){
//			setTimeout(function() {
//				alert(window.location.hash);
//				$('.ui-tabs-nav a[href="'+ window.location.hash +'"]').click();
//			}, 100);
//		}
	};
	
	gThis._Constructor();
	
};

GTabs.TABS_VERTICAL = 0;
GTabs.TABS_HORIZONTAL = 1;

GTabs.s_iId = 0;

new GPlugin('GTabs', oDefaults, GTabs);



/*
* TOOLTIP
*/

var oDefaults = {
	bClickToHide: false,
	iPositionX: 0,
	iPositionY: 0,
	iOffsetX: -27,
	iOffsetY: -34,
	jContent: $('<div/>'),
	bAutoShow: false,
	oClasses: {
		sTooltipClass: 'tooltip',
		sNEClass: 'tooltip-ne',
		sSEClass: 'tooltip-se',
		sSWClass: 'tooltip-sw',
		sSClass: 'tooltip-s',
		sEClass: 'tooltip-e',
		sWClass: 'tooltip-w',
		sNWClass: 'tooltip-nw'
	}
};

var GTooltip = function() {
	
	var gThis = this;
	
	gThis.m_iHeight = 0;
	
	this._Constructor = function() {
		GTooltip.s_oTooltips[gThis.m_iId] = gThis;
		gThis._PrepareTooltip();
	};
	
	gThis._PrepareTooltip = function() {
		$(gThis).append(gThis.m_oOptions.jContent).css({
			position: 'absolute',
			left: 0,
			bottom: 0,
			zIndex: 1000,
			display: 'none'
		});
		$(gThis).append('<span class="' + gThis._GetClass('W') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('NW') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('NE') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('SE') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('SW') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('S') + '"/>');
		$(gThis).append('<span class="' + gThis._GetClass('E') + '"/>');
		if (gThis.m_oOptions.bAutoShow) {
			gThis._ShowTooltip(gThis.m_oOptions.iPositionX, gThis.m_oOptions.iPositionY);
		}
	};
	
	gThis._ShowTooltip = function(iX, iY) {
		$('.GTooltip').not($(gThis)).each(function() {
			this._HideTooltip();
		});
		$(gThis).stop(true, true).css({
			left: iX + gThis.m_oOptions.iOffsetX,
			bottom: $('body').height() - (iY + gThis.m_oOptions.iOffsetY - gThis.m_iHeight)
		}).fadeIn(150);
	};
	
	gThis._HideTooltip = function() {
		$(gThis).stop(true, true).fadeOut(50, function() {
			$(this).remove();
		});
	};
	
	gThis._Constructor();
	
};

GTooltip.Create = function(oOptions) {
	var jTooltip = $('<div/>');
	$('body').append(jTooltip);
	jTooltip.GTooltip(oOptions);
	return jTooltip.get(0).m_iId;
};

GTooltip.ShowThumbForThis = GEventHandler(function(eEvent) {
	eEvent.stopImmediatePropagation();
	eEvent.preventDefault();
	var jTooltip = $('<div/>');
	jTooltip.append('<img src="' + $(this).attr('href') + '" alt=""/>');
	this.m_iTooltipId = GTooltip.Create({
		bClickToHide: true,
		iPositionX: eEvent.pageX,
		iPositionY: eEvent.pageY,
		jContent: jTooltip,
		bAutoShow: true
	});
	return false;
});

GTooltip.HideThumbForThis = GEventHandler(function(eEvent) {
	eEvent.stopImmediatePropagation();
	eEvent.preventDefault();
	if (GTooltip.s_oTooltips[this.m_iTooltipId] != undefined) {
		GTooltip.s_oTooltips[this.m_iTooltipId]._HideTooltip();
		delete GTooltip.s_oTooltips[this.m_iTooltipId];
	}
	return false;
});

GTooltip.ShowInfoForThis = GEventHandler(function(eEvent) {
	eEvent.stopImmediatePropagation();
	eEvent.preventDefault();
	var jTooltip = $('<div/>');
	jTooltip.append('<span>' + $(this).attr('title') + '</span>');
	this.m_iTooltipId = GTooltip.Create({
		bClickToHide: true,
		iPositionX: eEvent.pageX,
		iPositionY: eEvent.pageY,
		jContent: jTooltip,
		bAutoShow: true
	});
	return false;
});

GTooltip.HideInfoForThis = GEventHandler(function(eEvent) {
	eEvent.stopImmediatePropagation();
	eEvent.preventDefault();
	if (GTooltip.s_oTooltips[this.m_iTooltipId] != undefined) {
		GTooltip.s_oTooltips[this.m_iTooltipId]._HideTooltip();
		delete GTooltip.s_oTooltips[this.m_iTooltipId];
	}
	return false;
});

GTooltip.s_oTooltips = {};

new GPlugin('GTooltip', oDefaults, GTooltip);



/*
* XAJAX INTERFACE
*/

var GXajaxInterface = function() {
	
	var gThis = this;
	
	gThis.m_iNextRequestId = 0;
	gThis.m_ofErrorHandlers = {};
	
	gThis.Constructor = function() {
		if (window.xajax == undefined) {
			return;
		}
		window.xajax.callback.global.onRequest = gThis.OnRequest;
		window.xajax.callback.global.onComplete = gThis.OnComplete;
	};
	
	gThis.OnRequest = function(eEvent) {
		var xRequest = eEvent.request;
		xRequest['_id'] = gThis.m_iNextRequestId++;
	};
	
	gThis.OnComplete = function(eEvent) {
		var xRequest = eEvent.request;
		if (!xRequest.status || (parseInt(xRequest.status) < 200) || (parseInt(xRequest.status) >= 400) || (!xRequest.responseText.match(/^<\?xml[^>]+\?><xjx>.+<\/xjx>$/))) {
			gThis.OnError(eEvent);
		}
	};
	
	gThis.OnError = function(eEvent) {
		if (gThis.m_ofErrorHandlers[eEvent.request['_id']] != undefined) {
			gThis.m_ofErrorHandlers[eEvent.request['_id']](eEvent);
		}
	};
	
	GCore.OnLoad(gThis.Constructor);
	
};

GXajaxInterface.s_pgInstance;

GXajaxInterface.Initialize = function() {
	GXajaxInterface.s_pgInstance = new GXajaxInterface();
};

GXajaxInterface.Invoke = function(fFunction, amArguments, fOnError) {
	if ((fOnError != undefined) && (fOnError instanceof Function)) {
		GXajaxInterface.s_pgInstance.m_ofErrorHandlers[GXajaxInterface.s_pgInstance.m_iNextRequestId] = fOnError;
	}
	fFunction.apply(window, amArguments);
};


/*
* DEPENDENCIES
*/

var GFormDependency = function(sType, sFieldSource, mCondition, mArgument) {
	
	var gThis = this;
	
	gThis.m_iId;
	gThis.m_sType = sType;
	gThis.m_gForm;
	gThis.m_sFieldSource = sFieldSource;
	gThis.m_sFieldTarget;
	gThis.m_mArgument = mArgument;
	if (mCondition instanceof GFormCondition) {
		gThis.m_gCondition = mCondition;
	}
	else if (mCondition instanceof Function) {
		gThis.m_fSource = mCondition;
	}
	
	gThis.Constructor = function(gForm, sFieldTarget) {
		gThis.m_iId = GFormDependency.s_iNextId++;
		gThis.m_gForm = gForm;
		gThis.m_sFieldTarget = sFieldTarget;
		gThis._InitializeEvents();
	};
	
	gThis._InitializeEvents = function() {
		var gFieldTarget = gThis.m_gForm.GetField(gThis.m_sFieldTarget);
		var fHandler;
		switch (gThis.m_sType) {
			case GFormDependency.HIDE:
				fHandler = gThis.EvaluateHide;
				break;
			case GFormDependency.SHOW:
				fHandler = gThis.EvaluateShow;
				break;
			case GFormDependency.IGNORE:
				fHandler = gThis.EvaluateIgnore;
				break;
			case GFormDependency.SUGGEST:
				fHandler = gThis.EvaluateSuggest;
				break;
			case GFormDependency.INVOKE_CUSTOM_FUNCTION:
				fHandler = gThis.EvaluateInvoke;
				break;
			case GFormDependency.EXCHANGE_OPTIONS:
				fHandler = gThis.EvaluateExchangeOptions;
				break;
			default:
				return;
		}
		var bAlreadyInitialised = false;
		if (!gFieldTarget.m_oInitializedDependencies[gThis.m_iId]) {
			var gField = gThis.m_gForm.GetField(gThis.m_sFieldSource);
			gField.BindChangeHandler(fHandler, {
				gNode: gField
			});
			gField.m_afDependencyTriggers.push(fHandler);
			gFieldTarget.m_oInitializedDependencies[gThis.m_iId] = true;
			bAlreadyInitialised = true;
		}
		if (!bAlreadyInitialised || (gThis.m_sType != GFormDependency.EXCHANGE_OPTIONS)) {
			fHandler.apply(gThis.m_gForm.GetField(gThis.m_sFieldSource).m_jField);
		}
	};
	
	gThis.EvaluateShow = function(eEvent) {
		var gCurrentField, gDependentField;
		if (eEvent == undefined) {
			eEvent = {data: {gNode: $(this).closest('.GFormNode').get(0).gNode}};
		}
		gCurrentField = eEvent.data.gNode;
		gDependentField = gThis._FindFieldInCurrentRepetition(gCurrentField, gThis.m_gForm.GetField(gThis.m_sFieldTarget));
		if ((gCurrentField.m_gParent instanceof GFormFieldsetRepeatable) && (gCurrentField.m_gParent == gDependentField.m_gParent)) {
			gDependentField = gDependentField;
		}
		if (gThis.Evaluate(gCurrentField.GetValue())) {
			gDependentField.Show();
		}
		else {
			gDependentField.Hide();
		}
	};
	
	gThis.EvaluateHide = function(eEvent) {
		var gCurrentField, gDependentField;
		if (eEvent == undefined) {
			eEvent = {data: {gNode: $(this).closest('.GFormNode').get(0).gNode}};
		}
		gCurrentField = eEvent.data.gNode;
		gDependentField = gThis._FindFieldInCurrentRepetition(gCurrentField, gThis.m_gForm.GetField(gThis.m_sFieldTarget));
		if (gThis.Evaluate(gCurrentField.GetValue())) {
			gDependentField.Hide();
		}
		else {
			gDependentField.Show();
		}
	};
	
	gThis._FindFieldInCurrentRepetition = function(gCurrentField, gDependentField) {
		if ((gCurrentField.m_gParent instanceof GFormRepetition) && (gCurrentField.m_gParent.m_gParent == gDependentField.m_gParent.m_gParent)) {
			for (var i in gCurrentField.m_gParent.m_agFields) {
				var gField = gCurrentField.m_gParent.m_agFields[i];
				if (gField.m_oOptions.sName == gThis.m_sFieldTarget) {
					return gField;
				}
			}
		}
		return gDependentField;
	};
	
	gThis.EvaluateIgnore = function(eEvent) {
		var gCurrentField, gDependentField;
		if (eEvent == undefined) {
			eEvent = {data: {gNode: $(this).closest('.GFormNode').get(0).gNode}};
		}
		gCurrentField = eEvent.data.gNode;
		gDependentField = gThis._FindFieldInCurrentRepetition(gCurrentField, gThis.m_gForm.GetField(gThis.m_sFieldTarget));
		if (gThis.Evaluate(gCurrentField.GetValue())) {
			gDependentField.Ignore();
		}
		else {
			gDependentField.Unignore();
		}
	};
	
	gThis.EvaluateInvoke = function(eEvent) {
		if (eEvent == undefined) {
			eEvent = {
				data: {
					gNode: $(this).closest('.GFormNode').get(0).gNode,
					mArgument: gThis.m_mArgument
				}
			};
		}
		gThis.m_fSource({
			sValue: eEvent.data.gNode.GetValue(),
			gForm: gThis.m_gForm,
			sFieldTarget: gThis.m_sFieldTarget,
			mArgument: gThis.m_mArgument
		});
	};
	
	gThis.EvaluateSuggest = function(eEvent) {
		var gCurrentField, gDependentField;
		if (eEvent == undefined) {
			eEvent = {data: {gNode: $(this).closest('.GFormNode').get(0).gNode}};
		}
		gCurrentField = eEvent.data.gNode;
		gDependentField = gThis._FindFieldInCurrentRepetition(gCurrentField, gThis.m_gForm.GetField(gThis.m_sFieldTarget));
		gThis.m_fSource({
			value: eEvent.data.gNode.GetValue()
		}, GCallback(function(oData) {
			gDependentField.SetValue(oData.suggestion);
		}, {
			gForm: gThis.m_gForm,
			sFieldTarget: gThis.m_sFieldTarget
		}));
	};
	
	gThis.EvaluateExchangeOptions = function(eEvent) {
		if (eEvent == undefined) {
			eEvent = {
				data: {
					gNode: $(this).closest('.GFormNode').get(0).gNode,
					mArgument: gThis.m_mArgument
				}
			};
		}
		gThis.m_fSource({
			value: eEvent.data.gNode.GetValue()
		}, GCallback(function(oData) {
			oData.gForm.GetField(oData.sFieldTarget).ExchangeOptions(oData.options);
		}, {
			gForm: gThis.m_gForm,
			sFieldTarget: gThis.m_sFieldTarget
		}));
	};
	
	gThis.Evaluate = function(mValue) {
		return gThis.m_gCondition.Evaluate(mValue);
	};
	
};

GFormDependency.HIDE = 'hide';
GFormDependency.SHOW = 'show';
GFormDependency.IGNORE = 'ignore';
GFormDependency.SUGGEST = 'suggest';
GFormDependency.INVOKE_CUSTOM_FUNCTION = 'invoke';
GFormDependency.EXCHANGE_OPTIONS = 'exchangeOptions';

GFormDependency.s_iNextId = 0;


/*
* NODE
* Abstract class with base functionality used by all form nodes.
*/

var oDefaults = {
	oClasses: {
		
	}
};

var GFormNode = function(oOptions) {
	
	var gThis = this;
	
	gThis.m_jNode;
	gThis.m_gParent;
	gThis.m_gForm;
	gThis.m_sNamePrefix;
	gThis.m_bRepeatable;
	gThis.m_bIgnore = false;
	gThis.m_oInitializedDependencies = {};
	
	gThis._Constructor = function() {};
	
	gThis.Render = function() {
		gThis.m_bRepeatable = false;
		if ((gThis.m_oOptions.oRepeat != undefined) && (gThis.m_oOptions.oRepeat.iMax != undefined) && (gThis.m_oOptions.oRepeat.iMax > 1)) {
			gThis.m_bRepeatable = true;
		}
		gThis._PrepareNode();
		gThis.m_jNode.get(0).gNode = gThis;
		gThis.m_jNode.addClass('GFormNode');
		return gThis.m_jNode;
	};
	
	gThis._GetClass = function(sClassName) {
		var sClass = gThis.m_oOptions.oClasses['s' + sClassName + 'Class'];
		if (sClass == undefined) {
			return '';
		}
		else {
			return sClass;
		}
	};
	
	gThis._GetImage = function(sImageName) {
		var sImage = gThis.m_oOptions.oImages['s' + sImageName];
		if (sImage == undefined) {
			return '';
		}
		else {
			return GCore.DESIGN_PATH + sImage;
		}
	};
	
	gThis.GetName = function(sId) {
		if ((gThis.m_sNamePrefix == undefined) || (gThis.m_sNamePrefix == '')) {
			return (sId != undefined) ? gThis.m_oOptions.sName + '[' + sId + ']' : gThis.m_oOptions.sName;
		}
		var sName = gThis.m_sNamePrefix + '[' + gThis.m_oOptions.sName + ']';
		if (sId != undefined) {
			sName += '[' + sId + ']';
		}
		return sName;
	};
	
	gThis.GetId = function(sId) {
		var sName = gThis.GetName().replace(/[\[\]]+/g, '__').replace(/\_\_$/, '');
		if (sId != undefined) {
			sName += '__' + sId;
		}
		return sName;
	};
	
	gThis.Populate = function(mData) {};
	gThis.PopulateErrors = function(mData) {};
	gThis.Validate = function(bNoRequests, iRepetition) { return true; };
	
	gThis.OnInit = function() {
		gThis._Initialize();
		gThis._InitializeEvents();
		gThis._InitializeDependencies();
		gThis._InitializeRules();
	};
	
	gThis._InitializeDependencies = function() {
		if (gThis.m_oOptions.agDependencies != undefined) {
			for (var i in gThis.m_oOptions.agDependencies) {
				gThis.m_oOptions.agDependencies[i].Constructor(gThis.m_gForm, gThis.m_oOptions.sName);
			}
		}
	};
	
	gThis._InitializeRules = function() {
		if (!gThis.m_jNode) {
			return;
		}
		if (gThis.m_oOptions.aoRules != undefined) {
			for (var i = 0; i < gThis.m_oOptions.aoRules.length; i++) {
				if (gThis.m_oOptions.aoRules[i].sType == 'required') {
					gThis.m_jNode.addClass('required');
				}
			}
		}
	};
	
	gThis.OnReset = function() {};
	gThis.OnShow = function() {};
	gThis.OnHide = function() {};
	gThis.OnRemove = function() {};
	gThis.Reset = function() {};
	gThis.Focus = function() { return false; };
	
	gThis.BindChangeHandler = function(fHandler, oData) {
		return gThis.m_jNode.bind('change', oData, fHandler);
	};
	
	gThis._PrepareNode = function() {};
	gThis._Initialize = function() {};
	gThis._InitializeEvents = function() {};
	
	gThis.Ignore = function() {
		gThis.m_bIgnore = true;
	};
	
	gThis.Unignore = function() {
		gThis.m_bIgnore = false;
	};
	
	gThis.Show = function() {
		gThis.Unignore();
		gThis.m_jNode.slideDown(200);
		if (gThis.m_oOptions.sName != undefined) {
			$(gThis.m_gForm).find('.form-navigation li:has(a[href="#' + gThis.m_oOptions.sName + '"])').css('display', 'block');
		}
	};
	
	gThis.Hide = function() {
		gThis.Ignore();
		gThis.m_jNode.slideUp(150);
		if (gThis.m_oOptions.sName != undefined) {
			$(gThis.m_gForm).find('.form-navigation li:has(a[href="#' + gThis.m_oOptions.sName + '"])').css('display', 'none');
		}
	};
	
};


/*
* RULE
* Abstract class representing a form validation rule.
*/

var GFormRule = function() {};

GFormRule.Check = function(oRule, gField, bNoRequest, iRepetition) {
	if (bNoRequest == undefined) {
		bNoRequest = false;
	}
	var mValue = gField.GetValue(iRepetition);
	switch (oRule.sType) {
		
		case 'required':
			if(gField._GetField(iRepetition).find('option:selected').val() != undefined)
			{
				if(gField._GetField(iRepetition).find('option:selected').val() == 0){
					return gField.ValidationResult(false, oRule.sErrorMessage, iRepetition);
				}
			}
			else if (mValue == '') {
				return gField.ValidationResult(false, oRule.sErrorMessage, iRepetition);
			}
			return gField.ValidationResult(true, oRule.sErrorMessage, iRepetition);
		
		case 'format':
			if(mValue != ''){
				var rRE = new RegExp(oRule.sFormat.substr(1, oRule.sFormat.length - 2));
				return gField.ValidationResult(rRE.test(mValue), oRule.sErrorMessage, iRepetition);
			}
			return gField.ValidationResult(true);
			
		case 'email':
			if(mValue != ''){
				return gField.ValidationResult(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)$/i.test(mValue), oRule.sErrorMessage, iRepetition);
			}
			return gField.ValidationResult(true);
			
		case 'vat':
			var sValue = mValue.replace(/-/, '');
			sValue = sValue.replace(/-/, '');
			sValue = sValue.replace(/-/, '');

			if (sValue.length != 10){
	    	    return gField.ValidationResult(false, oRule.sErrorMessage, iRepetition);
       		}
 			
       		aoSteps = new Array(6, 5, 7, 2, 3, 4, 5, 6, 7);
	   	 	iSum=0;
 
	   		for (i = 0; i < 9; i++){
 
				iSum += aoSteps[i] * sValue.charAt(i);
 
		    }
 
		    iModulo = iSum % 11;
		    
    		if(iModulo == 10){
		   		iControl = 0;
			}else{
				iControl = iModulo;
			}
 
			if (iControl == sValue.charAt(9)){
			    return gField.ValidationResult(true, oRule.sErrorMessage, iRepetition);	
			}
 
		case 'compare':
			var jFieldToCompare = $('[id$="' + oRule.sFieldToCompare + '"]');
			if (!jFieldToCompare.length) {
				return gField.ValidationResult(false, oRule.sErrorMessage, iRepetition);
			}
			return gField.ValidationResult(mValue == jFieldToCompare.closest('.GFormNode').get(0).gNode.GetValue(), oRule.sErrorMessage, iRepetition);
		
		case 'dateto':
			var jFieldToCompare = $('[id$="' + oRule.sFieldToCompare + '"]');
			if (!jFieldToCompare.length) {
				return gField.ValidationResult(false, oRule.sErrorMessage, iRepetition);
			}
			return gField.ValidationResult(mValue >= jFieldToCompare.closest('.GFormNode').get(0).gNode.GetValue(), oRule.sErrorMessage, iRepetition);
		
		case 'unique':
			if (!bNoRequest) {
				gField.StartWaiting();
				oRule.fCheckFunction({
					value: mValue
				}, GCallback(GFormRule.ValidationResponse, {
					gField: gField,
					sErrorMessage: oRule.sErrorMessage,
					iRepetition: iRepetition
				}));
			}
			return gField.ValidationResult(true);
		
		case 'languageunique':
			if (!bNoRequest) {
				gField.StartWaiting();
				oRule.fCheckFunction({
					value: mValue,
					language: gField.m_gParent.m_oOptions.sName
				}, GCallback(GFormRule.ValidationResponse, {
					gField: gField,
					sErrorMessage: oRule.sErrorMessage,
					iRepetition: iRepetition
				}));
			}
			return gField.ValidationResult(true);
			
		case 'custom':
			var oParams = {};
			for (var sI in oRule.oParams) {
				if (sI.substr(0, 7) == '_field_') {
					oParams[sI.substr(7)] = gField.m_gForm.GetField(oRule.oParams[sI]).GetValue();
				}
				else {
					oParams[sI] = oRule.oParams[sI];
				}
			}
			if (!bNoRequest) {
				gField.StartWaiting();
				oRule.fCheckFunction({
					value: mValue,
					params: oParams
				}, GCallback(GFormRule.ValidationResponse, {
					gField: gField,
					sErrorMessage: oRule.sErrorMessage,
					iRepetition: iRepetition
				}));
			}
			return gField.ValidationResult(true);
		
	}
	return gField.ValidationResult(false);
};

GFormRule.ValidationResponse = function(oData) {
	oData.gField.StopWaiting();
	oData.gField.ValidationResult(oData.unique, oData.sErrorMessage, oData.iRepetition);
};

/*
* CONTAINER LANGUAGE
*/

var oDefaults = {
	sName: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sAddButtonClass: 'add-repetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	aoFields: [],
	aoLanguages: [],
	agFields: []
};

var GFormContainerLanguage = GCore.ExtendClass(GFormNode, function() {

	var gThis = this;

	gThis.m_oContainerRepetitions = {};
	gThis.m_iRepetitionIndex = 0;
	gThis.m_agFields = [];
	gThis.m_iChildIndex = 0;

	gThis._Constructor = function() {
		gThis.m_bRepeatable = true;
	};
	
	gThis._ConstructChildren = function() {
		
		for (var i = 0; i < gThis.m_oOptions.aoFields.length; i++) {
			var oField = gThis.m_oOptions.aoFields[i];
			var gChild = new oField.fType(oField);
			if (gChild._Constructor != undefined) {
				gChild._Constructor();
			}
			gThis.m_oOptions.agFields.push(gChild);
			gThis.m_agFields[gThis.m_iChildIndex++] = gChild;
		}
	};
	
	gThis.RenderChildren = function() {
		var jChildrenCollection = $('<div/>');
		for (var i = 0; i < gThis.m_oOptions.agFields.length; i++) {
			gThis._PrepareChild(gThis.m_oOptions.agFields[i]);
			jChildrenCollection.append(gThis.m_oOptions.agFields[i].Render());
		}
		return jChildrenCollection.children();
	};

	gThis._PrepareChild = function(gChild) {
		gChild.m_gForm = gThis.m_gForm;
		gChild.m_gParent = gThis;
		if (gChild.m_oOptions.sName != undefined) {
			gThis.m_gForm.m_ogFields[gChild.m_oOptions.sName] = gChild;
		}
		if (gThis.m_gParent == GCore.NULL) {
			gChild.m_sNamePrefix = '';
		}
		if (gThis.m_gForm == gThis.m_gParent) {
			gChild.m_sNamePrefix = gThis.m_oOptions.sName;
		}
		else {
			gChild.m_sNamePrefix = gThis.GetName();
		}
	};

	gThis.OnInit = function() {
		gThis._Initialize();
		gThis._InitializeEvents();
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnInit();
		}
		gThis._InitializeDependencies();
		gThis._InitializeRules();
	};

	gThis.OnShow = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnShow();
		}
	};

	gThis.OnRemove = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnRemove();
		}
	};

	gThis.OnHide = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnHide();
		}
	};

	gThis.OnReset = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnReset();
		}
	};

	gThis.Reset = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].Reset();
		}
	};
	
	gThis.Validate = function(bNoRequests) {
		var bResult = true;
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			if (!gThis.m_agFields[i].Validate(bNoRequests)) {
				bResult = false;
			}
		}
		return bResult;
	};
	
	gThis.AddRepetition = function(i) {
		
		var oOptions = GCore.Duplicate(gThis.m_oOptions, true);
		oOptions.sName = i;
		oOptions.agFields = [];
		oOptions.oRepeat = {};
		var gRepetition = new GFormRepetitionLanguage(oOptions);
		gRepetition._Constructor();
		gThis._PrepareChild(gRepetition);
		gThis.m_jNode.append(gRepetition.Render());
		gRepetition.OnInit();
		gThis.m_oContainerRepetitions[i] = gRepetition;
		gThis.m_agFields[gThis.m_iChildIndex++] = gRepetition;
		gRepetition.OnShow();
	};

	gThis.RemoveRepetition = function(i) {
		gRepetition = gThis.m_oContainerRepetitions[i];
		if (gRepetition == undefined) {
			return;
		}
		gRepetition.OnRemove();
		if (gRepetition.m_jNode != undefined) {
			gRepetition.m_jNode.remove();
		}
		for (var j in gThis.m_agFields) {
			if (gThis.m_oContainerRepetitions[i] == gThis.m_agFields[j]) {
				delete gThis.m_agFields[j];
			}
		}
		delete gThis.m_oContainerRepetitions[i];
		
	};
	
	gThis.Populate = function(mData) {
		
		
		$.each(gThis.m_oOptions.aoLanguages,function(l,language){
			gThis.RemoveRepetition(language.sValue);
		});
		
		if (mData == undefined) {
			return;
		}
		var i;
		
		if (!GCore.ObjectLength(mData)) {
			return;
		}
		
		$.each(gThis.m_oOptions.aoLanguages,function(l,language){
			gThis.AddRepetition(language.sValue);
		});
		
		for (var iFieldIndex in gThis.m_oOptions.aoFields) {
			var sFieldName = gThis.m_oOptions.aoFields[iFieldIndex].sName;

			for (var sRepetitionId in mData[sFieldName]) {
				if (gThis.m_oContainerRepetitions[sRepetitionId] == undefined) {
//					gThis.AddRepetition(sRepetitionId);
				}
				var oValueObject = {};
				oValueObject[sFieldName] = mData[sFieldName][sRepetitionId];
				gThis.m_oContainerRepetitions[sRepetitionId].Populate(oValueObject);
			}
		}                                    
	};

	gThis.PopulateErrors = function(mData) {
		if (mData == undefined) {
			return;
		}
		var i;

		for (i in mData) {
			if (gThis.m_oContainerRepetitions[i] != undefined) {
				gThis.m_oContainerRepetitions[i].PopulateErrors(mData[i]);
			}
		}                                    
		
	};
	
	gThis.Focus = function() {

		var aKeys = [];
		for (i in gThis.m_oContainerRepetitions) {
			aKeys.push(i);
		}
		aKeys.sort();
		for (i = 0; i < aKeys.length; i++) {
			var j = aKeys[i];
			if (gThis.m_oContainerRepetitions[j].Focus()) {
				return true;
			}
		}
		
		return false;
	};
	
	gThis._Initialize = function() {};

}, oDefaults);

/*
* CONTAINER
*/

var oDefaults = {
	sName: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sAddButtonClass: 'add-repetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	aoFields: [],
	agFields: []
};

var GFormContainer = GCore.ExtendClass(GFormNode, function() {

	var gThis = this;

	gThis.m_oContainerRepetitions = {};
	gThis.m_iRepetitionIndex = 0;
	gThis.m_agFields = [];
	gThis.m_iChildIndex = 0;

	gThis._Constructor = function() {
		gThis.m_bRepeatable = false;
		if ((gThis.m_oOptions.oRepeat != undefined) && (gThis.m_oOptions.oRepeat.iMax != undefined) && (gThis.m_oOptions.oRepeat.iMax > 1)) {
			gThis.m_bRepeatable = true;
		}
		if (!gThis.m_bRepeatable) {
			gThis._ConstructChildren();
		}
	};
	
	gThis._ConstructChildren = function() {
		for (var i = 0; i < gThis.m_oOptions.aoFields.length; i++) {
			var oField = gThis.m_oOptions.aoFields[i];
			var gChild = new oField.fType(oField);
			if (gChild._Constructor != undefined) {
				gChild._Constructor();
			}
			gThis.m_oOptions.agFields.push(gChild);
			gThis.m_agFields[gThis.m_iChildIndex++] = gChild;
		}
	};
	
	gThis.RenderChildren = function() {
		var jChildrenCollection = $('<div/>');
		for (var i = 0; i < gThis.m_oOptions.agFields.length; i++) {
			gThis._PrepareChild(gThis.m_oOptions.agFields[i]);
			jChildrenCollection.append(gThis.m_oOptions.agFields[i].Render());
		}
		return jChildrenCollection.children();
	};

	gThis._PrepareChild = function(gChild) {
		gChild.m_gForm = gThis.m_gForm;
		gChild.m_gParent = gThis;
		if (gChild.m_oOptions.sName != undefined) {
			gThis.m_gForm.m_ogFields[gChild.m_oOptions.sName] = gChild;
		}
		if (gThis.m_gParent == GCore.NULL) {
			gChild.m_sNamePrefix = '';
		}
		if (gThis.m_gForm == gThis.m_gParent) {
			gChild.m_sNamePrefix = gThis.m_oOptions.sName;
		}
		else {
			gChild.m_sNamePrefix = gThis.GetName();
		}
	};

	gThis.OnInit = function() {
		gThis._Initialize();
		gThis._InitializeEvents();
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnInit();
		}
		gThis._InitializeDependencies();
		gThis._InitializeRules();
	};

	gThis.OnShow = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnShow();
		}
	};

	gThis.OnRemove = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnRemove();
		}
	};

	gThis.OnHide = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnHide();
		}
	};

	gThis.OnReset = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].OnReset();
		}
		
	};

	gThis.Reset = function() {
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			gThis.m_agFields[i].Reset();
		}
	};
	
	gThis.Validate = function(bNoRequests) {
		var bResult = true;
		for (var i = 0; i < gThis.m_iChildIndex; i++) {
			if (gThis.m_agFields[i] == undefined) {
				continue;
			}
			if (!gThis.m_agFields[i].Validate(bNoRequests)) {
				bResult = false;
			}
		}
		return bResult;
	};
	
	gThis.AddRepetition = function(i) {
		if (i == undefined) {
			i = 'new-' + gThis.m_iRepetitionIndex++;
		}
		
		var oOptions = GCore.Duplicate(gThis.m_oOptions, true);
		oOptions.sName = i;
		oOptions.agFields = [];
		oOptions.oRepeat = {};
		var gRepetition = new GFormRepetition(oOptions);
		gRepetition._Constructor();
		gThis._PrepareChild(gRepetition);
		gThis.m_jNode.append(gRepetition.Render());
		gRepetition.OnInit();
		gRepetition.m_jNode.find('.' + gThis._GetClass('DeleteButton')).attr('rel', i).click(function() {
			gThis.RemoveRepetition($(this).attr('rel'));
			return false;
		});
		gRepetition.m_jNode.find('input').focus();
		gThis.m_oContainerRepetitions[i] = gRepetition;
		gThis.m_agFields[gThis.m_iChildIndex++] = gRepetition;
		if (GCore.ObjectLength(gThis.m_oContainerRepetitions) <= gThis.m_oOptions.oRepeat.iMin) {
			gThis.m_jNode.find('.' + gThis._GetClass('Repetition') + ' > .' + gThis._GetClass('DeleteButton')).css('display', 'none');
		}
		else {
			gThis.m_jNode.find('.' + gThis._GetClass('Repetition') + ' > .' + gThis._GetClass('DeleteButton')).css('display', 'block');
		}
		gRepetition.OnShow();
	};
	
	gThis.RemoveRepetition = function(i) {
		gRepetition = gThis.m_oContainerRepetitions[i];
		if (gRepetition == undefined) {
			return;
		}
		gRepetition.OnRemove();
		if (gRepetition.m_jNode != undefined) {
			gRepetition.m_jNode.remove();
		}
		for (var j in gThis.m_agFields) {
			if (gThis.m_oContainerRepetitions[i] == gThis.m_agFields[j]) {
				delete gThis.m_agFields[j];
			}
		}
		delete gThis.m_oContainerRepetitions[i];
		if (GCore.ObjectLength(gThis.m_oContainerRepetitions) <= gThis.m_oOptions.oRepeat.iMin) {
			gThis.m_jNode.find('.' + gThis._GetClass('Repetition') + ' > .' + gThis._GetClass('DeleteButton')).css('display', 'none');
		}
		if (GCore.ObjectLength(gThis.m_oContainerRepetitions) < gThis.m_oOptions.oRepeat.iMax) {
			gThis.m_jAdd.css('display', 'block');
		}
	};

	gThis.Populate = function(mData) {
		if (gThis.m_bRepeatable) {
			gThis.AddRepetition();
		}
		if (mData == undefined) {
			return;
		}
		var i;
		if (gThis.m_bRepeatable) {
			if (!GCore.ObjectLength(mData)) {
				return;
			}
			var aKeys = [];
			for (i in mData) {
				aKeys.push(i);
			}
//			aKeys.sort();
			if (!gThis.m_gForm.m_bPopulatedWithDefaults) {
				for (var k in gThis.m_oContainerRepetitions) {
					gThis.RemoveRepetition(k);
				}
				gThis.m_jNode.find('.' + gThis._GetClass('Repetition')).remove();
			}
			for (i = 0; i < aKeys.length; i++) {
				var j = aKeys[i];
				if (gThis.m_oContainerRepetitions[j] == undefined) {
					gThis.AddRepetition(j);
				}
				gThis.m_oContainerRepetitions[j].Populate(mData[j]);
			}                                    
		}
		else {
			for (i = 0; i < gThis.m_oOptions.agFields.length; i++) {
				if ((gThis.m_oOptions.agFields[i].m_oOptions.sName != undefined)) {
					gThis.m_oOptions.agFields[i].Populate(mData[gThis.m_oOptions.agFields[i].m_oOptions.sName]);
				}
			}
		}
	};

	gThis.PopulateErrors = function(mData) {
		if (mData == undefined) {
			return;
		}
		var i;
		if (gThis.m_bRepeatable) {
			for (i in mData) {
				if (gThis.m_oContainerRepetitions[i] != undefined) {
					gThis.m_oContainerRepetitions[i].PopulateErrors(mData[i]);
				}
			}                                    
		}
		else {
			for (i = 0; i < gThis.m_oOptions.agFields.length; i++) {
				if ((gThis.m_oOptions.agFields[i].m_oOptions.sName != undefined)) {
					gThis.m_oOptions.agFields[i].PopulateErrors(mData[gThis.m_oOptions.agFields[i].m_oOptions.sName]);
				}
			}
		}
	};
	
	gThis.Focus = function() {
		if (gThis.m_bRepeatable) {
			var aKeys = [];
			for (i in gThis.m_oContainerRepetitions) {
				aKeys.push(i);
			}
			aKeys.sort();
			for (i = 0; i < aKeys.length; i++) {
				var j = aKeys[i];
				if (gThis.m_oContainerRepetitions[j].Focus()) {
					return true;
				}
			}
		}
		else {
			for (var i = 0; i < gThis.m_oOptions.agFields.length; i++) {
				if (gThis.m_oOptions.agFields[i].Focus()) {
					return true;
				}
			}
		}
		return false;
	};
	
	gThis._Initialize = function() {
		if (gThis.m_bRepeatable && !gThis.m_agFields.length) {
			for (var i = 0; i < gThis.m_oOptions.oRepeat.iMin; i++) {
				gThis.AddRepetition();
			}
		}
	};

}, oDefaults);


/*
* FIELD
* Abstract class with base functionality used by all field types.
*/

var oDefaults = {
	oClasses: {
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	aoRules: [],
	sComment: ''
};

var GFormField = GCore.ExtendClass(GFormNode, function() {
	
	var gThis = this;
	
	gThis.m_jField = $('empty');
	gThis.m_oAlerts = {};
	gThis.m_oRepetitionLookup = {};
	gThis.m_sRepetitionCounter = 0;
	gThis.m_oRepetitions = {};
	gThis.m_bAlreadyPopulated = false;
	gThis.m_bSkipValidation = false;
	gThis.m_afDependencyTriggers = [];
	
	gThis.Populate = function(mData) {
		var i;
		if (gThis.m_bRepeatable) {
			if (GCore.ObjectLength(mData) && !gThis.m_bAlreadyPopulated) {
				gThis.m_bAlreadyPopulated = true;
				for (i in gThis.m_oRepetitions) {
					gThis.RemoveRepetition(i);
				}
			}
			for (i in mData) {
				if (gThis.m_oRepetitions[i] == undefined) {
					gThis.AddRepetition(i);
				}
				gThis.SetValue(mData[i], i);
			}
		}
		else {
			gThis.SetValue(mData);
		}
	};
	
	gThis.AddRepetition = function(sRepetition) {
		if (sRepetition == undefined) {
			sRepetition = 'new-' + gThis.m_sRepetitionCounter++;
		}
		var jRepetition = gThis._AddField(sRepetition);
		gThis.m_jNode.append(jRepetition);
		gThis.m_oRepetitions[sRepetition] = jRepetition;
		jRepetition.get(0).sRepetition = sRepetition;
		gThis._GetField(sRepetition).get(0).sRepetition = sRepetition;
		gThis._InitializeEvents(sRepetition);
		gThis._UpdateRepetitionButtons();
		gThis.OnInitRepetition(sRepetition);
		return sRepetition;
	};
	
	gThis.OnInitRepetition = function(sRepetition) {};
	
	gThis.RemoveRepetition = function(sRepetition) {
		gThis._RemoveAlerts(sRepetition);
		gThis.m_oRepetitions[sRepetition].addClass('to-remove');
		gThis.m_jField = gThis.m_jField.not('.to-remove *');
		gThis.m_oRepetitions[sRepetition].remove();
		delete gThis.m_oRepetitions[sRepetition];
		gThis._UpdateRepetitionButtons();
	};
	
	gThis.PopulateErrors = function(mData) {
		if ((mData == undefined) || (mData == '')) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mData) {
				if (gThis.m_oRepetitions[i] == undefined) {
					gThis.AddRepetition(i);
				}
				gThis.SetError(mData[i], i);
			}
		}
		else {
			gThis.SetError(mData);
		}
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis._GetField(sRepetition).val();
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis._GetField(sRepetition).val(mValue);
	};
	
	gThis.Validate = function(bNoRequests, sRepetition) {
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			for (var j in gThis.m_oRepetitions) {
				gThis._RemoveAlerts(j);
			}
			if (gThis.m_bIgnore || (gThis.m_oOptions.aoRules == undefined)) {
				return true;
			}
			var bResult = true;
			for (j in gThis.m_oRepetitions) {
				for (var i = 0; i < gThis.m_oOptions.aoRules.length; i++) {
					if (!GFormRule.Check(gThis.m_oOptions.aoRules[i], gThis, bNoRequests, j)) {
						bResult = false;
					}
				}
			}
			return bResult;
		}
		else {
			gThis._RemoveAlerts(sRepetition);
			if (gThis.m_bIgnore || (gThis.m_oOptions.aoRules == undefined)) {
				return true;
			}
			for (var i = 0; i < gThis.m_oOptions.aoRules.length; i++) {
				if (!GFormRule.Check(gThis.m_oOptions.aoRules[i], gThis, bNoRequests, sRepetition)) {
					return false;
				}
			}
		}
		return true;
	};
	
	gThis.OnRemove = function() {
		gThis._RemoveAlerts();
	};
	
	gThis._RemoveAlerts = function(sRepetition) {
		var i;
		if (sRepetition == undefined) {
			if (gThis.m_bRepeatable) {
				for (var j in gThis.m_oRepetitions) {
					for (i in gThis.m_oAlerts[j]) {
						GAlert.Destroy(gThis.m_oAlerts[j][i]);
					}
				}
			}
			else {
				for (i in gThis.m_oAlerts[0]) {
					GAlert.Destroy(gThis.m_oAlerts[0][i]);
				}
			}
		}
		else {
			if (gThis.m_oAlerts[sRepetition] != undefined) {
				for (i in gThis.m_oAlerts[sRepetition]) {
					GAlert.Destroy(gThis.m_oAlerts[sRepetition][i]);
				}
			}
		}
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().find('.required').html('');
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().removeClass(gThis._GetClass('Invalid'));
		
	};
	
	gThis.StartWaiting = function(sRepetition) {
		var jWaiting = $('<span class="' + gThis._GetClass('Waiting') + '"/>');
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().append(jWaiting);
		jWaiting.css('display', 'none').fadeIn(250);
	};
	
	gThis._GetField = function(sRepetition) {
		if (!gThis.m_bRepeatable || (sRepetition == undefined)) {
			return gThis.m_jField;
		}
		if (gThis.m_oRepetitions[sRepetition] == undefined) {
			return $();
		}
		gThis.m_oRepetitions[sRepetition].addClass('to-retrieve');
		var jField = gThis.m_jField.filter('.to-retrieve *');
		gThis.m_oRepetitions[sRepetition].removeClass('to-retrieve');
		return jField;
	};
	
	gThis.StopWaiting = function(sRepetition) {
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().find('.' + gThis._GetClass('Waiting')).fadeOut(250, function() {$(this).remove();});
	};
	
	gThis.ValidationResult = function(bResult, sMessage, sRepetition) {
		if (!bResult) {
			gThis.SetError(sMessage, sRepetition);
		}
		return bResult;
	};
	
	gThis.SetError = function(sMessage, sRepetition) {
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().addClass(gThis._GetClass('Invalid'));
		gThis._GetField(sRepetition).closest('.' + gThis._GetClass('FieldSpan')).parent().find('.required').html(sMessage).fadeOut(150, function() {
			$(this).fadeIn(150, function() {
				$(this).fadeOut(150, function() {
					$(this).fadeIn(150, function() {  });
				});
			});
		});
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).unbind('change', gThis.OnValidate).change(gThis.OnValidate);
		});
	};
	
	gThis._ActivateFocusedTab = function(eEvent) {
		if ($(eEvent.currentTarget).closest('.ui-tabs-panel').length && $(eEvent.currentTarget).closest('.ui-tabs-panel').hasClass('ui-tabs-hide')) {
			gThis.m_gForm.m_bDontFocus = true;
			$(eEvent.currentTarget).closest('.ui-tabs').tabs('select', $(eEvent.currentTarget).closest('.ui-tabs-panel').attr('id'));
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {};
	
	gThis.OnValidate = GEventHandler(function(eEvent) {
		if (!$(this).closest('.GFormNode').get(0).gNode.m_bSkipValidation) {
			gThis.Validate(false, this.sRepetition);
		}
	});
	
	gThis.Focus = function(sRepetition) {
		gThis._GetField(sRepetition).eq(0).focus();
		return true;
	};
	
	gThis._UpdateRepetitionButtons = function() {
		if (!gThis.m_bRepeatable) {
			return;
		}
		for (var j in gThis.m_oRepetitions) {
			gThis.m_oRepetitions[j].find('.' + gThis._GetClass('RemoveRepetition') + ', .' + gThis._GetClass('AddRepetition')).remove();
		}
		var jRepetitions = gThis.m_jNode.find('.' + gThis._GetClass('FieldRepetition'));
		var jTrigger;
		for (var i = 0; i < jRepetitions.length; i++) {
			if ((i == jRepetitions.length - 1) && (GCore.ObjectLength(gThis.m_oRepetitions) < gThis.m_oOptions.oRepeat.iMax)) {
				jTrigger = $('<a href="#" class="' + gThis._GetClass('AddRepetition') + '"/>').append('<img src="' + gThis._GetImage('AddRepetition') + '" alt="' + GForm.Language.add_field_repetition + '" title="' + GForm.Language.add_field_repetition + '"/>');
				jRepetitions.eq(i).find('.' + gThis._GetClass('FieldSpan')).after(jTrigger);
				jTrigger.click(function() {
					gThis.AddRepetition();
					return false;
				});
			}
			if (jRepetitions.length > 1) {
				jTrigger = $('<a href="#" class="' + gThis._GetClass('RemoveRepetition') + '"/>').append('<img src="' + gThis._GetImage('RemoveRepetition') + '" alt="' + GForm.Language.remove_field_repetition + '" title="' + GForm.Language.remove_field_repetition + '"/>');
				jRepetitions.eq(i).find('.' + gThis._GetClass('FieldSpan')).after(jTrigger);
				var sRepetition = jRepetitions.get(i).sRepetition;
				jTrigger.click(function() {
					gThis.RemoveRepetition($(this).closest('.' + gThis._GetClass('FieldRepetition')).get(0).sRepetition);
					return false;
				});
			}
		}
	};
	
}, oDefaults);


/*
* CHECKBOX
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-checkbox',
		sFieldSpanClass: 'field',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition'
	},
	sFieldType: 'checkbox',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormCheckbox = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return (gThis._GetField(sRepetition).is(':checked')) ? gThis._GetField(sRepetition).attr('value') : '';
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis._GetField(sRepetition).attr('value') == mValue) {
			gThis._GetField(sRepetition).parent().checkCheckboxes();
		}
		else {
			gThis._GetField(sRepetition).parent().unCheckCheckboxes();
		}
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		gThis.m_jNode.append(jLabel);
		gThis.m_jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="' + gThis.GetName() + '" id="' + gThis.GetId() + '" value="1"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(gThis.m_jField));
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jRepetitionNode.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jRepetitionNode);
	};
	
	gThis.OnFocus = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault);
	};
	
}, oDefaults);


/*
* FIELDSET LANGUAGE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sAddButtonClass: 'add-repetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	oImages: {
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	aoFields: [],
	aoLanguages: [],
	agFields: [],
	oRepeat: {
		iMin: 1,
		iMax: 1
	},
	sClass: ''
};

var GFormFieldsetLanguage = GCore.ExtendClass(GFormContainerLanguage, function() {
	
	var gThis = this;
	
	gThis.m_jAdd;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<fieldset/>');
		gThis.m_jNode.addClass(gThis.m_oOptions.sClass);
		gThis.m_jNode.attr('id', gThis.m_oOptions.sName);
		gThis.m_jNode.addClass('repeatable');
			
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jNode.bind('GFormShow', function() {
			gThis.m_gForm.m_bFocused = false;
			gThis.OnShow();
		});
		return false;
	};
	
	gThis.OnShow = function() {
		
		var aKeys = [];
		for (i in gThis.m_oContainerRepetitions) {
			aKeys.push(i);
		}
		aKeys.sort();
		for (i = 0; i < aKeys.length; i++) {
			var j = aKeys[i];
			gThis.m_oContainerRepetitions[j].OnShow();
			if (!gThis.m_gForm.m_bFocused) {
				gThis.m_gForm.m_bFocused = gThis.m_oContainerRepetitions[j].Focus();
			}
		}

		return gThis.m_gForm.m_bFocused;
	};
	
}, oDefaults);

/*
* FIELDSET
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sAddButtonClass: 'add-repetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	oImages: {
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	aoFields: [],
	agFields: [],
	oRepeat: {
		iMin: 1,
		iMax: 1
	},
	sClass: ''
};

var GFormFieldset = GCore.ExtendClass(GFormContainer, function() {
	
	var gThis = this;
	
	gThis.m_jAdd;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<fieldset/>');
		gThis.m_jNode.addClass(gThis.m_oOptions.sClass);
		gThis.m_jNode.attr('id', gThis.m_oOptions.sName);
		gThis.m_jNode.append('<legend><span>' + gThis.m_oOptions.sLabel + '</span></legend>');
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis.RenderChildren());
		}
		else {
			gThis.m_jNode.addClass('repeatable');
			gThis.m_jAdd = $('<a href="#" class="' + gThis._GetClass('AddButton') + '"/>');
			gThis.m_jAdd.append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.add_repetition + '" title="' + GForm.Language.add_repetition + '"/>');
			gThis.m_jNode.append(gThis.m_jAdd);
			if (GCore.ObjectLength(gThis.m_oContainerRepetitions) >= gThis.m_oOptions.oRepeat.iMax) {
				gThis.m_jAdd.css('display', 'none');
			}
		}
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jNode.bind('GFormShow', function() {
			gThis.m_gForm.m_bFocused = false;
			gThis.OnShow();
		});
		if (gThis.m_bRepeatable) {
			gThis.m_jAdd.click(function() {
				gThis.AddRepetition();
				if (GCore.ObjectLength(gThis.m_oContainerRepetitions) >= gThis.m_oOptions.oRepeat.iMax) {
					gThis.m_jAdd.css('display', 'none');
				}
				if (GCore.ObjectLength(gThis.m_oContainerRepetitions) > gThis.m_oOptions.oRepeat.iMin) {
					gThis.m_jNode.find('.' + gThis._GetClass('Repetition') + ' > .' + gThis._GetClass('DeleteButton')).css('display', 'block');
				}
				return false;
			});
		}
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bRepeatable) {
			var aKeys = [];
			for (i in gThis.m_oContainerRepetitions) {
				aKeys.push(i);
			}
			aKeys.sort();
			for (i = 0; i < aKeys.length; i++) {
				var j = aKeys[i];
				gThis.m_oContainerRepetitions[j].OnShow();
				if (!gThis.m_gForm.m_bFocused) {
					gThis.m_gForm.m_bFocused = gThis.m_oContainerRepetitions[j].Focus();
				}
			}
		}
		else {
			
			for (var i = 0; i < gThis.m_oOptions.agFields.length; i++) {
				gThis.m_oOptions.agFields[i].OnShow();
				if (!gThis.m_gForm.m_bFocused) {
					gThis.m_gForm.m_bFocused = gThis.m_oOptions.agFields[i].Focus();
				}
			}
		}
		
		gThis.m_jNode.find('input,select').bind('change', GEventHandler(function(eEvent) {
			var jPanel = $(eEvent.currentTarget).closest('.ui-tabs-panel');
			$('a[href="#' + jPanel.attr('id') + '"]').addClass('changed');
		}));
		
		return gThis.m_gForm.m_bFocused;
	};
	
}, oDefaults);


/*
* FORM
*/

var oDefaults = {
	sName: '',
	sFormName: '',
	sClass: '',
	oClasses: {
		sBlockClass: 'block',
		sButtonClass: 'button',
		sButtonImageRightClass: 'right',
		sNavigationClass: 'navigation',
		sPreviousClass: 'previous',
		sNextClass: 'next',
		sInputWithImageClass: 'with-image',
		sActionsClass: 'actions',
		sTabbedClass: 'tabbed'
	},
	oImages: {
		sArrowLeftGray: '_images_panel/icons/buttons/arrow-left-gray.png',
		sArrowRightGreen: '_images_panel/icons/buttons/arrow-right-green.png',
		sSave: '_images_panel/icons/buttons/check.png'
	},
	aoFields: [],
	agFields: [],
	oValues: {},
	iTabs: 0
};

var GForm = GCore.ExtendClass(GFormContainer, function() {
	
	var gThis = this;
	
	gThis.m_bDontFocus = false;
	gThis.m_bPopulatedWithDefaults = false;
	gThis.m_bFocused = false;
	gThis.m_ogFields = {};
	gThis.m_oLocks = {};
	gThis.m_iLockId = 0;
	
	gThis._Constructor = function() {
		GForm.s_agForms[gThis.m_oOptions.sFormName] = gThis;
		gThis.m_gForm = gThis;
		gThis.m_gParent = GCore.NULL;
		$(gThis).addClass(gThis.m_oOptions.sClass);
		gThis._ConstructChildren();
		$(gThis).append('<div class="' + gThis._GetClass('Actions') + '"/>');
		$(gThis).append(gThis.Render());
		gThis.MakeTabs();
		gThis.Populate(gThis.m_oOptions.oValues);
		gThis.m_bPopulatedWithDefaults = true;
		gThis.PopulateErrors(gThis.m_oOptions.oErrors);
		gThis.OnInit();
		gThis._InitButtons();
	};
	
	gThis._InitializeEvents = function() {
		$(gThis).submit(gThis.OnSubmit);
	};
	
	gThis.Lock = function(sTitle, sMessage) {
		gThis.m_oLocks[gThis.m_iLockId++] = {
			sTitle: sTitle,
			sMessage: sMessage
		};
		return gThis.m_iLockId - 1;
	};
	
	gThis.Unlock = function(iLockId) {
		delete gThis.m_oLocks[iLockId];
	};
	
	gThis.OnSubmit = function() {
		for (var i in gThis.m_oLocks) {
			GAlert(gThis.m_oLocks[i].sTitle, gThis.m_oLocks[i].sMessage);
			return false;
		}
		var bResult = gThis.Validate(true);
		if (bResult) {
			GCore.StartWaiting();
		}
		else
		{
			$(gThis).find('.invalid').first().find('input, select').focus();
			
			GAlert.DestroyAll();
			
			GAlert(GForm.Language.form_data_invalid,'', {
				bAutoFocus: false
			});
			
			window.setTimeout(GAlert.DestroyAll, 15000);
			
		}
		return bResult;
	};
	
	gThis.Submit = function(sAction) {
		$(gThis).find('.' + gThis._GetClass('Actions')).empty();
		if ((sAction != undefined) && (sAction != '')) {
			$(gThis).find('.' + gThis._GetClass('Actions')).append('<input type="hidden" name="_Action_' + sAction + '" value="1"/>');
		}
		$(gThis).submit();
	};
	
	gThis._InitButtons = function() {
		var jButtons = $('a[href="#' + $(gThis).attr('id') + '"]');
		jButtons.each(function() {
			var jButton = $(this);
			var sRel = jButton.attr('rel').match(/^[^\[]+/)[0];
			var sAction = jButton.attr('rel').match(/\[[^\]]+\]$/);
			if (sAction != null) {
				sAction = sAction[0].substr(1, sAction[0].length - 2);
			}
			else {
				sAction = null;
			}
			switch (sRel) {
				case 'submit':
					jButton.click(function() {
						gThis.Submit(sAction);
						return false;
					});
					break;
				case 'reset':
					jButton.click(function() {
						gThis.Reset();
						return false;
					});
					break;
			}
		});
	};
	
	gThis.Render = function() {
		return gThis.RenderChildren();
	};
	
	gThis.Reset = function() {
		gThis.OnReset();
		gThis.m_bPopulatedWithDefaults = false;
		gThis.m_oOptions.agFields = gThis.m_agFields;
		gThis.Populate(gThis.m_oOptions.oValues);
		gThis.m_bPopulatedWithDefaults = true;
	};
	
	gThis.MakeTabs = function() {
		if ($(gThis).is('.attributeGroupEditor, .statusChange, .editOrder')) {
			gThis.OnShow();
			return;
		}
		var oThisOptions = gThis.m_oOptions;
		$(gThis).GTabs({
			iType: gThis.m_oOptions.iTabs == GForm.TABS_HORIZONTAL
		});
		gThis.m_oOptions = oThisOptions;
	};
	
	gThis.GetField = function(sName) {
		var asName = sName.split('.');
		if (asName.length == 2) {
			if (asName[0] != gThis.m_oOptions.sFormName) {
				return GForm.GetForm(asName[0]).GetField(asName[1]);
			}
			sName = asName[1];
		}
		return gThis.m_ogFields[sName];
	};
	
	gThis._Constructor();
	
}, oDefaults);

GForm.INFINITE = 99999;

GForm.TABS_VERTICAL = 0;
GForm.TABS_HORIZONTAL = 1;
GForm.s_agForms = {};

GForm.GetForm = function(sName) {
	return GForm.s_agForms[sName];
};

new GPlugin('GForm', oDefaults, GForm);

/*
* LAYER SELECTOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-tree',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sItemNameClass: 'item-name',
		sExpanderClass: 'expander',
		sExpandedClass: 'expanded',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sExpandAllClass: 'expand-all',
		sRetractAllClass: 'retract-all'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sDelete: '_images_panel/icons/buttons/delete.png',
		sSave: '_images_panel/icons/buttons/save.png',
		sRestore: '_images_panel/icons/buttons/clean.png',
		sWaiting: '_images_panel/icons/loading/indicator.gif'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	oStores: {},
	sActive: ''
};

var GFormLayerSelector = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_jTree;
	gThis.m_jOptions;
	gThis.m_oStores;
	gThis.m_jExpandAll;
	gThis.m_jRetractAll;
	gThis.m_jFieldWrapper;
	gThis.m_jItemPlaceholder;
	gThis.m_jItemDragged;
	gThis.m_oStores = {};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<input type="hidden"/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jFieldWrapper = $('<div/>');
		gThis.m_jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jNode.append('<label>' + gThis.m_oOptions.sLabel + '</label>');
		gThis.m_jExpandAll = $('<a href="#"/>').text(GForm.Language.tree_expand_all);
		gThis.m_jRetractAll = $('<a href="#"/>').text('Zaznacz wszystkie');
		gThis.m_jTree = $('<ul/>');
		gThis.m_jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		gThis.Update();
		window.setTimeout(gThis.ResetExpansion, 500);
	};

	gThis.OnRetractAll = function(eEvent) {
		gThis.m_jTree.find('li:has(li)').removeClass(gThis._GetClass('Expanded'));
//		gThis.m_jTree.checkCheckboxes();
		return false;
	};
	
	gThis.OnExpandAll = function(eEvent) {
		gThis.m_jTree.find('li:has(li)').addClass(gThis._GetClass('Expanded'));
		return false;
	};
	
	gThis.GetOrder = function() {
		var jItems = gThis.m_jTree.find('li');
		var aoItems = [];
		for (var i = 0; i < jItems.length; i++) {
			var sId = jItems.eq(i).get(0).sId;
			var sParent = '';
			if (jItems.eq(i).parent().closest('li').length) {
				sParent = jItems.eq(i).parent().closest('li').get(0).sId;
			}
			var jSiblings = jItems.eq(i).parent().children('li');
			var iWeight = jSiblings.length - jSiblings.index(jItems.eq(i)) - 1;
			aoItems.push({
				id: sId,
				parent: sParent,
				weight: iWeight
			});
		}
		return aoItems;
	};
	
	gThis.GetValue = function(sRepetition) {
		
		return gThis.m_jFieldWrapper.find('input:first').attr('value');
		
		if (gThis.m_jField == undefined) {
			return '';
		}
		var aValues = [];
		var jValues = gThis._GetField(sRepetition).filter(':checked');
		for (var i in jValues) {
			aValues.push(jValues.eq(i).attr('value'));
		}
		return aValues;
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis._GetField(sRepetition).val(mValue).change();
	};
	
	gThis.ResetExpansion = function() {
		gThis.m_jTree.find('li').removeClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li.' + gThis._GetClass('Active')).parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li > label > input:checked').parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
	};
	
	gThis._WriteSubtree = function(jParent, sParent) {
		if (sParent == undefined) {
			sParent = null;
		}
		var oStores = GCore.FilterObject(gThis.m_oOptions.oStores, function(oStore) {return (oStore.parent == sParent);});
		var aIterationArray = GCore.GetIterationArray(oStores, function(oA, oB) {return (oA.weight < oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			var oStore = oStores[sId];
			jParent.append(gThis._WriteItem(sId, oStore));
		}
	};
	
	gThis._WriteItem = function(sId, oStore) {
		var jLi = $('<li/>');
		jLi.get(0).sId = sId;
		if (gThis.m_oOptions.sActive == sId) {
			jLi.addClass(gThis._GetClass('Active'));
		}
		
		if(oStore.type == 'store'){
			
			jLi.append($('<label class="' + gThis._GetClass('ItemName') + '"/>').css({'font-size':'13px','font-weight':'bold'}).append(oStore.name));
		
		}else{
			var jField = $('<input type="checkbox" value="' + sId + '"/>');	
			if (gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').length) {
				jField.click();
				jField.attr('checked', 'checked');
			}
			if((gThis.m_oOptions.bGlobal == true) && GCore.iActiveView > 0){
				jField.attr('disabled','disabled');
			}
			jLi.append($('<label class="' + gThis._GetClass('ItemName') + '"/>').append(jField).append(oStore.name));
		}
		

		var jUl = $('<ul/>');
		gThis._WriteSubtree(jUl, sId);
		var jExpander = $('<span class="' + gThis._GetClass('Expander') + '"/>');
		jLi.prepend(jExpander);
		jLi.append(jUl);
		jExpander.css('display', 'none');
		return jLi;
	};
	
	gThis.UpdateExpanders = function() {
		gThis.m_jTree.find('li::not(:has(li))').removeClass(gThis._GetClass('Expanded')).children('.' + gThis._GetClass('Expander')).css('display', 'none');
		gThis.m_jTree.find('li:has(li) > .' + gThis._GetClass('Expander')).css('display', 'block');
	};
	
	gThis.Update = function() {
		gThis.m_jTree.empty();
		gThis._WriteSubtree(gThis.m_jTree);
		gThis._InitializeNodeEvents();
		gThis.ResetExpansion();
	};
	
	gThis.Populate = function(mValue) {
		
		if ((mValue == undefined) || (mValue == '')) {
			mValue = [];
		}
		else if (!(mValue instanceof Array)) {
			mValue = [mValue];
		}
		
			gThis.m_jNode.unCheckCheckboxes();
			gThis.m_jFieldWrapper.empty();
			for (var i in mValue) {
				if (i == 'toJSON') {
					continue;
				}
				
				gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '[]" value="' + mValue[i] + '"/>');
				gThis.m_jNode.find('input:checkbox[value="v' + mValue[i] + '"]').parent().checkCheckboxes();
			}

		gThis.ResetExpansion();
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_bShown = true;
		}
		gThis.OnExpandAll();
	};
	
	gThis._OnClick = GEventHandler(function(eEvent) {
		GCore.StartWaiting();
	});
	
	gThis._InitializeEvents = function(sRepetition) {
		gThis.m_jExpandAll.click(gThis.OnExpandAll);
		gThis.m_jRetractAll.click(gThis.OnRetractAll);
		gThis._InitializeNodeEvents();
	};
	
	gThis._OnSelect = GEventHandler(function(eEvent) {
		
		var sStoreLabel = $(this).attr('value');
		
		var oStores = GCore.FilterObject(gThis.m_oOptions.oStores, function(oStore) {
			return (oStore.label == sStoreLabel);
		});
		
		var aoStore = oStores[sStoreLabel];
		var iStoreId = aoStore['id'];
		
		gThis.m_jFieldWrapper.find('input[value="' + iStoreId + '"]').remove();

		if ($(this).is(':checked')) {
			
			var jInput = $('<input type="hidden" name="' + gThis.GetName() + '[]" value="' + iStoreId + '"/>');
			gThis.m_jFieldWrapper.append(jInput);
			
		}
	});
	
	gThis._InitializeNodeEvents = function() {
		gThis.m_jTree.find('.' + gThis._GetClass('Expander')).unbind('click').click(function() {
			if ($(this).closest('li').hasClass(gThis._GetClass('Expanded'))) {
				$(this).closest('li').find('li').andSelf().removeClass(gThis._GetClass('Expanded'));
			}
			else {
				$(this).closest('li').addClass(gThis._GetClass('Expanded'));
				gThis._Expand($(this).closest('li'));
			}
		});
		gThis.m_jTree.find('input').unbind('click').click(gThis._OnSelect);
	};
	
	gThis._Expand = function(jParentLi) {
		var sId = jParentLi.get(0).sId;
		if (gThis.m_oStores[sId] != undefined) {
			return;
		}
	};
	
	gThis._OnChildrenLoaded = GEventHandler(function(eEvent) {
		var jUl = $('<ul/>');
		gThis.m_oStores[eEvent.parentNode.get(0).sId] = true;
		for (var i in eEvent.children) {
			jUl.append(gThis._WriteItem(i, eEvent.children[i]));
		}
		eEvent.parentNode.find('ul').remove();
		eEvent.parentNode.append(jUl);
		gThis._InitializeNodeEvents();
	});
	
}, oDefaults);

/*
* LAYOUT BOX SCHEME LIVE PREVIEW
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	sAlt: '',
	sSrc: '',
	oClasses: {
		sFieldClass: 'field-layout-box-scheme-preview'
	},
	sBoxName: '',
	sBoxScheme: '',
	sLayoutBoxTpl: '',
	sBoxTitle: 'Lorem Ipsum',
	sBoxContent: 'Lorem ipsum dolor sit amet enim.'
};

var GFormLayoutBoxSchemePreview = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_oValues = {};
	gThis.m_jWindow;
	gThis.m_bWindowLoaded = false;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jWindow = $('<iframe src="' + GCore.DESIGN_PATH + '_data_frontend/blank.htm" id="' + gThis.GetId() + '__window" width="300" height="100"/>');
		gThis.m_jNode.append(gThis.m_jWindow);
		gThis.m_jUnmodified = $('<input type="hidden" name="' + gThis.GetName() + '[unmodified]" value="1"/>');
		gThis.m_jNode.append(gThis.m_jUnmodified);
	};
	
	gThis._BuildWindow = function() {
		gThis.m_bWindowLoaded = true;
		var sTpl = gThis.m_oOptions.sLayoutBoxTpl;
		var sBoxScheme = gThis.m_oOptions.sBoxScheme;
		if (sBoxScheme.length) {
			sBoxScheme = 'layout-box layout-box-scheme-' + sBoxScheme;
		}
		else {
			sBoxScheme = 'layout-box';
		}
		var sBoxId = gThis.m_oOptions.sBoxName;
		if (sBoxId.length) {
			sBoxId = 'layout-box-' + sBoxId;
		}
		sTpl = sTpl.replace(/\{{\ box.id \}}/, sBoxId);
		sTpl = sTpl.replace(/\{{\ box.schemeClass \}}/, sBoxScheme);
		sTpl = sTpl.replace(/\{{\ box.heading \}}/, gThis.m_oOptions.sBoxTitle);
		sTpl = sTpl.replace(/\{%\ block content \%}/, '<p>' + gThis.m_oOptions.sBoxContent + '</p>');
		sTpl = sTpl.replace(/\{%\ endblock \%}/, '');
		sTpl = sTpl.replace(/\{%\ block headerurl \%}/, '');
		sTpl = sTpl.replace(/\{%\ endblock \%}/, '');
		var jBox = $(sTpl);
		jBox.find('.layout-box-icons').prepend('<span class="layout-box-close layout-box-icon"/>');
		jBox.find('.layout-box-icons').prepend('<span class="layout-box-uncollapse layout-box-icon"/>');
		gThis.m_jWindow.contents().find('head').empty();
		for (var i = 0; i < gThis.m_oOptions.asStylesheets.length; i++) {
			gThis.m_jWindow.contents().find('head').append('<link rel="stylesheet" href="' + gThis.m_oOptions.asStylesheets[i] + '?id=' + Math.random() + '" type="text/css"/>');
		}
		gThis.m_jWindow.contents().find('body').css({
			padding: 0,
			margin: 0,
			background: 'transparent',
			width: 'auto',
			minWidth: 0
		}).html(jBox.wrap('<div/>').parent().html());
		gThis.Refresh();
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		gThis.m_bShown = true;
		gThis._BuildWindow();
		gThis.m_jWindow.load(gThis._BuildWindow);
		gThis.m_jUnmodified.val('0');
	};
	
	gThis._InitializeEvents = function() {
		for (var i in gThis.m_oOptions.asTriggers) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.asTriggers[i]);
			if (gField instanceof GFormColourSchemePicker) {
				gThis._ConnectColourSchemePicker(gField);
			}
			else if (gField instanceof GFormFontStyle) {
				gThis._ConnectFontStyle(gField);
			}
			else if (gField instanceof GFormBorder) {
				gThis._ConnectBorder(gField);
			}
			else if (gField instanceof GFormSelect) {
				gThis._ConnectSelect(gField);
			}
			else if (gField instanceof GFormLocalFile) {
				gThis._ConnectLocalFile(gField);
			}
			else if (gField instanceof GFormTextField) {
				gThis._ConnectTextField(gField);
			}
		}
		gThis.Update({});
	};
	
	gThis._ConnectColourSchemePicker = function(gField) {
		gField.m_jFieldColourType.change(gThis.Update);
		gField.m_jFieldColourStart.change(gThis.Update);
		gField.m_jFieldColourEnd.change(gThis.Update);
		gField.m_jFileField.change(gThis.Update);
	};
	
	gThis._ConnectFontStyle = function(gField) {
		gField.m_jFieldFontFamily.change(gThis.Update);
		gField.m_jFieldFontStyleBold.change(gThis.Update);
		gField.m_jFieldFontStyleUnderline.change(gThis.Update);
		gField.m_jFieldFontStyleItalic.change(gThis.Update);
		gField.m_jFieldFontStyleUppercase.change(gThis.Update);
		gField.m_jFieldFontColour.change(gThis.Update);
		gField.m_jFieldFontSize.change(gThis.Update);
	};
	
	gThis._ConnectBorder = function(gField) {
		gField.m_fOnUpdate = gThis.Update;
	};
	
	gThis._ConnectSelect = function(gField) {
		gField.m_jField.change(gThis.Update);
	};
	
	gThis._ConnectTextField = function(gField) {
		gField.m_jField.change(gThis.Update);
	};
	
	gThis._ConnectLocalFile = function(gField) {
		gField.m_jField.change(gThis.Update);
	};
	
	gThis.Update = GEventHandler(function(eEvent) {
		gThis.m_oValues = {};
		for (var i in gThis.m_oOptions.asTriggers) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.asTriggers[i]);
			if (gField instanceof GFormColourSchemePicker) {
				gThis._UpdateColourSchemePicker(gField);
			}
			else if (gField instanceof GFormFontStyle) {
				gThis._UpdateFontStyle(gField);
			}
			else if (gField instanceof GFormBorder) {
				gThis._UpdateBorder(gField);
			}
			else if (gField instanceof GFormSelect) {
				gThis._UpdateSelect(gField);
			}
			else if (gField instanceof GFormLocalFile) {
				gThis._UpdateLocalFile(gField);
			}
			else if (gField instanceof GFormTextField) {
				gThis._UpdateTextField(gField);
			}
		}
		gThis.Refresh();
	});
	
	gThis._UpdateColourSchemePicker = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		switch (gField.m_jFieldColourType.val()) {
			case '3':
				gThis.m_oValues[sSelector]['background-color'] = '#' + gField.m_jFieldColourStart.val();
				if (gField.m_jFileField.val().length) {
					gThis.m_oValues[sSelector]['background-image'] = 'url(\'' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + gField.m_oOptions.sFilePath + gField.m_jFileField.val() + '\')';
					gThis.m_oValues[sSelector]['background-position'] = '0 0';
					gThis.m_oValues[sSelector]['background-repeat'] = 'repeat-x';
				}
				else {
					gThis.m_oValues[sSelector]['background-image'] = 'none';
				}
				break;
			case '2':
				gThis.m_oValues[sSelector]['background-gradient'] = '#' + gField.m_jFieldColourStart.val();
				gThis.m_oValues[sSelector]['background-color'] = '#' + gField.m_jFieldColourEnd.val();
				gThis.m_oValues[sSelector]['background-image'] = 'none';
				break;
			default:
				gThis.m_oValues[sSelector]['background-color'] = '#' + gField.m_jFieldColourStart.val();
				gThis.m_oValues[sSelector]['background-image'] = 'none';
		}
	};
	
	gThis._UpdateFontStyle = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		gThis.m_oValues[sSelector]['color'] = '#' + gField.m_jFieldFontColour.val();
		gThis.m_oValues[sSelector]['font-family'] = gField.m_jFieldFontFamily.val();
		gThis.m_oValues[sSelector]['font-weight'] = (gField.m_jFieldFontStyleBold.val() == '1') ? 'bold' : 'normal';
		gThis.m_oValues[sSelector]['font-style'] = (gField.m_jFieldFontStyleItalic.val() == '1') ? 'italic' : 'normal';
		gThis.m_oValues[sSelector]['text-decoration'] = (gField.m_jFieldFontStyleUnderline.val() == '1') ? 'underline' : 'none';
		gThis.m_oValues[sSelector]['text-transform'] = (gField.m_jFieldFontStyleUppercase.val() == '1') ? 'uppercase': 'none';
		gThis.m_oValues[sSelector]['font-size'] = gField.m_jFieldFontSize.val() + 'px';
	};
	
	gThis._UpdateBorder = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		var asSides = ['top', 'right', 'bottom', 'left'];
		for (var i in asSides) {
			var iSize = parseInt(gField.m_oSizeField[asSides[i]].val());
			if (iSize == 0) {
				gThis.m_oValues[sSelector]['border-' + asSides[i]] = 'none';
			}
			else {
				gThis.m_oValues[sSelector]['border-' + asSides[i]] = 'solid ' + iSize + 'px #' + gField.m_oColourField[asSides[i]].val();
			}
		}
	};
	
	gThis._UpdateSelect = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		var sAttribute = gField.m_oOptions.sCssAttribute;
		if ((sAttribute == undefined) || !sAttribute.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		switch (sAttribute) {
			case 'border-radius':
				if (gThis.m_oValues[sSelector + ' .layout-box-header'] == undefined) {
					gThis.m_oValues[sSelector + ' .layout-box-header'] = {};
				}
				if (gThis.m_oValues[sSelector + ' .layout-box-content'] == undefined) {
					gThis.m_oValues[sSelector + ' .layout-box-content'] = {};
				}
				var iNewValue = Math.max(0, parseInt(gField.m_jField.val()) - 1) + 'px';
				gThis.m_oValues[sSelector]['BorderRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector + ' .layout-box-header']['BorderTopLeftRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-header']['BorderTopRightRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['BorderBottomLeftRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['BorderBottomRightRadius'] = iNewValue;
				gThis.m_oValues[sSelector]['MozBorderRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector + ' .layout-box-header']['MozBorderRadiusTopleft'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-header']['MozBorderRadiusTopright'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['MozBorderRadiusBottomleft'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['MozBorderRadiusBottomright'] = iNewValue;
				gThis.m_oValues[sSelector]['WebkitBorderTopLeftRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector]['WebkitBorderTopRightRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector]['WebkitBorderBottomLeftRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector]['WebkitBorderBottomRightRadius'] = gField.m_jField.val();
				gThis.m_oValues[sSelector + ' .layout-box-header']['WebkitBorderTopLeftRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-header']['WebkitBorderTopRightRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['WebkitBorderBottomLeftRadius'] = iNewValue;
				gThis.m_oValues[sSelector + ' .layout-box-content']['WebkitBorderBottomRightRadius'] = iNewValue;
				break;
			default:
				gThis.m_oValues[sSelector][sAttribute] = gField.m_jField.val();
		}
	};
	
	gThis._UpdateTextField = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		var sAttribute = gField.m_oOptions.sCssAttribute;
		if ((sAttribute == undefined) || !sAttribute.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		switch (sAttribute) {
			case 'line-height':
				if (gThis.m_oValues[sSelector + ' .layout-box-icon'] == undefined) {
					gThis.m_oValues[sSelector + ' .layout-box-icon'] = {};
				}
				gThis.m_oValues[sSelector][sAttribute] = gField.m_jField.val() + 'px';
				gThis.m_oValues[sSelector]['height'] = gField.m_jField.val() + 'px';
				gThis.m_oValues[sSelector + ' .layout-box-icon']['height'] = gField.m_jField.val() + 'px';
				break;
			default:
				gThis.m_oValues[sSelector][sAttribute] = gField.m_jField.val();
		}
	};
	
	gThis._UpdateLocalFile = function(gField) {
		var sSelector = gField.m_oOptions.sSelector;
		if ((sSelector == undefined) || !sSelector.length) {
			return;
		}
		if (gThis.m_oValues[sSelector] == undefined) {
			gThis.m_oValues[sSelector] = {};
		}
		gThis.m_oValues[sSelector]['background'] = 'transparent url(\'' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + 'design/_images_frontend/upload/' + gField.m_jField.val() + '\') center center no-repeat';
	};
	
	gThis.Refresh = function() {
		if (!gThis.m_bWindowLoaded) {
			return;
		}
		gThis.m_jWindow.contents().find('body [style]').attr('style', '');
		gThis.m_jWindow.contents().find('.gradient').each(function() {
			$(this).parent().html($(this).next('div').html());
		});
		for (var i in gThis.m_oValues) {
			for (var j in gThis.m_oValues[i]) {
				if (j == 'background-gradient') {
					gThis.m_jWindow.contents().find(i).each(function() {
						$(this).append().gradient({
						from: gThis.m_oValues[i][j].substr(1),
						to: gThis.m_oValues[i]['background-color'].substr(1),
						direction: 'horizontal'
					})});
				}
				else {
					gThis.m_jWindow.contents().find(i).css(j, gThis.m_oValues[i][j]);
				}
			}
		}
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* PREVIEW
*/

var oDefaults = {
	sLabel: '',
	sUrl: '',
	iWidth: 400,
	iHeight: 250,
	oClasses: {
		sFieldClass: 'field-preview'
	}
};

var GFormPreview = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_jTrigger;
	gThis.m_wWindow;
	
	gThis._PrepareNode = function() {
		if (!gThis.m_oOptions.sLabel.length) {
			gThis.m_oOptions.sLabel = GForm.Language.preview_trigger_label;
		}
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jTrigger = $('<a href="#">' + gThis.m_oOptions.sLabel + '</a>');
		gThis.m_jNode.append(gThis.m_jTrigger);
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jTrigger.click(gThis.OnClick);
	};
	
	gThis.OnClick = GEventHandler(function(eEvent) {
		gThis.m_wWindow = window.open('', gThis.GetId(), 'width=' + gThis.m_oOptions.iWidth + 'px, height=' + gThis.m_oOptions.iHeight + 'px, location=false, menubar=false, status=false, toolbar=false');
		gThis.m_wWindow.focus();
		gThis._WriteFormAndSubmitIt();
	});
	
	gThis._WriteFormAndSubmitIt = function() {
		gThis.m_wWindow.document.open();
		gThis.m_wWindow.document.write('<html><head><title>...</title></head><body>');
		gThis.m_wWindow.document.write('<form style="display: none;" id="form" action="' + gThis.m_oOptions.sUrl + '" method="post">');
		gThis.m_wWindow.document.write('</form>');
		gThis.m_wWindow.document.write('</body></html>');
		gThis.m_wWindow.document.close();
		var jFormElements = gThis.m_jNode.closest('.GForm').find('[name]');
		$(gThis.m_wWindow.document.getElementById('form')).append(jFormElements.clone()).submit();
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* RADIO GROUP
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-radio-group',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormRadioGroup = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bResized;
	
	gThis._Constructor = function() {
		gThis.m_bResized = false;
	};
	
	gThis.GetValue = function() {
		return gThis.m_jField.filter(':checked').attr('value');
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label class="' + gThis._GetClass('Group') + '" for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jField = $();
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			var oOption = gThis.m_oOptions.aoOptions[i];
			var jInput = $('<input type="radio" name="' + gThis.GetName() + '" value="' + oOption.sValue + '">');
			gThis.m_jField.add(jInput);
			gThis.m_jNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append($('<label>' + oOption.sLabel + '</label>').prepend(jInput)));
		}
	};
	
	gThis.SetValue = function(mValue) {
		if ((gThis.m_jField != undefined) && (gThis.m_jField instanceof $)) {
			gThis.m_jField.find(':radio[value="' + mValue + '"]').click();
		}
	};
	
	gThis.Reset = function() {
	};
	
}, oDefaults);


/*
* RADIO VALUE GROUP
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-radio-group',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting'
	},
	oImages: {
		sCalendarIcon: '_images_panel/icons/buttons/calendar.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormRadioValueGroup = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bResized;
	
	gThis._Constructor = function() {
		gThis.m_bResized = false;
	};
	
	gThis.GetValue = function() {
		return gThis.m_jField.filter(':checked').attr('value');
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label class="' + gThis._GetClass('Group') + '" for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			var oOption = gThis.m_oOptions.aoOptions[i];
			var jInput = $('<input type="radio" name="' + gThis.GetName() + '[value]" value="' + oOption.sValue + '">');
			if (gThis.m_jField instanceof $) {
				gThis.m_jField = gThis.m_jField.add(jInput);
			}
			else {
				gThis.m_jField = jInput;
			}
			var iPosition;
			if ((iPosition = oOption.sLabel.indexOf('%input%')) != -1) {
				var jLabel = $('<label/>');
				jLabel.append('<span>' + oOption.sLabel.substr(0, iPosition) + '</span>');
				var jInnerInput = $('<input type="text" name="' + gThis.GetName() + '[' + oOption.sValue + ']" value=""/>');
				jLabel.append($('<span class="inner-field-text"/>').append(jInnerInput));
				jLabel.append('<span>' + oOption.sLabel.substr(iPosition + 7) + '</span>');
			}
			else if ((iPosition = oOption.sLabel.indexOf('%date%')) != -1) {
				var jLabel = $('<label/>');
				jLabel.append('<span>' + oOption.sLabel.substr(0, iPosition) + '</span>');
				var jInnerInput = $('<input class="date" type="text" name="' + gThis.GetName() + '[' + oOption.sValue + ']" value=""/>');
				var jTrigger = $('<img style="width: 16px; height: 16px; float: left; margin: 0 5px 0 0;" src="' + gThis._GetImage('CalendarIcon') + '" alt=""/>');
				jTrigger.css('cursor', 'pointer');
				jLabel.append($('<span class="inner-field-text"/>').append(jInnerInput)).append(jTrigger);
				jLabel.append('<span>' + oOption.sLabel.substr(iPosition + 6) + '</span>');
			}
			else if ((iPosition = oOption.sLabel.indexOf('%select%')) != -1) {
				var jLabel = $('<label/>');
				jLabel.append('<span>' + oOption.sLabel.substr(0, iPosition) + '</span>');
				var jInnerInput = $('<select name="' + gThis.GetName() + '[' + oOption.sValue + ']"/>');
				for (var j in gThis.m_oOptions.oSuboptions[oOption.sValue]) {
					var oSuboption = gThis.m_oOptions.oSuboptions[oOption.sValue][j];
					jInnerInput.append('<option value="' + oSuboption.value + '">' + oSuboption.label + '</option>');
				}
				jLabel.append($('<span class="inner-field-select"/>').append($('<span class="field"/>').append(jInnerInput)));
				jLabel.append('<span>' + oOption.sLabel.substr(iPosition + 8) + '</span>');
			}
			else {
				var jLabel = $('<label>' + oOption.sLabel + '</label>');
			}
			gThis.m_jNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jLabel.prepend(jInput)));
		}
	};
	
	gThis._InitializeEvents = function() {
		var f = function(eEvent) {
			if ($(this).is(':checked')) {
				$(this).closest('.field').find('input:text').focus();
			}
		};
		gThis.m_jField.change(f).click(f);
		gThis.m_jField.closest('.field').find('select').GSelect();
		gThis.m_jField.closest('.field').find('.inner-field-text input').focus(function(eEvent) {
			$(this).parent().addClass('focus');
			$(this).closest('.field').find('input:radio:not(:checked)').click();
		}).blur(function(eEvent) {
			$(this).parent().removeClass('focus');
		});
		gThis.m_jField.closest('.field').find('.inner-field-select select').focus(function(eEvent) {
			$(this).parent().addClass('focus');
		}).blur(function(eEvent) {
			$(this).parent().removeClass('focus');
			$(this).closest('.field').find('input:radio:not(:checked)').click();
		}).change(function(eEvent) {
			$(this).closest('.field').find('input:radio:not(:checked)').click();
		});
		gThis.m_jNode.find('input.date').datepicker();
	};
	
	gThis.SetValue = function(mValue) {
		for (var i in mValue) {
			if (i == 'value') {
				if ((gThis.m_jField != undefined) && (gThis.m_jField.length)) {
					gThis.m_jField.filter('[value="' + mValue[i] + '"]').click();
				}
			}
			else {
				gThis.m_jNode.find('input:text[name="' + gThis.GetName() + '[' + i + ']"]').val(mValue[i]);
				gThis.m_jNode.find('select[name="' + gThis.GetName() + '[' + i + ']"]').val(mValue[i]).change();
			}
		}
	};
	
	gThis.Reset = function() {
	};
	
}, oDefaults);


/*
* SELECT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-select',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sDisabledClass: 'disabled',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	sSelector: '',
	sCssAttribute: '',
	sAddItemPrompt: '',
	bAddable: false,
	fOnAdd: GCore.NULL,
	sAddItemPrompt: '',
};

var GFormSelect = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis._GetField(sRepetition).find('option:selected').attr('value');
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.val(mValue);
		if ((jField.get(0) != undefined) && (jField.get(0).Update != undefined)) {
			jField.get(0).Update.apply(jField.get(0));
		}
	};
	
	gThis.ExchangeOptions = function(aoNewOptions) {
		var sOldValueId = gThis.m_jField.val();
		gThis.m_oOptions.aoOptions = aoNewOptions;
		var jOldNode = gThis.m_jNode;
		gThis.m_jField = GCore.NULL;
		gThis._PrepareNode();
		gThis.m_jNode.addClass('GFormNode').get(0).gNode = gThis;
		jOldNode.replaceWith(gThis.m_jNode);
		gThis.m_bShown = false;
		gThis.m_bResized = false;
		gThis.OnShow();
		if (gThis.m_jField.find('option[value="' + sOldValueId + '"]').length) {
			gThis.m_jField.val(sOldValueId);
		}
		gThis._InitializeDependencies();
		gThis.m_jField.change();
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
		if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
			gThis.m_jField.attr('name', gThis.GetName() + '[value]');
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
		}
		if ((gThis.m_oOptions.sCssAttribute != undefined) && (gThis.m_oOptions.sCssAttribute.length)) {
			gThis.m_jField.attr('name', gThis.GetName() + '[value]');
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[css_attribute]" value="' + gThis.m_oOptions.sCssAttribute + '"/>');
		}
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<select name="' + gThis.GetName(sId) + '" id="' + gThis.GetId(sId) + '"/>');
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			var oOption = gThis.m_oOptions.aoOptions[i];
			jField.append('<option value="' + oOption.sValue + '">' + oOption.sLabel + '</option>');
		}
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		if (gThis.m_oOptions.sPrefix != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.sPrefix);
			jRepetitionNode.append(jPrefix);
		}
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}
		
		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {
			gThis.m_jTrigger = $('<a href="#" class="' + gThis._GetClass('AddRepetition') + '"/>').append('<img src="' + gThis._GetImage('AddRepetition') + '" alt="' + GForm.Language.add_field_repetition + '" title="' + GForm.Language.add_field_repetition + '"/>');
			jRepetitionNode.append(gThis.m_jTrigger);
		}
		var jError = $('<span class="' + gThis._GetClass('Required') + '"/>');
		jRepetitionNode.append(jError);
		gThis.jRepetitionNode = jRepetitionNode;
		return gThis.jRepetitionNode;
	};
	
	gThis.OnInitRepetition = function(sRepetition) {
		if (!gThis.m_bShown) {
			return;
		}
		gThis._GetField(sRepetition).GSelect();
	};
	
	gThis.OnShow = function() {
		gThis._UpdateRepetitionButtons();
		if (!gThis.m_bShown && gThis.m_bRepeatable) {
			gThis._InitializeEvents('new-0');
		}
		gThis.m_bShown = true;
		if (gThis.m_bRepeatable) {
			for (var i in gThis.m_oRepetitions) {
				if (!gThis.m_oRepetitions[i].m_bResized) {
					gThis.m_oRepetitions[i].m_bResized = true;
					gThis.OnInitRepetition(i);
				}
			}
		}
		else {
			if (!gThis.m_bResized) {
				gThis.m_bResized = true;
				gThis.OnInitRepetition();
			}
		}
		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {
			gThis.m_jTrigger.click(function() {
				GAlert.DestroyAll();
				GPrompt(gThis.m_oOptions.sAddItemPrompt, function(sName) {
					GCore.StartWaiting();
					gThis.m_oOptions.fOnAdd({
						name: sName
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						GAlert.DestroyAll();
						if(!eEvent.error){
							gThis.ExchangeOptions(eEvent.options);
							gThis.SetValue(eEvent.id);
							gThis.m_jField.triggerHandler('change');
							if(eEvent.vat){
								GCore.aoVatValues = eEvent.vat;
								$(window).trigger('OnVatChange');
							}
						}else{
							GError(eEvent.error);
						}
					}));
				});
				return false;
			});
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault).change();
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).change(GEventHandler(function(eEvent) {
				gThis.Validate(false, this.sRepetition);
			}));
		});
		jField.keydown(function(eEvent) {
			var dSelect = this;
			setTimeout(function() {
				dSelect.Update();
			}, 50);
			return true;
		});
		if (gThis.m_jNode.closest('.statusChange').length) {
			gThis.OnShow();
		}
	};
	
}, oDefaults);

/*
* SORTABLE LIST
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-tree',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sItemNameClass: 'item-name',
		sExpanderClass: 'expander',
		sExpandedClass: 'expanded',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sExpandAllClass: 'expand-all',
		sRetractAllClass: 'retract-all'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sDelete: '_images_panel/icons/buttons/delete.png',
		sSave: '_images_panel/icons/buttons/save.png',
		sRestore: '_images_panel/icons/buttons/clean.png',
		sWaiting: '_images_panel/icons/loading/indicator.gif'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	bSortable: false,
	bClickable: false,
	bDeletable: false,
	oItems: {},
	fOnClick: GCore.NULL,
	fOnSaveOrder: GCore.NULL,
	fOnAdd: GCore.NULL,
	fOnAfterAdd: GCore.NULL,
	fOnDelete: GCore.NULL,
	fOnAfterDelete: GCore.NULL,
	sActive: '',
	sAddItemPrompt: '',
	bPreventDuplicates: true
};

var GFormSortableList = GCore.ExtendClass(GFormField, function() {

	var gThis = this;

	gThis.m_bShown = false;
	gThis.m_jTree;
	gThis.m_jOptions;
	gThis.m_oItems;
	gThis.m_jFieldWrapper;
	gThis.m_jItemPlaceholder;
	gThis.m_jItemDragged;
	gThis.m_oItems = {};

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<input type="hidden"/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jFieldWrapper = $('<div/>');
		gThis.m_jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jNode.append('<label>' + gThis.m_oOptions.sLabel + '</label>');
		gThis.m_jTree = $('<ul/>');
		if(gThis.m_oOptions.iTotal > 0){
			gThis.m_jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		}
		gThis.Update();
		gThis._PrepareOptions();
		window.setTimeout(gThis.ResetExpansion, 500);
	};

	gThis._PrepareOptions = function() {
		var jA;
		gThis.m_jOptions = $('<div class="options"/>');

		if (gThis.m_oOptions.bSortable && (gThis.m_oOptions.fOnSaveOrder instanceof Function)) {
			jA = $('<a class="' + gThis._GetClass('Button') + '" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Save') + '" alt=""/>' + GForm.Language.tree_save_order + '</span>');
			jA.click(function(eEvent) {
				GCore.StartWaiting();
				gThis.m_oOptions.fOnSaveOrder({
					items: gThis.GetOrder()
				}, GCallback(function(eEvent) {
					GCore.StopWaiting();
					GMessage(eEvent.status);
				}));
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));

			jA = $('<a class="' + gThis._GetClass('Button') + '" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Restore') + '" alt=""/>' + GForm.Language.tree_restore_order + '</span>');
			jA.click(function(eEvent) {
				gThis.Update();
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));

		}

		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {

			jA = $('<a class="' + gThis._GetClass('Button') + '" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Add') + '" alt=""/>' + GForm.Language.tree_add_item + '</span>');
			jA.click(function(eEvent) {
				GAlert.DestroyAll();
				GPrompt(gThis.m_oOptions.sAddItemPrompt, function(sName) {
					if (gThis.m_oOptions.bPreventDuplicates) {
						var jSiblings = gThis.m_jTree.children('li');
						for (var i = 0; i < jSiblings.length; i++) {
							if (sName == jSiblings.eq(i).children('a').text()) {
								GAlert.DestroyThis.apply(this);
								GError(GForm.Language.tree_duplicate_entry, GForm.Language.tree_duplicate_entry_description);
								return;
							}
						}
					}
					GCore.StartWaiting();
					gThis.m_oOptions.fOnAdd({
						name: sName
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						if (gThis.m_oOptions.fOnAfterAdd instanceof Function) {
							gThis.m_oOptions.fOnAfterAdd(eEvent.id);
						}
					}));
				});
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));

		}

		if (gThis.m_oOptions.bDeletable && (gThis.m_oOptions.fOnDelete instanceof Function) && gThis.m_jTree.find('.' + gThis._GetClass('Active')).length) {

			var sId = gThis.m_jTree.find('.' + gThis._GetClass('Active')).get(0).sId;
			jA = $('<a class="' + gThis._GetClass('Button') + ' delete" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Delete') + '" alt=""/>' + GForm.Language.tree_delete_item + '</span>');
			jA.click(function(eEvent) {
				GAlert.DestroyAll();
				GWarning(gThis.m_oOptions.sDeleteItemPrompt,'', {
					bAutoExpand: true,
					aoPossibilities: [
						{mLink: function() {
							GCore.StartWaiting();
							gThis.m_oOptions.fOnDelete({
								id: sId
							}, GCallback(function(eEvent) {
								GCore.StopWaiting();
								if (gThis.m_oOptions.fOnAfterDelete instanceof Function) {
									gThis.m_oOptions.fOnAfterDelete();
								}
							}));
						}, sCaption: GForm.Language.tree_ok},
						{mLink: GAlert.DestroyThis, sCaption: GForm.Language.tree_cancel}
					]
				});
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));

		}

		gThis.m_jNode.append(gThis.m_jOptions);
	};

	gThis.GetOrder = function() {
		var jItems = gThis.m_jTree.find('li');
		var aoItems = [];
		for (var i = 0; i < jItems.length; i++) {
			var sId = jItems.eq(i).get(0).sId;
			var sParent = '';
			if (jItems.eq(i).parent().closest('li').length) {
				sParent = jItems.eq(i).parent().closest('li').get(0).sId;
			}
			var jSiblings = jItems.eq(i).parent().children('li');
			var iWeight = jSiblings.index(jItems.eq(i));
			aoItems.push({
				id: sId,
				parent: sParent,
				weight: iWeight
			});
		}
		return aoItems;
	};

	gThis.ResetExpansion = function() {
		gThis.m_jTree.find('li').removeClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li.' + gThis._GetClass('Active')).parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li > label > input:checked').parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
	};

	gThis._WriteSubtree = function(jParent, sParent) {
		if (sParent == undefined) {
			sParent = null;
		}
		var oItems = GCore.FilterObject(gThis.m_oOptions.oItems, function(oItem) {return (oItem.parent == sParent);});
		var aIterationArray = GCore.GetIterationArray(oItems, function(oA, oB) {return (oA.weight - oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			var oItem = oItems[sId];
			jParent.append(gThis._WriteItem(sId, oItem));
		}
	};

	gThis._WriteItem = function(sId, oItem) {
		var jLi = $('<li/>');
		jLi.get(0).sId = sId;
		if (gThis.m_oOptions.sActive == sId) {
			jLi.addClass(gThis._GetClass('Active'));
		}

		if (gThis.m_oOptions.bClickable) {
			var jA = $('<a href="#" class="' + gThis._GetClass('ItemName') + '">' + oItem.name + '</a>');
			jLi.append(jA);
		}
		else {
			jLi.append('<span class="' + gThis._GetClass('ItemName') + '">' + oItem.name + '</span>');
		}
		var jUl = $('<ul/>');
		gThis._WriteSubtree(jUl, sId);
		var jExpander = $('<span class="' + gThis._GetClass('Expander') + '"/>');
		jLi.prepend(jExpander);
		jLi.append(jUl);
		jExpander.css('display', 'none');
		if (oItem.hasChildren || jUl.children('li').length) {
			jExpander.css('display', 'block');
		}
		return jLi;
	};

	gThis.UpdateExpanders = function() {
		gThis.m_jTree.find('li::not(:has(li))').removeClass(gThis._GetClass('Expanded')).children('.' + gThis._GetClass('Expander')).css('display', 'none');
		gThis.m_jTree.find('li:has(li) > .' + gThis._GetClass('Expander')).css('display', 'block');
	};

	gThis.Update = function() {
		gThis.m_jTree.empty();
		gThis._WriteSubtree(gThis.m_jTree);
		gThis.m_jTree.sortable();
		gThis._InitializeNodeEvents();
		gThis.ResetExpansion();
	};

	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_bShown = true;
		}
	};

	gThis._OnClick = GEventHandler(function(eEvent) {
		GCore.StartWaiting();
	});

	gThis._InitializeEvents = function(sRepetition) {
		gThis._InitializeNodeEvents();
	};

	gThis._InitializeNodeEvents = function() {
		gThis.m_jTree.find('.' + gThis._GetClass('Expander')).unbind('click').click(function() {
			if ($(this).closest('li').hasClass(gThis._GetClass('Expanded'))) {
				$(this).closest('li').find('li').andSelf().removeClass(gThis._GetClass('Expanded'));
			}
			else {
				$(this).closest('li').addClass(gThis._GetClass('Expanded'));
				gThis._Expand($(this).closest('li'));
			}
		});
		if (gThis.m_oOptions.bClickable) {
			gThis.m_jTree.find('li').unbind('click').unbind('mousedown').each(function() {
				var sId = $(this).closest('li').get(0).sId;
				$(this).children('a').click(GEventHandler(function(eEvent) {
					if (gThis.m_bIgnoreClick) {
						return false;
					}
					gThis.m_jTree.find('li').removeClass(gThis._GetClass('Active'));
					$(this).closest('li').addClass(gThis._GetClass('Active'));
					gThis._OnClick.apply(this, [eEvent]);
					if (gThis.m_oOptions.fOnClick instanceof Function) {
						gThis.m_oOptions.fOnClick(sId);
					}
					return false;
				}));
			});
		}
		gThis.m_jTree.find('input').unbind('click').click(gThis._OnSelect);
	};

	gThis._OnChildrenLoaded = GEventHandler(function(eEvent) {
		var jUl = $('<ul/>');
		gThis.m_oItems[eEvent.parentNode.get(0).sId] = true;
		for (var i in eEvent.children) {
			jUl.append(gThis._WriteItem(i, eEvent.children[i]));
		}
		eEvent.parentNode.find('ul').remove();
		eEvent.parentNode.append(jUl);
		gThis._InitializeNodeEvents();
	});

}, oDefaults);

/*
* STATIC IMAGE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	sAlt: '',
	sSrc: '',
	oClasses: {
		sFieldClass: 'field-static-image'
	}
};

var GFormStaticImage = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jNode.append('<img id="' + gThis.GetId() + '" src="' + gThis.m_oOptions.sSrc + '" alt="' + gThis.m_oOptions.sAlt + '"/>');
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* STATIC LISTING
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	sTitle: '',
	bCollapsible: false,
	bExpanded: true,
	oClasses: {
		sFieldClass: 'field-static-listing'
	}
};

var GFormStaticListing = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_jListing;
	gThis.m_jCollapseTrigger;
	gThis.m_bExpanded;
	gThis.m_bShown = false;
	
	gThis._PrepareNode = function() {
		gThis.m_bExpanded = gThis.m_oOptions.bExpanded;
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		if ((gThis.m_oOptions.sLabel != undefined) && gThis.m_oOptions.sLabel.length) {
			gThis.m_jNode.append('<label>' + gThis.m_oOptions.sLabel + '</label>');
		}
		gThis.m_jListing = $('<span class="repetition"/>');
		gThis.m_jNode.append(gThis.m_jListing.empty().append(gThis._MakeListing(gThis.m_oOptions.sTitle, gThis.m_oOptions.aoValues)));
	};
	
	gThis._MakeListing = function(sTitle, aoValues) {
		var jListing = $('<div/>');
		jListing.append('<h3>' + sTitle + '</h3>');
		if (gThis.m_oOptions.bCollapsible) {
			if (gThis.m_bExpanded) {
				gThis.m_jCollapseTrigger = $('<a class="trigger" href="#">' + GForm.Language.static_listing_collapse + '</a>');
			}
			else {
				gThis.m_jCollapseTrigger = $('<a class="trigger" href="#">' + GForm.Language.static_listing_expand + '</a>');
			}
			jListing.append(gThis.m_jCollapseTrigger);
		}
		var jDl = $('<dl/>');
		if (!gThis.m_bExpanded) {
			jDl.css('display', 'none');
		}
		var iLength = aoValues.length;
		for (var i = 0; i < iLength; i++) {
			jDl.append('<dt>' + aoValues[i].sCaption + '</dt>');
			jDl.append('<dd>' + aoValues[i].sValue + '</dd>');
		}
		jListing.append(jDl);
		return jListing;
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		gThis.m_bShown = true;
		gThis._InitializeExpansion();
	};
	
	gThis.ChangeItems = function(aoItems, sTitle) {
		if (sTitle == undefined) {
			sTitle = gThis.m_oOptions.sTitle;
		}
		gThis.m_jListing.empty().append(gThis._MakeListing(sTitle, aoItems));
		if (gThis.m_bShown) {
			gThis._InitializeExpansion();
		}
	};
	
	gThis._InitializeExpansion = function() {
		if (gThis.m_jCollapseTrigger != undefined) {
			gThis.m_jCollapseTrigger.click(GEventHandler(function(eEvent) {
				gThis.m_bExpanded = !gThis.m_bExpanded;
				if (gThis.m_bExpanded) {
					gThis.m_jListing.find('dl').slideDown(300);
					gThis.m_jCollapseTrigger.text(GForm.Language.static_listing_collapse);
				}
				else {
					gThis.m_jListing.find('dl').slideUp(300);
					gThis.m_jCollapseTrigger.text(GForm.Language.static_listing_expand);
				}
				return false;
			}));
		}
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* STATIC TEXT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-static-text'
	}
};

var GFormStaticText = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jNode.append(gThis.m_oOptions.sText);
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* SUBMIT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-submit',
		sButtonClass: 'button'
	},
	sIcon: ''
};

var GFormSubmit = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jNode.append('<button class="' + gThis._GetClass('Button') + '" type="submit" name="' + gThis.GetName() + '"><span>' + gThis.m_oOptions.sLabel + '</span></button>');
		//gThis.m_jNode.append('<span class="' + gThis._GetClass('Button') + '"><span>' + ((gThis.m_oOptions.sIcon != '') ? '<img src="' + GCore.DESIGN_PATH + gThis.m_oOptions.sIcon + '" alt=""/>' : '') + '<input type="submit" name="' + gThis.GetName() + '" value="' + gThis.m_oOptions.sLabel + '"/></span></span>');
	};
	
}, oDefaults);


/*
* TECHNICAL DATA EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-technical-data-editor',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sDisabledClass: 'disabled',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-repetition',
		sRemoveRepetitionClass: 'remove-repetition',
		sGroupClass: 'group',
		sAttributeClass: 'attribute'
	},
	oImages: {
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sSaveIcon: '_images_panel/icons/datagrid/save.png',
		sAddIcon: '_images_panel/icons/datagrid/add.png',
		sBlankIcon: '_images_panel/icons/buttons/blank.png',
		sEditIcon: '_images_panel/icons/datagrid/edit.png',
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	fGetSets: GCore.NULL,
	fGetTechnicalAttributesForSet: GCore.NULL,
	fSaveSet: GCore.NULL,
	fDeleteSet: GCore.NULL,
	fSaveAttribute: GCore.NULL,
	fDeleteAttribute: GCore.NULL,
	fSaveAttributeGroup: GCore.NULL,
	fDeleteAttributeGroup: GCore.NULL,
	fGetValuesForAttribute: GCore.NULL,
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	aTechnicalAttributes: [],
	aAttributeGroups: []
};

var GFormTechnicalDataEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis.m_bSetAltered = false;
	gThis.m_bIgnoreSetChange = false;
	
	gThis.m_aoSets = [];
	gThis.m_aoAttributes = [];
	gThis.m_oValues = {};
	
	gThis.m_sCurrentSet = '';
	
	gThis.m_jSets;
	gThis.m_jAttributes;
	gThis.m_jAdd;
	gThis.m_jFields;
	
	gThis.m_sGroupOptions;
	gThis.m_sAttributeOptions;
	
	gThis.m_iLoads = ((gThis.m_oOptions.sSetId != undefined) && gThis.m_oOptions.sSetId) ? 0 : 1;
	
	gThis._PrepareNode = function() {
		
		gThis.m_sCurrentSet = gThis.m_oOptions.sSetId;
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jSets = $('<div/>');
		gThis.m_jAttributes = $('<fieldset/>');
		
		gThis.m_jFields = $('<div/>');
		
		gThis.m_jNode.append(gThis.m_jSets);
		gThis.m_jNode.append($('<div class="technical-data-info"><div class="groups">Grupa atrybutów</div><div class="attributes">Atrybuty</div><div class="values">Wartości</div></div>'));
		gThis.m_jNode.append(gThis.m_jAttributes);
		gThis.m_jNode.append(gThis.m_jFields);
		
	};
	
	gThis.UpdateFields = function() {
		gThis.m_jFields.empty();
		var sFields = '';
		sFields += ('<input type="hidden" name="' + gThis.GetName() + '[set]" value="' + gThis.m_sCurrentSet + '"/>');
		for (var i = 0; (gThis.m_aoAttributes[i] != undefined); i++) {
			sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][id]" value="' + gThis.m_aoAttributes[i].id + '"/>');
			if (String(gThis.m_aoAttributes[i].id).substr(0, 3) == 'new') {
				for (var l in GCore.aoLanguages) {
					sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][caption][' + l + ']" value="' + ((gThis.m_aoAttributes[i].caption[l] != undefined) ? gThis.m_aoAttributes[i].caption[l] : '') + '"/>');
				}
			}
			for (var j = 0; (gThis.m_aoAttributes[i].children[j] != undefined); j++) {
				sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][id]" value="' + gThis.m_aoAttributes[i].children[j].id + '"/>');
				sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][type]" value="' + gThis.m_aoAttributes[i].children[j].type + '"/>');
				if (String(gThis.m_aoAttributes[i].children[j].id).substr(0, 3) == 'new') {
					for (var l in GCore.aoLanguages) {
						var caption = ((gThis.m_aoAttributes[i].children[j].caption[l] != undefined) ? gThis.m_aoAttributes[i].children[j].caption[l] : '');
						sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][caption][' + l + ']" value="' + caption.replace('"',"''") + '"/>');
					}
				}
				switch (gThis.m_aoAttributes[i].children[j].type) {
					case GFormTechnicalDataEditor.FIELD_STRING:
						sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][value]" value="' + ((gThis.m_aoAttributes[i].children[j].value != undefined) ? gThis.m_aoAttributes[i].children[j].value : '') + '"/>');
						break;
					case GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING:
						for (var l in GCore.aoLanguages) {
							sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][value][' + l + ']" value="' + (((gThis.m_aoAttributes[i].children[j].value != undefined) && (gThis.m_aoAttributes[i].children[j].value[l] != undefined)) ? gThis.m_aoAttributes[i].children[j].value[l] : '') + '"/>');
						}
						break;
					case GFormTechnicalDataEditor.FIELD_TEXT:
						sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][value]" value="' + ((gThis.m_aoAttributes[i].children[j].value != undefined) ? gThis.m_aoAttributes[i].children[j].value : '') + '"/>');
						break;
					case GFormTechnicalDataEditor.FIELD_IMAGE:
						sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][value]" value="' + ((gThis.m_aoAttributes[i].children[j].value != undefined) ? gThis.m_aoAttributes[i].children[j].value : '') + '"/>');
						break;
					case GFormTechnicalDataEditor.FIELD_BOOLEAN:
						sFields += ('<input type="hidden" name="' + gThis.GetName() + '[groups][' + i + '][attributes][' + j + '][value]" value="' + (gThis.m_aoAttributes[i].children[j].value ? '1' : '0') + '"/>');
						break;
				}
			}
		}
		gThis.m_jFields.html(sFields);
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		gThis.m_bShown = true;
		gThis.LoadSets();
	};
	
	gThis.OnFocus = function(eEvent) {
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
	};
	
	gThis.OnReset = function() {
		gThis.m_sCurrentSet = gThis.m_oOptions.sSetId;
		gThis.m_iLoads = 0;
		gThis.LoadSets();
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue == undefined) {
			return;
		}
		gThis.m_aoAttributes = [];
		for (var i = 0; i < mValue.length; i++) {
			var bFound = false;
			var l;
			for (l in gThis.m_oOptions.aAttributeGroups) {
				if (gThis.m_oOptions.aAttributeGroups[l].id == mValue[i].id) {
					bFound = true;
					break;
				}
			}
			if (!bFound) {
				continue;
			}
			var aoChildren = [];
			if (mValue[i].children != undefined) {
				for (var j = 0; j < mValue[i].children.length; j++) {
					var oAttribute = mValue[i].children[j];
					var bFound = false;
					var k;
					for (k in gThis.m_oOptions.aTechnicalAttributes) {
						if (gThis.m_oOptions.aTechnicalAttributes[k].id == oAttribute.id) {
							bFound = true;
							break;
						}
					}
					if (!bFound) {
						continue;
					}
					aoChildren.push($.extend({}, gThis.m_oOptions.aTechnicalAttributes[k], {
						value: oAttribute.value,
						set_id: oAttribute.set_id
					}));
				}
			}
			gThis.m_aoAttributes.push($.extend({},gThis.m_oOptions.aAttributeGroups[l], {
				children: aoChildren,
				set_id: mValue[i].set_id
			}));
		}
		gThis._WriteTechnicalAttributes();
	};
	
	gThis._InitializeEvents = function(sRepetition) {
	};
	
	gThis.LoadSets = function(fOnSuccess) {
		gThis.m_jSets.html('<div class="field-select"><label>' + GForm.Language.technical_data_choose_set + '</label><span class="repetition"><span class="waiting"></span></span></div>');
		var sProductId = gThis.m_oOptions.sProductId;
		var asCategoryIds = [];
		gThis.m_oOptions.fGetSets({
			productId: sProductId,
			categoryIds: asCategoryIds
		}, GCallback(gThis._OnSetsLoad, {
			fOnSuccess: fOnSuccess
		}));
	};
	
	gThis._OnSetsLoad = GEventHandler(function(eEvent) {
		gThis.m_aoSets = eEvent.aoSets;
		gThis._WriteSets();
		if ((gThis.m_sCurrentSet == undefined) && (gThis.m_aoSets.length > 0)) {
			//gThis.LoadTechnicalAttributesForSet(gThis.m_aoSets[0].id);
		}
		else {
			var bFound = false;
			for (var i in gThis.m_aoSets) {
				if (gThis.m_aoSets[i].id == gThis.m_sCurrentSet) {
					bFound = true;
					break;
				}
			}
			if (bFound) {
				gThis.LoadTechnicalAttributesForSet(gThis.m_sCurrentSet);
			}
			else {
//				gThis.m_bSetAltered = false;
			}
		}
		if (eEvent.fOnSuccess != undefined) {
			eEvent.fOnSuccess(eEvent);
		}
	});
	
	gThis._WriteSets = function() {
		gThis.m_jSets.empty();
		var jSelect = $('<select id="' + gThis.GetName() + '__set"/>');
		jSelect.append('<option value="">---</option>');
		for (var i = 0; i < gThis.m_aoSets.length; i++) {
			var oSet = gThis.m_aoSets[i];
			jSelect.append('<option' + ((oSet.id == gThis.m_sCurrentSet) ? ' selected="selected"' : '') + ' value="' + oSet.id + '"' + (oSet.recommended ? ' class="strong"' : '') + '>' + oSet.caption + '</option>');
		}
		var jField = $('<div class="field-select"><label for="' + gThis.GetName() + '__set">' + GForm.Language.technical_data_choose_set + '</label><span class="repetition"><span class="field"></span></span></div>');
		
		jField.find('.field').append(jSelect).after($('<span class="suffix"></span>'));
		gThis.m_jSets.append(jField);
		jSelect.GSelect();
		jSelect.change(function(eEvent) {
			gThis._OnSetchange(eEvent);
		});
		
	};
	
	gThis._OnSetchange = new GEventHandler(function(eEvent) {
		var sChosenSet = $(eEvent.currentTarget).val();
		if (gThis.m_bIgnoreSetChange) {
			gThis.m_bIgnoreSetChange = false;
			return;
		}
		if (sChosenSet == '') {
			gThis.m_sCurrentSet = '';
			gThis.UpdateFields();
			return;
		}
		if (gThis.m_bSetAltered) {
			GAlert(GForm.Language.technical_data_unsaved_changes, GForm.Language.technical_data_unsaved_changes_description, {
				aoPossibilities: [
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
							gThis.LoadTechnicalAttributesForSet(sChosenSet);
						}),
						sCaption: GForm.Language.technical_data_unsaved_changes_discard
					},
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
							gThis.m_bIgnoreSetChange = true;
							gThis.m_jSets.find('select').val(gThis.m_sCurrentSet).change();
						}),
						sCaption: GForm.Language.technical_data_unsaved_changes_cancel
					}
				]
			});
		}
		else {
			gThis.LoadTechnicalAttributesForSet(sChosenSet);
		}
	});
	
	gThis.LoadTechnicalAttributesForSet = function(sId) {
		gThis.m_sCurrentSet = sId;
		if (gThis.m_iLoads++ < 1) {
			gThis._WriteTechnicalAttributes();
			return;
		}
		gThis.m_oOptions.fGetTechnicalAttributesForSet({
			setId: gThis.m_sCurrentSet
		}, GCallback(gThis._OnTechnicalAttributesLoad));
	};
	
	gThis._OnTechnicalAttributesLoad = GEventHandler(function(eEvent) {
		gThis.m_aoAttributes = eEvent.aoAttributes;
		gThis.m_bSetAltered = false;
		gThis._WriteTechnicalAttributes();
	});
	
	gThis._WriteTechnicalAttributes = function() {
		gThis._UpdateGroupOptions();
		gThis._UpdateAttributeOptions();
		gThis.m_jAttributes.empty();
		gThis.m_jAdd = $('<a href="#" class="add-repetition"/>');
		gThis.m_jAdd.append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.technical_data_add_new_group + '" title="' + GForm.Language.technical_data_add_new_group + '"/>');
		gThis.m_jAttributes.append(gThis.m_jAdd);
		gThis.m_jAdd.click(GEventHandler(function(eEvent) {
			var oGroup = gThis._CreateAttributeGroup();
			gThis.m_aoAttributes.push(oGroup);
			gThis.m_bSetAltered = true;
			gThis._UpdateGroupOptions();
			gThis.AddAttributeGroup(oGroup);
			gThis._UpdateIndices();
			gThis.UpdateFields();
			return false;
		}));
		for (var i = 0; (gThis.m_aoAttributes[i] != undefined); i++) {
			var oGroup = gThis.m_aoAttributes[i];
			gThis.AddAttributeGroup(oGroup);
		}
		gThis._UpdateIndices();
		gThis.UpdateValues();
	};
	
	gThis._CreateAttributeGroup = function() {
		var oGroup = {
			id: 'new-' + GFormTechnicalDataEditor.s_iNewId++,
			caption: {},
			children: []
		};
		for (var l in GCore.aoLanguages) {
			oGroup.caption[l] = '';
		}
		return oGroup;
	};
	
	gThis._CreateAttribute = function() {
		var oAttribute = {
			id: 'new-' + GFormTechnicalDataEditor.s_iNewId++,
			caption: {},
			type: GFormTechnicalDataEditor.FIELD_STRING,
			value: ''
		};
		for (var l in GCore.aoLanguages) {
			oAttribute.caption[l] = '';
		}
		return oAttribute;
	};
	
	gThis.DeleteAttributeGroup = function(iGroup) {
		gThis.m_aoAttributes.splice(iGroup, 1);
		gThis.m_jAttributes.children('.group:eq(' + iGroup + ')').remove();
		gThis._UpdateGroupOptions();
		gThis._UpdateIndices();
		gThis.UpdateFields();
	};
	
	gThis.DeleteAttribute = function(iGroup, iAttribute) {
		gThis.m_aoAttributes[iGroup].children.splice(iAttribute, 1);
		gThis.m_jAttributes.children('.group:eq(' + (iGroup + 1) + ') .attributes .attribute:eq(' + iAttribute + ')').remove();
		gThis._UpdateAttributeOptions();
		gThis._UpdateIndices();
		gThis.UpdateFields();
	};
	
	gThis._UpdateAttributeOptions = function() {
		gThis.m_sAttributeOptions = '';
		for (var j = 0; j < gThis.m_oOptions.aTechnicalAttributes.length; j++) {
			var oTechnicalAttribute = gThis.m_oOptions.aTechnicalAttributes[j];
			gThis.m_sAttributeOptions += '<option value="' + oTechnicalAttribute.id + '">' + oTechnicalAttribute.caption[GCore.iActiveLanguage] + '</option>';
		}
	};
	
	gThis._UpdateGroupOptions = function() {
		gThis.m_sGroupOptions = '';
		
		var aSelectedAttributes = [];
		$.each(gThis.m_aoAttributes, function(a, attribute){
			aSelectedAttributes.push(attribute.id);
		});
		gThis.m_sGroupOptions += '<option value="">---</option>';
		for (var j = 0; j < gThis.m_oOptions.aAttributeGroups.length; j++) {
			var oAttributeGroup = gThis.m_oOptions.aAttributeGroups[j];
			if ($.inArray(oAttributeGroup.id, aSelectedAttributes) != -1) {
				continue;
			}
			if(gThis.m_sCurrentSet > 0){
				if(oAttributeGroup.set_id != undefined && oAttributeGroup.set_id == gThis.m_sCurrentSet){
					gThis.m_sGroupOptions += '<option value="' + oAttributeGroup.id + '">' + oAttributeGroup.caption[GCore.iActiveLanguage] +'</option>';
				}
			}else{
				gThis.m_sGroupOptions += '<option value="' + oAttributeGroup.id + '">' + oAttributeGroup.caption[GCore.iActiveLanguage] +'</option>';
			}
		}
	}; 
	
	gThis.AddAttributeGroup = function(oGroup) {
		if (oGroup == undefined) {
			oGroup = {};
		}
		var jGroup = $('<div class="' + gThis._GetClass('Group') + ' GFormRepetition"/>');
		var jGroupSelect = $('<select/>').addClass('attribute-group');
		jGroupSelect.html(gThis.m_sGroupOptions);
		if(oGroup.set_id > 0){
			jGroup.append($('<div class="field-technical-group"/>').prepend($('<span class="constant" />').html(oGroup.caption[GCore.iActiveLanguage])));
			gThis.m_jAttributes.append(jGroup);
			var jAttributes = $('<div class="attributes"/>');
			jGroup.append(jAttributes);
			var jAddAttribute = $('<a href="#" class="add-attribute"><img src="' + gThis._GetImage('Icon') + '"/></a>');
			jAttributes.append(jAddAttribute);
			jAddAttribute.hide();
			if (oGroup.children != undefined) {
				for (var j = 0; (oGroup.children[j] != undefined); j++) {
					var oAttribute = oGroup.children[j];
					gThis.AddAttribute(jAttributes, oAttribute, oGroup.id);
				}
			}
		}else{
			var jGroupEdit = $('<img class="edit" src="' + gThis._GetImage('EditIcon') + '" alt="' + GForm.Language.technical_data_edit_group + '" title="' + GForm.Language.technical_data_edit_group + '"/>');
			jGroup.append($('<div class="field-select"/>').append($('<span class="suffix"/>').append(jGroupEdit)).prepend($('<span class="field"/>').append(jGroupSelect)));
			jGroupEdit.click(gThis._OnGroupEditClick);
			jGroupSelect.GComboBox();
			jGroupSelect.val(oGroup.id).change();
			jGroupSelect.change(GEventHandler(function(eEvent) {
				$(this).closest('.field-select').find('input:text').change();
			}));
			if (oGroup.caption != undefined) {
				jGroup.find('input:text').val(oGroup.caption[GCore.iActiveLanguage]);
			}
			jGroup.find('input:text').change(gThis._OnGroupChange);
			gThis.m_jAttributes.append(jGroup);
			var jAttributes = $('<div class="attributes"/>');
			jGroup.append(jAttributes);
			var jAddAttribute = $('<a href="#" class="add-attribute"><img src="' + gThis._GetImage('AddIcon') + '" title="' + GForm.Language.technical_data_add_new_attribute + '" alt="' + GForm.Language.technical_data_add_new_attribute + '"/></a>');
			jAttributes.append(jAddAttribute);
			jAddAttribute.click(gThis._OnAttributeAddClick);
			if (oGroup.children != undefined) {
				for (var j = 0; (oGroup.children[j] != undefined); j++) {
					var oAttribute = oGroup.children[j];
					gThis.AddAttribute(jAttributes, oAttribute);
				}
			}
			var jDelete = $('<a href="#" class="delete-repetition"/>');
			jDelete.append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.technical_data_delete_group + '" title="' + GForm.Language.technical_data_delete_group + '"/>');
			jDelete.click(gThis._OnGroupDeleteClick);
			jGroup.append(jDelete);
		}
		
	};
	
	gThis._OnGroupEditClick = GEventHandler(function(eEvent) {
		var iGroupIndex = $(this).closest('.group').data('iGroupIndex');
		var oGroup = gThis.m_aoAttributes[iGroupIndex];
		var jOverlay = $('<div class="technical-data-detail-editor"/>').data('iGroupIndex', iGroupIndex);
		$('body').append(jOverlay);
		jOverlay.GShadow();
		jOverlay.GOverlay({
			fClick: GEventHandler(function(eEvent) {
				jOverlay.remove();
			})
		});
		jOverlay.css({
			left: $(this).offset().left - 161,
			top: $(this).offset().top + 21
		});
		for (var l in GCore.aoLanguages) {
			var jLanguage = $('<div class="field-text"><span class="prefix"><img src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/' + GCore.aoLanguages[l].flag + '" alt="' + GCore.aoLanguages[l].name + '"/></span><span class="field"><input class="language-' + l + '" type="text" value="' + ((oGroup.caption[l] == undefined) ? '' : oGroup.caption[l]) + '"/></span></div>');
			jOverlay.append(jLanguage);
		}
		var jSaveButton = $('<a class="button ok" href="#"><span>' + GForm.Language.technical_data_save_group + '</span></a>');
		jSaveButton.click(GEventHandler(function(eEvent) {
			var iGroupIndex = $(this).closest('.technical-data-detail-editor').data('iGroupIndex');
			for (var l in GCore.aoLanguages) {
				gThis.m_aoAttributes[iGroupIndex].caption[l] = $(this).closest('.technical-data-detail-editor').find('.language-' + l).val();
			}
			GOverlay.RemoveAll();
			gThis.m_oOptions.fSaveAttributeGroup({
				attributeGroupId: oGroup.id,
				attributeGroupName: oGroup.caption
			}, GCallback(gThis._OnAttributeGroupSave, {
				iGroupIndex: iGroupIndex
			}));
			return false;
		}));
		var jDeleteButton = $('<a class="button delete" href="#"><span>' + GForm.Language.technical_data_delete_group_permanently + '</span></a>');
		jDeleteButton.click(GEventHandler(function(eEvent) {
			var iGroupIndex = $(this).closest('.technical-data-detail-editor').data('iGroupIndex');
			var sAttributeGroup = gThis.m_aoAttributes[iGroupIndex].id;
			GOverlay.RemoveAll();
			GAlert(GForm.Language.technical_data_delete_attribute_group, GForm.Language.technical_data_delete_attribute_group_description, {
				aoPossibilities: [
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
							for (var i in gThis.m_oOptions.aAttributeGroups) {
								if (gThis.m_oOptions.aAttributeGroups[i].id == gThis.m_aoAttributes[iGroupIndex].id) {
									gThis.m_oOptions.aAttributeGroups.splice(i, 1);
									break;
								}
							}
							gThis.DeleteAttributeGroup(iGroupIndex);
							gThis.m_oOptions.fDeleteAttributeGroup({
								attributeGroupId: sAttributeGroup
							}, GCallback(gThis._OnAttributeGroupDeleted));
						}),
						sCaption: GMessageBar.Language.ok
					},
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
						}),
						sCaption: GMessageBar.Language.cancel
					}
				]
			});
		}));
		jOverlay.append(jSaveButton).append(jDeleteButton);
	});
	
	gThis._OnGroupDeleteClick = GEventHandler(function(eEvent) {
		gThis.DeleteAttributeGroup($(this).closest('.group').data('iGroupIndex'));
		gThis.m_bSetAltered = true;
		return false;
	});
	
	gThis._OnGroupChange = GEventHandler(function(eEvent) {
		gThis.m_bSetAltered = true;
		var bFound = false;
		for (var k in gThis.m_oOptions.aAttributeGroups) {
			if (gThis.m_oOptions.aAttributeGroups[k].caption[GCore.iActiveLanguage] == $(this).val()) {
				bFound = true;
				break;
			}
		}
		if (bFound) {
			gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].id = gThis.m_oOptions.aAttributeGroups[k].id;					
			for (var l in GCore.aoLanguages) {
				gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].caption[l] = (gThis.m_oOptions.aAttributeGroups[k].caption[l] == undefined) ? '' : gThis.m_oOptions.aAttributeGroups[k].caption[l];
			}
		}
		else {
			gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].id = 'new-' + GFormTechnicalDataEditor.s_iNewId++;
			for (var l in GCore.aoLanguages) {
				if (l == GCore.iActiveLanguage) {
					gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].caption[l] = $(this).val();
					continue;
				}
				gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].caption[l] = '';
			}
		}
		gThis.UpdateFields();
	});
	
	gThis._OnAttributeAddClick = GEventHandler(function(eEvent) {
		var oNewAttribute = gThis._CreateAttribute();
		gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].children.push(oNewAttribute);
		gThis.AddAttribute($(this).closest('.group').children('.attributes:first'), oNewAttribute, gThis.m_aoAttributes[$(this).closest('.group').data('iGroupIndex')].id);
		gThis._UpdateIndices();
		gThis.UpdateFields();
		return false;
	});
	
	gThis.AddAttribute = function(jGroup, oAttribute, iGroupIndex) {
		if (oAttribute == undefined) {
			oAttribute = {};
		}
		var jAttribute = $('<div class="' + gThis._GetClass('Attribute') + '"/>');
		var jAttributeSelect = $('<select/>');
		
		var aoActiveGroupAttributes = [];
		for (var j = 0; j < gThis.m_oOptions.aAttributeGroups.length; j++) {
			var oGroup = gThis.m_oOptions.aAttributeGroups[j];
			if(oGroup.id == iGroupIndex){
				aoActiveGroupAttributes = oGroup.attributes;
			}
		}
		
		var sAttributeOptions = '<option value="">---</option>';
		for (var j = 0; j < gThis.m_oOptions.aTechnicalAttributes.length; j++) {
			var oTechnicalAttribute = gThis.m_oOptions.aTechnicalAttributes[j];
			if ($.inArray(oTechnicalAttribute.id, aoActiveGroupAttributes) != -1) {
				sAttributeOptions += '<option value="' + oTechnicalAttribute.id + '">' + oTechnicalAttribute.caption[GCore.iActiveLanguage] + '</option>';
			}
		}
			
		jAttributeSelect.html(sAttributeOptions);
		if(oAttribute.set_id > 0){
			jAttribute.append($('<div class="field-technical-attribute"/>').prepend($('<span class="constant"/>').html(oAttribute.caption[GCore.iActiveLanguage])));
			jGroup.children('.add-attribute').before(jAttribute);
			var jDelete = $('<img src="' + gThis._GetImage('BlankIcon') + '" />');
			jAttribute.find('.field-select:first').prepend($('<span class="prefix"/>').append(jDelete));
			var jValue = $('<div class="value"/>');
			jAttribute.append(jValue);
		}else{
			var jAttributeEdit = $('<img class="edit" src="' + gThis._GetImage('EditIcon') + '" alt="' + GForm.Language.technical_data_edit_attribute + '" title="' + GForm.Language.technical_data_edit_attribute + '"/>');
			jAttribute.append($('<div class="field-select"/>').append($('<span class="suffix"/>').append(jAttributeEdit)).prepend($('<span class="field"/>').append(jAttributeSelect)));
			jAttributeEdit.click(gThis._OnAttributeEditClick);
			jAttributeSelect.GComboBox();
			jAttributeSelect.val(oAttribute.id).change();
			jAttributeSelect.change(GEventHandler(function(eEvent) {
				$(this).closest('.field-select').find('input:text').change();
			}));
			if (oAttribute.caption != undefined) {
				jAttribute.find('input:text').val(oAttribute.caption[GCore.iActiveLanguage]);
			}
			jAttribute.find('input:text').change(gThis._OnAttributeChange);
			jGroup.children('.add-attribute').before(jAttribute);
			var jDelete = $('<a href="#"/>');
			jDelete.append('<img src="' + gThis._GetImage('DeleteIcon') + '" alt="' + GForm.Language.technical_data_delete_attribute + '" title="' + GForm.Language.technical_data_delete_attribute + '"/>');
			jDelete.click(gThis._OnAttributeDeleteClick);
			jAttribute.find('.field-select:first').prepend($('<span class="prefix"/>').append(jDelete));
			var jValue = $('<div class="value"/>');
			jAttribute.append(jValue);
		}
		
		gThis._UpdateValueField(oAttribute, jAttribute);
	};
	
	gThis._OnAttributeEditClick = GEventHandler(function(eEvent) {
		var iGroupIndex = $(this).closest('.group').data('iGroupIndex');
		var iAttributeIndex = $(this).closest('.attribute').data('iAttributeIndex');
		var oAttribute = gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex];
		var jOverlay = $('<div class="technical-data-detail-editor"/>').data('iGroupIndex', iGroupIndex).data('iAttributeIndex', iAttributeIndex);
		$('body').append(jOverlay);
		jOverlay.GShadow();
		jOverlay.GOverlay({
			fClick: GEventHandler(function(eEvent) {
				jOverlay.remove();
			})
		});
		jOverlay.css({
			left: $(this).offset().left - 161,
			top: $(this).offset().top + 21
		});
		for (var l in GCore.aoLanguages) {
			var jLanguage = $('<div class="field-text"><span class="prefix"><img src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/' + GCore.aoLanguages[l].flag + '" alt="' + GCore.aoLanguages[l].name + '"/></span><span class="field"><input class="language-' + l + '" type="text" value="' + ((oAttribute.caption[l] == undefined) ? '' : oAttribute.caption[l]) + '"/></span></div>');
			jOverlay.append(jLanguage);
		}
		var jTypeSelect = $('<select class="type"/>');
		var aoTypes = [
			{id: GFormTechnicalDataEditor.FIELD_STRING, caption: GForm.Language.technical_data_value_type_string},
			{id: GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING, caption: GForm.Language.technical_data_value_type_multilingual_string},
			{id: GFormTechnicalDataEditor.FIELD_TEXT, caption: GForm.Language.technical_data_value_type_text},
			//{id: GFormTechnicalDataEditor.FIELD_IMAGE, caption: GForm.Language.technical_data_value_type_image},
			{id: GFormTechnicalDataEditor.FIELD_BOOLEAN, caption: GForm.Language.technical_data_value_type_boolean}
		];
		for (var i = 0; i < aoTypes.length; i++) {
			jTypeSelect.append('<option' + ((aoTypes[i].id == oAttribute.type) ? ' selected="selected"' : '') + ' value="' + aoTypes[i].id + '">' + aoTypes[i].caption + '</option>');
		}
		jOverlay.append($('<div class="field-select"/>').append($('<span class="field"/>').append(jTypeSelect)));
		jTypeSelect.GSelect();
		var jSaveButton = $('<a class="button ok" href="#"><span>' + GForm.Language.technical_data_save_attribute + '</span></a>');
		jSaveButton.click(GEventHandler(function(eEvent) {
			var iAttributeIndex = $(this).closest('.technical-data-detail-editor').data('iAttributeIndex');
			var iGroupIndex = $(this).closest('.technical-data-detail-editor').data('iGroupIndex');
			for (var i in gThis.m_aoAttributes) {
				for (var j in gThis.m_aoAttributes[i].children) {
					if (gThis.m_aoAttributes[i].children[j].id != gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].id) {
						continue;
					}
					gThis.m_aoAttributes[i].children[j].type = parseInt($(this).closest('.technical-data-detail-editor').find('.type').val());
					for (var l in GCore.aoLanguages) {
						gThis.m_aoAttributes[i].children[j].caption[l] = $(this).closest('.technical-data-detail-editor').find('.language-' + l).val();
					}
				}
			}
			oAttribute = gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex];
			GOverlay.RemoveAll();
			gThis.m_oOptions.fSaveAttribute({
				attributeId: oAttribute.id,
				attributeName: oAttribute.caption,
				attributeType: oAttribute.type
			}, GCallback(gThis._OnAttributeSave, {
				iGroupIndex: iGroupIndex,
				iAttributeIndex: iAttributeIndex
			}));
			return false;
		}));
		var jDeleteButton = $('<a class="button delete" href="#"><span>' + GForm.Language.technical_data_delete_attribute_permanently + '</span></a>');
		jDeleteButton.click(GEventHandler(function(eEvent) {
			var iGroupIndex = $(this).closest('.technical-data-detail-editor').data('iGroupIndex');
			var iAttributeIndex = $(this).closest('.technical-data-detail-editor').data('iAttributeIndex');
			var sAttribute = gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].id;
			GOverlay.RemoveAll();
			GAlert(GForm.Language.technical_data_delete_attribute, GForm.Language.technical_data_delete_attribute_description, {
				aoPossibilities: [
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
							for (var i in gThis.m_oOptions.aTechnicalAttributes) {
								if (gThis.m_oOptions.aTechnicalAttributes[i].id == gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].id) {
									gThis.m_oOptions.aTechnicalAttributes.splice(i, 1);
									break;
								}
							}
							gThis.DeleteAttribute(iGroupIndex, iAttributeIndex);
							gThis.m_oOptions.fDeleteAttribute({
								attributeId: sAttribute
							}, GCallback(gThis._OnAttributeDeleted));
						}),
						sCaption: GMessageBar.Language.ok
					},
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
						}),
						sCaption: GMessageBar.Language.cancel
					}
				]
			});
		}));
		jOverlay.append(jSaveButton).append(jDeleteButton);
	});
	
	gThis._OnAttributeDeleteClick = GEventHandler(function(eEvent) {
		gThis.DeleteAttribute($(this).closest('.attribute').data('iGroupIndex'), $(this).closest('.attribute').data('iAttributeIndex'));
		gThis.m_bSetAltered = true;
		return false;
	});
	
	gThis._OnAttributeChange = GEventHandler(function(eEvent) {
		gThis.m_bSetAltered = true;
		var iGroupIndex = $(this).closest('.attribute').data('iGroupIndex');
		var iAttributeIndex = $(this).closest('.attribute').data('iAttributeIndex');
		var bFound = false;
		for (var k in gThis.m_oOptions.aTechnicalAttributes) {
			if (gThis.m_oOptions.aTechnicalAttributes[k].caption[GCore.iActiveLanguage] == $(this).val()) {
				bFound = true;
				break;
			}
		}
		if (bFound) {
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].id = gThis.m_oOptions.aTechnicalAttributes[k].id;
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].type = gThis.m_oOptions.aTechnicalAttributes[k].type;
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = '';
			for (var l in GCore.aoLanguages) {
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].caption[l] = (gThis.m_oOptions.aTechnicalAttributes[k].caption[l] == undefined) ? '' : gThis.m_oOptions.aTechnicalAttributes[k].caption[l];
			}
		}
		else {
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].id = 'new-' + GFormTechnicalDataEditor.s_iNewId++;
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].type = GFormTechnicalDataEditor.FIELD_STRING;
			gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = '';
			for (var l in GCore.aoLanguages) {
				if (l == GCore.iActiveLanguage) {
					gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].caption[l] = $(this).val();
					continue;
				}
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].caption[l] = '';
			}
			gThis.m_oOptions.aTechnicalAttributes.push(gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex]);
		}
		gThis._UpdateValueField(gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex], $(this).closest('.attribute'));
		gThis.UpdateFields();
	});
	
	gThis._UpdateValueField = function(oAttribute, jAttribute) {
		
		var jValue = jAttribute.find('.value').empty();
		switch (oAttribute.type) {
			case GFormTechnicalDataEditor.FIELD_STRING:
				gThis._WriteValueTypeString(jValue);
				break;
			case GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING:
				gThis._WriteValueTypeMultilingualString(jValue);
				break;
			case GFormTechnicalDataEditor.FIELD_TEXT:
				gThis._WriteValueTypeTextArea(jValue);
				break;
			case GFormTechnicalDataEditor.FIELD_IMAGE:
				gThis._WriteValueTypeImage(jValue);
				break;
			case GFormTechnicalDataEditor.FIELD_BOOLEAN:
				gThis._WriteValueTypeBoolean(jValue);
				break;
			case GFormTechnicalDataEditor.FIELD_SELECT:
				gThis._WriteValueTypeSelect(jValue);
				break;
		}
	};
	
	gThis._WriteValueTypeString = function(jTarget) {
		var jInput = $('<input type="text"/>');
		jInput.focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		}));
		var jInputNode = $('<div class="field-text"><span class="field"/></div>').append($('<span class="suffix"/>')).find('.field').append(jInput);
		jTarget.append(jInputNode.parent());
		jInput.change(gThis._OnChangeValue);
	};
	
	gThis._WriteValueTypeSelect = function(jTarget) {
		var jInput = $('<input type="text"/>');
		jInput.focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		}));
		var jInputNode = $('<div class="field-text"><span class="field"/></div>').append($('<span class="suffix"/>')).find('.field').append(jInput);
		jTarget.append(jInputNode.parent());
		jInput.change(gThis._OnChangeValue);
	};
	
	gThis._WriteValueTypeImage = function(jTarget) {
		gThis.m_jFileSelector = $('<div style="clear: both; padding-top: 10px;"/>');
		gThis.m_jSelectedFileName = $('<span class="filename"/>');
		gThis.m_jFileSelector.append(gThis.m_jSelectedFileName);
		gThis.m_jSwfUpload = $('<div class="' + gThis._GetClass('AddFiles') + '"/>').append('<span id="' + gThis.GetId() + '__upload"/>');
		gThis.m_jFileSelector.append(gThis.m_jSwfUpload);
		gThis.m_jChooseButton = $('<a href="#" class="button"><span><img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select + '</span></a>');
		gThis.m_jFileSelector.append($('<span class="browse-pictures"/>').append(gThis.m_jChooseButton));
		gThis.m_jQueue = $('<ul class="' + gThis._GetClass('Queue') + '"/>');
		gThis.m_jFileSelector.append(gThis.m_jQueue);
		gThis.m_jFilesDatagrid = $('<div/>');
		gThis.m_jFileSelector.append(gThis.m_jFilesDatagrid);
		gThis.m_jSelectedFiles = $('<div class="' + gThis._GetClass('SelectedTable') + '"/>');
		gThis.m_jFileSelector.append(gThis.m_jSelectedFiles);
		gThis.m_jFileField = $('<input type="hidden" name="' + gThis.GetName() + '[file]"/>');
		gThis.m_jFileSelector.append(gThis.m_jFileField);
		jTarget.append(gThis.m_jFileSelector);
	};
	
	gThis._WriteValueTypeTextArea = function(jTarget) {
		var jInput = $('<textarea rows="5" cols="5" style="width: 485px;" />');
		jInput.focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		}));
		var jInputNode = $('<div class="field-textarea"><span class="field"/></div>').append($('<span class="suffix"/>')).find('.field').append(jInput);
		jTarget.append(jInputNode.parent());
		jInput.change(gThis._OnChangeValue);
	};
	
	gThis._WriteValueTypeMultilingualString = function(jTarget) {
		var jInput = $('<input type="text"/>');
		jInput.focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		}));
		var jEdit = $('<img class="edit" src="' + gThis._GetImage('EditIcon') + '" alt="' + GForm.Language.technical_data_edit_multilingual_value + '" title="' + GForm.Language.technical_data_edit_multilingual_value + '"/>');
		jEdit.click(GEventHandler(function(eEvent) {
			var iGroupIndex = $(this).closest('.attribute').data('iGroupIndex');
			var iAttributeIndex = $(this).closest('.attribute').data('iAttributeIndex');
			var oAttribute = gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex];
			var jOverlay = $('<div class="technical-data-detail-editor"/>').data('iGroupIndex', iGroupIndex).data('iAttributeIndex', iAttributeIndex);
			$('body').append(jOverlay);
			jOverlay.GShadow();
			jOverlay.GOverlay({
				fClick: GEventHandler(function(eEvent) {
					jOverlay.remove();
				})
			});
			jOverlay.css({
				left: $(this).offset().left - 161,
				top: $(this).offset().top + 21
			});
			for (var l in GCore.aoLanguages) {
				var jLanguage = $('<div class="field-text"><span class="prefix"><img src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/' + GCore.aoLanguages[l].flag + '" alt="' + GCore.aoLanguages[l].name + '"/></span><span class="field"><input class="language-' + l + '" type="text" value="' + (((oAttribute.value == undefined) || (oAttribute.value[l] == undefined)) ? '' : oAttribute.value[l]) + '"/></span></div>');
				jOverlay.append(jLanguage);
			}
			var jSaveButton = $('<a class="button wide" href="#"><span>' + GForm.Language.technical_data_save_attribute + '</span></a>');
			jSaveButton.click(GEventHandler(function(eEvent) {
				var iAttributeIndex = $(this).closest('.technical-data-detail-editor').data('iAttributeIndex');
				var iGroupIndex = $(this).closest('.technical-data-detail-editor').data('iGroupIndex');
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = {};
				for (var l in GCore.aoLanguages) {
					gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value[l] = $(this).closest('.technical-data-detail-editor').find('.language-' + l).val();
				}
				gThis.UpdateValues();
				GOverlay.RemoveAll();
				return false;
			}));
			jOverlay.append(jSaveButton);
		}));
		var jInputNode = $('<div class="field-text"><span class="field"/></div>').append($('<span class="suffix"/>').append(jEdit)).find('.field').append(jInput);
		jTarget.append(jInputNode.parent());
		jInput.change(gThis._OnChangeValue);
	};
	
	gThis._WriteValueTypeBoolean = function(jTarget) {
		var jInput = $('<input type="checkbox"/>');
		var jInputNode = $('<div class="field-checkbox"><span class="field"/></div>').find('.field').append(jInput);
		jTarget.append(jInputNode.parent());
		jInput.change(gThis._OnChangeValue);
	};
	
	gThis._OnChangeValue = GEventHandler(function(eEvent) {
		var iGroupIndex = $(this).closest('.attribute').data('iGroupIndex');
		var iAttributeIndex = $(this).closest('.attribute').data('iAttributeIndex');
		switch (gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].type) {
			case GFormTechnicalDataEditor.FIELD_STRING:
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = $(this).val();
				break;
			case GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING:
				if (!(gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value instanceof Object)) {
					gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = {};
				}
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value[GCore.iActiveLanguage] = $(this).val();
				break;
			case GFormTechnicalDataEditor.FIELD_TEXT:
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = $(this).val();
				break;
			case GFormTechnicalDataEditor.FIELD_IMAGE:
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = $(this).val();
				break;
			case GFormTechnicalDataEditor.FIELD_BOOLEAN:
				gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].value = $(this).is(':checked');
				break;
		}
		gThis.UpdateFields();
	});
	
	gThis.UpdateValues = function() {
		for (var i in gThis.m_aoAttributes) {
			for (var j in gThis.m_aoAttributes[i].children) {
				if (gThis.m_aoAttributes[i].children[j].value != undefined) {
					gThis._UpdateValue(i, j, gThis.m_aoAttributes[i].children[j].value);
				}
			}
		}
		gThis.UpdateFields();
	};
	
	gThis._UpdateValue = function(iGroupIndex, iAttributeIndex, mValue) {
		switch (gThis.m_aoAttributes[iGroupIndex].children[iAttributeIndex].type) {
			case GFormTechnicalDataEditor.FIELD_STRING:
				gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value input:text').val(mValue);
				break;
			case GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING:
				gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value input:text').val(mValue[GCore.iActiveLanguage]);
				break;
			case GFormTechnicalDataEditor.FIELD_TEXT:
				gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value textarea').val(mValue);
				break;
			case GFormTechnicalDataEditor.FIELD_IMAGE:
				gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value input:text').val(mValue);
				break;
			case GFormTechnicalDataEditor.FIELD_BOOLEAN:
				if (Number(mValue)) {
					gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value').checkCheckboxes();
				}
				else {
					gThis.m_jAttributes.find('.group:eq(' + iGroupIndex + ') .attribute:eq(' + iAttributeIndex + ') .value').unCheckCheckboxes();
				}
				break;
		}
	};
	
	gThis._OnAttributeGroupSave = GEventHandler(function(eEvent) {
		gThis.m_aoAttributes[eEvent.iGroupIndex].id = eEvent.attributeGroupId;
		GMessage(GForm.Language.technical_data_save_group_success, GForm.Language.technical_data_save_group_success_description);
		var bFound = false;
		for (var i in gThis.m_oOptions.aAttributeGroups) {
			if (gThis.m_oOptions.aAttributeGroups[i].id == eEvent.attributeGroupId) {
				gThis.m_oOptions.aAttributeGroups[i] = $.extend({}, gThis.m_aoAttributes[eEvent.iGroupIndex]);
				bFound = true;
				break;
			}
		}
		if (!bFound) {
			gThis.m_oOptions.aAttributeGroups.push($.extend({}, gThis.m_aoAttributes[eEvent.iGroupIndex]));
		}
		gThis._WriteTechnicalAttributes();
	});
	
	gThis._OnAttributeGroupDeleted = GEventHandler(function(eEvent) {
		GMessage(GForm.Language.technical_data_delete_group_success, GForm.Language.technical_data_delete_group_success_description);
		gThis._WriteTechnicalAttributes();
	});
	
	gThis._OnAttributeSave = GEventHandler(function(eEvent) {
		gThis.m_aoAttributes[eEvent.iGroupIndex].children[eEvent.iAttributeIndex].id = eEvent.attributeId;
		GMessage(GForm.Language.technical_data_save_attribute_success, GForm.Language.technical_data_save_attribute_success_description);
		var bFound = false;
		for (var i in gThis.m_oOptions.aTechnicalAttributes) {
			if (gThis.m_oOptions.aTechnicalAttributes[i].id == eEvent.attributeId) {
				gThis.m_oOptions.aTechnicalAttributes[i] = $.extend({}, gThis.m_aoAttributes[eEvent.iGroupIndex].children[eEvent.iAttributeIndex]);
				bFound = true;
				break;
			}
		}
		if (!bFound) {
			gThis.m_oOptions.aTechnicalAttributes.push($.extend({}, gThis.m_aoAttributes[eEvent.iGroupIndex].children[eEvent.iAttributeIndex]));
		}
		gThis._WriteTechnicalAttributes();
	});
	
	gThis._OnAttributeDeleted = GEventHandler(function(eEvent) {
		GMessage(GForm.Language.technical_data_delete_attribute_success, GForm.Language.technical_data_delete_attribute_success_description);
		gThis._WriteTechnicalAttributes();
	});
	
	gThis._UpdateIndices = function() {
		gThis.m_jAttributes.children('.group').each(function(i) {
			$(this).data('iGroupIndex', i);
			$(this).find('.attribute').each(function(j) {
				$(this).data('iAttributeIndex', j);
				$(this).data('iGroupIndex', $(this).closest('.group').data('iGroupIndex'));
			});
		});
	};
	
}, oDefaults);

GFormTechnicalDataEditor.FIELD_STRING = 1;
GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING = 2;
GFormTechnicalDataEditor.FIELD_TEXT = 3;
GFormTechnicalDataEditor.FIELD_IMAGE = 4;
GFormTechnicalDataEditor.FIELD_BOOLEAN = 5;
GFormTechnicalDataEditor.FIELD_SELECT = 6;
//GFormTechnicalDataEditor.FIELD_MULTISELECT = 7;

GFormTechnicalDataEditor.s_iNewId = 0;


/*
* TEXTFIELD
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sDisabledClass: 'disabled',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormTextField = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis._PrepareNode = function() {

		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
		if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
			gThis.m_jField.attr('name', gThis.GetName() + '[value]');
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
		}
		if ((gThis.m_oOptions.sCssAttribute != undefined) && (gThis.m_oOptions.sCssAttribute.length)) {
			gThis.m_jField.attr('name', gThis.GetName() + '[value]');
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[css_attribute]" value="' + gThis.m_oOptions.sCssAttribute + '"/>');
		}
		
	};

	gThis._AddField = function(sId) {
		var jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="'+ gThis.GetName(sId) + '" id="' + gThis.GetId(sId) + '"/>');
		
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		if (gThis.m_oOptions.sPrefix != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.sPrefix);
			jRepetitionNode.append(jPrefix);
		}
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}
		var jError = $('<span class="' + gThis._GetClass('Required') + '"/>');
		jRepetitionNode.append(jError);
		gThis.jRepetitionNode = jRepetitionNode;
		return gThis.jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		gThis._UpdateRepetitionButtons();
		if (!gThis.m_bShown && gThis.m_bRepeatable) {
			gThis._InitializeEvents('new-0');
		}
		gThis.m_bShown = true;
		if (gThis.m_bRepeatable) {
			for (var i in gThis.m_oRepetitions) {
				if (!gThis.m_oRepetitions[i].m_bResized) {
					gThis.m_oRepetitions[i].m_bResized = true;
					var iWidth = parseInt(gThis._GetField(i).css('width'));
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).length) {
						iWidth -= (gThis._GetField(i).offset().left - gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
					}
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).length) {
						iWidth -= gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).width() + 4;
					}
					gThis._GetField(i).eq(i).css('width', iWidth);
				}
			}
		}
		else {
			if (!gThis.m_bResized) {
				gThis.m_bResized = true;
				var iWidth = parseInt(gThis.m_jField.css('width'));
				if (gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).length) {
					iWidth -= (gThis.m_jField.offset().left - gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
				}
				if (gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).length) {
					iWidth -= gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).width() + 4;
				}
				gThis.m_jField.css('width', iWidth);
			}
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault);
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).unbind('change', gThis.OnValidate).change(gThis.OnValidate);
		});
		
	};
	
	
}, oDefaults);

/**
 * CSRF
 */
var GFormTextFieldCSRF = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis._PrepareNode = function() {
		
		gThis.m_jNode = $('<div/>');
		
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
		
	};

	gThis._AddField = function(sId) {
		var jField = $('<input type="hidden" name="'+ gThis.GetName(sId) + '" id="' + gThis.GetId(sId) + '"/>');
		
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		
		var jError = $('<span class="' + gThis._GetClass('Required') + '"/>');
		jRepetitionNode.append(jError);
		gThis.jRepetitionNode = jRepetitionNode;
		
		return gThis.jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		gThis._UpdateRepetitionButtons();
		if (!gThis.m_bShown && gThis.m_bRepeatable) {
			gThis._InitializeEvents('new-0');
		}
		gThis.m_bShown = true;
		if (gThis.m_bRepeatable) {
			for (var i in gThis.m_oRepetitions) {
				if (!gThis.m_oRepetitions[i].m_bResized) {
					gThis.m_oRepetitions[i].m_bResized = true;
					var iWidth = parseInt(gThis._GetField(i).css('width'));
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).length) {
						iWidth -= (gThis._GetField(i).offset().left - gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
					}
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).length) {
						iWidth -= gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).width() + 4;
					}
					gThis._GetField(i).eq(i).css('width', iWidth);
				}
			}
		}
		else {
			if (!gThis.m_bResized) {
				gThis.m_bResized = true;
				var iWidth = parseInt(gThis.m_jField.css('width'));
				if (gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).length) {
					iWidth -= (gThis.m_jField.offset().left - gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
				}
				if (gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).length) {
					iWidth -= gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).width() + 4;
				}
				gThis.m_jField.css('width', iWidth);
			}
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault);
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).unbind('change', gThis.OnValidate).change(gThis.OnValidate);
		});
		
	};
	
	
}, oDefaults);

/*
* TIP
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-tip',
		sArrowClass: 'tip',
		sHideButtonClass: 'hide',
		sRetractableClass: 'retractable',
		sSwitchClass: 'switch'
	},
	sTip: '',
	sShortTip: '',
	bRetractable: false,
	sDirection: 'down',
	sDefaultState: 'retracted'
};

var GFormTip = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field')).addClass(gThis.m_oOptions.sDirection);
		gThis.m_jNode.append('<div class="' + gThis._GetClass('Arrow') + '"/>');
		gThis.m_jNode.append($('<div class="long"/>').append(gThis.m_oOptions.sTip));
		if (gThis.m_oOptions.bRetractable) {
			gThis.m_jNode.addClass(gThis._GetClass('Retractable'));
			if (gThis.m_oOptions.sDefaultState == 'retracted') {
				gThis.m_jNode.addClass('retracted');
			}
			gThis.m_jNode.append($('<div class="' + gThis._GetClass('Switch') + '"/>'));
			gThis.m_jNode.append($('<div class="short"/>').append(gThis.m_oOptions.sShortTip));
		}
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jNode.find('.' + gThis._GetClass('Switch')).click(function() {
			gThis.m_jNode.toggleClass('retracted');
			return false;
		});
	};
	
	gThis.Focus = function() { return false; };
	
}, oDefaults);


/*
* SORTABLE TREE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-tree',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sItemNameClass: 'item-name',
		sExpanderClass: 'expander',
		sExpandedClass: 'expanded',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sExpandAllClass: 'expand-all',
		sRetractAllClass: 'retract-all'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sDuplicate: '_images_panel/icons/buttons/duplicate.png',
		sDelete: '_images_panel/icons/buttons/delete.png',
		sSave: '_images_panel/icons/buttons/save.png',
		sRestore: '_images_panel/icons/buttons/clean.png',
		sWaiting: '_images_panel/icons/loading/indicator.gif'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	bSortable: false,
	iTotal: 0,
	iRestrict: 0,
	bSelectable: true,
	bChoosable: false,
	bClickable: false,
	bDeletable: false,
	bRetractable: true,
	oItems: {},
	fOnClick: GCore.NULL,
	fOnDuplicate: GCore.NULL,
	fOnSaveOrder: GCore.NULL,
	fOnAdd: GCore.NULL,
	fOnAfterAdd: GCore.NULL,
	fOnDelete: GCore.NULL,
	fOnAfterDelete: GCore.NULL,
	sOnAfterDeleteId: 0,
	sActive: '',
	sAddItemPrompt: '',
	bPreventDuplicates: true,
	bPreventDuplicatesOnAllLevels: false
};

var GFormTree = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_jTree;
	gThis.m_jOptions;
	gThis.m_oItems;
	gThis.m_jExpandAll;
	gThis.m_jRetractAll;
	gThis.m_jFieldWrapper;
	gThis.m_jItemPlaceholder;
	gThis.m_jItemDragged;
	gThis.m_oItems = {};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<input type="hidden"/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jFieldWrapper = $('<div/>');
		gThis.m_jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jNode.append('<label>' + gThis.m_oOptions.sLabel + '</label>');
		gThis.m_jExpandAll = $('<a href="#"/>').text(GForm.Language.tree_expand_all);
		gThis.m_jRetractAll = $('<a href="#"/>').text(GForm.Language.tree_retract_all).css('visibility','hidden');
		if (gThis.m_oOptions.fGetChildren instanceof Function) {
			if(gThis.m_oOptions.iTotal > 0){
				gThis.m_jNode.append($('<p class="' + gThis._GetClass('ExpandAll') + '"/>').append(gThis.m_jRetractAll));
			}
		}
		else {
			if(gThis.m_oOptions.bRetractable){
				gThis.m_jNode.append($('<p class="' + gThis._GetClass('RetractAll') + '"/>').append(gThis.m_jRetractAll));
			}
		}
		gThis.m_jTree = $('<ul/>');
		if(gThis.m_oOptions.iTotal > 0){
			gThis.m_jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		}
		gThis.Update();
		gThis._PrepareOptions();
		window.setTimeout(gThis.ResetExpansion, 500);
	};
	
	gThis.OnRetractAll = function(eEvent) {
		gThis.m_jTree.find('li:has(li)').removeClass(gThis._GetClass('Expanded'));
		return false;
	};
	
	gThis._PrepareOptions = function() {
		var jA;
		gThis.m_jOptions = $('<div class="options"/>');
		
		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {
			
			jA = $('<a id="add-category" class="' + gThis._GetClass('Button') + '" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Add') + '" alt=""/>' + gThis.m_oOptions.sAddLabel + '</span>');
			jA.click(function(eEvent) {
				GAlert.DestroyAll();
				GPrompt(gThis.m_oOptions.sAddItemPrompt, function(sName) {
					if (gThis.m_oOptions.bPreventDuplicatesOnAllLevels) {
						var jSiblings = gThis.m_jTree.find('li');
						for (var i = 0; i < jSiblings.length; i++) {
							if (sName == jSiblings.eq(i).children('a').text()) {
								GAlert.DestroyThis.apply(this);
								GError(GForm.Language.tree_duplicate_entry_all_levels, GForm.Language.tree_duplicate_entry_all_levels_description);
								return;
							}
						}
					}
					else if (gThis.m_oOptions.bPreventDuplicates) {
						var jSiblings = gThis.m_jTree.children('li');
						for (var i = 0; i < jSiblings.length; i++) {
							if (sName == jSiblings.eq(i).children('a').text()) {
								GAlert.DestroyThis.apply(this);
								GError(GForm.Language.tree_duplicate_entry, GForm.Language.tree_duplicate_entry_description);
								return;
							}
						}
					}
					GCore.StartWaiting();
					gThis.m_oOptions.fOnAdd({
						name: sName
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						if (gThis.m_oOptions.fOnAfterAdd instanceof Function) {
							gThis.m_oOptions.fOnAfterAdd(eEvent.id);
						}
					}));
				});
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));
			
			if (gThis.m_oOptions.fOnDuplicate instanceof Function) {
				jB = $('<a class="' + gThis._GetClass('Button') + ' duplicate" href="#"/>');
				jB.append('<span><img src="' + gThis._GetImage('Duplicate') + '" alt=""/>' + GForm.Language.tree_duplicate_item + '</span>');
				jB.click(function(eEvent) {
					if (gThis.m_oOptions.fOnDuplicate instanceof Function) {
						gThis.m_oOptions.fOnDuplicate(sId);
					}
				});
				if (gThis.m_jTree.find('.' + gThis._GetClass('Active')).length) {
					gThis.m_jOptions.append($('<p/>').append(jB));
				}
			}
			
			
			if (gThis.m_jTree.find('.' + gThis._GetClass('Active')).length) {
				
				var sId = gThis.m_jTree.find('.' + gThis._GetClass('Active')).get(0).sId;
				jA = $('<a class="' + gThis._GetClass('Button') + '" href="#"/>');
				jA.append('<span><img src="' + gThis._GetImage('Add') + '" alt=""/>' + GForm.Language.tree_add_subitem + '</span>');
				jA.click(function(eEvent) {
					GAlert.DestroyAll();
					GPrompt(gThis.m_oOptions.sAddItemPrompt, function(sName) {
						if (gThis.m_oOptions.bPreventDuplicatesOnAllLevels) {
							var jSiblings = gThis.m_jTree.find('li > a');
							for (var i = 0; i < jSiblings.length; i++) {
								if (sName == jSiblings.eq(i).text()) {
									GAlert.DestroyThis.apply(this);
									GError(GForm.Language.tree_duplicate_entry_all_levels, GForm.Language.tree_duplicate_entry_all_levels_description);
									return;
								}
							}
						}
						else if (gThis.m_oOptions.bPreventDuplicates) {
							var jSiblings = gThis.m_jTree.find('li.' + gThis._GetClass('Active') + ' > ul > li > a');
							for (var i = 0; i < jSiblings.length; i++) {
								if (sName == jSiblings.eq(i).text()) {
									GAlert.DestroyThis.apply(this);
									GError(GForm.Language.tree_duplicate_entry, GForm.Language.tree_duplicate_entry_description);
									return;
								}
							}
						}
						GCore.StartWaiting();
						gThis.m_oOptions.fOnAdd({
							parent: sId,
							name: sName
						}, GCallback(function(eEvent) {
							GCore.StopWaiting();
							if (gThis.m_oOptions.fOnAfterAdd instanceof Function) {
								gThis.m_oOptions.fOnAfterAdd(eEvent.id);
							}
						}));
					});
					return false;
				});
				gThis.m_jOptions.append($('<p/>').append(jA));
				
			}
			
		}
		
		if (gThis.m_oOptions.bDeletable && (gThis.m_oOptions.fOnDelete instanceof Function) && gThis.m_jTree.find('.' + gThis._GetClass('Active')).length) {
			
			var sId = gThis.m_jTree.find('.' + gThis._GetClass('Active')).get(0).sId;
			jA = $('<a id="delete-category" class="' + gThis._GetClass('Button') + ' delete" href="#"/>');
			jA.append('<span><img src="' + gThis._GetImage('Delete') + '" alt=""/>' + GForm.Language.tree_delete_item + '</span>');
			jA.click(function(eEvent) {
				GAlert.DestroyAll();
				GWarning(GForm.Language.tree_delete_item_warning, GForm.Language.tree_delete_item_warning_description, {
					bAutoExpand: true,
					aoPossibilities: [
						{mLink: function() {
							GCore.StartWaiting();
							gThis.m_oOptions.fOnDelete({
								id: sId
							}, GCallback(function(eEvent) {
								GCore.StopWaiting();
								if (gThis.m_oOptions.fOnAfterDelete instanceof Function) {
									gThis.m_oOptions.fOnAfterDelete(gThis.m_oOptions.sOnAfterDeleteId);
								}
							}));
						}, sCaption: GForm.Language.tree_ok},
						{mLink: GAlert.DestroyThis, sCaption: GForm.Language.tree_cancel}
					]
				});
				return false;
			});
			gThis.m_jOptions.append($('<p/>').append(jA));
			
		}
		
		gThis.m_jNode.append(gThis.m_jOptions);
	};
	
	gThis.GetOrder = function() {
		var jItems = gThis.m_jTree.find('li');
		var aoItems = [];
		for (var i = 0; i < jItems.length; i++) {
			var sId = jItems.eq(i).get(0).sId;
			var sParent = '';
			if (jItems.eq(i).parent().closest('li').length) {
				sParent = jItems.eq(i).parent().closest('li').get(0).sId;
			}
			var jSiblings = jItems.eq(i).parent().children('li');
			var iWeight = jSiblings.index(jItems.eq(i));
			aoItems.push({
				id: sId,
				parent: sParent,
				weight: iWeight
			});
		}
		return aoItems;
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_oOptions.bChoosable) {
			return gThis.m_jFieldWrapper.find('input:first').attr('value');
		}
		if (gThis.m_jField == undefined) {
			return '';
		}
		var aValues = [];
		var jValues = gThis._GetField(sRepetition).filter(':checked');
		for (var i in jValues) {
			aValues.push(jValues.eq(i).attr('value'));
		}
		return aValues;
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis._GetField(sRepetition).val(mValue).change();
	};
	
	gThis.ResetExpansion = function() {
		gThis.m_jTree.find('li').removeClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li.' + gThis._GetClass('Active')).parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
		gThis.m_jTree.find('li > label > input:checked').parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
	};
	
	gThis._WriteSubtree = function(jParent, sParent) {
		if (sParent == undefined) {
			sParent = null;
		}
		var oItems = GCore.FilterObject(gThis.m_oOptions.oItems, function(oItem) {return (oItem.parent == sParent);});
		var aIterationArray = GCore.GetIterationArray(oItems, function(oA, oB) {return (oA.weight - oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			var oItem = oItems[sId];
			jParent.append(gThis._WriteItem(sId, oItem));
		}
	};
	
	gThis._WriteItem = function(sId, oItem) {
		var jLi = $('<li/>');
		jLi.get(0).sId = sId;
		if (gThis.m_oOptions.sActive == sId) {
			jLi.addClass(gThis._GetClass('Active'));
			if(oItem.hasChildren){
				gThis._Expand(jLi);
			}
		}
		if (gThis.m_oOptions.bSelectable) {
			var jField = $('<input type="checkbox" value="' + sId + '"/>');
			if (gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').length) {
				jField.click();
				jField.attr('checked', 'checked');
			}
			if((gThis.m_oOptions.bGlobal == true) && GCore.iActiveView > 0){
				jField.attr('disabled','disabled');
			}
			if((oItem.allow != undefined) && (oItem.allow == 0) && GCore.iActiveView > 0){
				jField.attr('disabled','disabled');
			}
			jLi.append($('<label class="' + gThis._GetClass('ItemName') + '"/>').append(jField).append(oItem.name));
		}
		else if (gThis.m_oOptions.bChoosable) {
			var jField = $('<input type="radio" name="__' + gThis.GetName() + '" value="' + sId + '"/>');
			if (gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').length) {
				jField.click();
				jField.attr('checked', 'checked');
			}
			if((gThis.m_oOptions.iRestrict > 0) && gThis.m_oOptions.iRestrict == sId){
				jField.attr('disabled','disabled');
			}
			jLi.append($('<label class="' + gThis._GetClass('ItemName') + '"/>').append(jField).append(oItem.name));
		}
		else if (gThis.m_oOptions.bClickable) {
			var jA = $('<a href="#" class="' + gThis._GetClass('ItemName') + '">' + oItem.name + '</a>');
			jLi.append(jA);
		}
		else {
			jLi.append('<span class="' + gThis._GetClass('ItemName') + '">' + oItem.name + '</span>');
		}
		var jUl = $('<ul/>');
		gThis._WriteSubtree(jUl, sId);
		var jExpander = $('<span class="' + gThis._GetClass('Expander') + '"/>');
		jLi.prepend(jExpander);
		jLi.append(jUl);
		jExpander.css('display', 'none');
		if (oItem.hasChildren || jUl.children('li').length) {
			jExpander.css('display', 'block');
		}
		return jLi;
	};
	
	gThis.UpdateExpanders = function() {
		gThis.m_jTree.find('li::not(:has(li))').removeClass(gThis._GetClass('Expanded')).children('.' + gThis._GetClass('Expander')).css('display', 'none');
		gThis.m_jTree.find('li:has(li) > .' + gThis._GetClass('Expander')).css('display', 'block');
	};
	
	gThis.Update = function() {
		gThis.m_jTree.empty();
		gThis._WriteSubtree(gThis.m_jTree);
		if (gThis.m_oOptions.bSortable) {
			gThis.m_jTree.sortable({
				items: 'li',
				placeholder: 'item-faux-placeholder',
				opacity: .5,
				tolerance: 'cursor',
				cursor: 'move',
				cursorAt: 'left',
				delay: 200,
				start: function(e, ui) {
					gThis.m_bIgnoreClick = true;
					gThis.m_jItemPlaceholder = $('<li class="item-placeholder"/>');
				},
				change: function(e, ui) {
				},
				sort: function(e, ui) {
					gThis.m_jTree.find('li').removeClass('temporarly-expanded');
					if (ui.offset.left > $(ui.placeholder).prev().offset().left + 15) {
						$(ui.placeholder).prev().addClass('temporarly-expanded');
						gThis.m_jItemPlaceholder.appendTo($(ui.placeholder).prev().children('ul'));
					}
					else {
						gThis.m_jItemPlaceholder.insertAfter($(ui.placeholder));
					}
				},
				beforeStop: function(e, ui) {
					gThis.m_jTree.find('li.temporarly-expanded').removeClass('temporarly-expanded').addClass('expanded');
					gThis.m_jItemPlaceholder.replaceWith($(ui.helper));
					gThis.UpdateExpanders();
				},
				stop: function(e, ui) {
					if (gThis.m_oOptions.bPreventDuplicates) {
						var jLis = gThis.m_jTree.find('li');
						for (var i = 0; i < jLis.length; i++) {
							var jSiblings = jLis.eq(i).nextAll('li');
							var sItem = jLis.eq(i).children('a').text();
							for (var j = 0; j < jSiblings.length; j++) {
								if (sItem == jSiblings.eq(j).children('a').text()) {
									GError(GForm.Language.tree_found_duplicates + ': "' + sItem + '"', GForm.Language.tree_found_duplicates_description);
									return;
								}
							}
						}
					}
					GCore.StartWaiting();
					gThis.m_oOptions.fOnSaveOrder({
						items: gThis.GetOrder()
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
					}));
					
					setTimeout(function() {
						gThis.m_bIgnoreClick = false;
					}, 500);
				}
			});
		}
		gThis._InitializeNodeEvents();
		gThis.ResetExpansion();
	};
	
	gThis.Populate = function(mValue) {
		if ((mValue == undefined) || (mValue == '')) {
			mValue = [];
		}
		else if (!(mValue instanceof Array)) {
			mValue = [mValue];
		}
		if (gThis.m_oOptions.bChoosable) {
			gThis.m_jFieldWrapper.empty();
			for (var i in mValue) {
				if (i == 'toJSON') {
					continue;
				}
				gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '" value="' + mValue[i] + '"/>');
				gThis.m_jNode.find('input:radio[value="' + mValue[i] + '"]').click();
			}
		}
		else if (gThis.m_oOptions.bSelectable) {
			gThis.m_jNode.unCheckCheckboxes();
			gThis.m_jFieldWrapper.empty();
			for (var i in mValue) {
				if (i == 'toJSON') {
					continue;
				}
				gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '[]" value="' + mValue[i] + '"/>');
				gThis.m_jNode.find('input:checkbox[value="' + mValue[i] + '"]').parent().checkCheckboxes();
			}
		}
		gThis.ResetExpansion();
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_bShown = true;
		}
	};
	
	gThis._OnClick = GEventHandler(function(eEvent) {
		GCore.StartWaiting();
	});
	
	gThis._InitializeEvents = function(sRepetition) {
		gThis.m_jExpandAll.click(gThis.OnExpandAll);
		gThis.m_jRetractAll.click(gThis.OnRetractAll);
		gThis._InitializeNodeEvents();
	};
	
	gThis._OnSelect = GEventHandler(function(eEvent) {
		if ($(this).is(':radio')) {
			gThis.m_jFieldWrapper.find('input').remove();
		}
		else {
			gThis.m_jFieldWrapper.find('input[value="' + $(this).attr('value') + '"]').remove();
		}
		if ($(this).is(':checked')) {
			var jInput = $('<input type="hidden" name="' + gThis.GetName() + ($(this).is(':radio') ? '' : '[]') + '" value="' + $(this).attr('value') + '"/>');
			gThis.m_jFieldWrapper.append(jInput);
			for (var i in gThis.m_afDependencyTriggers) {
				gThis.m_afDependencyTriggers[i].apply(jInput.get(0), [{
					data: {
						gNode: gThis
					}
				}]);
			}
		}
	});
	
	gThis._InitializeNodeEvents = function() {
		gThis.m_jTree.find('.' + gThis._GetClass('Expander')).unbind('click').click(function() {
			if ($(this).closest('li').hasClass(gThis._GetClass('Expanded'))) {
				$(this).closest('li').find('li').andSelf().removeClass(gThis._GetClass('Expanded'));
			}
			else {
				$(this).closest('li').addClass(gThis._GetClass('Expanded'));
				gThis._Expand($(this).closest('li'));
			}

			if(gThis.m_jTree.find('.' + gThis._GetClass('Expanded')).size() == 0){
				gThis.m_jRetractAll.css('visibility','hidden');
			}else{
				gThis.m_jRetractAll.css('visibility','visible');
			}
		});
		if (gThis.m_oOptions.bClickable) {
			gThis.m_jTree.find('li').unbind('click').unbind('mousedown').each(function() {
				var sId = $(this).closest('li').get(0).sId;
				$(this).children('a').click(GEventHandler(function(eEvent) {
					if (gThis.m_bIgnoreClick) {
						return false;
					}
					gThis.m_jTree.find('li').removeClass(gThis._GetClass('Active'));
					$(this).closest('li').addClass(gThis._GetClass('Active'));
					gThis._OnClick.apply(this, [eEvent]);
					if (gThis.m_oOptions.fOnClick instanceof Function) {
						gThis.m_oOptions.fOnClick(sId);
					}
					return false;
				}));
			});
		}
		gThis.m_jTree.find('input').unbind('click').click(gThis._OnSelect);
	};
	
	gThis._Expand = function(jParentLi) {
		var sId = jParentLi.get(0).sId;
		if (gThis.m_oItems[sId] != undefined) {
			return;
		}
		if (gThis.m_oOptions.fGetChildren instanceof Function) {
			jParentLi.find('ul').remove();
			jParentLi.append('<ul><li><img src="' + gThis._GetImage('Waiting') + '" alt=""/></li></ul>');
			gThis.m_oOptions.fGetChildren({
				parent: sId
			}, GCallback(gThis._OnChildrenLoaded, {
				parentNode: jParentLi
			}));
		}
	};
	
	gThis._OnChildrenLoaded = GEventHandler(function(eEvent) {
		var jUl = $('<ul/>'); 
		gThis.m_oItems[eEvent.parentNode.get(0).sId] = true;
		var aIterationArray = GCore.GetIterationArray(eEvent.children, function(oA, oB) {return (oA.weight - oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			jUl.append(gThis._WriteItem(sId, eEvent.children[sId]));
		}
		eEvent.parentNode.find('ul').remove();
		eEvent.parentNode.append(jUl);
		gThis._InitializeNodeEvents();
	});
	
}, oDefaults);

/*
* BORDER
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-border',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sColourTypeClass: 'colour-type',
		sColourStartClass: 'colour-start',
		sColourEndClass: 'colour-end',
		sColourPreviewClass: 'colour-preview'
	},
	oImages: {
		sBold: '_images_panel/icons/font-style-bold.png',
		sUnderline: '_images_panel/icons/font-style-underline.png',
		sItalic: '_images_panel/icons/font-style-italic.png',
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png',
		sSideAll: '_images_panel/icons/buttons/border-all.png',
		sSide_top: '_images_panel/icons/buttons/border-top.png',
		sSide_right: '_images_panel/icons/buttons/border-right.png',
		sSide_bottom: '_images_panel/icons/buttons/border-bottom.png',
		sSide_left: '_images_panel/icons/buttons/border-left.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	sSelector: ''
};

var GFormBorder = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis.m_oSides = {};
	gThis.m_oSizeField = {};
	gThis.m_oColourField = {};
	gThis.m_oColourPreviewFields = {};
	
	gThis.m_jGlobalSide;
	gThis.m_jSeparationTrigger;
	
	gThis.m_bSeparated = false;
	
	gThis.m_fOnUpdate = GCore.NULL;
	
	gThis.m_asSides = ['top', 'right', 'bottom', 'left'];
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
				jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jLabel = jLabel;
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append(gThis._AddField());
		for (var i = 0; i < gThis.m_asSides.length; i++) {
			gThis.m_jNode.append(gThis._AddSideOptions(gThis.m_asSides[i]));
		}
		if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
		}
		gThis.SetValue({});
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis.m_jField.eq(0).val();
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue == undefined) {
			return;
		}
		var bSeparate = false;
		for (var i in gThis.m_asSides) {
			var sSide = gThis.m_asSides[i];
			var sColour;
			var sSize;
			if (mValue[sSide] != undefined) {
				gThis.m_oColourField[sSide].val((mValue[sSide].colour == undefined) ? '000000' : mValue[sSide].colour);
				gThis.m_oSizeField[sSide].val((mValue[sSide].size == undefined) ? '1' : mValue[sSide].size).change();
			}
			else {
				gThis.m_oColourField[sSide].val('000000');
				gThis.m_oSizeField[sSide].val('1').change();
			}
			if (sColour == undefined) {
				sColour = gThis.m_oColourField[sSide].val();
			}
			else if (sColour != gThis.m_oColourField[sSide].val()) {
				bSeparate = true;
			}
			if (sSize == undefined) {
				sSize = gThis.m_oSizeField[sSide].val();
			}
			else if (sSize != gThis.m_oSizeField[sSide].val()) {
				bSeparate = true;
			}
		}
		gThis.UpdateSeparation(bSeparate);
	};
	
	gThis.UpdateSeparation = function(bSeparate) {
		gThis.m_bSeparated = bSeparate;
		if (bSeparate) {
			gThis.m_jSeparationTrigger.removeClass('active');
			gThis.m_jNode.find('.side').slideDown(250);
			gThis.m_jGlobalSide.find('.field:not(.icon)').css('display', 'none');
			gThis.m_oColourPreviewFields['all'].css('visibility', 'hidden');
		}
		else {
			gThis.m_jSeparationTrigger.addClass('active');
			gThis.m_oColourField['all'].val(gThis.m_oColourField['top'].val()).change();
			gThis.m_oSizeField['all'].val(gThis.m_oSizeField['top'].val()).change();
			gThis.m_jNode.find('.side').css('display', 'none');
			gThis.m_jGlobalSide.find('.field:not(.icon)').css('display', 'inline');
			gThis.m_oColourPreviewFields['all'].css('visibility', 'visible');
		}
		gThis.UpdatePreview();
	};

	gThis.UpdatePreview = function(sSide) {
		for (var i in gThis.m_oColourField) {
			gThis.m_oColourPreviewFields[i].css('background-color', '#' + gThis.m_oColourField[i].val());
		}
		if (gThis.m_bSeparated) {
			gThis.m_oColourPreviewFields['all'].css('background-color', 'transparent').parent().addClass('none');
		}
		else {
			gThis.m_oColourPreviewFields['all'].parent().removeClass('none');
		}
		if (gThis.m_fOnUpdate instanceof Function) {
			gThis.m_fOnUpdate.apply(gThis, [{}]);
		}
	};
	
	gThis._AddSideOptions = function(sSide) {
		var jSide = $('<div class="side"/>');
		
		var jColourPreviewNode = $('<span class="' + gThis._GetClass('ColourPreview') + '"/>');
		
		var jSizeField = $('<select name="' + gThis.GetName() + '[' + sSide + '][size]" />');
		jSizeField.append('<option value="0">' + GForm.Language.border_none + '</option>');
		for (var i = 1; i < 5; i++) {
			jSizeField.append('<option value="' + i + '">' + i + ' px</option>');
		};
		
		var jColourField = $('<input type="text" name="' + gThis.GetName() + '[' + sSide + '][colour]" />');
		
		jSide.append('<label>' + GForm.Language['border_side_' + sSide] + '</label>');
		var jRepetition = $('<span class="repetition"/>');
		//jRepetition.append(jColourPreviewNode);
		jRepetition.append($('<span class="' + gThis._GetClass('ColourPreview') + '-container"/>').append(jColourPreviewNode));
		jRepetition.append('<span class="field icon"><img src="' + gThis._GetImage('Side_' + sSide) + '" alt="' + GForm.Language['border_side_' + sSide] + '"/></span>');
		jRepetition.append($('<span class="field size"/>').append(jSizeField));
		jRepetition.append($('<span class="field colour"/>').append(jColourField));
		jSide.append(jRepetition);
		
		gThis.m_oSides[sSide] = jSide;
		gThis.m_oSizeField[sSide] = jSizeField;
		gThis.m_oColourField[sSide] = jColourField;
		gThis.m_oColourPreviewFields[sSide] = jColourPreviewNode;
		return jSide;
	};
	
	gThis._AddField = function(sId) {
		
		var jSide = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		gThis.m_jGlobalSide = jSide;
		
		var jColourPreviewNode = $('<span class="' + gThis._GetClass('ColourPreview') + '"/>');
		
		var jSizeField = $('<select/>');
		jSizeField.append('<option value="0">' + GForm.Language.border_none + '</option>');
		for (var i = 1; i < 5; i++) {
			jSizeField.append('<option value="' + i + '">' + i + ' px</option>');
		};
		
		var jColourField = $('<input type="text"/>');
		
		//jSide.append(jColourPreviewNode);
		jSide.append($('<span class="' + gThis._GetClass('ColourPreview') + '-container"/>').append(jColourPreviewNode));
		
		gThis.m_jSeparationTrigger = $('<span class="field icon"><img src="' + gThis._GetImage('SideAll') + '" alt="' + GForm.Language['border_separate'] + '" title="' + GForm.Language['border_separate'] + '"/></span>');
		jSide.append(gThis.m_jSeparationTrigger);
		jSide.append($('<span class="field size"/>').append(jSizeField));
		jSide.append($('<span class="field colour"/>').append(jColourField));
		
		gThis.m_oSides['all'] = jSide;
		gThis.m_oSizeField['all'] = jSizeField;
		gThis.m_oColourField['all'] = jColourField;
		gThis.m_oColourPreviewFields['all'] = jColourPreviewNode;
	
		return jSide;
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		gThis.m_bShown = true;
		for (var i in gThis.m_oSizeField) {
			gThis.m_oSizeField[i].GSelect();
		}
	};
	
	gThis._Initialize = function() {
		gThis.UpdatePreview();
	};
	
	gThis._InitializeEvents = function() {
		
		gThis.m_jSeparationTrigger.css('cursor', 'pointer').click(GEventHandler(function(eEvent) {
			gThis.UpdateSeparation(!gThis.m_bSeparated);
		}));
		
		for (var i in gThis.m_oColourField) {
			gThis.m_oColourField[i].ColorPicker({
				color: '#' + gThis.m_oColourField[i].val(),
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				},
				onShow: function(colpkr) {
					$(colpkr).fadeIn(250);
					$(this).closest('.field').addClass('focus');
					$(colpkr).data('field', $(this));
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(250);
					$(colpkr).data('field').triggerHandler('change');
					$(colpkr).data('field').closest('.field').removeClass('focus');
					return false;
				},
				onChange: function(hsb, hex, rgb) {
					$(this).data('field').val(hex);
					$(this).data('field').closest('.side, .repetition').find('.colour-preview').css('background-color', '#' + hex);
				}
			}).change(GEventHandler(function(eEvent) {
				gThis.UpdatePreview();
			}));
			gThis.m_oSizeField[i].change(GEventHandler(function(eEvent) {
				gThis.UpdatePreview();
			})).triggerHandler('change');
		}
		
		gThis.m_oColourField['all'].change(GEventHandler(function() {
			var sValue = $(this).val();
			for (var i in gThis.m_oColourField) {
				gThis.m_oColourPreviewFields[i].css('background-color', '#' + sValue);
				gThis.m_oColourField[i].val(sValue);
			}
			gThis.m_oColourField['top'].change();
		}));
		
		gThis.m_oSizeField['all'].change(GEventHandler(function() {
			var sValue = $(this).val();
			for (var i in gThis.m_oSizeField) {
				if (i == 'all') {
					continue;
				}
				gThis.m_oColourPreviewFields[i].css('background-color', '#' + sValue);
				gThis.m_oSizeField[i].val(sValue).change();
			}
			if (!parseInt(sValue)) {
				gThis.m_oColourPreviewFields['all'].css('background-color', 'transparent').parent().addClass('none');
			}
			else {
				gThis.m_oColourPreviewFields['all'].parent().removeClass('none');
			}
		})).triggerHandler('change');
		
		for (var i in gThis.m_oSizeField) {
			if (i == 'all') {
				continue;
			}
			gThis.m_oSizeField[i].bind('change', {i: i}, GEventHandler(function(eEvent) {
				var sValue = $(this).val();
				if (!parseInt(sValue)) {
					gThis.m_oColourPreviewFields[eEvent.data.i].css('background-color', 'transparent').parent().addClass('none');
				}
				else {
					gThis.m_oColourPreviewFields[eEvent.data.i].parent().removeClass('none');
				}
			}));
		}
		
		gThis.m_oColourField['all'].change(GEventHandler(function() {
			var sValue = $(this).val();
			for (var i in gThis.m_oColourField) {
				if (i == 'all') {
					continue;
				}
				gThis.m_oColourPreviewFields[i].css('background-color', '#' + sValue);
				gThis.m_oColourField[i].val(sValue).change();
			}
		}));
	};

	gThis.Reset = function() {
		gThis.m_jField.eq(0).val(gThis.m_oOptions.sDefault).change();
	};
	
}, oDefaults);


/*
* COLOUR SCHEME PICKER
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-colour',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sColourTypeClass: 'colour-type',
		sColourStartClass: 'colour-start',
		sColourEndClass: 'colour-end',
		sColourPreviewClass: 'colour-preview',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sChooseIcon: '_images_panel/icons/datagrid/edit.png',
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	aoTypes: [
		{sValue: '1', sLabel: 'colour_scheme_picker_colour', sFlag: 'bAllowColour'},
		{sValue: '2', sLabel: 'colour_scheme_picker_gradient', sFlag: 'bAllowGradient'},
		{sValue: '3', sLabel: 'colour_scheme_picker_image', sFlag: 'bAllowImage'}
	],
	bAllowColour: true,
	bAllowGradient: true,
	bAllowImage: false,
	sUploadUrl: '',
	sSessionId: '',
	sSessionName: '',
	asFileTypes: [],
	sFileTypesDescription: '',
	fDeleteFile: GCore.NULL,
	fLoadFiles: GCore.NULL,
	sSwfUploadUrl: '_data_panel/swfupload.swf',
	iWidth: 131,
	iHeight: 34,
	iMaxFileSize: 100 * 1024,	// kB
	sSelector: '',
	iGradientHeight: 16
};

var GFormColourSchemePicker = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis.m_jRepetitionNode;
	gThis.m_jColourTypeNode;
	gThis.m_jColourStartNode;
	gThis.m_jColourEndNode;
	gThis.m_jColourPreviewNode;
	gThis.m_jFieldColourType;
	gThis.m_jFieldColourStart;
	gThis.m_jFieldColourEnd;

	gThis.m_jFileField;
	gThis.m_bShown = false;
	gThis.m_jFilesDatagrid;
	gThis.m_gDataProvider;
	gThis.m_gFilesDatagrid;
	gThis.m_jSelectedFiles;
	gThis.m_jSwfUpload;
	gThis.m_jQueue;
	gThis.m_iUploadsInProgress = 0;
	gThis.m_iLockId = -1;
	gThis.m_bLoadedDefaults = false;
	gThis.m_jChooseButton;
	gThis.m_jSelectedFileName;
	gThis.m_jFileSelector;
	gThis.m_jFilePosition;
	gThis.m_jFileRepeat;
	gThis.m_jFileRepeatField;
	
	gThis.m_sCWD;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append(gThis._AddField());
		if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
		}
		gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[gradient_height]" value="' + gThis.m_oOptions.iGradientHeight + '"/>');
	};

	gThis.Populate = function(mValue) {
		if (gThis.m_gFilesDatagrid) {
			gThis._UpdateDatagridSelection(mValue['file']);
		}
		gThis.SetValue(mValue);
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		gThis.m_gFilesDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gFilesDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gFilesDatagrid.LoadData();
		}
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jFileField == undefined) {
			return {};
		}
		return {
			file: gThis.m_jFileField.val()
		};
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue == undefined) {
			return;
		}
		if (mValue['type'] != undefined) {
			gThis.m_jFieldColourType.val(mValue.type).triggerHandler('change');
			gThis.m_jFieldColourStart.val(mValue.start);
			gThis.m_jFieldColourEnd.val(mValue.end);
		}
		if (mValue['repeat'] != undefined) {
			gThis.m_jFileRepeatField.val(mValue['repeat']);
		}
		if (mValue['position'] != undefined) {
			gThis.m_jFilePosition.find('input[value="' + mValue['position'] + '"]').click();
		}
		if (mValue['file'] == undefined) {
			gThis.m_jFileField.val('');
			gThis.m_jSelectedFileName.html('<span class="none">' + GForm.Language.localfile_none_selected + '</span>');
		}
		else {
			gThis.m_jFileField.val(mValue['file']).triggerHandler('change');
			gThis.m_jSelectedFileName.text(mValue['file']);
			if (gThis.m_jFieldColourType.val() == '3') {
				gThis.m_jColourPreviewNode.css({
					'background-image': 'url(\'' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + gThis.m_oOptions.sFilePath + gThis.m_jFileField.val() + '\')'
				});
			}
			if (gThis.m_gFilesDatagrid) {
				gThis.m_gFilesDatagrid.m_asSelected = [gThis.m_oOptions.sFilePath + mValue['file']];
			}
		}
		gThis.UpdatePreview();
	};
	
	gThis.UpdatePreview = function() {
		gThis.m_jColourPreviewNode.empty();
		gThis.m_jColourPreviewNode.css({
			'background-color': (gThis.m_jFieldColourStart.val() == 'transparent') ? 'transparent' : "#" + gThis.m_jFieldColourStart.val(),
			'background-image': 'none'
		});
		switch(gThis.m_jFieldColourType.val()) {
			case '1': //kolor
				gThis.m_jColourEndNode.css('visibility', 'hidden');
				gThis.m_jFileSelector.css('display', 'none');
				gThis.m_jFilePosition.css('display', 'none');
				gThis.m_jFileRepeat.css('display', 'none');
				break;
			
			case '2': //gradient
				gThis.m_jColourEndNode.css('visibility', 'visible');
				gThis.m_jFileSelector.css('display', 'none');
				gThis.m_jFilePosition.css('display', 'none');
				gThis.m_jFileRepeat.css('display', 'none');
				gThis.m_jColourPreviewNode.gradient({
					from:      gThis.m_jFieldColourStart.val(),
					to:        gThis.m_jFieldColourEnd.val(),
					direction: 'horizontal'
				});
				break;
			
			case '3': //plik
				gThis.m_jColourPreviewNode.css({
					'background-image': 'url(\'' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + gThis.m_oOptions.sFilePath + gThis.m_jFileField.val() + '\')',
					'background-repeat': gThis.m_jFileRepeatField.find('option:selected').attr('value'),
					'background-position': gThis.m_jFilePosition.find('input:checked').attr('value')
				});
				gThis.m_jColourStartNode.css('visibility', 'visible');
				gThis.m_jColourEndNode.css('visibility', 'hidden');
				gThis.m_jFileSelector.css('display', 'block');
				gThis.m_jFilePosition.css('display', 'block');
				gThis.m_jFileRepeat.css('display', 'block');
				break;
		}
	};
	
	gThis._AddField = function(sId) {
		
		var jColourTypeNode = $('<span class="' + gThis._GetClass('ColourType') + ' ' + gThis._GetClass('FieldSpan') +  '"/>');
		var jColourStartNode = $('<span class="' + gThis._GetClass('ColourStart') + '"/>');
		var jColourEndNode = $('<span class="' + gThis._GetClass('ColourEnd') + '"/>');

		var jColourPreviewNode = $('<span class="' + gThis._GetClass('ColourPreview') + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		
		var jFieldColourType = $('<select id="'+gThis.GetId()+'" name="' + gThis.GetName() + '[type]" />');
		for (var i = 0; i < gThis.m_oOptions.aoTypes.length; i++) {
			var oType = gThis.m_oOptions.aoTypes[i];
			if (gThis.m_oOptions[oType.sFlag]) {
				jFieldColourType.append('<option value="' + oType.sValue + '">' + GForm.Language[oType.sLabel] + '</option>');
			}
		}
		
		var jFieldColourStart = $('<input type="text" name="' + gThis.GetName() + '[start]" />');
		var jFieldColourEnd = $('<input type="text" name="' + gThis.GetName() + '[end]" />');


		jColourTypeNode.append(jFieldColourType);
		jColourStartNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldColourStart));
		jColourEndNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldColourEnd));
		
		jRepetitionNode.append($('<span class="' + gThis._GetClass('ColourPreview') + '-container"/>').append(jColourPreviewNode)).append(jColourTypeNode).append(jColourStartNode).append(jColourEndNode);

		gThis.m_jRepetitionNode = jRepetitionNode;
		gThis.m_jColourTypeNode = jColourTypeNode;
		gThis.m_jColourStartNode = jColourStartNode;
		gThis.m_jColourEndNode = jColourEndNode;
		gThis.m_jColourPreviewNode = jColourPreviewNode;
		gThis.m_jFieldColourType = jFieldColourType;
		gThis.m_jFieldColourStart = jFieldColourStart;
		gThis.m_jFieldColourEnd = jFieldColourEnd;
		
		gThis.m_jFileSelector = $('<div style="clear: both; padding-top: 10px;"/>');
		
		gThis.m_jSelectedFileName = $('<span class="filename"/>');
		gThis.m_jFileSelector.append(gThis.m_jSelectedFileName);
		gThis.m_jSwfUpload = $('<div class="' + gThis._GetClass('AddFiles') + '"/>').append('<span id="' + gThis.GetId() + '__upload"/>');
		gThis.m_jFileSelector.append(gThis.m_jSwfUpload);
		gThis.m_jChooseButton = $('<a href="#" class="button"><span><img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select + '</span></a>');
		gThis.m_jFileSelector.append($('<span class="browse-pictures"/>').append(gThis.m_jChooseButton));
		gThis.m_jQueue = $('<ul class="' + gThis._GetClass('Queue') + '"/>');
		gThis.m_jFileSelector.append(gThis.m_jQueue);
		gThis.m_jFilesDatagrid = $('<div/>');
		gThis.m_jFileSelector.append(gThis.m_jFilesDatagrid);
		gThis.m_jSelectedFiles = $('<div class="' + gThis._GetClass('SelectedTable') + '"/>');
		gThis.m_jFileSelector.append(gThis.m_jSelectedFiles);
		gThis.m_jFileField = $('<input type="hidden" name="' + gThis.GetName() + '[file]"/>');
		gThis.m_jFileSelector.append(gThis.m_jFileField);
		jRepetitionNode.append(gThis.m_jFileSelector);
		
		gThis.m_jFileRepeat = $('<div class="bg-repeat"/>').append('<label for="' + gThis.GetId() + '__position">' + GForm.Language.colour_scheme_picker_background_repeat + '</label>');
		gThis.m_jFileRepeatField = $('<select name="' + gThis.GetName() + '[repeat]" id="' + gThis.GetId() + '__position"/>');
		var asRepeatModes = [
			['no-repeat', GForm.Language.colour_scheme_picker_background_repeat_no],
			['repeat-x', GForm.Language.colour_scheme_picker_background_repeat_x],
			['repeat-y', GForm.Language.colour_scheme_picker_background_repeat_y],
			['repeat', GForm.Language.colour_scheme_picker_background_repeat_xy]
		];
		for (var i = 0; i < asRepeatModes.length; i++) {
			gThis.m_jFileRepeatField.append('<option value="' + asRepeatModes[i][0] + '"' + ((asRepeatModes[i][0] == 'repeat-x') ? ' selected="selected"' : '') + '>' + asRepeatModes[i][1] + '</option>');
		}
		gThis.m_jFileRepeat.append($('<span class="field"/>').append(gThis.m_jFileRepeatField));
		jRepetitionNode.append(gThis.m_jFileRepeat);
		
		gThis.m_jFilePosition = $('<div class="bg-position"/>').append('<label>' + GForm.Language.colour_scheme_picker_background_position + '</label>');
		jPositionRadios = $('<div/>');
		var asPositions = [
			'0 0', 'center 0', 'right 0',
			'0 center', 'center center', 'right center',
			'0 bottom', 'center bottom', 'right bottom'
		];
		for (var i = 0; i < asPositions.length; i++) {
			jPositionRadios.append('<input type="radio" name="' + gThis.GetName() + '[position]" value="' + asPositions[i] + '"' + ((asPositions[i] == '0 0') ? ' checked="checked"' : '') + '/>');
		}
		gThis.m_jFilePosition.append(jPositionRadios);
		jRepetitionNode.append(gThis.m_jFilePosition);
		
		gThis.m_jField = jRepetitionNode.find('input');
		
		return jRepetitionNode;
	};
	
	gThis._OnChoose = GEventHandler(function(eEvent) {
		gThis.m_jFilesDatagrid.slideToggle(250);
		if (!gThis.m_gFilesDatagrid) {
			gThis._InitFilesDatagrid();
		}
		return false;
	});
	
	gThis._InitializeEvents = function() {
		
		gThis.m_jChooseButton.click(gThis._OnChoose);

		gThis.m_jFieldColourType.change(function() {
			gThis.UpdatePreview();
		});
		
		gThis.m_jFieldColourStart.ColorPicker({
			color: '#' + gThis.m_jFieldColourStart.val(),
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			},
			onShow: function(colpkr) {
				$(colpkr).fadeIn(250);
				gThis.m_jFieldColourStart.closest('.field').addClass('focus');
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(250);
				gThis.m_jFieldColourStart.triggerHandler('change');
				gThis.m_jFieldColourStart.closest('.field').removeClass('focus');
				return false;
			},
			onChange: function(hsb, hex, rgb) {
				gThis.UpdatePreview();
				gThis.m_jFieldColourStart.val(hex);
			}
		}).change(GEventHandler(function(eEvent) {
			gThis.UpdatePreview();
		}));
		gThis.m_jFieldColourEnd.ColorPicker({
			color: '#' + gThis.m_jFieldColourEnd.val(),
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			},
			onShow: function(colpkr) {
				$(colpkr).fadeIn(250);
				gThis.m_jFieldColourEnd.closest('.field').addClass('focus');
				return false;
			},
			onHide: function(colpkr) {
				$(colpkr).fadeOut(250);
				gThis.m_jFieldColourEnd.triggerHandler('change');
				gThis.m_jFieldColourEnd.closest('.field').removeClass('focus');
				return false;
			},
			onChange: function(hsb, hex, rgb) {
				gThis.UpdatePreview();
				gThis.m_jFieldColourEnd.val(hex);
			}
		}).change(GEventHandler(function(eEvent) {
			gThis.UpdatePreview();
		}));
		
		gThis.m_jFieldColourType.GSelect().focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		}));
		
		gThis.m_jFileRepeatField.GSelect().focus(GEventHandler(function(eEvent) {
			$(this).closest('.field').addClass('focus');
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.field').removeClass('focus');
		})).change(GEventHandler(function(eEvent) {
			gThis.UpdatePreview();
		}));
		
		gThis.m_jFilePosition.find('input').click(GEventHandler(function(eEvent) {
			gThis.UpdatePreview();
		}));
		
	};

	gThis.Reset = function() {
		gThis.m_jField.eq(0).val(gThis.m_oOptions.sDefault).change();
		gThis.UpdatePreview();
	};
	
	gThis.Focus = function() {
		/*gThis.m_jFieldColourType.focus();*/
		return true;
	};
	
	gThis._OnClickRow = function(gDg, sId) {
		var oFile = gThis.m_gFilesDatagrid.GetRow(sId);
		if (oFile.dir) {
			if (oFile.name == '..') {
				gThis.m_sCWD = gThis.m_sCWD.substr(0, gThis.m_sCWD.lastIndexOf('/', gThis.m_sCWD.length - 2));
			}
			else {
				gThis.m_sCWD += oFile.name + '/';
			}
			gThis.m_jSwfUpload.swfupload('addPostParam', 'path', gThis.m_sCWD);
			gThis._RefreshFiles();
			return false;
		}
		return true;
	};
	
	gThis._OnSelect = function(gDg, sId) {
		var oFile = gDg.GetRow(sId);
		if (!oFile.dir) {
			gThis.SetValue({
				file: oFile.path.substr(gThis.m_oOptions.sFilePath.length)
			});
		}
	};

	gThis._OnDeselect = function(gDg, sId) {
		gThis.SetValue('');
		gThis.m_jFileField.triggerHandler('change');
	};
	
	gThis._Initialize = function() {
		var oValue = gThis.GetValue();
		var sPath = gThis.m_oOptions.sFilePath + oValue.file;
		sPath = sPath.substr(0, sPath.lastIndexOf('/') + 1);
		gThis.m_sCWD = sPath;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitUploader();
			gThis.m_bShown = true;
		}
	};

	gThis._ProcessFile = function(oRow) {
		if (oRow.dir) {
			if (oRow.name == '..') {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons['cdup'] + '" alt=""/>';
			}
			else {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons['directory'] + '" alt=""/>';
			}
		}
		else {
			var sExtension = oRow.name.substr(oRow.name.lastIndexOf('.') + 1);
			if (gThis.m_oOptions.oTypeIcons[sExtension] == undefined) {
				sExtension = 'unknown';
			}
			if ((sExtension == 'png') || (sExtension == 'jpg') || (sExtension == 'gif')) {
				oRow.thumbpreview = '<a href="' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + oRow.path + '" class="show-thumb"><img src="' + gThis.m_oOptions.oTypeIcons[sExtension] + '" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
			}
			else {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons[sExtension] + '" alt=""/>';
			}
		}
		return oRow;
	};

	gThis._InitUploader = function() {
		var sFileTypes = '';
		for (var i = 0; i < gThis.m_oOptions.asFileTypes.length; i++) {
			sFileTypes += '; *.' + gThis.m_oOptions.asFileTypes[i];
		}
		sFileTypes = sFileTypes.substr(2);
		var oPostParams = {};
		oPostParams[gThis.m_oOptions.sSessionName] = gThis.m_oOptions.sSessionId;
		gThis.m_jSwfUpload.swfupload({
			upload_url: gThis.m_oOptions.sUploadUrl,
			file_size_limit: gThis.m_oOptions.iMaxFileSize,
			file_types: sFileTypes,
			file_types_description: gThis.m_oOptions.sFileTypesDescription,
			file_upload_limit: 0,
			file_queue_limit: 0,
			button_image_url: gThis._GetImage('UploadButton'),
			button_placeholder_id: gThis.GetId() + '__upload',
			button_width: gThis.m_oOptions.iWidth,
			button_height: gThis.m_oOptions.iHeight,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			flash_url: GCore.DESIGN_PATH + gThis.m_oOptions.sSwfUploadUrl,
			post_params: oPostParams
		});
		gThis.m_jSwfUpload.bind("fileQueued", gThis.OnFileQueued);
		gThis.m_jSwfUpload.bind("uploadComplete", gThis.OnUploadComplete);
		gThis.m_jSwfUpload.bind("uploadSuccess", gThis.OnUploadSuccess);
		gThis.m_jSwfUpload.bind("uploadProgress", gThis.OnUploadProgress);
		gThis.m_jSwfUpload.bind("uploadError", gThis.OnUploadError);
	};

	gThis.OnFileQueued = function(eEvent, oFile) {
		if (gThis.m_iUploadsInProgress++ == 0) {
			gThis.m_iLockId = gThis.m_gForm.Lock(GForm.Language.file_selector_form_blocked, GForm.Language.file_selector_form_blocked_description);
		}
		gThis.m_jSwfUpload.swfupload("startUpload");
		var jLi = $('<li class="upload__' + oFile.index + '"/>');
		jLi.append('<h4>' + oFile.name + '</h4>');
		jLi.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		jLi.append('<div class="' + gThis._GetClass('ProgressBar') + '"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		gThis.m_jQueue.append(jLi);
	};

	gThis.OnDelete = function() {
		gThis.m_jSwfUpload.swfupload('cancelUpload', sFid);
	};

	gThis.OnUploadProgress = function(eEvent, oFile, iCompleted, iTotal) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		jLi.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + Math.ceil(iCompleted / 1024) + 'kB / ' + Math.ceil(iTotal / 1024) + 'kB');
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', (iCompleted / iTotal * 100) + '%');
	};

	gThis.OnUploadError = function(eEvent, oFile, iErrorCode, sMessage) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadError'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_error);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		GAlert(GForm.Language.file_selector_upload_error, sMessage);
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadSuccess = function(eEvent, oFile, sServerData, sResponse) {
		if (sServerData.substr(0, 11) != 'response = ') {
			gThis.OnUploadError(eEvent, oFile, 0, sServerData);
			return;
		}
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadSuccess'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_success);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		eval("var oResponse = " + sServerData.substr(11) + ";");
		if (oResponse.sFilename == undefined) {
			gThis.OnUploadError(eEvent, oFile, 0, GForm.Language.localfile_processing_error);
			return;
		}
		gThis.SetValue({
			file: (gThis.m_sCWD + oResponse.sFilename).substr(gThis.m_oOptions.sFilePath.length)
		});
		gThis._RefreshFiles();
		if (gThis.m_gFilesDatagrid) {
			gThis.m_gFilesDatagrid.LoadData();
		}
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadComplete = function(eEvent, oFile) {
		if (--gThis.m_iUploadsInProgress <= 0) {
			gThis.m_iUploadsInProgress = 0;
			gThis.m_gForm.Unlock(gThis.m_iLockId);
		}
	};

	gThis._InitColumns = function() {

	  var column_path = new GF_Datagrid_Column({
			id: 'path',
			caption: GForm.Language.localfile_fullpath,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_LEFT
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});

		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.localfile_filename,
			appearance: {
				width: 150,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});

		var column_size = new GF_Datagrid_Column({
			id: 'size',
			appearance: {
				width: 65,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			caption: GForm.Language.localfile_filesize
		});

		var column_mtime = new GF_Datagrid_Column({
			id: 'mtime',
			appearance: {
				width: 120,
				visible: false
			},
			caption: GForm.Language.localfile_filemtime
		});

		var column_owner = new GF_Datagrid_Column({
			id: 'owner',
			appearance: {
				width: 70,
				visible: false
			},
			caption: GForm.Language.localfile_fileowner
		});

		return [
			column_path,
			column_thumb,
			column_name,
			column_size,
			column_mtime,
			column_owner
		];

	};
	
	gThis._RefreshFiles = function() {
		gThis.m_oOptions.fLoadFiles({
			path: gThis.m_sCWD
		}, GCallback(gThis._OnFilesLoaded));
	};
	
	gThis._OnFilesLoaded = GEventHandler(function(eEvent) {
		if ((eEvent == undefined) || (eEvent.files == undefined) || (eEvent.cwd == undefined)) {
			return;
		}
		gThis.m_sCWD = eEvent.cwd;
		if (gThis.m_gDataProvider) {
			gThis.m_gDataProvider.ChangeData(eEvent.files);
			gThis.m_gFilesDatagrid.LoadData();
		}
	});

	gThis._Delete = function(iDg, sId) {
		var iAlertId = GWarning(GForm.Language.localfile_delete_warning, GForm.Language.localfile_delete_warning_description, {
			bAutoExpand: true,
			aoPossibilities: [
				{mLink: function() {
					GCore.StartWaiting();
					GAlert.Destroy(iAlertId);
					gThis.m_oOptions.fDeleteFile({
						file: sId
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						var oValue = gThis.GetValue();
						if (sId == gThis.m_oOptions.sFilePath + oValue.file) {
							gThis.m_gFilesDatagrid.ClearSelection();
						}
						gThis._RefreshFiles();
					}));
				}, sCaption: GForm.Language.localfile_ok},
				{mLink: GAlert.DestroyThis, sCaption: GForm.Language.localfile_cancel}
			]
		});
	};
	
	gThis._OnDataLoaded = function(dDg) {
		dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
	};
	
	gThis._InitFilesDatagrid = function() {

		var aoColumns = gThis._InitColumns();
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'path'
		}, []);
		
		var gActionDelete = new GF_Action({
			img: gThis._GetImage('DeleteIcon'),
			caption: GForm.Language.localfile_delete,
			action: gThis._Delete,
			condition: function(oRow) {
				return !oRow.dir;
			}
		});

    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 30,
				key: 'path',
				only_one_selected: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					return gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
				},
				loaded: gThis._OnDataLoaded,
				process: gThis._ProcessFile,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				click_row: gThis._OnClickRow
			},
			row_actions: [
				gActionDelete
			],
			columns: aoColumns
    };

		gThis.m_gFilesDatagrid = new GF_Datagrid(gThis.m_jFilesDatagrid, oOptions);
		
		var oValue = gThis.GetValue();
		var sFile = oValue.file;
		if (sFile != '') {
			gThis.m_gFilesDatagrid.m_asSelected = [gThis.m_oOptions.sFilePath + sFile];
		}
		
		gThis._RefreshFiles();
	};
	
}, oDefaults);


/*
* COLUMNS
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sColumnsClass: 'layout-two-columns',
		sColumnClass: 'column'
	},
	aoFields: [],
	agFields: [],
	sClass: ''
};

var GFormColumns = GCore.ExtendClass(GFormContainer, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>');
		gThis.m_jNode.addClass(gThis._GetClass('Columns'));
		gThis.m_jNode.addClass(gThis.m_oOptions.sClass);
		gThis.m_jNode.attr('id', gThis.m_oOptions.sName);
		gThis.m_jNode.append(gThis.RenderChildren());
		gThis.m_jNode.children().addClass(gThis._GetClass('Column'));
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jNode.bind('GFormShow', function() {
			gThis.m_gForm.m_bFocused = false;
			gThis.OnShow();
		});
	};
	
	gThis.OnShow = function() {
		if (gThis.m_gForm.m_bDontFocus || (gThis.m_gForm != gThis.m_gParent)) {
			gThis.m_gForm.m_bFocused = true;
			gThis.m_gForm.m_bDontFocus = false;
		}
		for (var i = 0; i < gThis.m_oOptions.agFields.length; i++) {
			gThis.m_oOptions.agFields[i].OnShow();
			if (!gThis.m_gForm.m_bFocused) {
				gThis.m_gForm.m_bFocused = gThis.m_oOptions.agFields[i].Focus();
			}
		}
		return gThis.m_gForm.m_bFocused;
	};
	
}, oDefaults);


/*
* CONSTANT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormConstant = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field')).addClass('field-constant');
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="' + gThis.GetName(sId) + '" id="' + gThis.GetId(sId) + '" disabled="disabled"/>');
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		if (gThis.m_oOptions.sPrefix != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.sPrefix);
			jRepetitionNode.append(jPrefix);
		}
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		gThis._UpdateRepetitionButtons();
		if (!gThis.m_bShown && gThis.m_bRepeatable) {
			gThis._InitializeEvents('new-0');
		}
		gThis.m_bShown = true;
		if (gThis.m_bRepeatable) {
			for (var i in gThis.m_oRepetitions) {
				if (!gThis.m_oRepetitions[i].m_bResized) {
					gThis.m_oRepetitions[i].m_bResized = true;
					var iWidth = parseInt(gThis._GetField(i).css('width'));
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).length) {
						iWidth -= (gThis._GetField(i).offset().left - gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
					}
					if (gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).length) {
						iWidth -= gThis.m_oRepetitions[i].find('.' + gThis._GetClass('Suffix')).width() + 4;
					}
					gThis._GetField(i).eq(i).css('width', iWidth);
				}
			}
		}
		else {
			if (!gThis.m_bResized) {
				gThis.m_bResized = true;
				var iWidth = parseInt(gThis.m_jField.css('width'));
				if (gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).length) {
					iWidth -= (gThis.m_jField.offset().left - gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
				}
				if (gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).length) {
					iWidth -= gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).width() + 4;
				}
				gThis.m_jField.css('width', iWidth);
			}
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault);
	};
	
}, oDefaults);


/*
* DATE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sCalendarIcon: '_images_panel/icons/buttons/calendar.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	sMinDate: null,
	sMaxDate: null
};

var GFormDate = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis.m_jTrigger;
	
	gThis._Constructor = function() {
		gThis.m_jTrigger = $('<img style="width: 16px; height: 16px;" src="' + gThis._GetImage('CalendarIcon') + '" alt=""/>');
		gThis.m_oOptions.sSuffix = gThis.m_jTrigger.css('cursor', 'pointer');
	};
	
	gThis.OnShow = function() {
		gThis.m_bShown = true;
		if (!gThis.m_bResized) {
			gThis.m_bResized = true;
			var iWidth = parseInt(gThis.m_jField.css('width'));
			if (gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).length) {
				iWidth -= (gThis.m_jField.offset().left - gThis.m_jNode.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
			}
			if (gThis.m_jNode.find('.' + gThis._GetClass('Suffix')).length) {
				iWidth -= 20;
			}
			gThis.m_jField.css('width', iWidth);
			gThis.m_jField.datepicker({
				minDate: gThis.m_oOptions.sMinDate,
				maxDate: gThis.m_oOptions.sMaxDate
			});
		}
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jTrigger.click(function() {
			gThis.m_jField.datepicker('show');
		});
		
		gThis.m_jField.unbind('change', gThis.OnValidate).change(gThis.OnValidate);
	};
	
}, oDefaults);


/*
* FAVOURITE CATEGORIES TREE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-tree',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sItemNameClass: 'item-name',
		sExpanderClass: 'expander',
		sExpandedClass: 'expanded',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sExpandAllClass: 'expand-all',
		sRetractAllClass: 'retract-all',
		sListClass: 'selected-list'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sDelete: '_images_panel/icons/buttons/delete.png',
		sSave: '_images_panel/icons/buttons/save.png',
		sRestore: '_images_panel/icons/buttons/clean.png',
		sWaiting: '_images_panel/icons/loading/indicator.gif'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	bSortable: false,
	bSelectable: true,
	bChoosable: false,
	bClickable: false,
	bDeletable: false,
	oItems: {},
	fOnClick: GCore.NULL,
	fOnSaveOrder: GCore.NULL,
	fOnAdd: GCore.NULL,
	fOnAfterAdd: GCore.NULL,
	fOnDelete: GCore.NULL,
	fOnAfterDelete: GCore.NULL,
	sActive: '',
	sAddItemPrompt: '',
	bPreventDuplicates: true
};

var GFormFavouriteCategories = GCore.ExtendClass(GFormTree, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_jTree;
	gThis.m_jOptions;
	gThis.m_oItems;
	gThis.m_jExpandAll;
	gThis.m_jRetractAll;
	gThis.m_jFieldWrapper;
	gThis.m_jItemPlaceholder;
	gThis.m_jItemDragged;
	gThis.m_oItems = {};
	gThis.m_jList;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jFieldWrapper = $('<div/>');
		gThis.m_jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jExpandAll = $('<a href="#"/>').text(GForm.Language.tree_expand_all);
		gThis.m_jRetractAll = $('<a href="#"/>').text(GForm.Language.tree_retract_all);
		gThis.m_jNode.append(gThis._CreateSelectedList());
		if (gThis.m_oOptions.fGetChildren instanceof Function) {
			gThis.m_jNode.append($('<p class="' + gThis._GetClass('ExpandAll') + '"/>').append(gThis.m_jRetractAll));
		}
		else {
			//gThis.m_jNode.append($('<p class="' + gThis._GetClass('ExpandAll') + '"/>').append(gThis.m_jExpandAll));
			gThis.m_jNode.append($('<p class="' + gThis._GetClass('RetractAll') + '"/>').append(gThis.m_jRetractAll));
		}
		gThis.m_jTree = $('<ul/>');
		gThis.m_jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		gThis.Update();
		gThis._PrepareOptions();
		window.setTimeout(gThis.ResetExpansion, 500);
	};
	
	gThis._CreateSelectedList = function() {
		gThis.m_jList = $('<div/>').addClass(gThis._GetClass('List'));
		var jTable = $('<table cellspacing="0"/>');
		var jThead = $('<thead/>');
		var jTr = $('<tr/>');
		var i;
		for (i = 0; i < gThis.m_oOptions.aoColumns.length; i++) {
			jTr.append($('<th>' + gThis.m_oOptions.aoColumns[i].caption + '</th>').css('width', (gThis.m_oOptions.aoColumns[i].width != undefined) ? gThis.m_oOptions.aoColumns[i].width + 'px' : 'auto'));
		}
		jTr.append('<th style="width: 16px;">&nbsp;</th>');
		jThead.append(jTr);
		jTable.append(jThead);
		var jBody = $('<tbody/>');
		jTable.append(jBody);
		gThis.m_jList.append('<label>' + gThis.m_oOptions.sLabel + '</label>');
		gThis.m_jList.append(jTable);
		return gThis.m_jList;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_bShown = true;
			var iSelected = gThis.m_jFieldWrapper.find('input').length;
			var fGetInfo = gThis.m_oOptions.fGetSelectedInfo;
			gThis.m_oOptions.fGetSelectedInfo = GCore.NULL;
			var asIds = [];
			for (var i = 0; i < iSelected; i++) {
				var sId = gThis.m_jFieldWrapper.find('input').eq(i).attr('value');
				gThis._AddToList(sId);
				asIds.push(sId);
				gThis.m_jList.find('tr.id__' + sId + ' td:first').html('<img src="' + gThis._GetImage('Waiting') + '" alt=""/>');
			};
			if (asIds.length) {
				fGetInfo({
					id: asIds
				}, GCallback(gThis._OnInfoLoaded));
			}
			gThis.m_oOptions.fGetSelectedInfo = fGetInfo;
		}
	};
	
	gThis._OnSelect = GEventHandler(function(eEvent) {
		gThis.m_jFieldWrapper.find('input[value="' + $(this).attr('value') + '"]').remove();
		if ($(this).is(':checked')) {
			gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '[]" value="' + $(this).attr('value') + '"/>');
			gThis._AddToList($(this).attr('value'));
		}
		else {
			gThis._RemoveFromList($(this).attr('value'));
		}
	});
	
	gThis._AddToList = function(sId) {
		if (gThis.m_jList.find('tr.id__' + sId).length) {
			return;
		}
		var jTr = $('<tr class="id__' + sId + '"/>');
		for (var i in gThis.m_oOptions.aoColumns) {
			var oColumn = gThis.m_oOptions.aoColumns;
			var jTd = $('<td/>');
			jTr.append(jTd);
		}
		jTd = $('<td/>');
		var jTrigger = $('<a href="#" title="' + GForm.Language.tree_deselect + '"/>');
		jTrigger.click(GEventHandler(function(eEvent) {
			var sId = $(this).closest('tr').attr('class').substr(4);
			gThis.m_jTree.find('input:checkbox[value="' + sId + '"]:checked').click();
			gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').remove();
			gThis._RemoveFromList(sId);
			return false;
		}));
		jTrigger.append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.tree_deselect + '"/>');
		jTd.append(jTrigger);
		jTr.append(jTd);
		if (gThis.m_oOptions.fGetSelectedInfo instanceof Function) {
			jTr.find('td:first').html('<img src="' + gThis._GetImage('Waiting') + '" alt=""/>');
			gThis.m_oOptions.fGetSelectedInfo({
				id: sId
			}, GCallback(gThis._OnInfoLoaded));
		}
		else {
			jTr.find('td:first').text(gThis.m_jTree.find('label:has(input:checkbox[value="' + sId + '"])').text());
		}
		gThis.m_jList.find('tbody').append(jTr);
	};
	
	gThis._OnInfoLoaded = GEventHandler(function(eEvent) {
		for (var j in eEvent.rows) {
			var jColumns = gThis.m_jList.find('tr.id__' + eEvent.rows[j].id + ' td');
			for (var i in gThis.m_oOptions.aoColumns) {
				if (eEvent.rows[j].values[i] != undefined) {
					jColumns.eq(i).html(eEvent.rows[j].values[i]);
				}
			}
		}
	});
	
	gThis._RemoveFromList = function(sId) {
		var jTr = gThis.m_jList.find('tr.id__' + sId);
		jTr.remove();
	};
	
}, oDefaults);



/*
* FIELDSET REPEATABLE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sAddButtonClass: 'add-repetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	oImages: {
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	aoFields: [],
	agFields: [],
	oRepeat: {
		iMin: 1,
		iMax: 1
	}
};

var GFormFieldsetRepeatable = GCore.ExtendClass(GFormFieldset, function() {
	
}, oDefaults);


/*
* FILE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-file',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'file',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormFile = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
	};
	
}, oDefaults);


/*
* FONT STYLE
*/

var oDefaults = {
		sName: '',
		sLabel: '',
		oClasses: {
			sFieldClass: 'field-font',
			sFieldSpanClass: 'field',
			sPrefixClass: 'prefix',
			sSuffixClass: 'suffix',
			sFocusedClass: 'focus',
			sInvalidClass: 'invalid',
			sRequiredClass: 'required',
			sWaitingClass: 'waiting',
			sFieldRepetitionClass: 'repetition',
			sAddRepetitionClass: 'add-field-repetition',
			sRemoveRepetitionClass: 'remove-field-repetition',
			sColourTypeClass: 'colour-type',
			sColourStartClass: 'colour-start',
			sColourEndClass: 'colour-end',
			sColourPreviewClass: 'colour-preview',
			sFontPreviewClass: 'font-preview'
		},
		oImages: {
			sBold: '_images_panel/icons/font-style-bold.png',
			sUnderline: '_images_panel/icons/font-style-underline.png',
			sItalic: '_images_panel/icons/font-style-italic.png',
			sUppercase: '_images_panel/icons/font-style-uppercase.png',
			sAddRepetition: '_images_panel/icons/buttons/add.png',
			sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
		},
		sFieldType: 'text',
		sDefault: '',
		aoRules: [],
		sComment: '',
		sSelector: ''
};

var GFormFontStyle = GCore.ExtendClass(GFormTextField, function() {
		
		var gThis = this;
		
		gThis.m_jFieldFontFamily;
		gThis.m_jFieldFontStyleBold;
		gThis.m_jFieldFontStyleUnderline;	
		gThis.m_jFieldFontStyleItalic;
		gThis.m_jFieldFontStyleUppercase;
		gThis.m_jFieldFontColour;
		gThis.m_jFieldFontSize;
		
		gThis._PrepareNode = function() {
			gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
			var jLabel = $('<label for="' + gThis.GetId() + '"/>');
			jLabel.text(gThis.m_oOptions.sLabel);
			if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
					jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
			}
			gThis.m_jLabel = jLabel;
			gThis.m_jNode.append(jLabel);
			gThis.m_jNode.append(gThis._AddField());
			if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
				gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
			}
		};
		
		gThis.GetValue = function(sRepetition) {
			if (gThis.m_jField == undefined) {
					return '';
			}
			return gThis.m_jField.eq(0).val();
		};
		
		gThis.SetValue = function(mValue, sRepetition) {
			if (mValue == undefined) {
				return;
			}
			gThis.m_jFieldFontFamily.val(mValue.family).triggerHandler('change');
			gThis.m_jFieldFontStyleBold.val(mValue.bold);
			gThis.m_jFieldFontStyleUnderline.val(mValue.underline);	
			gThis.m_jFieldFontStyleItalic.val(mValue.italic);
			gThis.m_jFieldFontStyleUppercase.val(mValue.uppercase);
			gThis.m_jFieldFontColour.val(mValue.colour);
			gThis.m_jFieldFontSize.val(mValue.size).triggerHandler('change');
			gThis.UpdatePreview();
		};

		gThis.UpdatePreview = function(){
			
			var bold;
			var underline;
			var italic;
			var uppercase;
			var colour;
			var bDarkBg;
			
			if(gThis.m_jFieldFontColour.val().substr(0, 11) == 'transparent'){
				var r = '00';
				var g = '00';
				var b = '00';
			}else{
				var r = gThis.m_jFieldFontColour.val().substr(0, 2);
				var g = gThis.m_jFieldFontColour.val().substr(2, 2);
				var b = gThis.m_jFieldFontColour.val().substr(4, 2);
				eval('bDarkBg = (0x' + (r ? r : '00') + ' + 0x' + (g ? g : '00') + ' + 0x' + (b ? b : '00') + ') / 3 > 127;');
			}
			
			gThis.m_jColourPreviewNode.css({
				'background-color': "#" + gThis.m_jFieldFontColour.val()
			});

			if(gThis.m_jFieldFontStyleBold.val()==1){
				gThis.m_jImgFontStyleBold.css({'background-color':'#efefef'});
				bold = 'bold';
			}
			else{
				gThis.m_jImgFontStyleBold.css({'background-color':'#ffffff'});
				bold = 'normal';
			}
			
			if(gThis.m_jFieldFontStyleUnderline.val()==1){
				gThis.m_jImgFontStyleUnderline.css({'background-color':'#efefef'});
				underline = 'underline';
			}else{
				gThis.m_jImgFontStyleUnderline.css({'background-color':'#ffffff'});
				underline = 'none';
			}
			
			if(gThis.m_jFieldFontStyleItalic.val()==1){
				italic = 'italic';
				gThis.m_jImgFontStyleItalic.css({'background-color':'#efefef'});
			}else{
				gThis.m_jImgFontStyleItalic.css({'background-color':'#ffffff'});
				italic = 'normal';
			}
			
			if(gThis.m_jFieldFontStyleUppercase.val()==1){
				uppercase = 'uppercase';
				gThis.m_jImgFontStyleUppercase.css({'background-color':'#efefef'});
			}else{
				gThis.m_jImgFontStyleUppercase.css({'background-color':'#ffffff'});
				uppercase = 'none';
			}
			
			gThis.m_jFontPreviewNode.css({
				'font-weight': bold,
				'text-decoration': underline,
				'font-style': italic,
				'font-family': gThis.m_jFieldFontFamily.val(),
				'font-size': gThis.m_jFieldFontSize.val()+"px",
				'color': "#"+gThis.m_jFieldFontColour.val(),
				'text-transform': uppercase,
				'background-color': bDarkBg ? '#000000' : '#ffffff'
			});
				
		};
		
		gThis._AddField = function(sId) {
			
			
			var jFontStyleNode = $('<span class="' + gThis._GetClass('FieldSpan') + ' style"/>');
			var jFontFamilyNode = $('<span class="' + gThis._GetClass('FieldSpan') + ' family"/>');
			var jFontSizeNode = $('<span class="' + gThis._GetClass('FieldSpan') + ' size"/>');
			var jFontPreviewNode = $('<span class="' + gThis._GetClass('FontPreview') + '"/>');
			var jFontColourNode = $('<span class="' + gThis._GetClass('FieldSpan') + ' colour"/>');
			var jColourPreviewNode = $('<span class="' + gThis._GetClass('ColourPreview') + '"/>');
			
			var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
			
			var opt = {'cursor':'pointer'};
			
			var jImgFontStyleBold = $('<img src="'+gThis._GetImage('Bold')+'" />').css(opt);
			var jFieldFontStyleBold = $('<input type="hidden" name="' + gThis.GetName() + '[bold]" value="0"/>');
			
			var jImgFontStyleUnderline = $('<img src="'+gThis._GetImage('Underline')+'" />').css(opt);
			var jFieldFontStyleUnderline = $('<input type="hidden" name="' + gThis.GetName() + '[underline]" value="0"/>');
			
			var jImgFontStyleItalic = $('<img src="'+gThis._GetImage('Italic')+'" />').css(opt);
			var jFieldFontStyleItalic = $('<input type="hidden" name="' + gThis.GetName() + '[italic]" value="0"/>');
			
			var jImgFontStyleUppercase = $('<img src="'+gThis._GetImage('Uppercase')+'" />').css(opt);
			var jFieldFontStyleUppercase = $('<input type="hidden" name="' + gThis.GetName() + '[uppercase]" value="0"/>');

			var jFieldFontFamily = $('<select name="' + gThis.GetName() + '[family]" />');
			for (var i = 0; i < gThis.m_oOptions.aoTypes.length; i++) {
					var oType = gThis.m_oOptions.aoTypes[i];
					jFieldFontFamily.append('<option value="' + oType.sValue + '">' + oType.sLabel + '</option>');
			}
			
			var jFieldFontSize = $('<select name="' + gThis.GetName() + '[size]" />');
			for (var i = 5; i < 25; i++) {
					var oType = gThis.m_oOptions.aoTypes[i];
					jFieldFontSize.append('<option value="' + i + '">' + i + ' px</option>');
			}
			var jFieldFontColour = $('<input type="text" name="' + gThis.GetName() + '[colour]" />');
			
			jFontPreviewNode.html('Lorem ipsum');
			
			jFontStyleNode.append(jImgFontStyleBold).append(jImgFontStyleUnderline).append(jImgFontStyleItalic).append(jImgFontStyleUppercase);
			jFontStyleNode.append(jFieldFontStyleBold).append(jFieldFontStyleUnderline).append(jFieldFontStyleItalic).append(jFieldFontStyleUppercase);
			
			jFontFamilyNode.append(jFieldFontFamily);
			jFontSizeNode.append(jFieldFontSize);
			jFontColourNode.append(jFieldFontColour);
			
			jRepetitionNode.append($('<span class="' + gThis._GetClass('ColourPreview') + '-container"/>').append(jColourPreviewNode)).append(jFontFamilyNode).append(jFontColourNode).append(jFontStyleNode).append(jFontSizeNode).append(jFontPreviewNode);
			gThis.m_jField = jRepetitionNode.find('input,select');
		
			gThis.m_jRepetitionNode = jRepetitionNode;
			gThis.m_jFontFamilyNode = jFontFamilyNode;
			gThis.m_jFontSizeNode = jFontSizeNode;
			gThis.m_jFieldFontFamily = jFieldFontFamily;
			gThis.m_jFieldFontSize = jFieldFontSize;
			gThis.m_jImgFontStyleBold = jImgFontStyleBold;
			gThis.m_jImgFontStyleUnderline = jImgFontStyleUnderline;
			gThis.m_jImgFontStyleItalic = jImgFontStyleItalic;
			gThis.m_jImgFontStyleUppercase = jImgFontStyleUppercase;
			gThis.m_jFieldFontStyleBold = jFieldFontStyleBold;
			gThis.m_jFieldFontStyleUnderline = jFieldFontStyleUnderline;
			gThis.m_jFieldFontStyleItalic = jFieldFontStyleItalic;
			gThis.m_jFieldFontStyleUppercase = jFieldFontStyleUppercase;
			gThis.m_jFontPreviewNode = jFontPreviewNode;
			gThis.m_jFontColourNode = jFontColourNode;
			gThis.m_jFieldFontColour = jFieldFontColour;
			gThis.m_jColourPreviewNode = jColourPreviewNode;
			
			return jRepetitionNode;
		};
		
		gThis.OnShow = function() {
				gThis.m_bShown = true;
		};
		
		gThis._InitializeEvents = function() {
			gThis.m_jField.bind('change keyup',function(){
				gThis.UpdatePreview();
			});
			
			gThis.m_jImgFontStyleBold.click(function(){
				if(gThis.m_jFieldFontStyleBold.val()==1){
					gThis.m_jImgFontStyleBold.css({'background-color':'#ffffff'});
					gThis.m_jFieldFontStyleBold.val(0).triggerHandler('change');	
				}
				else{
					gThis.m_jImgFontStyleBold.css({'background-color':'#efefef'});
					gThis.m_jFieldFontStyleBold.val(1).triggerHandler('change');	
				}
				gThis.UpdatePreview();
			});
			
			gThis.m_jImgFontStyleUnderline.click(function(){
				if(gThis.m_jFieldFontStyleUnderline.val()==1){
					gThis.m_jImgFontStyleUnderline.css({'background-color':'#ffffff'});
					gThis.m_jFieldFontStyleUnderline.val(0).triggerHandler('change');	
				}
				else{
					gThis.m_jImgFontStyleUnderline.css({'background-color':'#efefef'});
					gThis.m_jFieldFontStyleUnderline.val(1).triggerHandler('change');	
				}
				gThis.UpdatePreview();
			});
			
			gThis.m_jImgFontStyleItalic.click(function(){
				if(gThis.m_jFieldFontStyleItalic.val()==1){
					gThis.m_jImgFontStyleItalic.css({'background-color':'#ffffff'});
					gThis.m_jFieldFontStyleItalic.val(0).triggerHandler('change');	
				}
				else{
					gThis.m_jImgFontStyleItalic.css({'background-color':'#efefef'});
					gThis.m_jFieldFontStyleItalic.val(1).triggerHandler('change');	
				}
				gThis.UpdatePreview();
			});
			
			gThis.m_jImgFontStyleUppercase.click(function(){
				if(gThis.m_jFieldFontStyleUppercase.val()==1){
					gThis.m_jImgFontStyleUppercase.css({'background-color':'#ffffff'});
					gThis.m_jFieldFontStyleUppercase.val(0).triggerHandler('change');	
				}
				else{
					gThis.m_jImgFontStyleUppercase.css({'background-color':'#efefef'});
					gThis.m_jFieldFontStyleUppercase.val(1).triggerHandler('change');	
				}
				gThis.UpdatePreview();
			});
						
			gThis.m_jFieldFontFamily.GSelect().focus(GEventHandler(function(eEvent) {
				$(this).closest('.field').addClass('focus');
			})).blur(GEventHandler(function(eEvent) {
				$(this).closest('.field').removeClass('focus');
			}));
			
			gThis.m_jFieldFontSize.GSelect().focus(GEventHandler(function(eEvent) {
				$(this).closest('.field').addClass('focus');
			})).blur(GEventHandler(function(eEvent) {
				$(this).closest('.field').removeClass('focus');
			}));
			
			gThis.m_jFieldFontColour.ColorPicker({
				color: '#' + gThis.m_jFieldFontColour.val(),
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				},
				onShow: function(colpkr) {
					$(colpkr).fadeIn(250);
					gThis.m_jFieldFontColour.closest('.field').addClass('focus');
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(250);
					gThis.m_jFieldFontColour.triggerHandler('change');
					gThis.m_jFieldFontColour.closest('.field').removeClass('focus');
					return false;
				},
				onChange: function(hsb, hex, rgb) {
					gThis.UpdatePreview();
					gThis.m_jFieldFontColour.val(hex);
				}
			}).change(GEventHandler(function(eEvent) {
				gThis.UpdatePreview();
			}));
			
			gThis.UpdatePreview();
		};

		
		gThis.Reset = function() {
			gThis.m_jField.eq(0).val(gThis.m_oOptions.sDefault).change();
		};
		
}, oDefaults);

/*
* LAYOUT BOXES LIST
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	sComment: '',
	aoBoxes: [],
	oClasses: {
		sFieldClass: 'field-layout-boxes-list',
		sFieldTextClass: 'field-text',
		sFieldPriceClass: 'field-price',
		sFieldCheckboxClass: 'field-checkbox',
		sFieldSelectClass: 'field-select',
		sFieldSpanClass: 'field',
		sRangeColumnClass: 'price',
		sMinColumnClass: 'min',
		sMaxColumnClass: 'max',
		sOptionsColumnClass: 'options',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sNetPriceClass: 'net-price',
		sGrossPriceClass: 'gross-price'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sRemove: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormLayoutBoxesList = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_oBoxes = {};
	gThis.m_aBoxes = [];
	
	gThis.GetValue = function(sRepetition) {
		if (sRepetition == undefined) {
			var aoValues = [];
			var iLayoutBoxes = gThis.m_aBoxes.length;
			for (var i = 0; i < iLayoutBoxes; i++) {
				aoValues.push(gThis.GetValue(gThis.m_aBoxes[i]));
			}
			return aoValues;
		}
		else {
			var oBox = gThis.m_oBoxes[sRepetition];
			if (oBox == undefined) {
				return;
			}
			return {
				box: oBox.jBoxSelect.find('option:selected').attr('value'),
				span: isNaN(parseInt(oBox.jSpan.val())) ? 1 : parseInt(oBox.jSpan.val()),
				collapsed: oBox.jCollapsed.is(':checked')
			};
		}
	};
	
	gThis.Populate = function(mData) {
		while (gThis.m_aBoxes.length) {
			gThis.RemoveBox(gThis.m_aBoxes[0]);
		}
		gThis.SetValue(mData);
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (sRepetition == undefined) {
			if (mValue == '') {
				return;
			}
			for (var i in mValue) {
				gThis.SetValue(mValue[i], i);
			}
		}
		else {
			var oBox = gThis.m_oBoxes[sRepetition];
			if (oBox == undefined) {
				gThis.AddRepetition(sRepetition);
				oBox = gThis.m_oBoxes[sRepetition];
			}
			oBox.jSpan.val(mValue.span);
			if (mValue.collapsed == 1) {
				oBox.jCollapsed.parent().checkCheckboxes();
			}
			else {
				oBox.jCollapsed.parent().unCheckCheckboxes();
			}
			oBox.jBoxSelect.val(mValue.box).change();
		}
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
	};

	gThis._AddField = function(sId) {
		var jBoxSelect = $('<select name="'+ gThis.GetName(sId) + '[layoutbox]" id="' + gThis.GetId(sId) + '__layoutbox"/>');
		var iLayoutBoxes = gThis.m_oOptions.aoBoxes.length;
		for (var i = 0; i < iLayoutBoxes; i++) {
			var oBox = gThis.m_oOptions.aoBoxes[i];
			jBoxSelect.append('<option value="' + oBox.value + '">' + oBox.label + '</option>');
		}
		var jSpan = $('<input type="text" name="'+ gThis.GetName(sId) + '[span]" id="' + gThis.GetId(sId) + '__span" value="1"/>');
		var jCollapsed = $('<input type="checkbox" name="'+ gThis.GetName(sId) + '[collapsed]" id="' + gThis.GetId(sId) + '__collapsed" value="1"/>');
		
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jBoxSelect);
		}
		else {
			gThis.m_jField = jBoxSelect;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="box-select"/>').append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jBoxSelect)));
		jRepetitionNode.append($('<span class="box-span"/>').append('<label for="' + gThis.GetId(sId) + '__span">' + GForm.Language.layout_boxes_list_span + '</label>').append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jSpan)));
		jRepetitionNode.append($('<span class="box-collapsed"/>').append($('<span class=""/>').append(jCollapsed)).append('<label for="' + gThis.GetId(sId) + '__collapsed">' + GForm.Language.layout_boxes_list_collapsed + '</label>'));
		
		gThis.m_oBoxes[sId] = {
			jBoxSelect: jBoxSelect,
			jSpan: jSpan,
			jCollapsed: jCollapsed,
			iPosition: GCore.ObjectLength(gThis.m_oBoxes)
		};
		gThis.m_aBoxes.push(sId);
		
		return jRepetitionNode;
	};
	
	gThis._UpdateIcons = function() {
		
		gThis.m_jNode.find('.icon').remove();
		
		var iLayoutBoxes = gThis.m_aBoxes.length;
		
		for (var i = 0; i < iLayoutBoxes; i++) {
			
			var oBox = gThis.m_oBoxes[gThis.m_aBoxes[i]];
			
			var jRemove = $('<a class="icon" href="#"/>');
			jRemove.append('<img src="' + gThis._GetImage('Remove') + '" alt="' + GForm.Language.layout_boxes_list_remove + '" title="' + GForm.Language.layout_boxes_list_remove + '"/>');
			oBox.jCollapsed.closest('.repetition').append(jRemove);
			jRemove.bind('click', {i:gThis.m_aBoxes[i]}, GEventHandler(function(eEvent) {
				gThis.RemoveBox(eEvent.data.i);
				return false;
			}));
			
		}
		
		var jAdd = $('<a class="add-box icon" href="#"/>');
		jAdd.append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.layout_boxes_list_add + '" title="' + GForm.Language.layout_boxes_list_add + '"/>');
		gThis.m_jNode.append(jAdd);
		jAdd.bind('click', GEventHandler(function(eEvent) {
			gThis.AddBox();
			return false;
		}));
	};
	
	gThis.RemoveBox = function(sId) {
		var oBox = gThis.m_oBoxes[sId];
		gThis.RemoveRepetition(sId);
		var iBoxes = gThis.m_aBoxes.length;
		for (var i = 0; i < iBoxes; i++) {
			if (gThis.m_aBoxes[i] == sId) {
				gThis.m_aBoxes.splice(i, 1);
				break;
			}
		}
		delete gThis.m_oBoxes[sId];
	};
	
	gThis.AddBox = function() {
		gThis.AddRepetition();
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (sRepetition != undefined) {
			gThis.m_oBoxes[sRepetition].jBoxSelect.GSelect();
			gThis.m_oBoxes[sRepetition].jSpan.focus(GEventHandler(function(eEvent) {
				$(this).closest('.field').addClass('focus');
			})).blur(GEventHandler(function(eEvent) {
				$(this).closest('.field').removeClass('focus');
			}));
		}
		gThis._UpdateIcons();
	};
	
}, oDefaults);


/*
* LIST OF SELECTS
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-select',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	aoSelects: [],
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormListOfSelects = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_bResized = false;
	
	gThis.m_aoSelects = [];
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis._GetField(sRepetition).find('option:selected').attr('value');
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.val(mValue);
		if ((jField.get(0) != undefined) && (jField.get(0).Update != undefined)) {
			jField.get(0).Update.apply(jField.get(0));
		}
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>');
		gThis.m_aoSelects = gThis.m_oOptions.aoSelects;
		gThis.UpdateSelects();
	};
	
	gThis.UpdateSelects = function() {
		gThis.m_jNode.empty();
		gThis.m_jField.empty();
		var iSelects = gThis.m_aoSelects.length;
		for (var i = 0; i < iSelects; i++) {
			gThis.m_jNode.append(gThis._AddField(gThis.m_aoSelects[i]));
		}
	};
	
	gThis.ReplaceSelects = function(aoSelects) {
		gThis.m_aoSelects = aoSelects;
		gThis.UpdateSelects();
		gThis.m_bShown = false;
		gThis.m_bResized = false;
		gThis.OnShow();
		gThis._InitializeEvents();
	};
	
	gThis._AddField = function(oSelect) {
		var jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(oSelect.label);
		if ((oSelect.comment != undefined) && (oSelect.comment.length)) {
			jLabel.append(' <small>' + oSelect.comment + '</small>');
		}
		jNode.append(jLabel);
		var jField = $('<select name="' + gThis.GetName(oSelect.name) + '" id="' + gThis.GetId(oSelect.name) + '"/>');
		for (var i = 0; i < oSelect.options.length; i++) {
			var oOption = oSelect.options[i];
			jField.append('<option value="' + oOption.value + '"' + (((oSelect.default_value != undefined) && (oOption.value == oSelect.default_value)) ? ' selected="selected"' : '') + '>' + oOption.label + '</option>');
		}
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		if (oSelect.prefix != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(oSelect.prefix);
			jRepetitionNode.append(jPrefix);
		}
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		if (oSelect.suffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(oSelect.suffix);
			jRepetitionNode.append(jSuffix);
		}
		return jNode.append(jRepetitionNode);
	};
	
	gThis.OnInitRepetition = function(sRepetition) {
		if (!gThis.m_bShown) {
			return;
		}
		gThis._GetField(sRepetition).GSelect();
		var iSelects = gThis.m_aoSelects.length;
		for (var i = 0; i < iSelects; i++) {
			if (gThis.m_aoSelects[i].default_value != undefined) {
				gThis.m_jField.filter('[id$="' + gThis.m_aoSelects[i].name + '"]').val(gThis.m_aoSelects[i].default_value).triggerHandler('change');
			}
		}
	};
	
	gThis.OnShow = function() {
		gThis._UpdateRepetitionButtons();
		if (!gThis.m_bShown && gThis.m_bRepeatable) {
			gThis._InitializeEvents('new-0');
		}
		gThis.m_bShown = true;
		if (gThis.m_bRepeatable) {
			for (var i in gThis.m_oRepetitions) {
				if (!gThis.m_oRepetitions[i].m_bResized) {
					gThis.m_oRepetitions[i].m_bResized = true;
					gThis.OnInitRepetition(i);
				}
			}
		}
		else {
			if (!gThis.m_bResized) {
				gThis.m_bResized = true;
				gThis.OnInitRepetition();
			}
		}
	};
	
	gThis.OnFocus = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		gThis._ActivateFocusedTab(eEvent);
	};
	
	gThis.OnBlur = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.ReplaceSelects(gThis.m_oOptions.aoSelects);
		//gThis.m_jField.val(gThis.m_oOptions.sDefault).change();
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable && (sRepetition == undefined)) {
			return;
		}
		var jField = gThis._GetField(sRepetition);
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).change(GEventHandler(function(eEvent) {
				gThis.Validate(false, this.sRepetition);
			}));
		});
		jField.keydown(function(eEvent) {
			var dSelect = this;
			setTimeout(function() {
				dSelect.Update();
			}, 50);
			return true;
		});
		if (gThis.m_jNode.closest('.statusChange').length) {
			gThis.OnShow();
		}
	};
	
}, oDefaults);


/*
* MULTISELECT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-multiselect',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormMultiSelect = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis._Constructor = function() {
		gThis.m_bResized = false;
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		var aValues = [];
		var jValues = gThis._GetField(sRepetition).filter(':checked');
		for (var i in jValues) {
			aValues.push(jValues.eq(i).attr('value'));
		}
		return aValues;
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis._GetField(sRepetition).val(mValue).change();
	};
	
	gThis.ExchangeOptions = function(aoNewOptions) {
		gThis.m_oOptions.aoOptions = aoNewOptions;
		var jOldNode = gThis.m_jNode;
		gThis._PrepareNode();
		jOldNode.replaceWith(gThis.m_jNode);
		gThis.m_bShown = false;
		gThis.m_bResized = false;
		gThis.OnShow();
		gThis.m_jField.change();
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '" class="' + gThis._GetClass('Group') + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append(gThis._AddField());
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<ul/>');
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			var oOption = gThis.m_oOptions.aoOptions[i];
			jField.append('<li><label><input type="checkbox" name="' + gThis.GetName(sId) + '[]" value="' + oOption.sValue + '"/>' + oOption.sLabel + '</label></li>');
		}
		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {
			gThis.m_jTrigger = $('<li><a style="padding-left: 8px;line-height: 19px;"href="#" class="' + gThis._GetClass('AddRepetition') + '"><img src="' + gThis._GetImage('AddRepetition') + '" alt="' + GForm.Language.add_field_repetition + '" title="' + GForm.Language.add_field_repetition + '"/> Dodaj nowy</a></li>');
			jField.append(gThis.m_jTrigger);
		}
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		return jRepetitionNode;
	};
	
	gThis.OnReset = function() {
		gThis.m_jNode.find('input').parent().unCheckCheckboxes();
	};
	
	gThis.OnShow = function() {
		if (gThis.m_oOptions.bAddable && (gThis.m_oOptions.fOnAdd instanceof Function)) {
			gThis.m_jTrigger.click(function() {
				GAlert.DestroyAll();
				GPrompt(gThis.m_oOptions.sAddItemPrompt, function(sName) {
					GCore.StartWaiting();
					gThis.m_oOptions.fOnAdd({
						name: sName
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						GAlert.DestroyAll();
						if(!eEvent.error){
							gThis.ExchangeOptions(eEvent.options);
							gThis.Populate(eEvent.id);
						}else{
							GError(eEvent.error);
						}
					}));
				});
				return false;
			});
		}
	};
	
	gThis.Populate = function(mValue) {
		gThis.m_jNode.unCheckCheckboxes();
		for (var i in mValue) {
			if (i == 'toJSON') {
				continue;
			}
			gThis.m_jNode.find('input[value="' + mValue[i] + '"]').parent().checkCheckboxes();
		}
	};
	
	gThis.Focus = function() {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis.m_jField.eq(0).focus();
	};
	
}, oDefaults);


/*
* PASSWORD
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'password',
	sDefault: '',
	aoRules: []
};

var GFormPassword = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
}, oDefaults);


/*
* PRICE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-price',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sDisabledClass: 'disabled',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sNetPriceClass: 'net-price',
		sGrossPriceClass: 'gross-price'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormPrice = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append(gThis._AddField());
		$(window).bind('OnVatChange', function(){
			gThis._CalculateGrossPrice();
		});
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis.m_jField.eq(0).val();
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		gThis.m_jField.eq(0).val(mValue).change();
	};
	
	gThis._AddField = function(sId) {
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '" id="' + gThis.GetId() + '"/>');
		var jFieldGross = $('<input type="text" id="' + gThis.GetId() + '__gross"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		gThis.m_jField = jRepetitionNode.find('input');
		gThis.jRepetitionNode = jRepetitionNode;
		return gThis.jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		gThis._CalculateGrossPrice(gThis.m_jField.eq(0).val());
		gThis.m_bShown = true;
		if (!gThis.m_bResized) {
			gThis.m_bResized = true;
			gThis.m_jField.each(function() {
				var iWidth = Math.floor(parseInt($(this).css('width')) / 2) - 20;
				var jParent = $(this).closest('.' + gThis._GetClass('NetPrice') + ', .' + gThis._GetClass('GrossPrice'));
				if (jParent.find('.' + gThis._GetClass('Prefix')).length) {
					iWidth -= ($(this).offset().left - jParent.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
				}
				if (jParent.find('.' + gThis._GetClass('Suffix')).length) {
					iWidth -= jParent.find('.' + gThis._GetClass('Suffix')).width() + 4;
				}
				$(this).css('width', iWidth);
			});
		}
	};
	
	gThis._CalculateGrossPrice = function(sPrice) {
		var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = parseInt(gVat.GetValue());
		var fVat = 0;
		if (GCore.aoVatValues[iVatId] != undefined) {
			fVat = parseFloat(GCore.aoVatValues[iVatId]);
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jField.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		gThis.m_jField.eq(1).val((fPrice * (1 + fVat / 100)).toFixed(2));
	};
	
	gThis._CalculateNetPrice = function(sPrice) {
		var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = gVat.GetValue();
		var fVat = 0;
		if (GCore.aoVatValues[iVatId] != undefined) {
			fVat = parseFloat(GCore.aoVatValues[iVatId]);
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jField.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		gThis.m_jField.eq(0).val((fPrice / (1 + fVat / 100)).toFixed(4));
	};
	
	gThis._Initialize = function() {
		var fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				gThis._CalculateGrossPrice($(eEvent.currentTarget).val());
			}, 5);
		});
		gThis.m_jField.eq(0).keypress(fHandler).blur(fHandler).change(gThis.ValidateField);
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				gThis._CalculateNetPrice($(eEvent.currentTarget).val());
			}, 5);
		});
		gThis.m_jField.eq(1).keypress(fHandler).blur(fHandler).change(gThis.ValidateField);
		gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jField.change(GEventHandler(function(eEvent) {
			gThis._CalculateGrossPrice();
		}));
		gThis._CalculateGrossPrice();
		gThis.m_jField.eq(0).change();
	};
	
	gThis.ValidateField = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		$(eEvent.currentTarget).val(fPrice.toFixed(4));
	});
	
	gThis.Reset = function() {
		gThis.m_jField.eq(0).val(gThis.m_oOptions.sDefault).change();
		gThis._CalculateGrossPrice();
	};
	
	
}, oDefaults);

/*
* PRODUCT AGGREGATOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-product-aggregator',
		sFieldPriceClass: 'field-price',
		sFieldConstantClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sNetPriceClass: 'net-price',
		sGrossPriceClass: 'gross-price'
	},
	sFieldType: 'text',
	sProductsSourceField: '',
	fLoadProductData: GCore.NULL
};

var GFormProductAggregator = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_jProducts;
	gThis.m_aoProducts = [];
	gThis.m_iLockId = -1;
	gThis.m_jNetSum;
	gThis.m_jGrossSum;
	gThis.m_jCount;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jProducts = $('<div/>');
		gThis.m_jNode.append(gThis._AddProductCount(0));
		gThis.m_jNode.append(gThis.m_jProducts);
		gThis.m_jNode.append(gThis._AddSummary(0, 0));
	};
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		return gThis.m_jField.eq(0).val();
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		return;
	};
	
	gThis._RewriteProducts = function() {
		gThis.m_jProducts.empty();
		var iCount = gThis.m_aoProducts.length;
		var fNetSum = 0;
		var fGrossSum = 0;
		for (var i = 0; i < iCount; i++) {
			var oProduct = gThis.m_aoProducts[i];
			var jProductRow = gThis._AddProduct(oProduct);
			fNetSum += isNaN(parseFloat(jProductRow.find('input:text:eq(0)').val())) ? 0 : parseFloat(jProductRow.find('input:text:eq(0)').val());
			fGrossSum += isNaN(parseFloat(jProductRow.find('input:text:eq(1)').val())) ? 0 : parseFloat(jProductRow.find('input:text:eq(1)').val());
			gThis.m_jProducts.append(jProductRow);
		}
		gThis._UpdateWidths();
		gThis.UpdateCount(iCount);
		gThis.UpdateSum(fNetSum, fGrossSum);
	};
	
	gThis.UpdateSum = function(fNetSum, fGrossSum) {
		gThis.m_jNetSum.val(fNetSum.toFixed(2)).change();
		gThis.m_jGrossSum.val(fGrossSum.toFixed(2));
	};
	
	gThis.UpdateCount = function(iCount) {
		gThis.m_jCount.val(iCount).change();
	};
	
	gThis._AddProductCount = function(iCount) {
		var jElement = $('<div/>').addClass(gThis._GetClass('FieldConstant'));
		var jLabel = $('<label/>').css('font-weight', 'bold');
		var sLabel = GForm.Language.product_aggregator_count;
		jLabel.text(sLabel);
		jElement.append(jLabel);
		var jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" disabled="disabled" value="' + iCount + '"/>').css('cursor', 'default');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		jElement.append(jRepetitionNode);
		gThis.m_jCount = jField;
		return jElement;
	};
	
	gThis._AddSummary = function(fNetSum, fGrossSum) {
		var jElement = $('<div/>').addClass(gThis._GetClass('FieldPrice'));
		var jLabel = $('<label/>').css('font-weight', 'bold');
		var sLabel = GForm.Language.product_aggregator_sum;
		jLabel.text(sLabel);
		jElement.append(jLabel);
		var jFieldNet = $('<input disabled="disabled" type="text" name="' + gThis.GetName() + '" value="' + fNetSum.toFixed(2) + '"/>').css('cursor', 'default');
		gThis.m_jField = jFieldNet;
		var jFieldGross = $('<input disabled="disabled" type="text" value="' + fGrossSum.toFixed(2) + '"/>').css('cursor', 'default');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		jElement.append(jRepetitionNode);
		gThis.m_jNetSum = jFieldNet;
		gThis.m_jGrossSum = jFieldGross;
		return jElement;
	};
	
	gThis._AddProduct = function(oProduct) {
		var jElement = $('<div/>').addClass(gThis._GetClass('FieldPrice'));
		var jLabel = $('<label/>');
		var sLabel = oProduct.name;
		if (parseInt(oProduct.quantity) > 1) {
			sLabel = oProduct.quantity + ' ' + String.fromCharCode(0xd7) + ' ' + sLabel;
		}
		jLabel.text(sLabel);
		jElement.append(jLabel);
		jElement.append(gThis._AddPrice(oProduct));
		return jElement;
	};
	
	gThis._AddPrice = function(oProduct) {
		var jFieldNet = $('<input disabled="disabled" type="text" value="' + oProduct.sellprice + '"/>').css('cursor', 'default');
		var jFieldGross = $('<input disabled="disabled" type="text" value="' + oProduct.sellprice_gross + '"/>').css('cursor', 'default');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		//gThis.m_jField = jRepetitionNode.find('input');
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		gThis.m_bShown = true;
		gThis._UpdateWidths();
		gThis.Update();
	};
	
	gThis._UpdateWidths = function() {
		gThis.m_jNode.find('input:text').each(function(i) {
			if (i == 0) {
				return;
			}
			var iWidth = Math.floor(parseInt($(this).css('width')) / 2) - 20;
			var jParent = $(this).closest('.' + gThis._GetClass('NetPrice') + ', .' + gThis._GetClass('GrossPrice'));
			if (jParent.find('.' + gThis._GetClass('Prefix')).length) {
				iWidth -= ($(this).offset().left - jParent.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
			}
			if (jParent.find('.' + gThis._GetClass('Suffix')).length) {
				iWidth -= jParent.find('.' + gThis._GetClass('Suffix')).width() + 4;
			}
			$(this).css('width', iWidth);
		});
	};
	
	gThis._CalculateGrossPrice = function(sPrice) {
		/*var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = gVat.GetValue();
		var fVat = 0;
		for (var i in gThis.m_oOptions.aoVatValues) {
			if (gThis.m_oOptions.aoVatValues[i].id == iVatId) {
				fVat = gThis.m_oOptions.aoVatValues[i].value;
				break;
			}
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jField.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		//gThis.m_jField.eq(0).val(fPrice.toFixed(2));
		gThis.m_jField.eq(1).val((fPrice * (1 + fVat / 100)).toFixed(2));*/
	};
	
	gThis._CalculateNetPrice = function(sPrice) {
		/*var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = gVat.GetValue();
		var fVat = 0;
		for (var i in gThis.m_oOptions.aoVatValues) {
			if (gThis.m_oOptions.aoVatValues[i].id == iVatId) {
				fVat = gThis.m_oOptions.aoVatValues[i].value;
				break;
			}
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jField.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		//gThis.m_jField.eq(1).val(fPrice.toFixed(2));
		gThis.m_jField.eq(0).val((fPrice / (1 + fVat / 100)).toFixed(2));*/
	};
	
	gThis.StartWaiting = function() {
		gThis.m_jNode.css('opacity', .5);
	};
	
	gThis.EndWaiting = function() {
		gThis.m_jNode.css('opacity', 1);
	};
	
	gThis.Update = function() {
		var jProducts = gThis.m_gForm.GetField(gThis.m_oOptions.sProductsSourceField).m_jField.find('input');
		gThis.m_iLockId = gThis.m_gForm.Lock(GForm.Language.product_aggregator_form_blocked, GForm.Language.product_aggregator_form_blocked_description);
		gThis.StartWaiting();
		var aoProducts = [];
		for (var i = 0; i < jProducts.length - 2; i += 3) {
			aoProducts.push({
				id: jProducts.eq(i + 0).attr('value'),
				quantity: jProducts.eq(i + 1).attr('value'),
				variant: jProducts.eq(i + 2).attr('value')
			});
		}
		gThis.m_oOptions.fLoadProductData({
			products: aoProducts
		}, GCallback(GEventHandler(function(eEvent) {
			gThis.m_aoProducts = eEvent.products;
			gThis._RewriteProducts();
			gThis.m_gForm.Unlock(gThis.m_iLockId);
			gThis.EndWaiting();
		})));
	};
	
	gThis.Reset = function() {
		gThis.Update();
	};
	
	gThis.Focus = function() {
		return false;
	};
	
}, oDefaults);


/*
* PROGRESS INDICATOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-image',
		sFieldSpanClass: 'field',
		sButtonClass: 'button',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sThumbClass: 'thumb',
		sNameClass: 'name',
		sSelectedTableClass: 'selected',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	iWidth: 121,
	iHeight: 27,
};

var GFormProgressIndicator = GCore.ExtendClass(GFormFile, function() {

	var gThis = this;

	gThis.m_bShown = false;

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		
		gThis.m_jNode.append('<h4>' + gThis.m_oOptions.sLabel + '</h4>');
		gThis.m_jNode.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		gThis.m_jNode.append('<div class="' + gThis._GetClass('ProgressBar') + '"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		gThis.m_jNode.find('.' + gThis._GetClass('Progress')).text(GForm.Language.progress_indicator_run_comment);
		gThis.m_jNode.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', 0 + '%');
		
		if(gThis.m_oOptions.bPreventSubmit){
			jA = $('<a class="' + gThis._GetClass('Button') + '" href="#"/>');
			jA.append('<span>' + GForm.Language.progress_indicator_run + '</span>');
			gThis.m_jNode.append($('<p></p>').append('<br />').append(jA));
		}else{
			window.setTimeout(gThis.OnLoad, 1500);
		}
	};
	
	gThis._InitializeEvents = function(sRepetition) {
		$('.navigation').remove();
		if(gThis.m_oOptions.bPreventSubmit){
			jA.click(gThis.OnLoad);
		}
		
	};
	
	gThis.OnLoad = function(){
		if(gThis.m_oOptions.bPreventSubmit){
			jA.hide();
		}
		
		gThis.m_oOptions.fLoadRecords({
			iStartFrom: 0
		}, GCallback(gThis.OnDataLoaded));
	};
	
	gThis.OnStartProcess = function(iFrom){
		GXajaxInterface.Invoke(gThis.m_oOptions.fProcessRecords, [
			{
				iStartFrom: iFrom,
				iChunks: gThis.m_oOptions.iChunks,
				iTotal: gThis.iTotal
			},
			GCallback(gThis.OnProcess)
		], function(eEvent) {
			GError('Serwer zwrócił niepoprawną odpowiedź', 'Czy spróbować jeszcze raz?', {
				aoPossibilities: [
					{
						mLink: GEventHandler(function(eEvent) {
							GAlert.DestroyAll();
							gThis.OnStartProcess(iFrom);
						}),
						sCaption: 'Ponów próbę'
					},
					{
						mLink: GAlert.DestroyThis,
						sCaption: GMessageBar.Language.cancel
					}
				]
			});
		});
	};
	
	gThis.UpdateIndicator = function(completed){
		var iCompleted = completed;
		if (gThis.iTotal == undefined) {
			return;
		}
		var iTotal = gThis.iTotal;
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		gThis.m_jNode.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + iCompleted  + ' / ' + iTotal + ' ' + gThis.m_oOptions.sComment);
		gThis.m_jNode.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', (iCompleted / iTotal * 100) + '%');
	};
	
	gThis.OnDataLoaded = GEventHandler(function(eEvent) {
		var iCompleted = eEvent.iCompleted;
		var iTotal = eEvent.iTotal;
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		gThis.m_jNode.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + iCompleted  + ' / ' + iTotal + ' ' + gThis.m_oOptions.sComment);
		gThis.iTotal = iTotal;
		gThis.m_jNode.find('h4').text('Przetwarzanie danych');
		gThis.OnStartProcess(iCompleted, 0);
	});
	
	gThis.OnProcess = GEventHandler(function(eEvent) {
		gThis.UpdateIndicator(eEvent.iStartFrom);
		if ((eEvent.bFinished != undefined) && eEvent.bFinished) {
			gThis.m_oOptions.fSuccessRecords({
			   bFinished: eEvent.bFinished
			}, GCallback(gThis.OnProcessSucceded));
			return;
		}
		if(eEvent.iStartFrom > 0){
			gThis.OnStartProcess(eEvent.iStartFrom);
		}
	});
	
	gThis.OnProcessSucceded = GEventHandler(function(eEvent) {
		if ((eEvent.bCompleted != undefined) && eEvent.bCompleted) {
			GAlert(GForm.Language.progress_indicator_success);
			return;
		}
	});

}, oDefaults);

/*
* PROGRESS BAR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-image',
		sFieldSpanClass: 'field',
		sButtonClass: 'button',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sThumbClass: 'thumb',
		sNameClass: 'name',
		sSelectedTableClass: 'selected',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	iWidth: 121,
	iHeight: 27,
};

var GFormProgressBar = GCore.ExtendClass(GFormFile, function() {

	var gThis = this;

	gThis.m_bShown = false;

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jNode.css({marginBottom:20});
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		var iTotal = gThis.m_oOptions.iTotal;
		var iCompleted = gThis.m_oOptions.iCompleted;
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		
		gThis.m_jNode.append('<h4>' + gThis.m_oOptions.sLabel + '</h4>');
		
		gThis.m_jNode.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		
		gThis.m_jNode.append('<div class="' + gThis._GetClass('ProgressBar') + ((parseInt(iCompleted) > parseInt(iTotal) && parseInt(iTotal) > 0) ? ' error' : '') +'"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		
		if(iTotal == 0){
			gThis.m_jNode.find('.' + gThis._GetClass('Progress')).text('Brak limitu');
		}else{
			gThis.m_jNode.find('.' + gThis._GetClass('Progress')).text(Math.round(gThis.m_oOptions.iCompleted / gThis.m_oOptions.iTotal * 100) + '%: ' + gThis.m_oOptions.iCompleted  + ' / ' + gThis.m_oOptions.iTotal + ' ' + gThis.m_oOptions.sComment);
		}
		
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			var percentage = gThis.m_oOptions.iCompleted / gThis.m_oOptions.iTotal * 100;
			gThis.m_jNode.find('.' + gThis._GetClass('ProgressBarIndicator')).animate({
				width: (percentage > 100) ? '100%' : percentage + '%'
			}, 500);
		}
		gThis.m_bShown = true;
	};

}, oDefaults);


/*
* RANGE EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	asPrefixes: [],
	sSuffix: '',
	aoVatValues: [],
	oClasses: {
		sFieldClass: 'field-range-editor',
		sFieldTextClass: 'field-text',
		sFieldPriceClass: 'field-price',
		sFieldCheckboxClass: 'field-checkbox',
		sFieldSelectClass: 'field-select',
		sFieldSpanClass: 'field',
		sRangeColumnClass: 'price',
		sMinColumnClass: 'min',
		sMaxColumnClass: 'max',
		sOptionsColumnClass: 'options',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sNetPriceClass: 'net-price',
		sGrossPriceClass: 'gross-price'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sRemove: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	aoOptions: [],
	sComment: '',
	iPricePrecision: 2,
	iRangePrecision: 2,
	iRangeLimitsPrecision: 0
};

var GFormRangeEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	gThis.m_jTable;
	gThis.m_jVat;
	gThis.m_jUseVat;
	gThis.m_jVatField;
	gThis.m_jUseVatField;
	gThis.m_jRangeFields;
	gThis.m_jPriceFields;
	gThis.m_jNetPriceFields;
	gThis.m_jGrossPriceFields;
	gThis.m_aoRanges = [];
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis._AddVatCheckbox();
		gThis._AddVat();
		gThis._AddTable();
	};
	
	gThis._AddTable = function() {
		gThis.m_jTable = $('<table cellspacing="0"/>');
		gThis.m_jNode.append(gThis.m_jTable);
	};
	
	gThis._WriteTable = function() {
		if (!(gThis.m_aoRanges instanceof Array) || !gThis.m_aoRanges.length) {
			gThis.m_aoRanges = [{
				min: (0).toFixed(gThis.m_oOptions.iRangePrecision),
				max: (0).toFixed(gThis.m_oOptions.iRangePrecision),
				price: (gThis.m_oOptions.aoOptions.length) ? gThis.m_oOptions.aoOptions[0].sValue : (0).toFixed(gThis.m_oOptions.iPricePrecision)
			}];
		}
		gThis.m_jTable.find('tbody').remove();
		gThis.m_jGrossPriceFields = $();
		gThis.m_jNetPriceFields = $();
		var jBody = $('<tbody/>');
		var iRanges = gThis.m_aoRanges.length;
		for (var i = 0; i < iRanges; i++) {
			var jTr = $('<tr/>');
			var jMin = $('<td class="' + gThis._GetClass('RangeColumn') + ' ' + gThis._GetClass('MinColumn') + '"/>');
//			if (i > 0) {
				jMin.append(gThis._AddMin(i, gThis.m_aoRanges[i]));
//			}
			var jMax = $('<td class="' + gThis._GetClass('RangeColumn') + ' ' + gThis._GetClass('MaxColumn') + '"/>');
			if (i < iRanges ) {
				jMax.append(gThis._AddMax(i, gThis.m_aoRanges[i]));
			}
			var jPrice = $('<td/>');
			if (gThis.m_oOptions.aoOptions.length) {
				jPrice.append(gThis._AddSelect(i, gThis.m_aoRanges[i]));
			}
			else if (gThis.m_jUseVatField.is(':checked')) {
				jPrice.append(gThis._AddPriceWithVat(i, gThis.m_aoRanges[i]));
			}
			else {    
				jPrice.append(gThis._AddPrice(i, gThis.m_aoRanges[i]));
			}
			jOptions = $('<td class="' + gThis._GetClass('OptionsColumn') + '"/>');
			var jAdd = $('<a tabindex="-1" href="#"/>');
			jAdd.append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.range_editor_add_range + '" title="' + GForm.Language.range_editor_add_range + '"/>');
			jOptions.append(jAdd);
			jAdd.bind('click', {i:i}, GEventHandler(function(eEvent) {
				gThis.AddNewRange(eEvent.data.i);
				return false;
			}));
			if (iRanges > 1) {
				var jRemove = $('<a tabindex="-1" href="#"/>');
				jRemove.append('<img src="' + gThis._GetImage('Remove') + '" alt="' + GForm.Language.range_editor_remove_range + '" title="' + GForm.Language.range_editor_remove_range + '"/>');
				jOptions.append(jRemove);
				jRemove.bind('click', {i:i}, GEventHandler(function(eEvent) {
					gThis.RemoveRange(eEvent.data.i);
					return false;
				}));
			}
			jTr.append(jMin);
			jTr.append(jMax);
			jTr.append(jPrice);
			jTr.append(jOptions);
			jBody.append(jTr);
		}
		gThis.m_jTable.append(jBody);
		gThis.InitTable();
	};
	
	gThis.AddNewRange = function(iAfterWhich) {
		var fOffset = 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision);
		var oRange = {
			min: (parseFloat(gThis.m_aoRanges[iAfterWhich].max) + fOffset).toFixed(gThis.m_oOptions.iRangePrecision),
			max: (0).toFixed(gThis.m_oOptions.iRangePrecision),
			price: (gThis.m_oOptions.aoOptions.length) ? gThis.m_oOptions.aoOptions[0].sValue : (0).toFixed(gThis.m_oOptions.iPricePrecision)
		};
		if (gThis.m_aoRanges[iAfterWhich + 1] != undefined) {
			var fRange = Math.max(fOffset, parseFloat(gThis.m_aoRanges[iAfterWhich + 1].max) - parseFloat(oRange.min));
			oRange.max = (parseFloat(oRange.min) + fRange / 2).toFixed(gThis.m_oOptions.iRangePrecision);
			gThis.m_aoRanges[iAfterWhich + 1].min = (parseFloat(oRange.max) + fOffset).toFixed(gThis.m_oOptions.iRangePrecision);
		}
		var aoRanges = gThis.m_aoRanges.slice(0, iAfterWhich + 1).concat(oRange, gThis.m_aoRanges.slice(iAfterWhich + 1));
		gThis.m_aoRanges = aoRanges;
		gThis._WriteTable();
	};
	
	gThis.RemoveRange = function(iWhich) {
		gThis.m_aoRanges.splice(iWhich, 1);
		var fOffset = 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision);
		if ((gThis.m_aoRanges[iWhich] != undefined) && (gThis.m_aoRanges[iWhich - 1] != undefined)) {
			gThis.m_aoRanges[iWhich].min = gThis.m_aoRanges[iWhich - 1].max;
			gThis.m_aoRanges[iWhich].min = (parseFloat(gThis.m_aoRanges[iWhich].min) + fOffset).toFixed(gThis.m_oOptions.iRangePrecision);
		}
		gThis._WriteTable();
	};
	
	gThis._AddMin = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][min]" id="' + gThis.GetId() + '__' + sId + '__min" value="' + oRange.min + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
		jPrefix.html(GForm.Language.range_editor_from);
		jRepetitionNode.append(jPrefix);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sRangeSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sRangeSuffix);
			jRepetitionNode.append(jSuffix);
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis._AddMax = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][max]" id="' + gThis.GetId() + '__' + sId + '__max" value="' + oRange.max + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
		jPrefix.html(GForm.Language.range_editor_to);
		jRepetitionNode.append(jPrefix);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sRangeSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sRangeSuffix);
			jRepetitionNode.append(jSuffix);
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis.InitTable = function() {
		
		gThis.m_jTable.find('tbody input').focus(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
		}));
		gThis._ResizeFields();
		
		var fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var jNet = $(eEvent.currentTarget);
				jNet.closest('td').find('.' + gThis._GetClass('GrossPrice') + ' input').val(gThis._CalculateGrossPrice(jNet.val()));
			}, 5);
		});
		gThis.m_jTable.find('tbody .' + gThis._GetClass('NetPrice') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidatePrice).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].price = $(this).val();
		}));
		gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input').each(function() {
			$(this).triggerHandler('blur');
		});
		
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var jGross = $(eEvent.currentTarget);
				jGross.closest('td').find('.' + gThis._GetClass('NetPrice') + ' input').val(gThis._CalculateNetPrice(jGross.val()));
			}, 5);
		});
		gThis.m_jTable.find('tbody .' + gThis._GetClass('GrossPrice') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidatePrice).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].price = $(this).closest('td').find('.' + gThis._GetClass('NetPrice') + ' input').val();
		}));
		
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var fValue = Math.abs(parseFloat($(eEvent.currentTarget).val().replace(/,/, '.')));
				if (isNaN(fValue)) {
					return;
				}
				$(eEvent.currentTarget).closest('tr').prev('tr').find('.' + gThis._GetClass('MaxColumn') + ' input').val((fValue - 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision)).toFixed(gThis.m_oOptions.iRangePrecision)).change();
			}, 5);
		});
		gThis.m_jTable.find('tbody tr .' + gThis._GetClass('MinColumn') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidateRange).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].min = $(this).val();
		}));
		
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var fValue = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
				if (isNaN(fValue)) {
					return;
				}
				$(eEvent.currentTarget).closest('tr').next('tr').find('.' + gThis._GetClass('MinColumn') + ' input').val((fValue + 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision)).toFixed(gThis.m_oOptions.iRangePrecision)).change();
			}, 5);
		});
		gThis.m_jTable.find('tbody tr .' + gThis._GetClass('MaxColumn') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidateRange).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].max = $(this).val();
		}));
		
		gThis.m_jTable.find('select').each(function(i) {
			$(this).bind('change', {i: i}, GEventHandler(function(eEvent) {
				gThis.m_aoRanges[i].price = $(this).find('option:selected').attr('value');
			})).GSelect();
		});
		
	};
	
	gThis._AddVat = function() {
		gThis.m_jVat = $('<div/>').addClass(gThis._GetClass('FieldSelect'));
		var jLabel = $('<label for="' + gThis.GetId() + '__vat"/>');
		jLabel.text(GForm.Language.range_editor_vat);
		gThis.m_jVat.append(jLabel);
		var jField = $('<select name="' + gThis.GetName() + '[vat]" id="' + gThis.GetId() + '__vat"/>');
		for (var i in gThis.m_oOptions.aoVatValues) {
			jField.append('<option value="' + i + '">' + gThis.m_oOptions.aoVatValues[i] + '</option>');
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
		jSuffix.text('%');
		jRepetitionNode.append(jSuffix);
		gThis.m_jVat.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jVat);
		gThis.m_jVatField = jField;
	};
	
	gThis._AddVatCheckbox = function() {
		if (!gThis.m_oOptions.aoVatValues.length) {
			gThis.m_jUseVatField = $('<div/>');
			return;
		}
		gThis.m_jUseVat = $('<div/>').addClass(gThis._GetClass('FieldCheckbox'));
		var jLabel = $('<label for="' + gThis.GetId() + '__use_vat"/>');
		jLabel.text(GForm.Language.range_editor_use_vat);
		gThis.m_jUseVat.append(jLabel);
		var jField = $('<input type="checkbox" name="' + gThis.GetName() + '[use_vat]" id="' + gThis.GetId() + '__use_vat" value="1"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		gThis.m_jUseVat.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jUseVat);
		gThis.m_jUseVatField = jField;
	};
	
	gThis.GetValue = function(sRepetition) {
		return {
			use_vat: gThis.m_jUseVatField.is(':checked'),
			vat: gThis.m_jVatField.find('option:selected').attr('value'),
			ranges: gThis.m_aoRanges
		};
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue != undefined) {
			if ((mValue['use_vat'] && !gThis.m_jUseVatField.is(':checked')) || (!mValue['use_vat'] && gThis.m_jUseVatField.is(':checked'))) {
				gThis.m_jUseVatField.click();
			}
			mValue['vat'] && gThis.m_jVatField.val(mValue['vat']).change();
			if ((mValue['ranges'] == undefined) || !(mValue['ranges'] instanceof Array)) {
				mValue['ranges'] = [];
			}
			gThis.m_aoRanges = mValue['ranges'];
		}
		
		gThis._WriteTable();
		gThis.m_jUseVatField.triggerHandler('click');
	};
	
	gThis._AddPriceWithVat = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldPrice'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price" value="' + oRange.price + '"/>');
		var jFieldGross = $('<input type="text" id="' + gThis.GetId() + '__' + sId + '__gross"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}
		if ((gThis.m_jGrossPriceFields instanceof $) && gThis.m_jGrossPriceFields.length) {
			gThis.m_jGrossPriceFields.add(jRepetitionNode.find('input:eq(1)'));
		}
		else {
			gThis.m_jGrossPriceFields = jRepetitionNode.find('input:eq(1)');
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis._AddPrice = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price" value="' + oRange.price + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}
		if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis._AddSelect = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldSelect'));
		var jFieldNet = $('<select name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price"/>');
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			jFieldNet.append('<option value="' + gThis.m_oOptions.aoOptions[i].sValue + '"' + ((oRange.price == gThis.m_oOptions.aoOptions[i].sValue) ? ' selected="selected"' : '') + '>' + gThis.m_oOptions.aoOptions[i].sLabel + '</option>');
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}
		if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_jVatField.GSelect();
		}
		gThis.m_bShown = true;
		if (!gThis.m_bResized) {
			gThis.m_bResized = true;
			gThis._ResizeFields();
		}
		gThis.m_jUseVatField.triggerHandler('click');
	};
	
	gThis._ResizeFields = function() {
		gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input, .' + gThis._GetClass('GrossPrice') + ' input').each(function() {
			var iWidth = Math.floor(parseInt($(this).css('width')) / 2) - 20;
			var jParent = $(this).closest('.' + gThis._GetClass('NetPrice') + ', .' + gThis._GetClass('GrossPrice'));
			if (jParent.find('.' + gThis._GetClass('Prefix')).length) {
				iWidth -= ($(this).offset().left - jParent.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
			}
			if (jParent.find('.' + gThis._GetClass('Suffix')).length) {
				iWidth -= jParent.find('.' + gThis._GetClass('Suffix')).width() + 4;
			}
			$(this).css('width', iWidth);
		});
	};
	
	gThis._CalculateGrossPrice = function(sPrice) {
		var iVatId = gThis.m_jVatField.find('option:selected').attr('value');
		var fVat = gThis.m_oOptions.aoVatValues[iVatId];
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		return (fPrice * (1 + fVat / 100)).toFixed(gThis.m_oOptions.iPricePrecision);
	};
	
	gThis._CalculateNetPrice = function(sPrice) {
		var iVatId = gThis.m_jVatField.find('option:selected').attr('value');
		var fVat = gThis.m_oOptions.aoVatValues[iVatId];
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		return (fPrice / (1 + fVat / 100)).toFixed(gThis.m_oOptions.iPricePrecision);
	};
	
	gThis._Initialize = function() {
		if (gThis.m_oOptions.aoVatValues.length) {
			gThis.m_jUseVatField.click(GEventHandler(function(eEvent) {
				gThis._WriteTable();
				if ($(this).is(':checked')) {
					gThis.m_jVat.slideDown(100);
					gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input').each(function() {
						$(this).triggerHandler('blur');
					});
				}
				else {
					gThis.m_jVat.slideUp(100);
				}
			}));
			gThis.m_jVatField.change(GEventHandler(function(eEvent) {
				gThis._WriteTable();
			}));
		}
		else {
			gThis.m_jVat.css('display', 'none');
		}
	};
	
	gThis.ValidatePrice = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : Math.abs(fPrice);
		$(eEvent.currentTarget).val(fPrice.toFixed(gThis.m_oOptions.iPricePrecision));
	});
	
	gThis.ValidateRange = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : Math.abs(fPrice);
		$(eEvent.currentTarget).val(fPrice.toFixed(gThis.m_oOptions.iRangePrecision));
	});
	
	gThis.Reset = function() {
		gThis.SetValue(gThis.m_oOptions.sDefault);
	};
	
}, oDefaults);

//new
var GFormRangeEditorLimits = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	gThis.m_jTable;
	//gThis.m_jVat;
	//gThis.m_jUseVat;
	//gThis.m_jVatField;
	//gThis.m_jUseVatField;
	gThis.m_jRangeFields;
	gThis.m_jPriceFields;
	//gThis.m_jNetPriceFields;
	//gThis.m_jGrossPriceFields;
	gThis.m_aoRanges = [];
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		//gThis._AddVatCheckbox();
		//gThis._AddVat();
		gThis._AddTable();
	};
	
	gThis._AddTable = function() {
		gThis.m_jTable = $('<table cellspacing="0"/>');
		gThis.m_jNode.append(gThis.m_jTable);
	};
	
	gThis._WriteTable = function() {
		if (!(gThis.m_aoRanges instanceof Array) || !gThis.m_aoRanges.length) {
			gThis.m_aoRanges = [{
				min: (0).toFixed(gThis.m_oOptions.iRangeLimitsPrecision),
				max: (0).toFixed(gThis.m_oOptions.iRangeLimitsPrecision),
				//price: (gThis.m_oOptions.aoOptions.length) ? gThis.m_oOptions.aoOptions[0].sValue : (0).toFixed(gThis.m_oOptions.iRangeLimitsPrecision)
			}];
		}
		gThis.m_jTable.find('tbody').remove();
		//gThis.m_jGrossPriceFields = $();
		//gThis.m_jNetPriceFields = $();
		var jBody = $('<tbody/>');
		var iRanges = gThis.m_aoRanges.length;
		for (var i = 0; i < iRanges; i++) {
			var jTr = $('<tr/>');
			var jMin = $('<td class="' + gThis._GetClass('RangeColumn') + ' ' + gThis._GetClass('MinColumn') + '"/>');
//			if (i > 0) {
				jMin.append(gThis._AddMin(i, gThis.m_aoRanges[i]));
//			}
			var jMax = $('<td class="' + gThis._GetClass('RangeColumn') + ' ' + gThis._GetClass('MaxColumn') + '"/>');
			if (i < iRanges ) {
				jMax.append(gThis._AddMax(i, gThis.m_aoRanges[i]));
			}
			var jPrice = $('<td/>');
			if (gThis.m_oOptions.aoOptions.length) {
				jPrice.append(gThis._AddSelect(i, gThis.m_aoRanges[i]));
			}
			/*else if (gThis.m_jUseVatField.is(':checked')) {
				jPrice.append(gThis._AddPriceWithVat(i, gThis.m_aoRanges[i]));
			}*/
			else {
				jPrice.append(gThis._AddPrice(i, gThis.m_aoRanges[i]));
			}
			jOptions = $('<td class="' + gThis._GetClass('OptionsColumn') + '"/>');
			var jAdd = $('<a href="#"/>');
			jAdd.append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.range_editor_add_range + '" title="' + GForm.Language.range_editor_add_range + '"/>');
			jOptions.append(jAdd);
			jAdd.bind('click', {i:i}, GEventHandler(function(eEvent) {
				gThis.AddNewRange(eEvent.data.i);
				return false;
			}));
			if (iRanges > 1) {
				var jRemove = $('<a href="#"/>');
				jRemove.append('<img src="' + gThis._GetImage('Remove') + '" alt="' + GForm.Language.range_editor_remove_range + '" title="' + GForm.Language.range_editor_remove_range + '"/>');
				jOptions.append(jRemove);
				jRemove.bind('click', {i:i}, GEventHandler(function(eEvent) {
					gThis.RemoveRange(eEvent.data.i);
					return false;
				}));
			}
			jTr.append(jMin);
			jTr.append(jMax);
			jTr.append(jPrice);
			jTr.append(jOptions);
			jBody.append(jTr);
		}
		gThis.m_jTable.append(jBody);
		gThis.InitTable();
	};
	
	gThis.AddNewRange = function(iAfterWhich) {
		var fOffset = 1 / Math.pow(10, gThis.m_oOptions.iRangeLimitsPrecision);
		var oRange = {
			min: (parseFloat(gThis.m_aoRanges[iAfterWhich].max) + fOffset).toFixed(gThis.m_oOptions.iRangeLimitsPrecision),
			max: (0).toFixed(gThis.m_oOptions.iRangeLimitsPrecision),
			price: (gThis.m_oOptions.aoOptions.length) ? gThis.m_oOptions.aoOptions[0].sValue : (0).toFixed(gThis.m_oOptions.iRangeLimitsPrecision)
		};
		if (gThis.m_aoRanges[iAfterWhich + 1] != undefined) {
			var fRange = Math.max(fOffset, parseFloat(gThis.m_aoRanges[iAfterWhich + 1].max) - parseFloat(oRange.min));
			oRange.max = (parseFloat(oRange.min) + fRange / 2).toFixed(gThis.m_oOptions.iRangeLimitsPrecision);
			gThis.m_aoRanges[iAfterWhich + 1].min = (parseFloat(oRange.max) + fOffset).toFixed(gThis.m_oOptions.iRangeLimitsPrecision);
		}
		var aoRanges = gThis.m_aoRanges.slice(0, iAfterWhich + 1).concat(oRange, gThis.m_aoRanges.slice(iAfterWhich + 1));
		gThis.m_aoRanges = aoRanges;
		gThis._WriteTable();
	};
	
	gThis.RemoveRange = function(iWhich) {
		gThis.m_aoRanges.splice(iWhich, 1);
		var fOffset = 1 / Math.pow(10, gThis.m_oOptions.iRangeLimitsPrecision);
		if ((gThis.m_aoRanges[iWhich] != undefined) && (gThis.m_aoRanges[iWhich - 1] != undefined)) {
			gThis.m_aoRanges[iWhich].min = gThis.m_aoRanges[iWhich - 1].max;
			gThis.m_aoRanges[iWhich].min = (parseFloat(gThis.m_aoRanges[iWhich].min) + fOffset).toFixed(gThis.m_oOptions.iRangeLimitsPrecision);
		}
		gThis._WriteTable();
	};
	
	gThis._AddMin = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][min]" id="' + gThis.GetId() + '__' + sId + '__min" value="' + oRange.min + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
		jPrefix.html(GForm.Language.range_editor_from);
		jRepetitionNode.append(jPrefix);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		/*if (gThis.m_oOptions.sRangeSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sRangeSuffix);
			jRepetitionNode.append(jSuffix);
		}*/
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis._AddMax = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][max]" id="' + gThis.GetId() + '__' + sId + '__max" value="' + oRange.max + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
		jPrefix.html(GForm.Language.range_editor_to);
		jRepetitionNode.append(jPrefix);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		/*if (gThis.m_oOptions.sRangeSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sRangeSuffix);
			jRepetitionNode.append(jSuffix);
		}*/
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis.InitTable = function() {
		
		gThis.m_jTable.find('tbody input').focus(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
		}));
		gThis._ResizeFields();
		
		/*var fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var jNet = $(eEvent.currentTarget);
				jNet.closest('td').find('.' + gThis._GetClass('GrossPrice') + ' input').val(gThis._CalculateGrossPrice(jNet.val()));
			}, 5);
		});
		gThis.m_jTable.find('tbody .' + gThis._GetClass('NetPrice') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidatePrice).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			//gThis.m_aoRanges[i].price = $(this).val();
		}));
		gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input').each(function() {
			$(this).triggerHandler('blur');
		});
		
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var jGross = $(eEvent.currentTarget);
				jGross.closest('td').find('.' + gThis._GetClass('NetPrice') + ' input').val(gThis._CalculateNetPrice(jGross.val()));
			}, 5);
		});
		gThis.m_jTable.find('tbody .' + gThis._GetClass('GrossPrice') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidatePrice).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			//gThis.m_aoRanges[i].price = $(this).closest('td').find('.' + gThis._GetClass('NetPrice') + ' input').val();
		}));*/
		
		var fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var fValue = Math.abs(parseFloat($(eEvent.currentTarget).val().replace(/,/, '.')));
				if (isNaN(fValue)) {
					return;
				}
				$(eEvent.currentTarget).closest('tr').prev('tr').find('.' + gThis._GetClass('MaxColumn') + ' input').val((fValue - 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision)).toFixed(gThis.m_oOptions.iRangePrecision)).change();
			}, 5);
		});
		gThis.m_jTable.find('tbody tr .' + gThis._GetClass('MinColumn') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidateRange).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].min = $(this).val();
		}));
		
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				var fValue = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
				if (isNaN(fValue)) {
					return;
				}
				$(eEvent.currentTarget).closest('tr').next('tr').find('.' + gThis._GetClass('MinColumn') + ' input').val((fValue + 1 / Math.pow(10, gThis.m_oOptions.iRangePrecision)).toFixed(gThis.m_oOptions.iRangePrecision)).change();
			}, 5);
		});
		gThis.m_jTable.find('tbody tr .' + gThis._GetClass('MaxColumn') + ' input').keypress(fHandler).blur(fHandler).change(gThis.ValidateRange).change(GEventHandler(function(eEvent) {
			var i = gThis.m_jTable.find('tbody tr').index($(this).closest('tr'));
			gThis.m_aoRanges[i].max = $(this).val();
		}));
		
		gThis.m_jTable.find('select').each(function(i) {
			$(this).bind('change', {i: i}, GEventHandler(function(eEvent) {
				gThis.m_aoRanges[i].price = $(this).find('option:selected').attr('value');
			})).GSelect();
		});
		
	};
	
	/*gThis._AddVat = function() {
		gThis.m_jVat = $('<div/>').addClass(gThis._GetClass('FieldSelect'));
		var jLabel = $('<label for="' + gThis.GetId() + '__vat"/>');
		jLabel.text(GForm.Language.range_editor_vat);
		gThis.m_jVat.append(jLabel);
		var jField = $('<select name="' + gThis.GetName() + '[vat]" id="' + gThis.GetId() + '__vat"/>');
		for (var i in gThis.m_oOptions.aoVatValues) {
			jField.append('<option value="' + i + '">' + gThis.m_oOptions.aoVatValues[i] + '</option>');
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
		jSuffix.text('%');
		jRepetitionNode.append(jSuffix);
		gThis.m_jVat.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jVat);
		gThis.m_jVatField = jField;
	};
	
	gThis._AddVatCheckbox = function() {
		if (!gThis.m_oOptions.aoVatValues.length) {
			gThis.m_jUseVatField = $('<div/>');
			return;
		}
		gThis.m_jUseVat = $('<div/>').addClass(gThis._GetClass('FieldCheckbox'));
		var jLabel = $('<label for="' + gThis.GetId() + '__use_vat"/>');
		jLabel.text(GForm.Language.range_editor_use_vat);
		gThis.m_jUseVat.append(jLabel);
		var jField = $('<input type="checkbox" name="' + gThis.GetName() + '[use_vat]" id="' + gThis.GetId() + '__use_vat" value="1"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		gThis.m_jUseVat.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jUseVat);
		gThis.m_jUseVatField = jField;
	};*/
	
	gThis.GetValue = function(sRepetition) {
		return {
			//use_vat: gThis.m_jUseVatField.is(':checked'),
			//vat: gThis.m_jVatField.find('option:selected').attr('value'),
			ranges: gThis.m_aoRanges
		};
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue != undefined) {
			/*if ((mValue['use_vat'] && !gThis.m_jUseVatField.is(':checked')) || (!mValue['use_vat'] && gThis.m_jUseVatField.is(':checked'))) {
				gThis.m_jUseVatField.click();
			}*/
			//mValue['vat'] && gThis.m_jVatField.val(mValue['vat']).change();
			if ((mValue['ranges'] == undefined) || !(mValue['ranges'] instanceof Array)) {
				mValue['ranges'] = [];
			}
			gThis.m_aoRanges = mValue['ranges'];
		}
		
		gThis._WriteTable();
		//gThis.m_jUseVatField.triggerHandler('click');
	};
	
	/*gThis._AddPriceWithVat = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldPrice'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price" value="' + oRange.price + '"/>');
		var jFieldGross = $('<input type="text" id="' + gThis.GetId() + '__' + sId + '__gross"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}
		if ((gThis.m_jGrossPriceFields instanceof $) && gThis.m_jGrossPriceFields.length) {
			gThis.m_jGrossPriceFields.add(jRepetitionNode.find('input:eq(1)'));
		}
		else {
			gThis.m_jGrossPriceFields = jRepetitionNode.find('input:eq(1)');
		}
		jPrice.append(jRepetitionNode);
		return jPrice;
	};*/
	
	gThis._AddPrice = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price" value="' + oRange.price + '"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		/*if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}*/
		/*if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}*/
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis._AddSelect = function(sId, oRange) {
		var jPrice = $('<div/>').addClass(gThis._GetClass('FieldSelect'));
		var jFieldNet = $('<select name="' + gThis.GetName() + '[ranges][' + sId + '][price]" id="' + gThis.GetId() + '__' + sId +'__price"/>');
		for (var i = 0; i < gThis.m_oOptions.aoOptions.length; i++) {
			jFieldNet.append('<option value="' + gThis.m_oOptions.aoOptions[i].sValue + '"' + ((oRange.price == gThis.m_oOptions.aoOptions[i].sValue) ? ' selected="selected"' : '') + '>' + gThis.m_oOptions.aoOptions[i].sLabel + '</option>');
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		/*if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jRepetitionNode.append(jSuffix);
		}*/
		/*if ((gThis.m_jNetPriceFields instanceof $) && gThis.m_jNetPriceFields.length) {
			gThis.m_jNetPriceFields.add(jRepetitionNode.find('input:eq(0)'));
		}
		else {
			gThis.m_jNetPriceFields = jRepetitionNode.find('input:eq(0)');
		}*/
		jPrice.append(jRepetitionNode);
		return jPrice;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			//gThis.m_jVatField.GSelect();
		}
		gThis.m_bShown = true;
		if (!gThis.m_bResized) {
			gThis.m_bResized = true;
			gThis._ResizeFields();
		}
		//gThis.m_jUseVatField.triggerHandler('click');
	};
	
	gThis._ResizeFields = function() {
		gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input, .' + gThis._GetClass('GrossPrice') + ' input').each(function() {
			var iWidth = Math.floor(parseInt($(this).css('width')) / 2) - 20;
			var jParent = $(this).closest('.' + gThis._GetClass('NetPrice') + ', .' + gThis._GetClass('GrossPrice'));
			if (jParent.find('.' + gThis._GetClass('Prefix')).length) {
				iWidth -= ($(this).offset().left - jParent.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
			}
			if (jParent.find('.' + gThis._GetClass('Suffix')).length) {
				iWidth -= jParent.find('.' + gThis._GetClass('Suffix')).width() + 4;
			}
			$(this).css('width', iWidth);
		});
	};
	
	/*gThis._CalculateGrossPrice = function(sPrice) {
		var iVatId = gThis.m_jVatField.find('option:selected').attr('value');
		var fVat = gThis.m_oOptions.aoVatValues[iVatId];
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		return (fPrice * (1 + fVat / 100)).toFixed(gThis.m_oOptions.iPricePrecision);
	};
	
	gThis._CalculateNetPrice = function(sPrice) {
		var iVatId = gThis.m_jVatField.find('option:selected').attr('value');
		var fVat = gThis.m_oOptions.aoVatValues[iVatId];
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		return (fPrice / (1 + fVat / 100)).toFixed(gThis.m_oOptions.iPricePrecision);
	};*/
	
	gThis._Initialize = function() {
		/*if (gThis.m_oOptions.aoVatValues.length) {
			gThis.m_jUseVatField.click(GEventHandler(function(eEvent) {
				gThis._WriteTable();
				if ($(this).is(':checked')) {
					gThis.m_jVat.slideDown(100);
					gThis.m_jTable.find('.' + gThis._GetClass('NetPrice') + ' input').each(function() {
						$(this).triggerHandler('blur');
					});
				}
				else {
					gThis.m_jVat.slideUp(100);
				}
			}));
			gThis.m_jVatField.change(GEventHandler(function(eEvent) {
				gThis._WriteTable();
			}));
		}
		else {
			gThis.m_jVat.css('display', 'none');
		}
		*/
	};
	
	gThis.ValidatePrice = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : Math.abs(fPrice);
		$(eEvent.currentTarget).val(fPrice.toFixed(gThis.m_oOptions.iRangeLimitsPrecision));
	});
	
	gThis.ValidateRange = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : Math.abs(fPrice);
		$(eEvent.currentTarget).val(fPrice.toFixed(gThis.m_oOptions.iRangeLimitsPrecision));
	});
	
	gThis.Reset = function() {
		gThis.SetValue(gThis.m_oOptions.sDefault);
	};
	
}, oDefaults);
//new

/*
* TEXTAREA
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-textarea',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	iRows: 3,
	iCols: 60,
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormTextarea = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis.m_bResized;
	
	gThis._Constructor = function() {
		gThis.m_bResized = false;
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<textarea name="' + gThis.GetName() + '" id="' + gThis.GetId() + '" rows="' + gThis.m_oOptions.iRows + '" cols="' + gThis.m_oOptions.iCols + '"/>');
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {};
	
}, oDefaults);

/*
* CODE EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-textarea',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	iRows: 3,
	iCols: 60,
	sDefault: '',
	aoRules: [],
	sComment: ''
};

var GFormCodeEditor = GCore.ExtendClass(GFormTextField, function() {
	
	var gThis = this;
	
	gThis.m_bResized;
	
	gThis._Constructor = function() {
		gThis.m_bResized = false;
		gThis.m_bShown = false;
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (!gThis.m_bRepeatable) {
			gThis.m_jNode.append(gThis._AddField());
		}
		else {
			gThis.AddRepetition();
		}
		
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<textarea name="' + gThis.GetName() + '" id="' + gThis.GetId() + '" rows="' + gThis.m_oOptions.iRows + '" cols="' + gThis.m_oOptions.iCols + '"/>');
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		if(gThis.m_bShown){
			return;
		}else{
			CodeMirror.modeURL = GCore.DESIGN_PATH + '_js_libs/codemirror/mode/%N/%N.js';
			var editor = CodeMirror.fromTextArea(document.getElementById(gThis.GetId()), {
				width: '450px',
				tabMode: "indent",
				lineNumbers: true,
			});
			editor.setOption("mode", gThis.m_oOptions.sMode);
			CodeMirror.autoLoadMode(editor, gThis.m_oOptions.sMode);
			gThis.m_bShown = true;
		}
	};
	
}, oDefaults);

/*
* CLIENT SELECT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-client-select',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sHiddenClass: 'hidden',
		sButtonClass: 'button',
		sTriggerClass: 'trigger'
	},
	oImages: {
		sAddIcon: '_images_panel/icons/buttons/add-customer.png',
		sDeselectIcon: '_images_panel/icons/datagrid/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	fLoadClients: GCore.NULL,
	fLoadClientData: GCore.NULL
};

var GFormClientSelect = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadClients;
	gThis.m_fProcessProduct;
	gThis.m_jDatagrid;
	gThis.m_jDatagridWrapper;
	gThis.m_jTrigger;
	gThis.m_jSelectedDatagrid;
	gThis.m_gDatagrid;
	gThis.m_gSelectedDatagrid;
	gThis.m_gDataProvider;
	gThis.m_bFirstLoad = true;
	gThis.m_jClientName;
	gThis.m_jClientEmail;
	gThis.m_jClientGroup;
	gThis.m_aoAddresses = [];
	gThis.m_agListeners = [];
	
	gThis.m_bFirstLoad = true;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			for (var i = 0; i < jValues.length; i++) {
				aValues.push(jValues.eq(i).val());
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '" value="' + mValue[i] + '"/>');
			}
		}
		else {
			gThis.m_jField.val(mValue).change();
		}
	};
	
	gThis._OnDataLoaded = function(oData) {
		gThis.m_jClientName.text(oData.name);
		gThis.m_jClientEmail.text(oData.email);
		gThis.m_jClientGroup.text(oData.group);
		
		$('#address_data__billing_data__firstname').val(oData.billing_address.firstname);
		$('#address_data__billing_data__surname').val(oData.billing_address.surname);
		$('#address_data__billing_data__street').val(oData.billing_address.street);
		$('#address_data__billing_data__streetno').val(oData.billing_address.streetno);
		$('#address_data__billing_data__placeno').val(oData.billing_address.placeno);
		$('#address_data__billing_data__place').val(oData.billing_address.placename);
		$('#address_data__billing_data__postcode').val(oData.billing_address.postcode);
		$('#address_data__billing_data__companyname').val(oData.billing_address.companyname);
		$('#address_data__billing_data__nip').val(oData.billing_address.nip);
		$('#address_data__billing_data__email').val(oData.email);
		$('#address_data__billing_data__phone').val(oData.phone);
		
		$('#address_data__shipping_data__firstname').val(oData.delivery_address.firstname);
		$('#address_data__shipping_data__surname').val(oData.delivery_address.surname);
		$('#address_data__shipping_data__street').val(oData.delivery_address.street);
		$('#address_data__shipping_data__streetno').val(oData.delivery_address.streetno);
		$('#address_data__shipping_data__placeno').val(oData.delivery_address.placeno);
		$('#address_data__shipping_data__place').val(oData.delivery_address.placename);
		$('#address_data__shipping_data__postcode').val(oData.delivery_address.postcode);
		$('#address_data__shipping_data__companyname').val(oData.delivery_address.companyname);
		$('#address_data__shipping_data__nip').val(oData.delivery_address.nip);
		$('#address_data__shipping_data__email').val(oData.email);
		$('#address_data__shipping_data__phone').val(oData.phone);
		
		gThis.m_bFirstLoad = false;
	};
	
	gThis._OnSelect = function(gDg, sId) {
		gThis.SetValue(sId);
		gThis.m_oOptions.fLoadClientData({
			client: sId
		}, GCallback(gThis._OnDataLoaded));
	};
	
	gThis._OnDeselect = function(gDg, sId) {
		
	};
	
	gThis._OnChange = function(eEvent) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		var aoData = [];
		for (var i in eEvent.rows) {
			aoData.push({
				id: eEvent.rows[i].idproduct,
				quantity: eEvent.rows[i].quantity,
				variant: eEvent.rows[i].variant
			});
		}
		gThis.SetValue(aoData);
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '"/>');
		gThis.m_jDatagrid = $('<div/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jDatagridWrapper = $('<div class="existing-users"/>');
		gThis.m_jDatagridWrapper.append(gThis.m_jDatagrid);
		gThis.m_jNode.append(gThis.m_jDatagridWrapper);
		gThis.m_jDatagridWrapper.addClass(gThis._GetClass('Hidden'));
		gThis.m_jTrigger = $('<p class="' + gThis._GetClass('Trigger') + '"/>');
		
		var jA = $('<a href="#" id="__select" class="' + gThis._GetClass('Button') + '"/>');
		jA.append('<span><img src="' + gThis._GetImage('AddIcon') + '" alt=""/>' + GForm.Language.client_select_select_client + '</span>');
		jA.click(GEventHandler(function(eEvent) {
			var jImg = gThis.m_jTrigger.find('a#__select span img');
			if (gThis.m_jDatagridWrapper.hasClass(gThis._GetClass('Hidden'))) {
				gThis.m_jDatagridWrapper.css('display', 'none').removeClass(gThis._GetClass('Hidden'));
			}
			if (!gThis.m_jDatagridWrapper.get(0).bHidden) {
				gThis.m_gDatagrid.LoadData();
				gThis.m_jDatagridWrapper.get(0).bHidden = true;
				gThis.m_jTrigger.find('a#__select span').empty().append(jImg).append(GForm.Language.product_select_close_selection);
			}
			else {
				gThis.m_jDatagridWrapper.get(0).bHidden = false;
				gThis.m_jTrigger.find('a#__select span').empty().append(jImg).append(GForm.Language.client_select_select_client);
			}
			gThis.m_jDatagridWrapper.slideToggle(250);
			return false;
		}));
		
		var jAdd = $('<a style="margin-left: 20px;" href="#" class="' + gThis._GetClass('Button') + '"/>');
		jAdd.append('<span>' + GForm.Language.client_select_add_client + '</span>');
		jAdd.click(GEventHandler(function(eEvent) {
			if(gThis.m_jDatagridWrapper.get(0).bHidden){
				jA.click();
			}
			window.open(GCore.sAdminUrl + 'client/add');
		}));
		
		gThis.m_jTrigger.append(jA);
		gThis.m_jTrigger.append(jAdd);
		gThis.m_jNode.append(gThis.m_jTrigger);
		var jColumns = $('<div class="layout-two-columns"/>');
		var jLeftColumn = $('<div class="column"/>');
		jColumns.append(jLeftColumn);
		gThis.m_jClientName = $('<span class="constant"/>');
		jLeftColumn.append($('<div class="field-text"/>').append('<label>' + GForm.Language.client_select_client_name + '</label>').append($('<span class="repetition"/>').append(gThis.m_jClientName)));
		gThis.m_jClientEmail = $('<span class="constant"/>');
		jLeftColumn.append($('<div class="field-text"/>').append('<label>' + GForm.Language.client_select_client_email + '</label>').append($('<span class="repetition"/>').append(gThis.m_jClientEmail)));
		gThis.m_jClientGroup = $('<span class="constant"/>');
		jLeftColumn.append($('<div class="field-text"/>').append('<label>' + GForm.Language.client_select_client_group + '</label>').append($('<span class="repetition"/>').append(gThis.m_jClientGroup)));
		gThis.m_jNode.append(jColumns);
	};
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (!gThis.m_gDatagrid) {
			return;
		}
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		gThis._UpdateDatagridSelection(mValue);
		gThis.SetValue(mValue);
		if (gThis.m_bRepeatable) {
			gThis.m_gSelectedDatagrid.LoadData();
		}
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		gThis.m_gDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gDatagrid.LoadData();
		}
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitDatagrid();
			if (gThis.m_bRepeatable) {
				gThis._InitSelectedDatagrid();
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
			if (gThis.GetValue()) {
				gThis.m_oOptions.fLoadClientData({
					client: gThis.GetValue()
				}, GCallback(gThis._OnDataLoaded));
			}
		}
	};
	
	gThis._ProcessProduct = function(oProduct) {
		return oProduct;
	};
	
	gThis._ProcessSelectedProduct = function(oProduct) {
		oProduct = gThis.m_fProcessProduct(oProduct);
		return oProduct;
	};
	
	gThis._InitColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idclient',
			caption: GForm.Language.client_select_id,
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_firstname = new GF_Datagrid_Column({
			id: 'firstname',
			caption: GForm.Language.client_select_first_name,
			appearance: {
				width: 200
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_surname = new GF_Datagrid_Column({
			id: 'surname',
			caption: GForm.Language.client_select_surname,
			appearance: {
				width: 200
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_email = new GF_Datagrid_Column({
			id: 'email',
			caption: GForm.Language.client_select_email,
			appearance: {
				width: 180,
				visible: false
			}
		});
		
		var column_phone = new GF_Datagrid_Column({
			id: 'phone',
			caption: GForm.Language.client_select_phone,
			appearance: {
				width: 110,
				visible: false
			}
		});
		
		var column_group = new GF_Datagrid_Column({
			id: 'groupname',
			caption: GForm.Language.client_select_group
		});
		
		var column_adddate = new GF_Datagrid_Column({
			id: 'adddate',
			caption: GForm.Language.client_select_adddate,
			appearance: {
				width: 140,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		return [
			column_id,
			column_surname,
			column_firstname,
			column_group,
			column_email,
			column_phone,
			column_adddate
		];
		
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_fProcessProduct = gThis._ProcessProduct;
		gThis.m_fLoadClients = gThis.m_oOptions.fLoadClients;
		
		var aoColumns = gThis._InitColumns();
		
    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 15,
				key: 'idclient',
				only_one_selected: !gThis.m_bRepeatable,
				persistent: false
			},
			event_handlers: {
				load: gThis.m_fLoadClients,
				process: gThis.m_fProcessProduct,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect//,
				//selection_changed: gThis._OnChange
			},
			columns: aoColumns
    };
    
    gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		
	};
	
	gThis._Deselect = function(iDg, mId) {
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			gThis.m_gDatagrid.DeselectRow(mId[i]);
		}
		gThis.m_gSelectedDatagrid.ClearSelection();
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._InitSelectedDatagrid = function() {
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'idclient',
			event_handlers: {
				change: gThis._OnChange
			}
		}, []);
		
		var aoColumns = gThis._InitColumns();
		
		var gActionDeselect = new GF_Action({
			img: gThis._GetImage('DeselectIcon'),
			caption: GForm.Language.product_select_deselect,
			action: gThis._Deselect
		});
		
    var oOptions = {
			id: gThis.GetId() + '_selected',
			mechanics: {
				rows_per_page: 15,
				key: 'idclient',
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (gThis.m_bFirstLoad) {
						gThis.m_bFirstLoad = false;
						gThis._LoadSelected(oRequest, sResponseHandler);
					}
					else {
						gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
					}
				},
				update_row: function(sId, oRow) {
					gThis.m_gDataProvider.UpdateRow(sId, oRow);
				},
				process: gThis._ProcessSelectedProduct
			},
			columns: aoColumns,
			row_actions: [
				gActionDeselect
			],
			context_actions: [
				gActionDeselect
			],
			group_actions: [
				gActionDeselect
			]
    };
		
		gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);
		
	};
	
	gThis._LoadSelected = function(oRequest, sResponseHandler) {
		if (gThis.m_oOptions.bAdvancedEditor) {
			var asDefaults = [];
			for (var i in gThis.m_oOptions.asDefaults) {
				asDefaults.push(gThis.m_oOptions.asDefaults[i].id);
			}
			oRequest.where = [{
				column: 'idclient',
				value: asDefaults,
				operator: 'IN'
			}];
		}
		else {
			oRequest.where = [{
				column: 'idclient',
				value: gThis.m_oOptions.asDefaults,
				operator: 'IN'
			}];
		}
		gThis.m_fLoadClients(oRequest, GCallback(function(eEvent) {
			gThis.m_gDataProvider.ChangeData(eEvent.rows);
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
	gThis.GetAddress = function(sAddress) {
		for (var i in gThis.m_aoAddresses) {
			if (gThis.m_aoAddresses[i]._id == sAddress) {
				return GCore.Duplicate(gThis.m_aoAddresses[i], true);
			}
		}
		return {};
	};
	
	gThis.AddListener = function(gNode) {
		gThis.m_agListeners.push(gNode);
	};
	
}, oDefaults);

/*
* DATAGRID SELECT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-datagrid-select',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition'
	},
	oImages: {
		sDeselectIcon: '_images_panel/icons/datagrid/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	fLoadRecords: GCore.NULL,
	bAdvancedEditor: false
};

var GFormDatagridSelect = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadRecords;
	gThis.m_fProcessRecords;
	gThis.m_jDatagrid;
	gThis.m_jSelectedDatagrid;
	gThis.m_gDatagrid;
	gThis.m_gSelectedDatagrid;
	gThis.m_gDataProvider;
	gThis.m_bFirstLoad = true;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			for (var i = 0; i < jValues.length; i++) {
				aValues.push(jValues.eq(i).val());
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '" value="' + mValue[i] + '"/>');
			}
		}
		else {
			gThis.m_jField.val(mValue).change();
		}
	};
	
	gThis._OnSelect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			var oSelectedRow = gDg.GetRow(sId);
			gThis.m_gDataProvider.AddRow(oSelectedRow);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue(sId);
		}
	};
	
	gThis._OnDeselect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			gThis.m_gDataProvider.DeleteRow(sId);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue('');
		}
	};
	
	gThis._OnChange = function(eEvent) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		var aoData = [];
		for (var i in eEvent.rows) {
			aoData.push(eEvent.rows[i][gThis.m_oOptions.sKey]);
		}
		gThis.SetValue(aoData);
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (gThis.m_bRepeatable) {
			gThis.m_jField = $('<div/>');
			gThis.m_jDatagrid = $('<div/>');
			jLabel = $('<label/>');
			jLabel.text(GForm.Language.datagrid_select_selected + ':');
			gThis.m_jSelectedDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
			gThis.m_jNode.append(jLabel);
			gThis.m_jNode.append(gThis.m_jSelectedDatagrid);
		}
		else {
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '"/>');
			gThis.m_jDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
		}
		gThis.m_jNode.append(gThis.m_jField);
	};
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (!gThis.m_gDatagrid) {
			return;
		}
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		gThis._UpdateDatagridSelection(mValue);
		gThis.SetValue(mValue);
		if (gThis.m_bRepeatable) {
			gThis.m_gSelectedDatagrid.LoadData();
		}
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		gThis.m_gDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gDatagrid.LoadData();
		}
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitDatagrid();
			if (gThis.m_bRepeatable) {
				gThis._InitSelectedDatagrid();
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
		}
	};
	
	gThis._ProcessRecord = function(oRecord) {
		return oRecord;
	};
	
	gThis._ProcessSelectedRecord = function(oRecord) {
		oRecord = gThis.m_fProcessRecords(oRecord);
		return oRecord;
	};
	
	gThis._InitOptions = function(aoOptions) {
		var agOptions = [];
		for (var i in aoOptions) {
			agOptions.push(new GF_Datagrid_Column(aoOptions[i]));
		}
		return agOptions;
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_fProcessRecords = gThis._ProcessRecord;
		gThis.m_fLoadRecords = gThis.m_oOptions.fLoadRecords;
		
		var aoColumns = gThis._InitOptions(gThis.m_oOptions.aoColumns);
		
    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 15,
				key: gThis.m_oOptions.sKey,
				only_one_selected: !gThis.m_bRepeatable,
				persistent: false
			},
			event_handlers: {
				load: gThis.m_fLoadRecords,
				process: gThis.m_fProcessRecords,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect//,
				//selection_changed: gThis._OnChange
			},
			columns: aoColumns
    };
		
		try {
			gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		}
		catch (xException) {
			GException.Handle(xException);
		}
		
	};
	
	gThis._Deselect = function(iDg, mId) {
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			gThis.m_gDatagrid.DeselectRow(mId[i]);
		}
		gThis.m_gSelectedDatagrid.ClearSelection();
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._InitSelectedDatagrid = function() {
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: gThis.m_oOptions.sKey,
			event_handlers: {
				change: gThis._OnChange
			}
		}, []);
		
		if (gThis.m_oOptions.aoSelectedColumns == undefined) {
			var aoColumns = gThis._InitOptions(gThis.m_oOptions.aoColumns);
		}
		else {
			var aoColumns = gThis._InitOptions(gThis.m_oOptions.aoSelectedColumns);
		}
		
		var gActionDeselect = new GF_Action({
			img: gThis._GetImage('DeselectIcon'),
			caption: GForm.Language.datagrid_select_deselect,
			action: gThis._Deselect
		});
		
    var oOptions = {
			id: gThis.GetId() + '_selected',
			mechanics: {
				rows_per_page: 500,
				key: gThis.m_oOptions.sKey,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (gThis.m_bFirstLoad) {
						gThis.m_bFirstLoad = false;
						gThis._LoadSelected(oRequest, sResponseHandler);
					}
					else {
						gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
					}
				},
				update_row: function(sId, oRow) {
					gThis.m_gDataProvider.UpdateRow(sId, oRow);
				},
				process: gThis._ProcessSelectedRecord
			},
			columns: aoColumns,
			row_actions: [
				gActionDeselect
			],
			context_actions: [
				gActionDeselect
			],
			group_actions: [
				gActionDeselect
			]
    };
		
		try {
			gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);
		}
		catch (xException) {
			GException.Handle(xException);
		}
		
	};
	
	gThis._LoadSelected = function(oRequest, sResponseHandler) {
		oRequest.where = [{
			column: gThis.m_oOptions.sKey,
			value: gThis.m_oOptions.asDefaults,
			operator: 'IN'
		}];
		gThis.m_fLoadRecords(oRequest, GCallback(function(eEvent) {
			gThis.m_gDataProvider.ChangeData(eEvent.rows);
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
}, oDefaults);


/*
* DATETIME
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-datetime',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition'
	},
	oImages: {
		sCalendarIcon: '_images_panel/icons/buttons/calendar.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	sMinDate: null,
	sMaxDate: null
};

var GFormDateTime = GCore.ExtendClass(GFormDate, function() {
	
	var gThis = this;
	
	gThis.m_jTrigger;
	
	gThis._Constructor = function() {
		gThis.m_jTrigger = $('<img style="width: 16px; height: 16px;" src="' + gThis._GetImage('CalendarIcon') + '" alt=""/>');
		gThis.m_oOptions.sSuffix = gThis.m_jTrigger.css('cursor', 'pointer');
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="' + gThis.GetName(sId) + '[d]" id="' + gThis.GetId(sId) + '_d"/>');
		if ((gThis.m_jField instanceof $) && gThis.m_jField.length) {
			gThis.m_jField = gThis.m_jField.add(jField);
		}
		else {
			gThis.m_jField = jField;
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jSuffix.append(' ' + GForm.Language.datetime_hour);
			jRepetitionNode.append(jSuffix);
		}
		
		jField = $('<select name="' + gThis.GetName(sId) + '[h]" id="' + gThis.GetId(sId) + '_h"/>');
		for (var i = 0; i < 24; i++) {
			jField.append('<option value="' + i  + '">' + ((i < 10) ? '0' + i : i) + '</option>');
		}
		gThis.m_jField = gThis.m_jField.add(jField);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		
		jField = $('<select name="' + gThis.GetName(sId) + '[m]" id="' + gThis.GetId(sId) + '_m"/>');
		for (var i = 0; i < 60; i++) {
			jField.append('<option value="' + i  + '">' + ((i < 10) ? '0' + i : i) + '</option>');
		}
		gThis.m_jField = gThis.m_jField.add(jField);
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		gThis.m_bShown = true;		
		gThis.m_jField.filter('input').datepicker({
			minDate: gThis.m_oOptions.sMinDate,
			maxDate: gThis.m_oOptions.sMaxDate
		});
		var jField = gThis.m_jField.filter('select');
		jField.focus(gThis.OnFocus);
		jField.blur(gThis.OnBlur);
		jField.each(function() {
			$(this).change(GEventHandler(function(eEvent) {
				gThis.Validate(false, this.sRepetition);
			}));
		});
		jField.keydown(function(eEvent) {
			var dSelect = this;
			setTimeout(function() {
				dSelect.Update();
			}, 50);
			return true;
		});
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jTrigger.click(function() {
			gThis.m_jField.filter('input').datepicker('show');
		});
		gThis.m_jField.filter('select').GSelect();
	};
	
	gThis.SetValue = function(mValue) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (mValue != undefined) {
			gThis.m_jField.filter('input[name$="[d]"]').val(mValue.d);
			gThis.m_jField.filter('select[name$="[h]"]').val(mValue.h);
			gThis.m_jField.filter('select[name$="[m]"]').val(mValue.m);
		}
	};
	
}, oDefaults);


/*
* DOWNLOADER
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-image',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sThumbClass: 'thumb',
		sNameClass: 'name',
		sSelectedTableClass: 'selected',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sChooseIcon: '_images_panel/icons/filetypes/directory.png',
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	sUploadUrl: '',
	sSessionId: '',
	sSessionName: '',
	asFileTypes: [],
	sFileTypesDescription: '',
	fDeleteHandler: GCore.NULL,
	fLoadFiles: GCore.NULL,
	sSwfUploadUrl: '_data_panel/swfupload.swf',
	iWidth: 131,
	iHeight: 34,
	iMaxFileSize: 100 * 1024,	// kB
	sMainId: 0
};

var GFormDownloader = GCore.ExtendClass(GFormFile, function() {

	var gThis = this;

	gThis.m_bShown = false;
	gThis.m_jFilesDatagrid;
	gThis.m_gFilesDatagrid;
	gThis.m_jSelectedFiles;
	gThis.m_jSwfUpload;
	gThis.m_jQueue;
	gThis.m_iUploadsInProgress = 0;
	gThis.m_iLockId = -1;
	gThis.m_bLoadedDefaults = false;
	gThis.m_jUnmodified;

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jSwfUpload = $('<div class="' + gThis._GetClass('AddFiles') + '"/>').append('<span id="' + gThis.GetId() + '__upload"/>');
		gThis.m_jNode.append(gThis.m_jSwfUpload);
		gThis.m_jChooseButton = $('<a href="#" class="button expand"><span><img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfiles_select + '</span></a>');
		gThis.m_jNode.append($('<span class="browse-pictures" style="float: right;margin-right: 5px;"/>').append(gThis.m_jChooseButton));
		gThis.m_jQueue = $('<ul class="' + gThis._GetClass('Queue') + '"/>');
		gThis.m_jNode.append(gThis.m_jQueue);
		gThis.m_jFilesDatagrid = $('<div/>').css('display', 'none');
		gThis.m_jNode.append(gThis.m_jFilesDatagrid);
		if (gThis.m_bRepeatable) {
			var jTable = $('<table class="' + gThis._GetClass('SelectedTable') + '" cellspacing="0"/>');
			var jThead = $('<thead/>');
			jThead.append('<th>' + GForm.Language.file_selector_selected_image + '</th>');
			jThead.append('<th>' + GForm.Language.file_selector_photo_cancel + '</th>');
			gThis.m_jSelectedFiles = $('<tbody/>');
			jTable.append(jThead);
			jTable.append(gThis.m_jSelectedFiles);
			gThis.m_jNode.append(jTable);
			gThis.m_jField = $('<div/>');
		}
		else {
			gThis.m_jSelectedFiles = $('<div class="' + gThis._GetClass('SelectedTable') + '"/>');
			gThis.m_jNode.append(gThis.m_jSelectedFiles);
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '[0]"/>');
		}
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jUnmodified = $('<input type="hidden" name="' + gThis.GetName() + '[unmodified]" value="1"/>');
		gThis.m_jNode.append(gThis.m_jUnmodified);
	};

	gThis._InitializeEvents = function() {
		gThis.m_jChooseButton.click(gThis._OnChoose);
	};
	
	gThis._OnChoose = GEventHandler(function(eEvent) {
		gThis.m_jFilesDatagrid.slideToggle(250);
		gThis.m_jChooseButton.toggleClass('expand');
		if(gThis.m_jChooseButton.hasClass('expand')){
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select);
		}else{
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_unselect);
		}
		return false;
	});
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			for (var i = 0; i < jValues.length; i++) {
				aValues.push(jValues.eq(i).val());
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};

	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '" value="' + mValue[i] + '"/>');
			}
		}
		else {
			gThis.m_jField.val(mValue);
		}
	};
	
	gThis.Populate = function(mValue) {
		if (!gThis.m_gFilesDatagrid) {
			return;
		}
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		gThis._UpdateDatagridSelection(mValue);
		gThis.SetValue(mValue);
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		gThis.m_gFilesDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gFilesDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gFilesDatagrid.LoadData();
		}
	};

	gThis._OnSelect = function(gDg, sId) {
		var oFile = gDg.GetRow(sId);
		gThis._AddImage(sId, oFile);
	};
	
	gThis._AddImage = function(sId, oFile) {
		if (gThis.m_bRepeatable) {
			var jFileTr = $('<tr class="file__' + sId + '"/>');
			jFileTr.append('<th scope="row"><span class="' + gThis._GetClass('Name') + '">' + oFile.filename + '</span></th>');
			var jRemove = $('<a href="#"/>');
			jRemove.click(function() {
				var sId = $(this).closest('tr').attr('class').substr(6);
				gThis.m_gFilesDatagrid.DeselectRow(sId);
				return false;
			});
			jRemove.append('<img src="' + gThis._GetImage('DeleteIcon') + '" alt="' + GForm.Language.file_selector_deselect + '" title="' + GForm.Language.file_selector_deselect + '"/>');
			jFileTr.append($('<td></td>').append(jRemove));
			gThis.m_jSelectedFiles.append(jFileTr);
		}
		else {
			gThis.m_jSelectedFiles.empty().append('<h4>' + GForm.Language.file_selector_selected_image + '</h4>').append('<img src="' + oFile.thumb + '" alt=""/>');
		}
	};

	gThis._OnDeselect = function(gDg, sId) {
		gThis._RemoveImage(sId);
	};
	
	gThis._RemoveImage = function(sId, oFile) {
		if (gThis.m_bRepeatable) {
			var bCheck = false;
			var jFileTr = gThis.m_jSelectedFiles.find('tr.file__' + sId);
			if (jFileTr.find('input[name="' + gThis.GetName() + '[main]"]:checked').length) {
				bCheck = true;
			}
			jFileTr.remove();
			if (bCheck) {
				gThis.m_jSelectedFiles.find('tr:first input[name="' + gThis.GetName() + '[main]"]').click();
			}
		}
		else {
			gThis.m_jSelectedFiles.empty();
		}
	};

	gThis._OnChange = function(iDg, asIds) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		gThis.SetValue(asIds);
	};
	
	gThis.OnReset = function() {
		gThis.m_bLoadedDefaults = false;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitFilesDatagrid();
			gThis._InitUploader();
			if (gThis.m_bRepeatable) {
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
			gThis.m_jUnmodified.val('0');
		}
	};

	gThis._ProcessFile = function(oRow) {
		if (oRow.thumb != '') {
			oRow.thumbpreview = '<a href="' + oRow.thumb + '" class="show-thumb"><img src="' + GCore.DESIGN_PATH + '_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
		}
		return oRow;
	};

	gThis._InitUploader = function() {
		var sFileTypes = '';
		for (var i = 0; i < gThis.m_oOptions.asFileTypes.length; i++) {
			sFileTypes += '; *.' + gThis.m_oOptions.asFileTypes[i];
		}
		sFileTypes = sFileTypes.substr(2);
		var oPostParams = {};
		oPostParams[gThis.m_oOptions.sSessionName] = gThis.m_oOptions.sSessionId;
		gThis.m_jSwfUpload.swfupload({
			upload_url: gThis.m_oOptions.sUploadUrl,
			file_size_limit: gThis.m_oOptions.iMaxFileSize,
			file_types: sFileTypes,
			file_types_description: gThis.m_oOptions.sFileTypesDescription,
			file_upload_limit: 0,
			file_queue_limit: 0,
			button_image_url: gThis._GetImage('UploadButton'),
			button_placeholder_id: gThis.GetId() + '__upload',
			button_width: gThis.m_oOptions.iWidth,
			button_height: gThis.m_oOptions.iHeight,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			flash_url: GCore.DESIGN_PATH + gThis.m_oOptions.sSwfUploadUrl,
			post_params: oPostParams
		});
		gThis.m_jSwfUpload.bind("fileQueued", gThis.OnFileQueued);
		gThis.m_jSwfUpload.bind("uploadComplete", gThis.OnUploadComplete);
		gThis.m_jSwfUpload.bind("uploadSuccess", gThis.OnUploadSuccess);
		gThis.m_jSwfUpload.bind("uploadProgress", gThis.OnUploadProgress);
		gThis.m_jSwfUpload.bind("uploadError", gThis.OnUploadError);
	};

	gThis.OnFileQueued = function(eEvent, oFile) {
		if (gThis.m_iUploadsInProgress++ == 0) {
			gThis.m_iLockId = gThis.m_gForm.Lock(GForm.Language.file_selector_form_blocked, GForm.Language.file_selector_form_blocked_description);
		}
		gThis.m_jSwfUpload.swfupload("startUpload");
		var jLi = $('<li class="upload__' + oFile.index + '"/>');
		jLi.append('<h4>' + oFile.name + '</h4>');
		jLi.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		jLi.append('<div class="' + gThis._GetClass('ProgressBar') + '"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		gThis.m_jQueue.append(jLi);
	};

	gThis.OnDelete = function() {
		gThis.m_jSwfUpload.swfupload('cancelUpload', sFid);
	};

	gThis.OnUploadProgress = function(eEvent, oFile, iCompleted, iTotal) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		jLi.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + Math.ceil(iCompleted / 1024) + 'kB / ' + Math.ceil(iTotal / 1024) + 'kB');
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', (iCompleted / iTotal * 100) + '%');
	};

	gThis.OnUploadError = function(eEvent, oFile, iErrorCode, sMessage) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadError'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_error);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		GAlert(GForm.Language.file_selector_upload_error, sMessage);
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadSuccess = function(eEvent, oFile, sServerData, sResponse) {
		if (sServerData.substr(0, 11) != 'response = ') {
			gThis.OnUploadError(eEvent, oFile, 0, sServerData);
			return;
		}
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadSuccess'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_success);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		eval("var oResponse = " + sServerData.substr(11) + ";");
		if (!oResponse.sId) {
			gThis.OnUploadError(eEvent, oFile, 0, GForm.Language.file_selector_processing_error);
			return;
		}
		if ((gThis.m_oOptions.oRepeat.iMax == 1) || !(gThis.m_gFilesDatagrid.m_asSelected instanceof Array)) {
			gThis.m_gFilesDatagrid.m_asSelected = [];
		}
		gThis.m_gFilesDatagrid.m_asSelected.push(oResponse.sId);
		gThis._OnChange(0, gThis.m_gFilesDatagrid.m_asSelected);
		gThis._AddImage(oResponse.sId, {
			idfile: oResponse.sId,
			filename: oResponse.sFilename,
			thumb: oResponse.sThumb,
			filetype: oResponse.sFileType,
			fileextension: oResponse.sExtension
		});
		gThis.m_gFilesDatagrid.LoadData();
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadComplete = function(eEvent, oFile) {
		if (--gThis.m_iUploadsInProgress <= 0) {
			gThis.m_iUploadsInProgress = 0;
			gThis.m_gForm.Unlock(gThis.m_iLockId);
		}
	};

	gThis._InitColumns = function() {

	  var column_id = new GF_Datagrid_Column({
			id: 'idfile',
			caption: GForm.Language.file_selector_id,
			sorting: {
				default_order: GF_Datagrid.SORT_DIR_DESC
			},
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});

		var column_filename = new GF_Datagrid_Column({
			id: 'filename',
			caption: GForm.Language.file_selector_filename,
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}

		});

		var column_fileextension = new GF_Datagrid_Column({
			id: 'fileextension',
			appearance: {
				width: 110,
			},
			caption: GForm.Language.file_selector_extension,
		});

		return [
			column_id,
			column_filename,
			column_fileextension,
		];

	};
	
	gThis._LoadDefaults = function(oRequest) {
		gThis.m_jSelectedFiles.empty();
		if (gThis.m_bRepeatable) {
			oRequest.where = [{
				column: 'idfile',
				value: gThis.m_oOptions.asDefaults,
				operator: 'IN'
			}];
		}
		else {
			oRequest.where = [{
				column: 'idfile',
				value: gThis.m_oOptions.sDefault,
				operator: 'IN'
			}];
		}
		oRequest.starting_from = 0;
		gThis.m_oOptions.fLoadFiles(oRequest, GCallback(gThis._DefaultsLoaded));
	};
	
	gThis._DefaultsLoaded = function(oData) {
		for (var i = 0; i < oData.rows.length; i++) {
			gThis._AddImage(oData.rows[i].idfile, oData.rows[i]);
		}
		gThis.m_bLoadedDefaults = true;
	};
	
	gThis._OnDataLoaded = function(dDg) {
		dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
	};

	gThis._InitFilesDatagrid = function() {

		var aoColumns = gThis._InitColumns();

    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 15,
				key: 'idfile',
				no_column_modification: true,
				only_one_selected: !gThis.m_bRepeatable,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (!gThis.m_bLoadedDefaults) {
						gThis._LoadDefaults(GCore.Duplicate(true, oRequest));
						gThis.m_bLoadedDefaults = true;
					}
					gThis.m_oOptions.fLoadFiles(oRequest, sResponseHandler);
				},
				loaded: gThis._OnDataLoaded,
				process: gThis._ProcessFile,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				selection_changed: gThis._OnChange
			},
			columns: aoColumns
    };

		gThis.m_gFilesDatagrid = new GF_Datagrid(gThis.m_jFilesDatagrid, oOptions);
	};

}, oDefaults);

/*
* IMAGE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-image',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sThumbClass: 'thumb',
		sNameClass: 'name',
		sSelectedTableClass: 'selected',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sChooseIcon: '_images_panel/icons/filetypes/directory.png',
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	sUploadUrl: '',
	sSessionId: '',
	sSessionName: '',
	asFileTypes: [],
	sFileTypesDescription: '',
	fDeleteHandler: GCore.NULL,
	fLoadFiles: GCore.NULL,
	sSwfUploadUrl: '_data_panel/swfupload.swf',
	iWidth: 131,
	iHeight: 34,
	iMaxFileSize: 100 * 1024,	// kB
	sMainId: 0
};

var GFormImage = GCore.ExtendClass(GFormFile, function() {

	var gThis = this;

	gThis.m_bShown = false;
	gThis.m_jFilesDatagrid;
	gThis.m_gFilesDatagrid;
	gThis.m_jSelectedFiles;
	gThis.m_jSwfUpload;
	gThis.m_jQueue;
	gThis.m_iUploadsInProgress = 0;
	gThis.m_iLockId = -1;
	gThis.m_bLoadedDefaults = false;
	gThis.m_jUnmodified;

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jSwfUpload = $('<div class="' + gThis._GetClass('AddFiles') + '"/>').append('<span id="' + gThis.GetId() + '__upload"/>');
		gThis.m_jNode.append(gThis.m_jSwfUpload);
		gThis.m_jChooseButton = $('<a href="#" class="button expand"><span><img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfiles_select + '</span></a>');
		gThis.m_jNode.append($('<span class="browse-pictures" style="float: right;margin-right: 5px;"/>').append(gThis.m_jChooseButton));
		gThis.m_jQueue = $('<ul class="' + gThis._GetClass('Queue') + '"/>');
		gThis.m_jNode.append(gThis.m_jQueue);
		gThis.m_jFilesDatagrid = $('<div/>').css('display', 'none');
		gThis.m_jNode.append(gThis.m_jFilesDatagrid);
		if (gThis.m_bRepeatable) {
			var jTable = $('<table class="' + gThis._GetClass('SelectedTable') + '" cellspacing="0"/>');
			var jThead = $('<thead/>');
			jThead.append('<th>' + GForm.Language.file_selector_photo + '</th>');
			jThead.append('<th>' + GForm.Language.file_selector_photo_main + '</th>');
			jThead.append('<th>' + GForm.Language.file_selector_photo_cancel + '</th>');
			gThis.m_jSelectedFiles = $('<tbody/>');
			jTable.append(jThead);
			jTable.append(gThis.m_jSelectedFiles);
			gThis.m_jNode.append(jTable);
			gThis.m_jField = $('<div/>');
		}
		else {
			gThis.m_jSelectedFiles = $('<div class="' + gThis._GetClass('SelectedTable') + '"/>');
			gThis.m_jNode.append(gThis.m_jSelectedFiles);
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '[0]"/>');
		}
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jUnmodified = $('<input type="hidden" name="' + gThis.GetName() + '[unmodified]" value="1"/>');
		gThis.m_jNode.append(gThis.m_jUnmodified);
	};

	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			for (var i = 0; i < jValues.length; i++) {
				aValues.push(jValues.eq(i).val());
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};

	gThis._InitializeEvents = function() {
		gThis.m_jChooseButton.click(gThis._OnChoose);
	};
	
	gThis._OnChoose = GEventHandler(function(eEvent) {
		gThis.m_jFilesDatagrid.slideToggle(250);
		gThis.m_jChooseButton.toggleClass('expand');
		if(gThis.m_jChooseButton.hasClass('expand')){
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select);
		}else{
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_unselect);
		}
		return false;
	});
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '" value="' + mValue[i] + '"/>');
			}
		}
		else {
			gThis.m_jField.val(mValue);
		}
	};
	
	gThis.Populate = function(mValue) {
		if (!gThis.m_gFilesDatagrid) {
			return;
		}
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		gThis._UpdateDatagridSelection(mValue);
		gThis.SetValue(mValue);
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		
		gThis.m_gFilesDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gFilesDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gFilesDatagrid.LoadData();
		}
	};

	gThis._OnSelect = function(gDg, sId) {
		var oFile = gDg.GetRow(sId);
		gThis._AddImage(sId, oFile);
	};
	
	gThis._AddImage = function(sId, oFile) {
		if (gThis.m_bRepeatable) {
			var jFileTr = $('<tr class="file__' + sId + '"/>');
			jFileTr.append('<th scope="row"><span class="' + gThis._GetClass('Thumb') + '"><img src="' + oFile.thumb + '" alt=""/></span><span class="' + gThis._GetClass('Name') + '">' + oFile.filename + '</span></th>');
			var jRadio = $('<input type="radio" name="' + gThis.GetName() + '[main]" value="' + sId + '"/>');
			if (gThis.m_oOptions.sMainId) {
				
				if (!gThis.m_bLoadedDefaults) {
					if (sId == gThis.m_oOptions.sMainId) {
						
						jRadio.attr('checked', 'checked');
					}
				}
				else {
					if (sId == gThis.m_oOptions.sMainId) {
						
						jRadio.attr('checked', 'checked');
					}
					if (!gThis.m_jSelectedFiles.children('tr').length) {
						jRadio.attr('checked', 'checked');
					}
				}
			}
			else if (!gThis.m_jSelectedFiles.children('tr').length) {
				jRadio.attr('checked', 'checked');
			}
			jFileTr.append($('<td/>').append(jRadio));
			var jRemove = $('<a href="#"/>');
			jRemove.click(function() {
				var sId = $(this).closest('tr').attr('class').substr(6);
				gThis.m_gFilesDatagrid.DeselectRow(sId);
				return false;
			});
			jRemove.append('<img src="' + gThis._GetImage('DeleteIcon') + '" alt="' + GForm.Language.file_selector_deselect + '" title="' + GForm.Language.file_selector_deselect + '"/>');
			jFileTr.append($('<td></td>').append(jRemove));
			gThis.m_jSelectedFiles.append(jFileTr);
		}
		else {
			var jRemove = $('<a href="#"/>');
			jRemove.click(function() {
				gThis.m_gFilesDatagrid.DeselectRow(sId);
				gThis.m_jSelectedFiles.empty();
				return false;
			});
			jRemove.append('<img src="' + gThis._GetImage('DeleteIcon') + '" alt="' + GForm.Language.file_selector_deselect + '" title="' + GForm.Language.file_selector_deselect + '"/>');
			gThis.m_jSelectedFiles.empty().append('<h4>' + GForm.Language.file_selector_selected_image + '</h4>').append('<img src="' + oFile.thumb + '" alt=""/>').append(jRemove);
		}
	};

	gThis._OnDeselect = function(gDg, sId) {
		gThis._RemoveImage(sId);
	};
	
	gThis._RemoveImage = function(sId, oFile) {
		if (gThis.m_bRepeatable) {
			var bCheck = false;
			var jFileTr = gThis.m_jSelectedFiles.find('tr.file__' + sId);
			if (jFileTr.find('input[name="' + gThis.GetName() + '[main]"]:checked').length) {
				bCheck = true;
			}
			jFileTr.remove();
			if (bCheck) {
				gThis.m_jSelectedFiles.find('tr:first input[name="' + gThis.GetName() + '[main]"]').click();
			}
		}
		else {
			gThis.m_jSelectedFiles.empty();
		}
	};

	gThis._OnChange = function(iDg, asIds) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		gThis.SetValue(asIds);
	};
	
	gThis.OnReset = function() {
		gThis.m_bLoadedDefaults = false;
	};
	
	gThis.OnShow = function() {
		if (gThis.m_bShown === false) {
			gThis._InitFilesDatagrid();
			gThis._InitUploader();
			if (gThis.m_bRepeatable) {
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
			gThis.m_jUnmodified.val('0');
		}
	};

	gThis._ProcessFile = function(oRow) {
		if (oRow.thumb != '') {
			oRow.thumbpreview = '<a href="' + oRow.thumb + '" ><img src="' + oRow.thumb + '" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
		}
		return oRow;
	};

	gThis._InitUploader = function() {
		var sFileTypes = '';
		for (var i = 0; i < gThis.m_oOptions.asFileTypes.length; i++) {
			sFileTypes += '; *.' + gThis.m_oOptions.asFileTypes[i];
		}
		sFileTypes = sFileTypes.substr(2);
		var oPostParams = {};
		oPostParams[gThis.m_oOptions.sSessionName] = gThis.m_oOptions.sSessionId;
		gThis.m_jSwfUpload.swfupload({
			upload_url: gThis.m_oOptions.sUploadUrl,
			file_size_limit: gThis.m_oOptions.iMaxFileSize,
			file_types: sFileTypes,
			file_types_description: gThis.m_oOptions.sFileTypesDescription,
			file_upload_limit: 0,
			file_queue_limit: 0,
			button_image_url: gThis._GetImage('UploadButton'),
			button_placeholder_id: gThis.GetId() + '__upload',
			button_width: gThis.m_oOptions.iWidth,
			button_height: gThis.m_oOptions.iHeight,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			flash_url: GCore.DESIGN_PATH + gThis.m_oOptions.sSwfUploadUrl,
			post_params: oPostParams
		});
		gThis.m_jSwfUpload.bind("fileQueued", gThis.OnFileQueued);
		gThis.m_jSwfUpload.bind("uploadComplete", gThis.OnUploadComplete);
		gThis.m_jSwfUpload.bind("uploadSuccess", gThis.OnUploadSuccess);
		gThis.m_jSwfUpload.bind("uploadProgress", gThis.OnUploadProgress);
		gThis.m_jSwfUpload.bind("uploadError", gThis.OnUploadError);
	};

	gThis.OnFileQueued = function(eEvent, oFile) {
		if (gThis.m_iUploadsInProgress++ == 0) {
			gThis.m_iLockId = gThis.m_gForm.Lock(GForm.Language.file_selector_form_blocked, GForm.Language.file_selector_form_blocked_description);
		}
		gThis.m_jSwfUpload.swfupload("startUpload");
		var jLi = $('<li class="upload__' + oFile.index + '"/>');
		jLi.append('<h4>' + oFile.name + '</h4>');
		jLi.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		jLi.append('<div class="' + gThis._GetClass('ProgressBar') + '"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		gThis.m_jQueue.append(jLi);
	};

	gThis.OnDelete = function() {
		gThis.m_jSwfUpload.swfupload('cancelUpload', sFid);
	};

	gThis.OnUploadProgress = function(eEvent, oFile, iCompleted, iTotal) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		jLi.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + Math.ceil(iCompleted / 1024) + 'kB / ' + Math.ceil(iTotal / 1024) + 'kB');
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', (iCompleted / iTotal * 100) + '%');
	};

	gThis.OnUploadError = function(eEvent, oFile, iErrorCode, sMessage) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadError'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_error);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		GAlert(GForm.Language.file_selector_upload_error, sMessage);
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadSuccess = function(eEvent, oFile, sServerData, sResponse) {
		if (sServerData.substr(0, 11) != 'response = ') {
			gThis.OnUploadError(eEvent, oFile, 0, sServerData);
			return;
		}
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadSuccess'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_success);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		eval("var oResponse = " + sServerData.substr(11) + ";");
		if (!oResponse.sId) {
			gThis.OnUploadError(eEvent, oFile, 0, GForm.Language.file_selector_processing_error);
			return;
		}
		if ((gThis.m_oOptions.oRepeat.iMax == 1) || !(gThis.m_gFilesDatagrid.m_asSelected instanceof Array)) {
			gThis.m_gFilesDatagrid.m_asSelected = [];
		}
		gThis.m_gFilesDatagrid.m_asSelected.push(oResponse.sId);
		gThis._OnChange(0, gThis.m_gFilesDatagrid.m_asSelected);
		gThis._AddImage(oResponse.sId, {
			idfile: oResponse.sId,
			filename: oResponse.sFilename,
			thumb: oResponse.sThumb,
			filetype: oResponse.sFileType,
			fileextension: oResponse.sExtension
		});
		gThis.m_gFilesDatagrid.LoadData();
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadComplete = function(eEvent, oFile) {
		if (--gThis.m_iUploadsInProgress <= 0) {
			gThis.m_iUploadsInProgress = 0;
			gThis.m_gForm.Unlock(gThis.m_iLockId);
		}
	};

	gThis._InitColumns = function() {

	  var column_id = new GF_Datagrid_Column({
			id: 'idfile',
			caption: GForm.Language.file_selector_id,
			sorting: {
				default_order: GF_Datagrid.SORT_DIR_DESC
			},
			appearance: {
				width: 90,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});

		var column_filename = new GF_Datagrid_Column({
			id: 'filename',
			caption: GForm.Language.file_selector_filename,
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}

		});

		var column_fileextension = new GF_Datagrid_Column({
			id: 'fileextension',
			caption: GForm.Language.file_selector_extension
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});

		return [
			column_id,
			column_thumb,
			column_filename,
			column_fileextension,
		];

	};
	
	gThis._LoadDefaults = function(oRequest) {
		gThis.m_jSelectedFiles.empty();
		if (gThis.m_bRepeatable) {
			oRequest.where = [{
				column: 'idfile',
				value: gThis.m_oOptions.asDefaults,
				operator: 'IN'
			}];
		}
		else {
			oRequest.where = [{
				column: 'idfile',
				value: gThis.m_oOptions.sDefault,
				operator: 'IN'
			}];
		}
		oRequest.starting_from = 0;
		gThis.m_oOptions.fLoadFiles(oRequest, GCallback(gThis._DefaultsLoaded));
	};
	
	gThis._DefaultsLoaded = function(oData) {
		for (var i = 0; i < oData.rows.length; i++) {
			gThis._AddImage(oData.rows[i].idfile, oData.rows[i]);
		}
		gThis.m_bLoadedDefaults = true;
	};
	
	gThis._OnDataLoaded = function(dDg) {
		dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
	};

	gThis._InitFilesDatagrid = function() {

		var aoColumns = gThis._InitColumns();

    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 15,
				key: 'idfile',
				only_one_selected: !gThis.m_bRepeatable,
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (!gThis.m_bLoadedDefaults) {
						gThis._LoadDefaults(GCore.Duplicate(true, oRequest));
						gThis.m_bLoadedDefaults = true;
					}
					gThis.m_oOptions.fLoadFiles(oRequest, sResponseHandler);
				},
				loaded: gThis._OnDataLoaded,
				process: gThis._ProcessFile,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				selection_changed: gThis._OnChange
			},
			columns: aoColumns
    };

		gThis.m_gFilesDatagrid = new GF_Datagrid(gThis.m_jFilesDatagrid, oOptions);
	};

}, oDefaults);


/*
* LOCAL FILE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-localfile',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sThumbClass: 'thumb',
		sNameClass: 'name',
		sSelectedTableClass: 'selected',
		sAddFilesClass: 'add-pictures',
		sQueueClass: 'upload-queue',
		sProgressClass: 'progress',
		sProgressBarClass: 'progress-bar',
		sProgressBarIndicatorClass: 'indicator',
		sUploadErrorClass: 'upload-error',
		sUploadSuccessClass: 'upload-success'
	},
	oImages: {
		sChooseIcon: '_images_panel/icons/filetypes/directory.png',
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sUploadButton: '_images_panel/buttons/add-pictures.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	sUploadUrl: '',
	sSessionId: '',
	sSessionName: '',
	asFileTypes: [],
	sFileTypesDescription: '',
	fDeleteFile: GCore.NULL,
	fLoadFiles: GCore.NULL,
	sSwfUploadUrl: '_data_panel/swfupload.swf',
	iWidth: 131,
	iHeight: 34,
	iMaxFileSize: 100 * 1024	// kB
};

var GFormLocalFile = GCore.ExtendClass(GFormFile, function() {

	var gThis = this;

	gThis.m_bShown = false;
	gThis.m_jFilesDatagrid;
	gThis.m_gDataProvider;
	gThis.m_gFilesDatagrid;
	gThis.m_jSelectedFiles;
	gThis.m_jSwfUpload;
	gThis.m_jQueue;
	gThis.m_iUploadsInProgress = 0;
	gThis.m_iLockId = -1;
	gThis.m_bLoadedDefaults = false;
	gThis.m_jChooseButton;
	gThis.m_jSelectedFileName;
	
	gThis.m_sCWD;

	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append(gThis._AddField());
		if ((gThis.m_oOptions.sSelector != undefined) && (gThis.m_oOptions.sSelector.length)) {
			gThis.m_jNode.append('<input type="hidden" name="' + gThis.GetName() + '[selector]" value="' + gThis.m_oOptions.sSelector + '"/>');
		}
	};
	
	gThis._AddField = function() {
		var jRepetition = $('<span class="repetition"/>');
		gThis.m_jSelectedFileName = $('<span class="filename"/>');
		jRepetition.append(gThis.m_jSelectedFileName);
		gThis.m_jSwfUpload = $('<div class="' + gThis._GetClass('AddFiles') + '"/>').append('<span id="' + gThis.GetId() + '__upload"/>');
		jRepetition.append(gThis.m_jSwfUpload);
		gThis.m_jChooseButton = $('<a href="#" class="button expand"><span><img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select + '</span></a>');
		jRepetition.append($('<span class="browse-pictures"/>').append(gThis.m_jChooseButton));
		gThis.m_jQueue = $('<ul class="' + gThis._GetClass('Queue') + '"/>');
		jRepetition.append(gThis.m_jQueue);
		gThis.m_jFilesDatagrid = $('<div/>');
		jRepetition.append(gThis.m_jFilesDatagrid);
		gThis.m_jSelectedFiles = $('<div class="' + gThis._GetClass('SelectedTable') + '"/>');
		jRepetition.append(gThis.m_jSelectedFiles);
		gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '[file]"/>');
		jRepetition.append(gThis.m_jField);
		return jRepetition;
	};
	
	gThis._OnChoose = GEventHandler(function(eEvent) {
		if (!gThis.m_gFilesDatagrid) {
			gThis._InitFilesDatagrid();
			gThis.m_jFilesDatagrid.slideDown(250);
		}else{
			gThis.m_jFilesDatagrid.slideDown(250);
		}
		
		gThis.m_jChooseButton.toggleClass('expand');
		
		if(gThis.m_jChooseButton.hasClass('expand')){
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_select);
		}else{
			gThis.m_jChooseButton.find('span').html('<img src="' + gThis._GetImage('ChooseIcon') + '" alt=""/>' + GForm.Language.localfile_unselect);
		}
		return false;
	});

	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return {};
		}
		return {
			file: gThis.m_jField.val()
		};
	};

	gThis.SetValue = function(mValue, sRepetition) {
		if (mValue == undefined) {
			return;
		}
		if (gThis.m_jField == undefined) {
			return;
		}
		if (mValue['file'] == undefined) {
			gThis.m_jField.val('');
			gThis.m_jSelectedFileName.html('<span class="none">' + GForm.Language.localfile_none_selected + '</span>');
		}
		else {
			gThis.m_jField.val(mValue['file']).change();
			gThis.m_jSelectedFileName.text(mValue['file']);
			if (gThis.m_gFilesDatagrid) {
				gThis.m_gFilesDatagrid.m_asSelected = [gThis.m_oOptions.sFilePath + mValue['file']];
			}
		}
	};
	
	gThis.Populate = function(mValue) {
		if (gThis.m_gFilesDatagrid) {
			gThis._UpdateDatagridSelection(mValue['file']);
		}
		gThis.SetValue(mValue);
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		gThis.m_gFilesDatagrid.m_asSelected = [];
		for (var i = 0; i < mValue.length; i++) {
			gThis.m_gFilesDatagrid.m_asSelected[i] = mValue[i];
		}
		if (gThis.m_bShown) {
			gThis.m_gFilesDatagrid.LoadData();
		}
	};

	gThis._OnClickRow = function(gDg, sId) {
		var oFile = gThis.m_gFilesDatagrid.GetRow(sId);
		if (oFile.dir) {
			if (oFile.name == '..') {
				gThis.m_sCWD = gThis.m_sCWD.substr(0, gThis.m_sCWD.lastIndexOf('/', gThis.m_sCWD.length - 2));
			}
			else {
				gThis.m_sCWD += oFile.name + '/';
			}
			gThis.m_jSwfUpload.swfupload('addPostParam', 'path', gThis.m_sCWD);
			gThis._RefreshFiles();
			return false;
		}
		return true;
	};
	
	gThis._OnSelect = function(gDg, sId) {
		var oFile = gDg.GetRow(sId);
		if (!oFile.dir) {
			gThis.SetValue({
				file: oFile.path.substr(gThis.m_oOptions.sFilePath.length)
			});
		}
	};

	gThis._OnDeselect = function(gDg, sId) {
		gThis.SetValue('');
	};
	
	gThis._Initialize = function() {
		var oValue = gThis.GetValue();
		var sPath = gThis.m_oOptions.sFilePath + oValue.file;
		sPath = sPath.substr(0, sPath.lastIndexOf('/') + 1);
		gThis.m_sCWD = sPath;
	};
	
	gThis._InitializeEvents = function() {
		gThis.m_jChooseButton.click(gThis._OnChoose);
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitUploader();
			gThis.m_bShown = true;
		}
	};

	gThis._ProcessFile = function(oRow) {
		if (oRow.dir) {
			if (oRow.name == '..') {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons['cdup'] + '" alt=""/>';
			}
			else {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons['directory'] + '" alt=""/>';
			}
		}
		else {
			var sExtension = oRow.name.substr(oRow.name.lastIndexOf('.') + 1);
			if (gThis.m_oOptions.oTypeIcons[sExtension] == undefined) {
				sExtension = 'unknown';
			}
			if ((sExtension == 'png') || (sExtension == 'jpg') || (sExtension == 'gif')) {
				oRow.thumbpreview = '<a href="' + GCore.DESIGN_PATH.substr(0, GCore.DESIGN_PATH.lastIndexOf('/', GCore.DESIGN_PATH.length - 2)) + '/' + oRow.path + '" class="show-thumb"><img src="' + gThis.m_oOptions.oTypeIcons[sExtension] + '" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
			}
			else {
				oRow.thumbpreview = '<img src="' + gThis.m_oOptions.oTypeIcons[sExtension] + '" alt=""/>';
			}
		}
		return oRow;
	};

	gThis._InitUploader = function() {
		var sFileTypes = '';
		for (var i = 0; i < gThis.m_oOptions.asFileTypes.length; i++) {
			sFileTypes += '; *.' + gThis.m_oOptions.asFileTypes[i];
		}
		sFileTypes = sFileTypes.substr(2);
		var oPostParams = {};
		oPostParams[gThis.m_oOptions.sSessionName] = gThis.m_oOptions.sSessionId;
		gThis.m_jSwfUpload.swfupload({
			upload_url: gThis.m_oOptions.sUploadUrl,
			file_size_limit: gThis.m_oOptions.iMaxFileSize,
			file_types: sFileTypes,
			file_types_description: gThis.m_oOptions.sFileTypesDescription,
			file_upload_limit: 0,
			file_queue_limit: 0,
			button_image_url: gThis._GetImage('UploadButton'),
			button_placeholder_id: gThis.GetId() + '__upload',
			button_width: gThis.m_oOptions.iWidth,
			button_height: gThis.m_oOptions.iHeight,
			button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
			flash_url: GCore.DESIGN_PATH + gThis.m_oOptions.sSwfUploadUrl,
			post_params: oPostParams
		});
		gThis.m_jSwfUpload.bind("fileQueued", gThis.OnFileQueued);
		gThis.m_jSwfUpload.bind("uploadComplete", gThis.OnUploadComplete);
		gThis.m_jSwfUpload.bind("uploadSuccess", gThis.OnUploadSuccess);
		gThis.m_jSwfUpload.bind("uploadProgress", gThis.OnUploadProgress);
		gThis.m_jSwfUpload.bind("uploadError", gThis.OnUploadError);
	};

	gThis.OnFileQueued = function(eEvent, oFile) {
		if (gThis.m_iUploadsInProgress++ == 0) {
			gThis.m_iLockId = gThis.m_gForm.Lock(GForm.Language.file_selector_form_blocked, GForm.Language.file_selector_form_blocked_description);
		}
		gThis.m_jSwfUpload.swfupload("startUpload");
		var jLi = $('<li class="upload__' + oFile.index + '"/>');
		jLi.append('<h4>' + oFile.name + '</h4>');
		jLi.append('<p class="' + gThis._GetClass('Progress') + '"/>');
		jLi.append('<div class="' + gThis._GetClass('ProgressBar') + '"><div class="' + gThis._GetClass('ProgressBarIndicator') + '"></div>');
		gThis.m_jQueue.append(jLi);
	};

	gThis.OnDelete = function() {
		gThis.m_jSwfUpload.swfupload('cancelUpload', sFid);
	};

	gThis.OnUploadProgress = function(eEvent, oFile, iCompleted, iTotal) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		var iPercentage = Math.round(iCompleted / iTotal * 100);
		jLi.find('.' + gThis._GetClass('Progress')).text(iPercentage + '%: ' + Math.ceil(iCompleted / 1024) + 'kB / ' + Math.ceil(iTotal / 1024) + 'kB');
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', (iCompleted / iTotal * 100) + '%');
	};

	gThis.OnUploadError = function(eEvent, oFile, iErrorCode, sMessage) {
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadError'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_error);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		GAlert(GForm.Language.file_selector_upload_error, sMessage);
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadSuccess = function(eEvent, oFile, sServerData, sResponse) {
		if (sServerData.substr(0, 11) != 'response = ') {
			gThis.OnUploadError(eEvent, oFile, 0, sServerData);
			return;
		}
		var jLi = gThis.m_jQueue.find('.upload__' + oFile.index);
		jLi.addClass(gThis._GetClass('UploadSuccess'));
		jLi.find('.' + gThis._GetClass('Progress')).text(GForm.Language.file_selector_upload_success);
		jLi.find('.' + gThis._GetClass('ProgressBarIndicator')).css('width', '100%');
		eval("var oResponse = " + sServerData.substr(11) + ";");
		if (oResponse.sFilename == undefined) {
			gThis.OnUploadError(eEvent, oFile, 0, GForm.Language.localfile_processing_error);
			return;
		}
		gThis.SetValue({
			file: (gThis.m_sCWD + oResponse.sFilename).substr(gThis.m_oOptions.sFilePath.length)
		});
		gThis._RefreshFiles();
		if (gThis.m_gFilesDatagrid) {
			gThis.m_gFilesDatagrid.LoadData();
		}
		jLi.delay(2000).fadeOut(250, function() {
			$(this).remove();
		});
	};

	gThis.OnUploadComplete = function(eEvent, oFile) {
		if (--gThis.m_iUploadsInProgress <= 0) {
			gThis.m_iUploadsInProgress = 0;
			gThis.m_gForm.Unlock(gThis.m_iLockId);
		}
	};

	gThis._InitColumns = function() {

	  var column_path = new GF_Datagrid_Column({
			id: 'path',
			caption: GForm.Language.localfile_fullpath,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_LEFT
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});

		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.localfile_filename,
			appearance: {
				width: 150,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});

		var column_size = new GF_Datagrid_Column({
			id: 'size',
			appearance: {
				width: 65,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			caption: GForm.Language.localfile_filesize
		});

		var column_mtime = new GF_Datagrid_Column({
			id: 'mtime',
			appearance: {
				width: 120,
				visible: false
			},
			caption: GForm.Language.localfile_filemtime
		});

		var column_owner = new GF_Datagrid_Column({
			id: 'owner',
			appearance: {
				width: 70,
				visible: false
			},
			caption: GForm.Language.localfile_fileowner
		});

		return [
			column_path,
			column_thumb,
			column_name,
			column_size,
			column_mtime,
			column_owner
		];

	};
	
	gThis._RefreshFiles = function() {
		gThis.m_oOptions.fLoadFiles({
			path: gThis.m_sCWD
		}, GCallback(gThis._OnFilesLoaded));
	};
	
	gThis._OnFilesLoaded = GEventHandler(function(eEvent) {
		if ((eEvent == undefined) || (eEvent.files == undefined) || (eEvent.cwd == undefined)) {
			return;
		}
		gThis.m_sCWD = eEvent.cwd;
		if (gThis.m_gDataProvider) {
			gThis.m_gDataProvider.ChangeData(eEvent.files);
			gThis.m_gFilesDatagrid.LoadData();
		}
	});

	gThis._Delete = function(iDg, sId) {
		var iAlertId = GWarning(GForm.Language.localfile_delete_warning, GForm.Language.localfile_delete_warning_description, {
			bAutoExpand: true,
			aoPossibilities: [
				{mLink: function() {
					GCore.StartWaiting();
					GAlert.Destroy(iAlertId);
					gThis.m_oOptions.fDeleteFile({
						file: sId
					}, GCallback(function(eEvent) {
						GCore.StopWaiting();
						var oValue = gThis.GetValue();
						if (sId == gThis.m_oOptions.sFilePath + oValue.file) {
							gThis.m_gFilesDatagrid.ClearSelection();
						}
						gThis._RefreshFiles();
					}));
				}, sCaption: GForm.Language.localfile_ok},
				{mLink: GAlert.DestroyThis, sCaption: GForm.Language.localfile_cancel}
			]
		});
	};
	
	gThis._OnDataLoaded = function(dDg) {
		dDg.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
	};
	
	gThis._InitFilesDatagrid = function() {

		var aoColumns = gThis._InitColumns();
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'path'
		}, []);
		
		var gActionDelete = new GF_Action({
			img: gThis._GetImage('DeleteIcon'),
			caption: GForm.Language.localfile_delete,
			action: gThis._Delete,
			condition: function(oRow) {
				return !oRow.dir;
			}
		});

    var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 30,
				key: 'path',
				only_one_selected: true,
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					return gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
				},
				loaded: gThis._OnDataLoaded,
				process: gThis._ProcessFile,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				click_row: gThis._OnClickRow
			},
			row_actions: [
				gActionDelete
			],
			columns: aoColumns
    };

		gThis.m_gFilesDatagrid = new GF_Datagrid(gThis.m_jFilesDatagrid, oOptions);
		
		var oValue = gThis.GetValue();
		var sFile = oValue.file;
		if (sFile != '') {
			gThis.m_gFilesDatagrid.m_asSelected = [gThis.m_oOptions.sFilePath + sFile];
		}
		
		gThis._RefreshFiles();
	};

}, oDefaults);


/*
* PRICE MODIFIER
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-price-modifier',
		sFieldPriceClass: 'field-price',
		sFieldSelectClass: 'field-select',
		sFieldTextClass: 'field-text',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sNetPriceClass: 'net-price',
		sGrossPriceClass: 'gross-price'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	sFieldType: 'text',
	sDefault: '',
	aoRules: [],
	sComment: '',
	aoVatValues: [],
	oSuffixes: {},
	sVatField: '',
	sBasePriceField: ''
};

var GFormPriceModifier = GCore.ExtendClass(GFormPrice, function() {
	
	var gThis = this;
	gThis.m_jPrice;
	gThis.m_jModifier;
	gThis.m_jValue;
	gThis.m_jPriceFields;
	gThis.m_jModifierField;
	gThis.m_jValueField;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis._AddModifier();
		gThis._AddValue();
		gThis._AddPrice();
	};
	
	gThis._AddModifier = function() {
		gThis.m_jValue = $('<div/>').addClass(gThis._GetClass('FieldSelect'));
		var jLabel = $('<label for="' + gThis.GetId() + '__modifier"/>');
		jLabel.text(GForm.Language.price_modifier_value);
		gThis.m_jValue.append(jLabel);
		var jField = $('<select name="' + gThis.GetName() + '[modifier]" id="' + gThis.GetId() + '__modifier"/>');
		for (var i in gThis.m_oOptions.oSuffixes) {
			jField.append('<option value="' + i + '">' + gThis.m_oOptions.oSuffixes[i] + '</option>');
		}
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		gThis.m_jValue.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jValue);
		gThis.m_jModifierField = jField;
	};
	
	gThis._AddValue = function() {
		gThis.m_jValue = $('<div/>').addClass(gThis._GetClass('FieldText'));
		var jLabel = $('<label for="' + gThis.GetId() + '__value"/>');
		jLabel.text(GForm.Language.price_modifier_value);
		gThis.m_jValue.append(jLabel);
		var jField = $('<input type="text" name="' + gThis.GetName() + '[value]" id="' + gThis.GetId() + '__value"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jField));
		gThis.m_jValue.append(jRepetitionNode);
		gThis.m_jNode.append(gThis.m_jValue);
		gThis.m_jValueField = jField;
	};
	
	gThis._AddPrice = function() {
		gThis.m_jPrice = $('<div/>').addClass(gThis._GetClass('FieldPrice'));
		var jLabel = $('<label for="' + gThis.GetId() + '"__price/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jPrice.append(jLabel);
		gThis.m_jPrice.append(gThis._AddField());
		gThis.m_jNode.append(gThis.m_jPrice);
	};
	
	gThis.GetValue = function(sRepetition) {
		return {
			modifier: gThis.m_jModifierField.val(),
			value: gThis.m_jValueField.val()
		};
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		gThis.m_jModifierField.val(mValue['modifier']).change();
		gThis.m_jValueField.val(mValue['value']).change();
	};
	
	gThis._AddField = function(sId) {
		var jFieldNet = $('<input type="text" name="' + gThis.GetName() + '[price]" id="' + gThis.GetId() + '__price"/>');
		var jFieldGross = $('<input type="text" id="' + gThis.GetId() + '__gross"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		var jNetNode = $('<span class="' + gThis._GetClass('NetPrice') + '"/>');
		var jGrossNode = $('<span class="' + gThis._GetClass('GrossPrice') + '"/>');
		if (gThis.m_oOptions.asPrefixes[0] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[0]);
			jNetNode.append(jPrefix);
		}
		jNetNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldNet));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jNetNode.append(jSuffix);
		}
		if (gThis.m_oOptions.asPrefixes[1] != undefined) {
			var jPrefix = $('<span class="' + gThis._GetClass('Prefix') + '"/>');
			jPrefix.html(gThis.m_oOptions.asPrefixes[1]);
			jGrossNode.append(jPrefix);
		}
		jGrossNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(jFieldGross));
		if (gThis.m_oOptions.sSuffix != undefined) {
			var jSuffix = $('<span class="' + gThis._GetClass('Suffix') + '"/>');
			jSuffix.html(gThis.m_oOptions.sSuffix);
			jGrossNode.append(jSuffix);
		}
		jRepetitionNode.append(jNetNode).append(jGrossNode);
		gThis.m_jPriceFields = jRepetitionNode.find('input');
		return jRepetitionNode;
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis.m_jModifierField.GSelect();
		}
		gThis.m_bShown = true;
		if (!gThis.m_bResized) {
			gThis.m_bResized = true;
			gThis.m_jPriceFields.each(function() {
				var iWidth = Math.floor(parseInt($(this).css('width')) / 2) - 20;
				var jParent = $(this).closest('.' + gThis._GetClass('NetPrice') + ', .' + gThis._GetClass('GrossPrice'));
				if (jParent.find('.' + gThis._GetClass('Prefix')).length) {
					iWidth -= ($(this).offset().left - jParent.find('.' + gThis._GetClass('Prefix')).offset().left) - 1;
				}
				if (jParent.find('.' + gThis._GetClass('Suffix')).length) {
					iWidth -= jParent.find('.' + gThis._GetClass('Suffix')).width() + 4;
				}
				$(this).css('width', iWidth);
			});
		}
	};
	
	gThis._CalculateGrossPrice = function(sPrice) {
		var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = gVat.GetValue();
		var fVat = 0;
		for (var i in gThis.m_oOptions.aoVatValues) {
			if (gThis.m_oOptions.aoVatValues[i].id == iVatId) {
				fVat = gThis.m_oOptions.aoVatValues[i].value;
				break;
			}
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jPriceFields.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		gThis.m_jPriceFields.eq(1).val((fPrice * (1 + fVat / 100)).toFixed(2));
	};
	
	gThis._CalculateNetPrice = function(sPrice) {
		var gVat = gThis.m_gForm.GetField(gThis.m_oOptions.sVatField);
		var iVatId = gVat.GetValue();
		var fVat = 0;
		for (var i in gThis.m_oOptions.aoVatValues) {
			if (gThis.m_oOptions.aoVatValues[i].id == iVatId) {
				fVat = gThis.m_oOptions.aoVatValues[i].value;
				break;
			}
		}
		if (sPrice == undefined) {
			var sPrice = gThis.m_jPriceFields.eq(0).val();
		}
		var fPrice = parseFloat(sPrice.replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		gThis.m_jPriceFields.eq(0).val((fPrice / (1 + fVat / 100)).toFixed(2));
	};
	
	gThis._UpdatePrice = function(sModifier, fModifierValue) {
		if (sModifier == undefined) {
			sModifier = $('#' + gThis.GetId() + '__modifier option:selected').text();
		}
		if (fModifierValue == undefined) {
			fModifierValue = parseFloat($('#' + gThis.GetId() + '__value').val().replace(/,/, '.'));
		}
		else {
			fModifierValue = parseFloat(('' + fModifierValue).replace(/,/, '.'));
		}
		var fBasePrice = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sBasePriceField).GetValue().replace(/,/, '.'));
		fModifierValue = isNaN(fModifierValue) ? 0 : fModifierValue;
		fBasePrice = isNaN(fBasePrice) ? 0 : fBasePrice;
		var fPrice = 0;
		switch (sModifier) {
			case '%':
				fPrice = fBasePrice * (fModifierValue / 100);
				break;
			case '+':
				fPrice = fBasePrice + fModifierValue;
				break;
			case '-':
				fPrice = fBasePrice - fModifierValue;
				break;
			case '=':
				fPrice = fModifierValue;
				break;
		}
		$('#' + gThis.GetId() + '__price').val(fPrice.toFixed(2));
		var fVatvalue = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jNode.find('option:selected').text());
		fVatvalue = isNaN(fVatvalue) ? 0 : fVatvalue;
		var fGrossPrice = fPrice * (1 + fVatvalue / 100);
		$('#' + gThis.GetId() + '__gross').val(fGrossPrice.toFixed(2));
	};
	
	gThis._UpdateModificatorValue = function(fPrice) {
		var sModifier = $('#' + gThis.GetId() + '__modifier option:selected').text();
		if (fPrice == undefined) {
			fPrice = parseFloat($('#' + gThis.GetId() + '__price').val().replace(/,/, '.'));
		}
		else {
			fPrice = parseFloat(('' + fPrice).replace(/,/, '.'));
		}
		var fBasePrice = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sBasePriceField).GetValue().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		fBasePrice = isNaN(fBasePrice) ? 0 : fBasePrice;
		var fModifierValue = 0;
		if (fBasePrice > 0) {
			switch (sModifier) {
				case '%':
					fModifierValue = (fPrice / fBasePrice) * 100;
					break;
				case '+':
					fModifierValue = fPrice - fBasePrice;
					break;
				case '-':
					fModifierValue = fBasePrice - fPrice;
					break;
				case '=':
					fModifierValue = fPrice;
					break;
			}
		}
		$('#' + gThis.GetId() + '__value').val(fModifierValue.toFixed(2));
	};
	
	gThis._Initialize = function() {
		var fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				gThis._CalculateGrossPrice($(eEvent.currentTarget).val());
				gThis._UpdateModificatorValue($(eEvent.currentTarget).val());
			}, 5);
		});
		gThis.m_jPriceFields.eq(0).keypress(fHandler).blur(fHandler).change(gThis.ValidateField);
		fHandler = GEventHandler(function(eEvent) {
			setTimeout(function() {
				gThis._CalculateNetPrice($(eEvent.currentTarget).val());
				gThis._UpdateModificatorValue();
			}, 5);
		});
		gThis.m_jPriceFields.eq(1).keypress(fHandler).blur(fHandler).change(gThis.ValidateField);
		gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jField.change(GEventHandler(function(eEvent) {
			gThis._CalculateGrossPrice();
		}));
		gThis.m_gForm.GetField(gThis.m_oOptions.sBasePriceField).m_jField.change(GEventHandler(function(eEvent) {
			gThis.UpdatePrices();
		}));
		gThis.m_jPriceFields.add(gThis.m_jValueField).focus(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
		})).blur(GEventHandler(function(eEvent) {
			$(this).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
		}));
		gThis.m_jValueField.keypress(GEventHandler(function(eEvent) {
			setTimeout(function() {
				gThis.UpdatePrices(undefined, $(eEvent.currentTarget).val());
			}, 5);
		}));
		gThis.m_jModifierField.change(GEventHandler(function(eEvent) {
			gThis.UpdatePrices($(eEvent.currentTarget).find('option:selected').text());
		}));
		gThis.UpdatePrices();
	};
	
	gThis.UpdatePrices = function() {
		setTimeout(function() {
			gThis._UpdatePrice();
			gThis.m_jPriceFields.change();
		}, 5);
	};
	
	gThis.ValidateField = GEventHandler(function(eEvent) {
		var fPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		$(eEvent.currentTarget).val(fPrice.toFixed(2));
	});
	
	gThis.Reset = function() {
		gThis.m_jField.eq(0).val(gThis.m_oOptions.sDefault).change();
	};
	
}, oDefaults);


/*
* PRODUCT SELECT
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-product-select',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition'
	},
	oImages: {
		sDeselectIcon: '_images_panel/icons/datagrid/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	fLoadProducts: GCore.NULL,
	bAdvancedEditor: false,
	bAllowHierarchy: false,
};

var GFormProductSelect = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadProducts;
	gThis.m_fProcessProduct;
	gThis.m_jDatagrid;
	gThis.m_jSelectedDatagrid;
	gThis.m_gDatagrid;
	gThis.m_gSelectedDatagrid;
	gThis.m_gDataProvider;
	gThis.m_bFirstLoad = true;
	gThis.m_aoExclude = [];
	gThis.m_sDependentSelector;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				if (gThis.m_oOptions.bAdvancedEditor) {
					return oValue = {
						id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]').val(),
						quantity: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[quantity]"]').val(),
						variant: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[variant]"]').val()
					};
				}
				else {
					return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
				}
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			if (gThis.m_oOptions.bAdvancedEditor) {
				for (var i = 0; i < jValues.length / 3; i++) {
					aValues.push({
						id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]:eq(' + i + ')').val(),
						quantity: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[quantity]"]:eq(' + i + ')').val(),
						variant: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[variant]"]:eq(' + i + ')').val()
					});
				}
			}
			else {
				for (var i = 0; i < jValues.length; i++) {
					aValues.push(jValues.eq(i).val());
				}
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};
	
	gThis.PopulateErrors = function(mData) {
		if ((mData == undefined) || (mData == '')) {
			return;
		}
		gThis.SetError(mData);
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				if (i == 'toJSON') {
					continue;
				}
				if (gThis.m_oOptions.bAdvancedEditor) {
					gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[id]" value="' + mValue[i]['id'] + '"/>');
					gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[quantity]" value="' + mValue[i]['quantity'] + '"/>');
					gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[variant]" value="' + mValue[i]['variant'] + '"/>');
				}
				else {
					
					gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '" value="' + mValue[i] + '"/>');
				}
			}
			
		}
		else {
			gThis.m_bSkipValidation = true;
			gThis.m_jField.val(mValue).change();
			gThis.m_bSkipValidation = false;
		}
	};
	
	gThis._OnSelect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			var oSelectedRow = gDg.GetRow(sId);
			oSelectedRow.quantity = 1;
			oSelectedRow.variant = '';
			gThis.m_gDataProvider.AddRow(oSelectedRow);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue(sId);
		}
	};
	
	gThis._OnDeselect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			gThis.m_gDataProvider.DeleteRow(sId);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue('');
		}
	};
	
	gThis._OnChange = function(eEvent) {
		if (!gThis.m_oOptions.bAdvancedEditor) {
			if (gThis.m_bRepeatable) {
				gThis.m_jField.empty();
			}
			var asIds = [];
			for (var i in eEvent.rows) {
				asIds.push(eEvent.rows[i].idproduct);
			}
			gThis.SetValue(asIds);
		}
		else {
			if (gThis.m_bRepeatable) {
				gThis.m_jField.empty();
			}
			var aoData = [];
			for (var i in eEvent.rows) {
				if (i == 'toJSON') {
					continue;
				}
				aoData.push({
					id: eEvent.rows[i].idproduct,
					quantity: eEvent.rows[i].quantity,
					variant: eEvent.rows[i].variant
				});
			}
			gThis.SetValue(aoData);
		}
	};
	
	gThis._PrepareNode = function() {
		gThis.m_oOptions.oParsedFilterData = {};
		for (var i in gThis.m_oOptions.oFilterData) {
			$.globalEval('var oParsed = [' + gThis.m_oOptions.oFilterData[i] + '];');
			gThis.m_oOptions.oParsedFilterData[i] = $.extend({}, oParsed);
		}
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (gThis.m_bRepeatable) {
			gThis.m_jField = $('<div/>');
			gThis.m_jDatagrid = $('<div/>');
			jLabel = $('<label/>');
			jLabel.text(GForm.Language.product_select_selected + ':');
			gThis.m_jSelectedDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
			gThis.m_jNode.append(jLabel);
			gThis.m_jNode.append(gThis.m_jSelectedDatagrid);
		}
		else {
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '"/>');
			gThis.m_jDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
		}
		gThis.m_jNode.append(gThis.m_jField);
		if (gThis.m_oOptions.sExcludeFrom != undefined) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.sExcludeFrom);
			gField.m_sDependentSelector = gThis.m_oOptions.sName;
		}
	};
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			if (gThis.m_gDatagrid) {
				gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
			}
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		if (gThis.m_gDatagrid) {
			gThis._UpdateDatagridSelection(mValue);
		}
		gThis.SetValue(mValue);
		if (gThis.m_gDatagrid && gThis.m_bRepeatable) {
			gThis.m_gSelectedDatagrid.LoadData();
		}
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		if (gThis.m_gDatagrid) {
			gThis.m_gDatagrid.m_asSelected = [];
			for (var i = 0; i < mValue.length; i++) {
				if (gThis.m_oOptions.bAdvancedEditor) {
					gThis.m_gDatagrid.m_asSelected[i] = mValue[i].id;
				}
				else {
					gThis.m_gDatagrid.m_asSelected[i] = mValue[i];
				}
			}
		}
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitDatagrid();
			if (gThis.m_bRepeatable) {
				gThis._InitSelectedDatagrid();
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
		}
		else {
			gThis.m_gDatagrid.LoadData();
		}
	};
	
	gThis._ProcessProduct = function(oProduct) {
		if (oProduct.thumb != '') {
			oProduct.thumbpreview = '<a href="' + oProduct.thumb + '" ><img src="' + oProduct.thumb + '" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
		}
		return oProduct;
	};
	
	gThis._ProcessSelectedProduct = function(oProduct) {
		oProduct = gThis.m_fProcessProduct(oProduct);
		return oProduct;
	};
	
	gThis._InitColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT,
				width: 240
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_price_gross = new GF_Datagrid_Column({
			id: 'sellprice_gross',
			caption: GForm.Language.product_select_price_gross,
			appearance: {
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_buyprice = new GF_Datagrid_Column({
			id: 'buyprice',
			caption: GForm.Language.product_select_buyprice,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_buyprice_gross = new GF_Datagrid_Column({
			id: 'buyprice_gross',
			caption: GForm.Language.product_select_buyprice_gross,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_barcode = new GF_Datagrid_Column({
			id: 'barcode',
			caption: GForm.Language.product_select_barcode,
			appearance: {
				width: 150,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_producer = new GF_Datagrid_Column({
			id: 'producer',
			caption: GForm.Language.product_select_producer,
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: gThis.m_oOptions.oParsedFilterData['producer'],
			}
		});
		
		var column_vat = new GF_Datagrid_Column({
			id: 'vat',
			caption: GForm.Language.product_select_vat,
			appearance: {
				width: 60,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: gThis.m_oOptions.oParsedFilterData['vat'],
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: GForm.Language.product_select_categories,
			appearance: {
				width: 200,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: gThis.m_oOptions.oParsedFilterData['categoryid'],
				load_children: gThis.m_oOptions.fLoadCategoryChildren
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});
		
		return [
			column_id,
			column_thumb,
			column_name,
			column_category,
			column_producer,
			column_price,
			column_price_gross,
			column_buyprice,
			column_buyprice_gross,
			column_barcode,
			column_vat,
		];
		
	};
	
	gThis._InitAdvancedColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT,
				width: 240
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_price_gross = new GF_Datagrid_Column({
			id: 'sellprice_gross',
			caption: GForm.Language.product_select_price_gross,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_barcode = new GF_Datagrid_Column({
			id: 'barcode',
			caption: GForm.Language.product_select_barcode,
			appearance: {
				width: 150,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_producer = new GF_Datagrid_Column({
			id: 'producer',
			caption: GForm.Language.product_select_producer,
			appearance: {
				width: 150,
				visible: false
			}
		});
		
		var column_vat = new GF_Datagrid_Column({
			id: 'vat',
			caption: GForm.Language.product_select_vat,
			appearance: {
				width: 60,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: GForm.Language.product_select_categories,
			appearance: {
				width: 200,
				align: GF_Datagrid.ALIGN_LEFT,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: gThis.m_oOptions.oParsedFilterData['categoryid'],
				load_children: gThis.m_oOptions.fLoadCategoryChildren
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});
		
		var column_quantity = new GF_Datagrid_Column({
			id: 'quantity',
			caption: GForm.Language.product_select_quantity,
			editable: true,
			appearance: {
				width: 70
			}
		});
		
		var column_variant = new GF_Datagrid_Column({
			id: 'variant',
			caption: GForm.Language.product_select_variant,
			selectable: true,
			appearance: {
				width: 150
			}
		});
		
		return [
		    column_id,
			column_thumb,
			column_name,
			column_category,
			column_price_gross,
			column_quantity,
			column_variant,
			column_producer,
			column_vat,
			column_barcode,
		];
		
	};
	
	gThis._UpdateExcludes = function() {
		gThis.m_aoExclude = [];
		if (gThis.m_oOptions.sExcludeFrom != undefined) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.sExcludeFrom);
			gThis.m_aoExclude = [gField.GetValue()];
			if (!gThis.m_gDataProvider) {
				return;
			}
			for (var i in gThis.m_gDataProvider.m_aoData) {
				if (gThis.m_gDataProvider.m_aoData[i].idproduct == gField.GetValue()) {
					gThis.m_gDatagrid.DeselectRow(gThis.m_gDataProvider.m_aoData[i].idproduct);
					return;
				}
			}
		}
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_fProcessProduct = gThis._ProcessProduct;
		gThis.m_fLoadProducts = gThis.m_oOptions.fLoadProducts;
		
		var aoColumns = gThis._InitColumns();
		var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 10,
				key: 'idproduct',
				only_one_selected: !gThis.m_bRepeatable,
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oData, sProcessFunction) {
					gThis._UpdateExcludes();
					oData.dynamic_exclude = gThis.m_aoExclude;
					return gThis.m_fLoadProducts(oData, sProcessFunction);
				},
				process: gThis.m_fProcessProduct,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				selection_changed: function() {
					if (gThis.m_sDependentSelector != undefined) {
						var gField = gThis.m_gForm.GetField(gThis.m_sDependentSelector);
						gField._UpdateExcludes();
					}
				}
			},
			columns: aoColumns
    };
    
    gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		
	};
	
	gThis._Deselect = function(iDg, mId) {
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			gThis.m_gDatagrid.DeselectRow(mId[i]);
		}
		gThis.m_gSelectedDatagrid.ClearSelection();
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._InitSelectedDatagrid = function() {
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'idproduct',
			event_handlers: {
				change: gThis._OnChange
			}
		}, []);
		
		if (gThis.m_oOptions.bAdvancedEditor) {
			var aoColumns = gThis._InitAdvancedColumns();
		}
		else {
			var aoColumns = gThis._InitColumns();
		}
		
		var gActionDeselect = new GF_Action({
			img: gThis._GetImage('DeselectIcon'),
			caption: GForm.Language.product_select_deselect,
			action: gThis._Deselect
		});
		
		var oOptions = {
			id: gThis.GetId() + '_selected',
			appearance: {
				filter: false
			},
			mechanics: {
				rows_per_page: 1000,
				key: 'idproduct',
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (gThis.m_bFirstLoad) {
						gThis.m_bFirstLoad = false;
						gThis._LoadSelected(oRequest, sResponseHandler);
					}
					else {
						gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
					}
				},
				update_row: function(sId, oRow) {
					gThis.m_gDataProvider.UpdateRow(sId, oRow);
				},
				process: gThis._ProcessSelectedProduct
			},
			columns: aoColumns,
			row_actions: [
				gActionDeselect
			],
			context_actions: [
				gActionDeselect
			],
			group_actions: [
				gActionDeselect
			]
    };
		
		gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);
		
	};
	
	gThis._LoadSelected = function(oRequest, sResponseHandler) {
		if (gThis.m_oOptions.bAdvancedEditor) {
			var asDefaults = [];
			for (var i in gThis.m_oOptions.asDefaults) {
				asDefaults.push(gThis.m_oOptions.asDefaults[i].id);
			}
			oRequest.where = [{
				column: 'idproduct',
				value: asDefaults,
				operator: 'IN'
			}];
		}
		else {
			oRequest.where = [{
				column: 'idproduct',
				value: gThis.m_oOptions.asDefaults,
				operator: 'IN'
			}];
		}
		gThis.m_fLoadProducts(oRequest, GCallback(function(eEvent) {
			if (gThis.m_oOptions.bAdvancedEditor) {
				for (var i in eEvent.rows) {
					var sId = eEvent.rows[i].idproduct;
					for (var j in gThis.m_oOptions.asDefaults) {
						if (gThis.m_oOptions.asDefaults[j].id == sId) {
							eEvent.rows[i].quantity = gThis.m_oOptions.asDefaults[j].quantity;
							eEvent.rows[i].variant = gThis.m_oOptions.asDefaults[j].variant;
						}
					}
				}
			}
			gThis.m_gDataProvider.ChangeData(eEvent.rows);
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
}, oDefaults);

/*
* PRODUCT SELECT RELATED
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-product-select',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition'
	},
	oImages: {
		sDeselectIcon: '_images_panel/icons/datagrid/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	fLoadProducts: GCore.NULL,
};

var GFormProductSelectRelated = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadProducts;
	gThis.m_fProcessProduct;
	gThis.m_jDatagrid;
	gThis.m_jSelectedDatagrid;
	gThis.m_gDatagrid;
	gThis.m_gSelectedDatagrid;
	gThis.m_gDataProvider;
	gThis.m_bFirstLoad = true;
	gThis.m_aoExclude = [];
	gThis.m_sDependentSelector;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		if (gThis.m_bRepeatable) {
			if (sRepetition != undefined) {
				return oValue = {
					id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]').val(),
					hierarchy: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[hierarchy]"]').val(),
				};
			}
			var aValues = [];
			var jValues = gThis.m_jField.find('input');
			for (var i = 0; i < jValues.length / 3; i++) {
				aValues.push({
					id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]:eq(' + i + ')').val(),
					hierarchy: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[hierarchy]"]:eq(' + i + ')').val(),
				});
			}
			return aValues;
		}
		else {
			return gThis.m_jField.val();
		}
	};
	
	gThis.PopulateErrors = function(mData) {
		if ((mData == undefined) || (mData == '')) {
			return;
		}
		gThis.SetError(mData);
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		if (gThis.m_bRepeatable) {
			for (var i in mValue) {
				if (i == 'toJSON') {
					continue;
				}
				
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[id]" value="' + mValue[i]['id'] + '"/>');
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[hierarchy]" value="' + mValue[i]['hierarchy'] + '"/>');
			}
		}
		else {
			gThis.m_bSkipValidation = true;
			gThis.m_jField.val(mValue).change();
			gThis.m_bSkipValidation = false;
		}
	};
	
	gThis._OnSelect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			var oSelectedRow = gDg.GetRow(sId);
			oSelectedRow.hierarchy = 0;
			gThis.m_gDataProvider.AddRow(oSelectedRow);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue(sId);
		}
	};
	
	gThis._OnDeselect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			gThis.m_gDataProvider.DeleteRow(sId);
			gThis.m_gSelectedDatagrid.LoadData();
		}
		else {
			gThis.SetValue('');
		}
	};
	
	gThis._OnChange = function(eEvent) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		var aoData = [];
		for (var i in eEvent.rows) {
			if (i == 'toJSON') {
				continue;
			}
			
			aoData.push({
				id: eEvent.rows[i].idproduct,
				hierarchy: eEvent.rows[i].hierarchy,
			});
		}
		
		gThis.SetValue(aoData);
	};
	
	gThis._PrepareNode = function() {
		gThis.m_oOptions.oParsedFilterData = {};
		for (var i in gThis.m_oOptions.oFilterData) {
			$.globalEval('var oParsed = [' + gThis.m_oOptions.oFilterData[i] + '];');
			gThis.m_oOptions.oParsedFilterData[i] = $.extend({}, oParsed);
		}
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		if (gThis.m_bRepeatable) {
			gThis.m_jField = $('<div/>');
			gThis.m_jDatagrid = $('<div/>');
			jLabel = $('<label/>');
			jLabel.text(GForm.Language.product_select_selected + ':');
			gThis.m_jSelectedDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
			gThis.m_jNode.append(jLabel);
			gThis.m_jNode.append(gThis.m_jSelectedDatagrid);
		}
		else {
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '"/>');
			gThis.m_jDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagrid);
		}
		gThis.m_jNode.append(gThis.m_jField);
		if (gThis.m_oOptions.sExcludeFrom != undefined) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.sExcludeFrom);
			gField.m_sDependentSelector = gThis.m_oOptions.sName;
		}
	};
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
			if (gThis.m_gDatagrid) {
				gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
			}
		}
		else {
			gThis.m_oOptions.sDefault = mValue;
		}
		if (gThis.m_gDatagrid) {
			gThis._UpdateDatagridSelection(mValue);
		}
		gThis.SetValue(mValue);
		if (gThis.m_gDatagrid && gThis.m_bRepeatable) {
			gThis.m_gSelectedDatagrid.LoadData();
		}
	};
	
	gThis._UpdateDatagridSelection = function(mValue) {
		if (!(mValue instanceof Array)) {
			if ((mValue == undefined) || !mValue.length) {
				mValue = [];
			}
			else {
				mValue = [mValue];
			}
		}
		if (gThis.m_gDatagrid) {
			gThis.m_gDatagrid.m_asSelected = [];
			for (var i = 0; i < mValue.length; i++) {
				gThis.m_gDatagrid.m_asSelected[i] = mValue[i].id;
			}
		}
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitDatagrid();
			if (gThis.m_bRepeatable) {
				gThis._InitSelectedDatagrid();
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
		}
		else {
			gThis.m_gDatagrid.LoadData();
		}
	};
	
	gThis._ProcessProduct = function(oProduct) {
		if (oProduct.thumb != '') {
			oProduct.thumbpreview = '<a href="' + oProduct.thumb + '" ><img src="' + oProduct.thumb + '" style="vertical-align: middle;" alt="' + GForm.Language.file_selector_show_thumb + '"/></a>';
		}
		return oProduct;
	};
	
	gThis._ProcessSelectedProduct = function(oProduct) {
		oProduct = gThis.m_fProcessProduct(oProduct);
		return oProduct;
	};
	
	gThis._InitColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT,
				width: 240
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_price_gross = new GF_Datagrid_Column({
			id: 'sellprice_gross',
			caption: GForm.Language.product_select_price_gross,
			appearance: {
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_buyprice = new GF_Datagrid_Column({
			id: 'buyprice',
			caption: GForm.Language.product_select_buyprice,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_buyprice_gross = new GF_Datagrid_Column({
			id: 'buyprice_gross',
			caption: GForm.Language.product_select_buyprice_gross,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_barcode = new GF_Datagrid_Column({
			id: 'barcode',
			caption: GForm.Language.product_select_barcode,
			appearance: {
				width: 150,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_producer = new GF_Datagrid_Column({
			id: 'producer',
			caption: GForm.Language.product_select_producer,
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: gThis.m_oOptions.oParsedFilterData['producer'],
			}
		});
		
		var column_vat = new GF_Datagrid_Column({
			id: 'vat',
			caption: GForm.Language.product_select_vat,
			appearance: {
				width: 60,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: gThis.m_oOptions.oParsedFilterData['vat'],
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: GForm.Language.product_select_categories,
			appearance: {
				width: 200,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: gThis.m_oOptions.oParsedFilterData['categoryid'],
				load_children: gThis.m_oOptions.fLoadCategoryChildren
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});
		
		return [
			column_id,
			column_thumb,
			column_name,
			column_category,
			column_producer,
			column_price,
			column_price_gross,
			column_buyprice,
			column_buyprice_gross,
			column_barcode,
			column_vat,
		];
		
	};
	
	gThis._InitAdvancedColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT,
				width: 240
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			appearance: {
				width: 70,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_price_gross = new GF_Datagrid_Column({
			id: 'sellprice_gross',
			caption: GForm.Language.product_select_price_gross,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_RIGHT
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_barcode = new GF_Datagrid_Column({
			id: 'barcode',
			caption: GForm.Language.product_select_barcode,
			appearance: {
				width: 150,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_producer = new GF_Datagrid_Column({
			id: 'producer',
			caption: GForm.Language.product_select_producer,
			appearance: {
				width: 150,
				visible: false
			}
		});
		
		var column_vat = new GF_Datagrid_Column({
			id: 'vat',
			caption: GForm.Language.product_select_vat,
			appearance: {
				width: 60,
				visible: false,
				align: GF_Datagrid.ALIGN_RIGHT
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: GForm.Language.product_select_categories,
			appearance: {
				width: 200,
				align: GF_Datagrid.ALIGN_LEFT,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: gThis.m_oOptions.oParsedFilterData['categoryid'],
				load_children: gThis.m_oOptions.fLoadCategoryChildren
			}
		});
		
		var column_thumb = new GF_Datagrid_Column({
			id: 'thumbpreview',
			caption: GForm.Language.file_selector_thumb,
			appearance: {
				width: 30,
				no_title: true
			}
		});
		
		var column_hierarchy = new GF_Datagrid_Column({
			id: 'hierarchy',
			caption: GForm.Language.product_select_hierarchy,
			editable: true,
			appearance: {
				width: 70
			}
		});
		
		
		
		return [
		    column_id,
			column_thumb,
			column_name,
			column_category,
			column_price_gross,
			column_hierarchy,
			column_producer,
			column_vat,
			column_barcode,
		];
		
	};
	
	gThis._UpdateExcludes = function() {
		gThis.m_aoExclude = [];
		if (gThis.m_oOptions.sExcludeFrom != undefined) {
			var gField = gThis.m_gForm.GetField(gThis.m_oOptions.sExcludeFrom);
			gThis.m_aoExclude = [gField.GetValue()];
			if (!gThis.m_gDataProvider) {
				return;
			}
			for (var i in gThis.m_gDataProvider.m_aoData) {
				if (gThis.m_gDataProvider.m_aoData[i].idproduct == gField.GetValue()) {
					gThis.m_gDatagrid.DeselectRow(gThis.m_gDataProvider.m_aoData[i].idproduct);
					return;
				}
			}
		}
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_fProcessProduct = gThis._ProcessProduct;
		gThis.m_fLoadProducts = gThis.m_oOptions.fLoadProducts;
		
		var aoColumns = gThis._InitColumns();
		var oOptions = {
			id: gThis.GetId(),
			mechanics: {
				rows_per_page: 10,
				key: 'idproduct',
				only_one_selected: !gThis.m_bRepeatable,
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oData, sProcessFunction) {
					gThis._UpdateExcludes();
					oData.dynamic_exclude = gThis.m_aoExclude;
					return gThis.m_fLoadProducts(oData, sProcessFunction);
				},
				process: gThis.m_fProcessProduct,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect,
				selection_changed: function() {
					if (gThis.m_sDependentSelector != undefined) {
						var gField = gThis.m_gForm.GetField(gThis.m_sDependentSelector);
						gField._UpdateExcludes();
					}
				}
			},
			columns: aoColumns
    };
    
    gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		
	};
	
	gThis._Deselect = function(iDg, mId) {
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			gThis.m_gDatagrid.DeselectRow(mId[i]);
		}
		gThis.m_gSelectedDatagrid.ClearSelection();
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._InitSelectedDatagrid = function() {
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'idproduct',
			event_handlers: {
				change: gThis._OnChange
			}
		}, []);
		
		var aoColumns = gThis._InitAdvancedColumns();
		
		var gActionDeselect = new GF_Action({
			img: gThis._GetImage('DeselectIcon'),
			caption: GForm.Language.product_select_deselect,
			action: gThis._Deselect
		});
		
		var oOptions = {
			id: gThis.GetId() + '_selected',
			appearance: {
				filter: false
			},
			mechanics: {
				rows_per_page: 1000,
				key: 'idproduct',
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (gThis.m_bFirstLoad) {
						gThis.m_bFirstLoad = false;
						gThis._LoadSelected(oRequest, sResponseHandler);
					}
					else {
						gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
					}
				},
				update_row: function(sId, oRow) {
					gThis.m_gDataProvider.UpdateRow(sId, oRow);
				},
				process: gThis._ProcessSelectedProduct
			},
			columns: aoColumns,
			row_actions: [
				gActionDeselect
			],
			context_actions: [
				gActionDeselect
			],
			group_actions: [
				gActionDeselect
			]
    };
		
		gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);
		
	};
	
	gThis._LoadSelected = function(oRequest, sResponseHandler) {
		var asDefaults = [];
		for (var i in gThis.m_oOptions.asDefaults) {
			asDefaults.push(gThis.m_oOptions.asDefaults[i].id);
		}
		oRequest.where = [{
			column: 'idproduct',
			value: asDefaults,
			operator: 'IN'
		}];
		
		gThis.m_fLoadProducts(oRequest, GCallback(function(eEvent) {
			for (var i in eEvent.rows) {
				var sId = eEvent.rows[i].idproduct;
				for (var j in gThis.m_oOptions.asDefaults) {
					if (gThis.m_oOptions.asDefaults[j].id == sId) {
						eEvent.rows[i].hierarchy = gThis.m_oOptions.asDefaults[j].hierarchy;
					}
				}
			}
			gThis.m_gDataProvider.ChangeData(eEvent.rows);
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
}, oDefaults);

/*
* RICH TEXT EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-rich-text-editor',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sAddRepetitionClass: 'add-field-repetition',
		sRemoveRepetitionClass: 'remove-field-repetition',
		sLanguage: 'pl'
	},
	oImages: {
		sAddRepetition: '_images_panel/icons/buttons/add.png',
		sRemoveRepetition: '_images_panel/icons/buttons/delete.png'
	},
	iRows: 3,
	iCols: 60,
	sDefault: '',
	aoRules: [],
	sComment: '',
	bAdvanced: false,
	sLanguage: 'pl'
};

var GFormRichTextEditor = GCore.ExtendClass(GFormTextarea, function() {
	
	var gThis = this;
	
	gThis.OnShow = function() {
		if (gThis.m_bShown) {
			return;
		}
		var iDelay = 500;
		gThis.m_bShown = true;
		window.setTimeout(function() {
			CKEDITOR.replace(gThis.GetId(),{
				customConfig : 'config.js'
			});
		}, iDelay);
		
	};
	
}, oDefaults);

//var GFormRichTextEditor = GCore.ExtendClass(GFormTextarea, function() {
//	
//	var gThis = this;
//	
//	gThis.OnShow = function() {
//		if (gThis.m_bShown) {
//			return;
//		}
//		
//		if(!gThis.m_bShown){
//			$('#' + gThis.GetId()).redactor({ 
//				lang: 'pl',
//				imageUpload: GCore.sAdminUrl + 'redactor/add',
//				imageGetJson: GCore.sAdminUrl + 'redactor/view/',
//			});
//			gThis.m_bShown = true;
//		}
//	};
//	
//}, oDefaults);

/*
* HELP
*/

var GHelp = function() {
	
	var gThis = this;

	gThis.OnContentLoaded = GEventHandler(function(eEvent) {

		gThis.m_sContent.html(eEvent.sContent);
		gThis.bLoaded = true;
	});
	
	gThis._OnExpandHelp = GEventHandler(function(eEvent) {

		gThis.m_sContent.slideToggle('fast');
		
		if(gThis.bLoaded == false){
			
			xajax_getHelpForPage ({
				sController: GCore.sCurrentController,
				sAction: GCore.sCurrentAction
			}, GCallback(gThis.OnContentLoaded));
			
		}
		
		return false;
	});
	
	gThis.MakeHelpButton = function() {
		gThis.bLoaded = false;
		gThis.m_jLi = $('<li>');
		gThis.m_jA = $('<a class="button" href="#"/>');
		gThis.m_jA.append('<span><img src="'+GCore.DESIGN_PATH+'_images_panel/icons/buttons/help.png" alt=""/>' + GForm.Language.help + '</span>');
		gThis.m_jA.appendTo(gThis.m_jLi);	
		gThis.m_jLi.appendTo($('.possibilities'));
		gThis.m_jA.bind('click', gThis._OnExpandHelp);
		gThis.m_sContent = $('<div></div>').addClass('block').html('<p>Trwa wczytywanie pomocy...</p>');
		gThis.m_sContent.insertAfter($('.possibilities'));
		gThis.m_sContent.hide();
	};
	
	gThis.MakeHelpButton();
	
};

new GPlugin('GHelp', oDefaults, GHelp);

/*
* LANGUAGE SELECTOR
*/


var GLanguageSelector = function() {
	
	var gThis = this;
	gThis.m_sLanguage;
	
		
	gThis.SwitchLanguage = function(sLanguage) {

	   if(sLanguage ==  gThis.m_sLanguage){
			return;
	   }
	   
	   var hasForm = ($('.GForm').attr('id')) ? true : false;
	   var hasDg = ($('.GF_Datagrid').attr('id')) ? true : false;
	   
	   if(hasDg){
	   	
		   gThis.m_sLanguage = sLanguage;
		   gThis.m_jLanguageSelectorField.parent().find('span').text(GCore.aoLanguages[gThis.m_sLanguage].name);
		   return xajax_ChangeInterfaceLanguage(sLanguage,true);	
	   
	   }else if(hasForm){
	   	
	       var title = 'Przełączenie spowoduje utratę niezapisanych danych';
	       var msg = 'Czy chcesz przełączyć na '+GCore.aoLanguages[sLanguage].name+' ?';
	       var params = {
	           id: sLanguage
	       };
	       var confirm = function(p) {
	       		return xajax_ChangeInterfaceLanguage(p.id,true);
	       };       
	       
	       var cancel = function(p) {
	       		gThis.m_jLanguageSelectorField.val(gThis.m_sLanguage);
	       };
	       
	       new GF_Alert(title, msg, confirm,cancel,params,params);
       
	   }else{
	   	
	   		gThis.m_sLanguage = sLanguage;
	   		gThis.m_jLanguageSelectorField.parent().find('span').text(GCore.aoLanguages[gThis.m_sLanguage].name);
	   		return xajax_ChangeInterfaceLanguage(sLanguage,true);		
	   }
       
	};
	
	gThis.MakeLanguageSelector = function() {
		gThis.m_sLanguage = GCore.iActiveLanguage;
		gThis.m_jLanguageSelectorNode = $('<div class="field-select"/>').css('float','right');
		gThis.m_jLanguageSelectorField = $('<select id="language_selector" />');
		for (var i in GCore.aoLanguages) {
			gThis.m_jLanguageSelectorField.append('<option value="' + i + '">' + GCore.aoLanguages[i].name + '</option>');
		}
		gThis.m_jLanguageSelectorNode.append($('<span class="repetition"/>').append($('<span class="field"/>').append(gThis.m_jLanguageSelectorField)));
		gThis.m_jLanguageSelectorField.val(gThis.m_sLanguage);
		gThis.m_jLanguageSelectorField.GSelect();
		$('#selectors').append(gThis.m_jLanguageSelectorNode);
		gThis.m_jLanguageSelectorField.parent().find('span').text(GCore.aoLanguages[GCore.iActiveLanguage].name);
		gThis.m_jLanguageSelectorField.change(function(){
			gThis.SwitchLanguage($(this).val());
		});
		
	};
	
	gThis.MakeLanguageSelector();
	

};

/*
* LAYER SELECTOR
*/


var GLayerSelector = function() {
	
	var gThis = this;
	gThis.m_sLayer;
	
	gThis._OnExpandCategorySelector = GEventHandler(function(eEvent) {
		$('.layer-selector').remove();
		var jExpander = $(this);
		jExpander.parent().addClass('focus');
		
		var jList = $('<div class="layer-selector"/>');
		jList.get(0).m_jExpander = jExpander;
		
		var jTabAll = $('<div id="layer-selector-all" class="GForm"/>');
		jTabAll.append(gThis._PrepareTreeNode());
		jList.append(jTabAll);
		
		$('body:eq(0)').append(jList);
		
		jList.css({
			left: $(this).offset().left - 1,
			top: $(this).offset().top + 20,
			display: 'none'
		});
		
		jList.GShadow().GOverlay({
			fClick: function(eEvent) {
				jExpander.parent().removeClass('focus');
				jList.remove();
			}
		});
		
		jList.fadeIn(300);
		eEvent.stopImmediatePropagation();
		return false;
	});
	
	gThis.SwitchLayer = function(sLayer) {

	   if(sLayer ==  gThis.m_sLayer){
			return;
	   }
	   
	   var hasForm = ($('.GForm').attr('id')) ? true : false;
	   var hasDg = ($('.GF_Datagrid').attr('id')) ? true : false;
	   
	   if(hasForm){
	   	
       var title = 'Przełączenie spowoduje utratę niezapisanych danych';
       var msg = 'Czy chcesz przełączyć na '+GCore.aoLayers[sLayer].name+' ?';
       var params = {
           id: sLayer
       };
       var confirm = function(p) {
       		return xajax_ChangeActiveLayer(p.id);
       };       
       
       new GF_Alert(title, msg, confirm,true,params);
	   }else{
	   		gThis.m_sLayer = sLayer;
	   		gThis.m_jLayerSelectorField.parent().find('span').text(GCore.aoLayers[gThis.m_sLayer].name);
	   		return xajax_ChangeActiveLayer(sLayer,hasDg);		
	   }
       
	};
	
	gThis.MakeLayerSelector = function() {
		gThis.m_sLayer = GCore.iActiveLayer;
		gThis.m_jLayerSelectorNode = $('<div class="field-select"/>').css('float','right');
		gThis.m_jLayerSelectorField = $('<select id="layer_selector" />');

		gThis.m_jLayerSelectorNode.append($('<span class="repetition"/>').append($('<span class="field"/>').append(gThis.m_jLayerSelectorField)));
		gThis.m_jLayerSelectorField.GSelect();
		$('#selectors').append(gThis.m_jLayerSelectorNode);
		gThis.m_jLayerSelectorField.parent().find('span').text(GCore.aoLayers[GCore.iActiveLayer].name);
		gThis.m_jLayerSelectorField.bind('mousedown', gThis._OnExpandCategorySelector);
		
	};
	
	gThis.MakeLayerSelector();
	
	gThis._PrepareTreeNode = function() {
		var jNode = $('<div/>').addClass('field-tree');
		gThis.m_jFieldWrapper = $('<div/>');
		jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jTree = $('<ul/>');
		jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		gThis.Update();
		gThis.m_jTree.find('li:has(li)').addClass('expanded');
		return jNode;
	};
	
	
	gThis._WriteSubtree = function(jParent, sParent) {
		if (sParent == undefined) {
			sParent = null;
		}
		var aoItems = GCore.aoLayers;	
		var oItems = GCore.FilterObject(aoItems, function(oItem) {return (oItem.parent == sParent);});
		var aIterationArray = GCore.GetIterationArray(oItems, function(oA, oB) {return (oA.weight < oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			var oItem = oItems[sId];
			jParent.append(gThis._WriteItem(sId, oItem));
		}
	};
	
	gThis._WriteItem = function(sId, oItem) {
		var jLi = $('<li/>');
		jLi.get(0).sId = sId;
		if (gThis.m_sLayer == sId) {
			jLi.addClass('active');
		}
		var jField = $('<input type="radio" name="__layer" value="' + sId + '"/>');
		if (gThis.m_sLayer == sId) {
			jField.click();
			jField.attr('checked', 'checked');
		}

		jLi.append($('<label/>').append(jField).append(oItem.name));
		var jUl = $('<ul/>');
		gThis._WriteSubtree(jUl, sId);
		jLi.append(jUl);
		return jLi;
	};
	
	gThis.Update = function() {
		gThis.m_jTree.empty();
		gThis._WriteSubtree(gThis.m_jTree);
		gThis._InitializeNodeEvents();
		
	};
	
	gThis._OnTreeClick = GEventHandler(function(eEvent) {
		GCore.StartWaiting();
	});
	
	gThis._InitializeNodeEvents = function() {

		gThis.m_jTree.find('input').click(gThis._OnTreeSelect);
	};
	
	gThis._OnTreeSelect = GEventHandler(function(eEvent) {

		var sId = $(this).attr('value');
		var sCaption = $(this).parent().text();
		var jList = $(this).closest('.layer-selector');
		var jExpander = jList.get(0).m_jExpander;
		jExpander.parent().removeClass('focus');
		jList.remove();
		GOverlay.RemoveAll();
		gThis.SwitchLayer(sId);
		
	});
};

new GPlugin('GLayerSelector', oDefaults, GLayerSelector);

/*
* VIEW SELECTOR
*/


var GViewSelector = function() {
	
	var gThis = this;
	gThis.m_sView;
	
	gThis._OnExpandCategorySelector = GEventHandler(function(eEvent) {
		$('.view-selector').remove();
		var jExpander = $(this);
		jExpander.addClass('focus');
		
		var jList = $('<div class="view-selector"/>');
		jList.get(0).m_jExpander = jExpander;
		
		var jTabAll = $('<div id="view-selector-all" class="GForm"/>');
		jTabAll.append(gThis._PrepareTreeNode());
		jList.append(jTabAll);
		
		$('body:eq(0)').append(jList);
		
		jList.css({
			left: $(this).offset().left - 1,
			top: $(this).offset().top + 20,
			display: 'none'
		});
		
		jList.GShadow().GOverlay({
			fClick: function(eEvent) {
				jExpander.removeClass('focus');
				jList.remove();
			}
		});
		
		jList.fadeIn(300);
		eEvent.stopImmediatePropagation();
		return false;
	});
	
	gThis.SwitchView = function(sView) {

	   if(sView ==  gThis.m_sView){
			return;
	   }
	   
	   var hasForm = ($('.GForm').attr('id')) ? true : false;
	   var hasDg = ($('.GF_Datagrid').attr('id')) ? true : false;
	   
	   
	   if(hasDg){
	   	
	       gThis.m_sView = sView;
		   gThis.m_jViewSelectorSpan.find('span').text(GCore.aoViews[gThis.m_sView].name);
		   return xajax_ChangeActiveView(sView);	
       
	   }else if(hasForm) {
	   	
		   var title = 'Przełączenie spowoduje utratę niezapisanych danych.';
	       var msg = 'Czy chcesz przełączyć na '+GCore.aoViews[sView].name+' ?';
	       var params = {
	           id: sView
	       };
	       var confirm = function(p) {
				gThis.m_jViewSelectorSpan.removeClass('focus');
	       		return xajax_ChangeActiveView(p.id);
	       };       
	       
	       new GF_Alert(title, msg, confirm,true,params);
       		
	   }else{
	   
		   gThis.m_sView = sView;
		   gThis.m_jViewSelectorSpan.find('span').text(GCore.aoViews[gThis.m_sView].name);
		   return xajax_ChangeActiveView(sView);	
	   
	   }
       
	};
	
	gThis.MakeViewSelector = function() {
		gThis.m_sView = GCore.iActiveView;
		gThis.m_jViewSelectorNode = $('<div class="field-select"/>').css('float','right');
		gThis.m_jViewSelectorField = $('<select id="view_selector" />');
		gThis.m_jViewSelectorSpan = $('<span class="field"/>').append(gThis.m_jViewSelectorField);
		gThis.m_jViewSelectorNode.append($('<span class="repetition"/>').append(gThis.m_jViewSelectorSpan));
		gThis.m_jViewSelectorField.GSelect();
		$('#selectors').append(gThis.m_jViewSelectorNode);
		gThis.m_jViewSelectorField.parent().find('span').text(GCore.aoViews[GCore.iActiveView].name);
		gThis.m_jViewSelectorField.parent().bind('click', gThis._OnExpandCategorySelector).css('cursor', 'pointer');
		gThis.m_jViewSelectorField.remove();
		
	};
	
	gThis.MakeViewSelector();
	
	gThis._PrepareTreeNode = function() {
		var jNode = $('<div/>').addClass('field-tree');
		gThis.m_jFieldWrapper = $('<div/>');
		jNode.append(gThis.m_jFieldWrapper);
		gThis.m_jTree = $('<ul/>');
		jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
		gThis.Update();
		gThis.m_jTree.find('li:has(li)').addClass('expanded');
		return jNode;
	};
	
	
	gThis._WriteSubtree = function(jParent, sParent) {
		if (sParent == undefined) {
			sParent = null;
		}
		var aoItems = GCore.aoViews;	
		var oItems = GCore.FilterObject(aoItems, function(oItem) {return (oItem.parent == sParent);});
		var aIterationArray = GCore.GetIterationArray(oItems, function(oA, oB) {return (oA.weight < oB.weight);});
		var iLength = aIterationArray.length;
		for (var i = 0; i < iLength; i++) {
			var sId = aIterationArray[i];
			var oItem = oItems[sId];
			jParent.append(gThis._WriteItem(sId, oItem));
		}
	};
	
	gThis._WriteItem = function(sId, oItem) {
		var jLi = $('<li/>');
		jLi.get(0).sId = sId;
		if (gThis.m_sView == sId) {
			jLi.addClass('active');
		}
		var jField;
		if(oItem.type != 'store'){
		
			jField = $('<input type="radio" name="__view" value="' + sId + '"/>');
			if (gThis.m_sView == sId) {
				jField.click();
				jField.attr('checked', 'checked');
			}
			
		}

		jLi.append($('<label/>').append(jField).append(oItem.name));
		var jUl = $('<ul/>');
		gThis._WriteSubtree(jUl, sId);
		jLi.append(jUl);
		return jLi;
	};
	
	gThis.Update = function() {
		gThis.m_jTree.empty();
		gThis._WriteSubtree(gThis.m_jTree);
		gThis._InitializeNodeEvents();
		
	};
	
	gThis._OnTreeClick = GEventHandler(function(eEvent) {
		GCore.StartWaiting();
	});
	
	gThis._InitializeNodeEvents = function() {

		gThis.m_jTree.find('input').click(gThis._OnTreeSelect);
	};
	
	gThis._OnTreeSelect = GEventHandler(function(eEvent) {

		var sId = $(this).attr('value');
		var sCaption = $(this).parent().text();
		var jList = $(this).closest('.view-selector');
		var jExpander = jList.get(0).m_jExpander;
		jExpander.parent().removeClass('focus');
		jList.remove();
		GOverlay.RemoveAll();
		gThis.SwitchView(sId);
		
	});
};

new GPlugin('GViewSelector', oDefaults, GViewSelector);

/*
* ATTRIBUTE EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	fDeleteAttribute: GCore.NULL,
	oClasses: {
		sFieldClass: 'field-attribute-editor',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sAttributesClass: 'attributes-list',
		sValuesClass: 'values-list',
		sAttributeRepetitionClass: 'attribute-repetition',
		sValueRepetitionClass: 'value-repetition',
		sNameClass: 'name'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sEdit: '_images_panel/icons/buttons/edit.png',
		sRename: '_images_panel/icons/buttons/edit.png',
		sDeleteFromBase: '_images_panel/icons/buttons/delete.png',
		sDelete: '_images_panel/icons/buttons/delete-2.png'
	}
};

var GFormAttributeEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_oAttributes = {};
	gThis.m_iNewAttributeIndex = 0;
	
	gThis.m_jAttributes;
	gThis.m_jValues;
	gThis.m_aSelectedAttributes = [];
	gThis.m_sActiveAttribute = 0;
	
	gThis._Constructor = function() {
		for (var i in gThis.m_oOptions.aoAttributes) {
			gThis._AddAttribute(GCore.Duplicate(gThis.m_oOptions.aoAttributes[i], true));
		}
		gThis.m_sActiveAttribute = 0;
	};
	
	gThis._AddAttribute = function(oAttribute) {
		if (oAttribute instanceof String) {
			oAttribute = {
				name: oAttribute
			};
		}
		oAttribute = $.extend(GCore.Duplicate({
			name: '',
			id: 'new-' + gThis.m_iNewAttributeIndex++,
			values: []
		}, true), oAttribute);
		if (gThis.m_oAttributes[oAttribute.id] == undefined) {
			gThis.m_oAttributes[oAttribute.id] = $.extend(oAttribute, {
				iNewValueIndex: 0
			});
		}
		gThis.m_aSelectedAttributes.push(oAttribute.id);
		gThis.m_sActiveAttribute = oAttribute.id;
		return oAttribute.id;
	};
	
	gThis._AddValue = function(sAttribute, oValue) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oAttribute = gThis.m_oAttributes[sAttribute];
		if (oValue instanceof String) {
			oValue = {
				name: oValue
			};
		}
		oValue = $.extend(GCore.Duplicate({
			id: 'new-' + oAttribute.iNewValueIndex++,
			name: ''
		}, true), oValue);
		gThis.m_oAttributes[sAttribute].values.push(oValue);
		return oValue.id;
	};
	
	gThis._WriteValue = function(sAttribute, sValue) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oValue = gThis.m_oAttributes[sAttribute].values[sValue];
		var jValue = $('<li class="' + gThis._GetClass('ValueRepetition') + '"/>');
		/*var jEdit = $('<a rel="edit" href="#"/>').append('<img src="' + gThis._GetImage('Edit') + '" alt="' + GForm.Language.attribute_editor_edit_attribute_values + '" title="' + GForm.Language.attribute_editor_edit_attribute_values + '"/>');
		jEdit.click(GEventHandler(function(eEvent) {
			gThis.EditAttribute(oAttribute.id);
			return false;
		}));*/
		var jRename = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Rename') + '" alt="' + GForm.Language.attribute_editor_rename_value + '" title="' + GForm.Language.attribute_editor_rename_value + '"/>');
		jRename.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			GPrompt(GForm.Language.attribute_editor_rename_value_provide_new_name, function(sName) {
				GCore.StartWaiting();
				gThis.m_oOptions.fRenameValue({
					id: oValue.id,
					name: sName
				}, GCallback(function(eEvent) {
					GCore.StopWaiting();
					GAlert.DestroyThis.apply(eEvent.dMessage);
					if (eEvent.status) {
						gThis._RenameValue(oValue.id, sAttribute, sName);
					}
					else {
						GError(GForm.Language.attribute_editor_rename_value_error, GForm.Language.attribute_editor_rename_value_error_description);
					}
				}, {
					dMessage: this
				}));
			}, {
				sDefault: oValue.name
			});
			return false;
		}));
		var jDelete = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('DeleteFromBase') + '" alt="' + GForm.Language.attribute_editor_remove_value + '" title="' + GForm.Language.attribute_editor_remove_value + '"/>');
		jDelete.click(GEventHandler(function(eEvent) {
			gThis.RemoveValue(gThis.m_sActiveAttribute, oValue.id);
			return false;
		}));
		jValue.append('<span class="' + gThis._GetClass('Name') + '">' + oValue.name + '</span>').append(jDelete).append(jRename);
		return jValue;
	};
	
	gThis._RenameAttribute = function(sId, sName) {
		gThis.m_oAttributes[sId].name = sName;
		gThis.Update();
	};
	
	gThis._RenameValue = function(sId, sAttribute, sName) {
		for (var i in gThis.m_oAttributes[sAttribute].values) {
			if (gThis.m_oAttributes[sAttribute].values[i].id == sId) {
				gThis.m_oAttributes[sAttribute].values[i].name = sName;
			}
		}
		gThis.Update();
	};
	
	gThis._WriteAttribute = function(sAttribute) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oAttribute = gThis.m_oAttributes[sAttribute];
		var jAttribute = $('<li class="' + gThis._GetClass('AttributeRepetition') + ((gThis.m_sActiveAttribute == oAttribute.id) ? ' ' + gThis._GetClass('Active') : '') + '"/>');
		//var jEdit = $('<a rel="edit" href="#"/>').append('<img src="' + gThis._GetImage('Edit') + '" alt="' + GForm.Language.attribute_editor_edit_attribute_values + '" title="' + GForm.Language.attribute_editor_edit_attribute_values + '"/>');
		jAttribute.click(GEventHandler(function(eEvent) {
			gThis.EditAttribute(oAttribute.id);
			return false;
		}));
		var jRename = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Rename') + '" alt="' + GForm.Language.attribute_editor_rename_attribute + '" title="' + GForm.Language.attribute_editor_rename_attribute + '"/>');
		jRename.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			GPrompt(GForm.Language.attribute_editor_rename_attribute_provide_new_name, function(sName) {
				GCore.StartWaiting();
				gThis.m_oOptions.fRenameAttribute({
					id: oAttribute.id,
					name: sName
				}, GCallback(function(eEvent) {
					GCore.StopWaiting();
					GAlert.DestroyThis.apply(eEvent.dMessage);
					if (eEvent.status) {
						gThis._RenameAttribute(oAttribute.id, sName);
					}
					else {
						GError(GForm.Language.attribute_editor_rename_attribute_error, GForm.Language.attribute_editor_rename_attribute_error_description);
					}
				}, {
					dMessage: this
				}));
			}, {
				sDefault: oAttribute.name
			});
			return false;
		}));
		var jDelete = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.attribute_editor_remove_attribute + '" title="' + GForm.Language.attribute_editor_remove_attribute + '"/>');
		jDelete.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			gThis.RemoveAttribute(oAttribute.id);
			return false;
		}));
		var jDeleteFromBase = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('DeleteFromBase') + '" alt="' + GForm.Language.attribute_editor_remove_attribute_from_base + '" title="' + GForm.Language.attribute_editor_remove_attribute_from_base + '"/>');
		jDeleteFromBase.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			GWarning(GForm.Language.attribute_editor_remove_attribute_from_base_confirm, GForm.Language.attribute_editor_remove_attribute_from_base_confirm_description, {
				bAutoExpand: true,
				aoPossibilities: [
					{mLink: function() {
						GCore.StartWaiting();
						gThis.m_oOptions.fDeleteAttribute({
							id: oAttribute.id,
							set_id: gThis.m_oOptions.sSetId
						}, GCallback(function(eEvent) {
							GCore.StopWaiting();
							GAlert.DestroyThis.apply(eEvent.dMessage);
							if (eEvent.status) {
								gThis.RemoveAttribute(oAttribute.id);
							}
							else {
								GError(GForm.Language.attribute_editor_remove_attribute_from_base_error, GForm.Language.attribute_editor_remove_attribute_from_base_error_description);
							}
						}, {
							dMessage: this
						}));
					}, sCaption: GForm.Language.attribute_editor_remove_attribute_from_base_ok},
					{mLink: GAlert.DestroyThis, sCaption: GForm.Language.attribute_editor_remove_attribute_from_base_cancel}
				]
			});
			return false;
		}));
		jAttribute.append('<span class="' + gThis._GetClass('Name') + '">' + oAttribute.name + '</span>').append(jDelete).append(jDeleteFromBase).append(jRename);//.append(jEdit);
		return jAttribute;
	};
	
	gThis.EditAttribute = function(sAttribute) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		gThis.m_sActiveAttribute = sAttribute;
		gThis.Update();
	};
	
	gThis.RemoveValue = function(sAttribute, sValue) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		for (var i in gThis.m_oAttributes[sAttribute].values) {
			if (gThis.m_oAttributes[sAttribute].values[i].id == sValue) {
				gThis.m_oAttributes[sAttribute].values.splice(i, 1);
				break;
			}
		}
		gThis.Update();
	};
	
	gThis.RemoveAttribute = function(sAttribute) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		for (var i in gThis.m_aSelectedAttributes) {
			if (gThis.m_aSelectedAttributes[i] == sAttribute) {
				gThis.m_aSelectedAttributes.splice(i, 1);
				break;
			}
		}
		if (gThis.m_sActiveAttribute == sAttribute) {
			gThis.m_sActiveAttribute = 0;
		}
		if (sAttribute.substr(0, 4) == 'new-') {
			delete gThis.m_oAttributes[sAttribute];
		}
		gThis.Update();
	};
	
	gThis._WriteValueAdder = function() {
		var jValue = $('<li class="' + gThis._GetClass('ValueRepetition') + '"/>');
		var jInput = $('<input type="text"/>');
		var jAdd = $('<a rel="add" href="#"/>').append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.attribute_editor_add_value + '" title="' + GForm.Language.attribute_editor_add_value + '"/>');
		jAdd.click(GEventHandler(function(eEvent) {
			gThis._OnValueAdd($(this).closest('li').find('input'));
			return false;
		}));
		jValue.append($('<div class="field-text"/>').append($('<span class="field"/>').append(jInput)).append(jAdd));
		return jValue;
	};
	
	gThis._WriteAttributeAdder = function() {
		var jAttribute = $('<li class="' + gThis._GetClass('AttributeRepetition') + '"/>');
		var jSelect = $('<select/>');
		jSelect.append('<option value="">' + GForm.Language.attribute_editor_choose_attribute + '</option>');
		for (var i in gThis.m_oAttributes) {
			var oAttribute = gThis.m_oAttributes[i];
			if ($.inArray(oAttribute.id, gThis.m_aSelectedAttributes) != -1) {
				continue;
			}
			jSelect.append('<option value="' + oAttribute.id + '">' + oAttribute.name + '</option>');
		}
		var jAdd = $('<a rel="add" href="#"/>').append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.attribute_editor_add_attribute + '" title="' + GForm.Language.attribute_editor_add_attribute + '"/>');
		jAdd.click(GEventHandler(function(eEvent) {
			gThis._OnAttributeAdd($(this).closest('li').find('select'));
			return false;
		}));
		jAttribute.append($('<div class="field-select"/>').append($('<span class="field"/>').append(jSelect)).append(jAdd));
		return jAttribute;
	};
	
	gThis._WriteValues = function() {
		gThis.m_jValues.empty();
		if (gThis.m_oAttributes[gThis.m_sActiveAttribute] == undefined) {
			gThis.m_jValues.parent().find('h3').text(GForm.Language.attribute_editor_values);
			return;
		}
		gThis.m_jValues.parent().find('h3').text(GForm.Language.attribute_editor_values + ': ' + gThis.m_oAttributes[gThis.m_sActiveAttribute].name);
		var jValueAdder = gThis._WriteValueAdder();
		for (var i in gThis.m_oAttributes[gThis.m_sActiveAttribute].values) {
			gThis.m_jValues.append(gThis._WriteValue(gThis.m_sActiveAttribute, i));
		}
		gThis.m_jValues.append(jValueAdder);
	};
	
	gThis._WriteAttributes = function() {
		gThis.m_jAttributes.empty();
		var jAttributeAdder = gThis._WriteAttributeAdder();
		for (var i in gThis.m_aSelectedAttributes) {
			gThis.m_jAttributes.append(gThis._WriteAttribute(gThis.m_aSelectedAttributes[i]));
		}
		gThis.m_jAttributes.append(jAttributeAdder);
	};
	
	gThis.Update = function() {
		gThis._WriteAttributes();
		gThis._WriteValues();
		gThis._InitializeEditorEvents();
		gThis.m_jField.empty();
		for (var i in gThis.m_aSelectedAttributes) {
			var oAttribute = gThis.m_oAttributes[gThis.m_aSelectedAttributes[i]];
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + i + ']" value="' + oAttribute.id + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][id]" value="' + oAttribute.id + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][name]" value="' + (oAttribute.name).replace('"',"''") + '"/>');
			for (var j in oAttribute.values) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][values][' + j + '][id]" value="' + oAttribute.values[j].id + '"/>');
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][values][' + j + '][name]" value="' + (oAttribute.values[j].name).replace('"',"''") + '"/>');
			}
		}
	};
	
	gThis._InitializeEditorEvents = function() {
		gThis.m_jAttributes.find('select').GComboBox();
		gThis.m_jAttributes.find('select').bind('GChange', GEventHandler(function(eEvent) {
			if (gThis._OnAttributeAdd($(this))) {
				gThis.m_jAttributes.find('input').focus();
			}
		}));
		gThis.m_jValues.find('input').keydown(GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis._OnValueAdd($(this));
				gThis.m_jValues.find('input').focus();
			}
		}));
	};
	
	gThis._OnValueAdd = function(jInput) {
		if (jInput.val() == '') {
			return false;
		}
		gThis._AddValue(gThis.m_sActiveAttribute, {
			name: jInput.val()
		});
		gThis.Update();
		return true;
	};
	
	gThis._OnAttributeAdd = function(jSelect) {
		if (jSelect.val() == '') {
			return false;
		}
		if (jSelect.val() == '_new_') {
			gThis._AddAttribute({
				name: jSelect.find('option:selected').text()
			}, true);
		}
		else {
			gThis._AddAttribute({
				id: jSelect.val(),
				name: jSelect.find('option:selected').text()
			});
		}
		gThis.Update();
		return true;
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<div/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jAttributes = $('<ul/>');
		gThis.m_jValues = $('<ul/>');
		gThis.m_jNode.append($('<div class="' + gThis._GetClass('Attributes') + '"/>').append('<h3>' + GForm.Language.attribute_editor_attributes + '</h3>').append(gThis.m_jAttributes));
		gThis.m_jNode.append($('<div class="' + gThis._GetClass('Values') + '"/>').append('<h3>' + GForm.Language.attribute_editor_values + '</h3>').append(gThis.m_jValues));
		gThis.Update();
	};
	
	gThis.Populate = function(mValue) {
		gThis.m_aSelectedAttributes = mValue;
		gThis.Update();
	};
	
	gThis.Reset = function() {
		gThis.m_oAttributes = {};
		for (var i in gThis.m_oOptions.aoAttributes) {
			gThis._AddAttribute(GCore.Duplicate(gThis.m_oOptions.aoAttributes[i], true));
		}
		gThis.m_sActiveAttribute = 0;
		gThis.Update();
	};
	
}, oDefaults);

/*
* TECHNICAL ATTRIBUTE EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	fDeleteAttribute: GCore.NULL,
	oClasses: {
		sFieldClass: 'field-attribute-editor',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sActiveClass: 'active',
		sButtonClass: 'button',
		sAttributesClass: 'attributes-list',
		sValuesClass: 'values-list',
		sAttributeRepetitionClass: 'attribute-repetition',
		sValueRepetitionClass: 'value-repetition',
		sNameClass: 'name'
	},
	oImages: {
		sAdd: '_images_panel/icons/buttons/add.png',
		sEdit: '_images_panel/icons/buttons/edit.png',
		sRename: '_images_panel/icons/buttons/edit.png',
		sDeleteFromBase: '_images_panel/icons/buttons/delete.png',
		sDelete: '_images_panel/icons/buttons/delete-2.png'
	}
};

var GFormTechnicalAttributeEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	gThis.m_oAttributes = {};
	gThis.m_iNewAttributeIndex = 0;
	
	gThis.m_jAttributes;
	gThis.m_jValues;
	gThis.m_aSelectedAttributes = [];
	gThis.m_sActiveAttribute = 0;
	
	gThis._Constructor = function() {
		for (var i in gThis.m_oOptions.aoAttributes) {
			gThis._AddAttribute(GCore.Duplicate(gThis.m_oOptions.aoAttributes[i], true));
		}
		gThis.m_sActiveAttribute = 0;
	};
	
	gThis._AddAttribute = function(oAttribute) {
		
		if (oAttribute instanceof String) {
			oAttribute = {
				name: oAttribute
			};
		}
		var sName = [];
		sName[GCore.iActiveLanguage] = '';
		oAttribute = $.extend(GCore.Duplicate({
			name: sName,
			id: 'new-' + gThis.m_iNewAttributeIndex++,
			values: []
		}, true), oAttribute);
		if (gThis.m_oAttributes[oAttribute.id] == undefined) {
			gThis.m_oAttributes[oAttribute.id] = $.extend(oAttribute, {
				iNewValueIndex: 0
			});
		}
		gThis.m_aSelectedAttributes.push(oAttribute.id);
		gThis.m_sActiveAttribute = oAttribute.id;
		return oAttribute.id;
	};
	
	gThis._AddValue = function(sAttribute, oValue) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oAttribute = gThis.m_oAttributes[sAttribute];
		if (oValue instanceof String) {
			oValue = {
				name: oValue
			};
		}
		var sName = [];
		sName[GCore.iActiveLanguage] = '';
		oValue = $.extend(GCore.Duplicate({
			id: 'new-' + oAttribute.iNewValueIndex++,
			name: sName,
			type: GFormTechnicalDataEditor.FIELD_STRING,
		}, true), oValue);
		gThis.m_oAttributes[sAttribute].values.push(oValue);
		return oValue.id;
	};
	
	gThis._WriteValue = function(sAttribute, sValue) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oValue = gThis.m_oAttributes[sAttribute].values[sValue];
		var jValue = $('<li class="' + gThis._GetClass('ValueRepetition') + '"/>');
		var jRename = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Rename') + '" alt="' + GForm.Language.attribute_editor_rename_value + '" title="' + GForm.Language.attribute_editor_rename_value + '"/>');
		jRename.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			var jOverlay = $('<div class="technical-data-detail-editor"/>');
			$('body').append(jOverlay);
			jOverlay.GShadow();
			jOverlay.GOverlay({
				fClick: GEventHandler(function(eEvent) {
					jOverlay.remove();
				})
			});
			jOverlay.css({
				left: $(this).offset().left - 161,
				top: $(this).offset().top + 21
			});
			for (var l in GCore.aoLanguages) {
				var jLanguage = $('<div class="field-text"><span class="prefix"><img style="margin-top: 4px;margin-left: 4px;" src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/' + GCore.aoLanguages[l].flag + '" alt="' + GCore.aoLanguages[l].name + '"/></span><span class="field"><input class="language-' + l + '" type="text" value="' + oValue.name[l] + '"/></span></div>');
				jOverlay.append(jLanguage);
			}
			var jSaveButton = $('<a class="button" href="#"><span>' + GForm.Language.technical_data_save_group + '</span></a>');
			jSaveButton.click(GEventHandler(function(eEvent) {
				for (var l in GCore.aoLanguages) {
					var sName = $('.technical-data-detail-editor').find('.language-' + l).val();
					gThis.m_oOptions.fRenameValue({
						id: oValue.id, 
						name: sName,
						languageid: l
					}, GCallback(function(eEvent) {
						gThis._RenameValue(eEvent.id, sAttribute, eEvent.name, eEvent.languageid);
					}));
				}
				GOverlay.RemoveAll();
				return false;
			}));
			var jCancelButton = $('<a class="button" href="#"><span>' + GForm.Language.tree_cancel + '</span></a>');
			jCancelButton.click(GEventHandler(function(eEvent) {
				GOverlay.RemoveAll();
				return false;
			}));
			jOverlay.append(jSaveButton).append(jCancelButton);
			return false;
		}));
		var jDelete = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('DeleteFromBase') + '" alt="' + GForm.Language.attribute_editor_remove_value + '" title="' + GForm.Language.attribute_editor_remove_value + '"/>');
		jDelete.click(GEventHandler(function(eEvent) {
			gThis.RemoveValue(gThis.m_sActiveAttribute, oValue.id);
			return false;
		}));
		
		var jTypeSelect = $('<select name="" class="type"/>');
		var aoTypes = [
			{id: GFormTechnicalDataEditor.FIELD_STRING, caption: GForm.Language.technical_data_value_type_string},
			{id: GFormTechnicalDataEditor.FIELD_MULTILINGUAL_STRING, caption: GForm.Language.technical_data_value_type_multilingual_string},
			{id: GFormTechnicalDataEditor.FIELD_TEXT, caption: GForm.Language.technical_data_value_type_text},
			{id: GFormTechnicalDataEditor.FIELD_BOOLEAN, caption: GForm.Language.technical_data_value_type_boolean},
		];
		for (var i = 0; i < aoTypes.length; i++) {
			jTypeSelect.append('<option' + ((aoTypes[i].id == oValue.type) ? ' selected="selected"' : '') + ' value="' + aoTypes[i].id + '">' + aoTypes[i].caption + '</option>');
		}
		
		jTypeSelect.change(function(){
			oValue.type = $(this).val();
			gThis.Update();
		});
		
		if ((oValue.id).substr(0, 4) == 'new-') {
			jValue.append('<span class="' + gThis._GetClass('Name') + '">' + oValue.name[GCore.iActiveLanguage] + '</span>').append(jDelete).append($('<div class="field-select"/>').append($('<span class="field"/>').append(jTypeSelect)));	
		}else{
			jValue.append('<span class="' + gThis._GetClass('Name') + '">' + oValue.name[GCore.iActiveLanguage] + '</span>').append(jDelete).append(jRename).append($('<div class="field-select"/>').append($('<span class="field"/>').append(jTypeSelect)));
		}
		jTypeSelect.GSelect();
		return jValue;
	};
	
	gThis._RenameAttribute = function(sId, sName, sLanguageId) {
		gThis.m_oAttributes[sId].name[sLanguageId] = sName;
		gThis.Update();
	};
	
	gThis._RenameValue = function(sId, sAttribute, sName, sLanguageId) {
		for (var i in gThis.m_oAttributes[sAttribute].values) {
			if (gThis.m_oAttributes[sAttribute].values[i].id == sId) {
				gThis.m_oAttributes[sAttribute].values[i].name[sLanguageId] = sName;
			}
		}
		gThis.Update();
	};
	
	gThis._WriteAttribute = function(sAttribute) {
		if ((sAttribute == undefined) || (gThis.m_oAttributes[sAttribute] == undefined)) {
			return null;
		}
		var oAttribute = gThis.m_oAttributes[sAttribute];
		var jAttribute = $('<li class="' + gThis._GetClass('AttributeRepetition') + ((gThis.m_sActiveAttribute == oAttribute.id) ? ' ' + gThis._GetClass('Active') : '') + '"/>');
		jAttribute.click(GEventHandler(function(eEvent) {
			gThis.EditAttribute(oAttribute.id);
			return false;
		}));
		var jRename = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Rename') + '" alt="' + GForm.Language.attribute_editor_rename_attribute + '" title="' + GForm.Language.attribute_editor_rename_attribute + '"/>');
		jRename.click(GEventHandler(function(eEvent) {
			var jOverlay = $('<div class="technical-data-detail-editor"/>');
			$('body').append(jOverlay);
			jOverlay.GShadow();
			jOverlay.GOverlay({
				fClick: GEventHandler(function(eEvent) {
					jOverlay.remove();
				})
			});
			jOverlay.css({
				left: $(this).offset().left - 161,
				top: $(this).offset().top + 21
			});
			for (var l in GCore.aoLanguages) {
				var jLanguage = $('<div class="field-text"><span class="prefix"><img style="margin-top: 4px;margin-left: 4px;" src="' + GCore.DESIGN_PATH + '_images_common/icons/languages/' + GCore.aoLanguages[l].flag + '" alt="' + GCore.aoLanguages[l].name + '"/></span><span class="field"><input class="language-' + l + '" type="text" value="' + oAttribute.name[l] + '"/></span></div>');
				jOverlay.append(jLanguage);
			}
			var jSaveButton = $('<a class="button" href="#"><span>' + GForm.Language.technical_data_save_group + '</span></a>');
			jSaveButton.click(GEventHandler(function(eEvent) {
				for (var l in GCore.aoLanguages) {
					var sName = $('.technical-data-detail-editor').find('.language-' + l).val();
					gThis.m_oOptions.fRenameAttribute({
						id: oAttribute.id, 
						name: sName,
						languageid: l
					}, GCallback(function(eEvent) {
						gThis._RenameAttribute(eEvent.id, eEvent.name, eEvent.languageid);
					}));
				}
				GOverlay.RemoveAll();
				return false;
			}));
			var jCancelButton = $('<a class="button" href="#"><span>' + GForm.Language.tree_cancel + '</span></a>');
			jCancelButton.click(GEventHandler(function(eEvent) {
				GOverlay.RemoveAll();
				return false;
			}));
			jOverlay.append(jSaveButton).append(jCancelButton);
		}));
		
		var jDelete = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.attribute_editor_remove_attribute + '" title="' + GForm.Language.attribute_editor_remove_attribute + '"/>');
		jDelete.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			gThis.RemoveAttribute(oAttribute.id);
			return false;
		}));
		
		var jDeleteFromBase = $('<a rel="delete" href="#"/>').append('<img src="' + gThis._GetImage('DeleteFromBase') + '" alt="' + GForm.Language.attribute_editor_remove_attribute_from_base + '" title="' + GForm.Language.attribute_editor_remove_attribute_from_base + '"/>');
		jDeleteFromBase.click(GEventHandler(function(eEvent) {
			eEvent.stopImmediatePropagation();
			GWarning(GForm.Language.attribute_editor_remove_attribute_from_base_confirm, GForm.Language.attribute_editor_remove_attribute_from_base_confirm_description, {
				bAutoExpand: true,
				aoPossibilities: [
					{mLink: function() {
						GCore.StartWaiting();
						gThis.m_oOptions.fDeleteAttribute({
							id: oAttribute.id,
							set_id: gThis.m_oOptions.sSetId
						}, GCallback(function(eEvent) {
							GCore.StopWaiting();
							GAlert.DestroyThis.apply(eEvent.dMessage);
							if (eEvent.status) {
								gThis.RemoveAttribute(oAttribute.id);
							}
							else {
								GError(GForm.Language.attribute_editor_remove_attribute_from_base_error, GForm.Language.attribute_editor_remove_attribute_from_base_error_description);
							}
						}, {
							dMessage: this
						}));
					}, sCaption: GForm.Language.attribute_editor_remove_attribute_from_base_ok},
					{mLink: GAlert.DestroyThis, sCaption: GForm.Language.attribute_editor_remove_attribute_from_base_cancel}
				]
			});
			return false;
		}));
		if ((oAttribute.id).substr(0, 4) == 'new-') {
			jAttribute.append('<span class="' + gThis._GetClass('Name') + '">' + oAttribute.name[GCore.iActiveLanguage] + '</span>').append(jDelete);	
		}else{
			jAttribute.append('<span class="' + gThis._GetClass('Name') + '">' + oAttribute.name[GCore.iActiveLanguage] + '</span>').append(jDeleteFromBase).append(jRename);	
		}
		return jAttribute;
	};
	
	gThis.EditAttribute = function(sAttribute) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		gThis.m_sActiveAttribute = sAttribute;
		GCookie('edited-technical-data-attribute-' + gThis.m_oOptions.sSetId, gThis.m_sActiveAttribute, {
			expires: GCore.p_oParams.iCookieLifetime
		});
		gThis.Update();
	};
	
	gThis.RemoveValue = function(sAttribute, sValue) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		for (var i in gThis.m_oAttributes[sAttribute].values) {
			if (gThis.m_oAttributes[sAttribute].values[i].id == sValue) {
				gThis.m_oAttributes[sAttribute].values.splice(i, 1);
				break;
			}
		}
		gThis.Update();
	};
	
	gThis.RemoveAttribute = function(sAttribute) {
		if (gThis.m_oAttributes[sAttribute] == undefined) {
			return false;
		}
		for (var i in gThis.m_aSelectedAttributes) {
			if (gThis.m_aSelectedAttributes[i] == sAttribute) {
				gThis.m_aSelectedAttributes.splice(i, 1);
				break;
			}
		}
		if (gThis.m_sActiveAttribute == sAttribute) {
			gThis.m_sActiveAttribute = 0;
		}
		if (sAttribute.substr(0, 4) == 'new-') {
			delete gThis.m_oAttributes[sAttribute];
		}
		gThis.Update();
	};
	
	gThis._WriteValueAdder = function() {
		var jValue = $('<li class="' + gThis._GetClass('ValueRepetition') + '"/>');
		var jInput = $('<input type="text"/>');
		var jAdd = $('<a rel="add" href="#"/>').append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.attribute_editor_add_value + '" title="' + GForm.Language.attribute_editor_add_value + '"/>');
		jAdd.click(GEventHandler(function(eEvent) {
			gThis._OnValueAdd($(this).closest('li').find('input'));
			return false;
		}));
		jValue.append($('<div class="field-text"/>').append($('<span class="field"/>').append(jInput)).append(jAdd));
		return jValue;
	};
	
	gThis._WriteAttributeAdder = function() {
		var jAttribute = $('<li class="' + gThis._GetClass('AttributeRepetition') + '"/>');
		var jInput = $('<input type="text"/>');
		var jAdd = $('<a rel="add" href="#"/>').append('<img src="' + gThis._GetImage('Add') + '" alt="' + GForm.Language.attribute_editor_add_attribute + '" title="' + GForm.Language.attribute_editor_add_attribute + '"/>');
		jAdd.click(GEventHandler(function(eEvent) {
			gThis._OnAttributeAdd($(this).closest('li').find('input'));
			return false;
		}));
		jAttribute.append($('<div class="field-text"/>').append($('<span class="field"/>').append(jInput)).append(jAdd));
		return jAttribute;
	};
	
	gThis._WriteValues = function() {
		gThis.m_jValues.empty();
		if (gThis.m_oAttributes[gThis.m_sActiveAttribute] == undefined) {
			gThis.m_jValues.parent().find('h3').text(GForm.Language.technical_attribute_editor_values);
			return;
		}
		gThis.m_jValues.parent().find('h3').text(GForm.Language.technical_attribute_editor_values + ': ' + gThis.m_oAttributes[gThis.m_sActiveAttribute].name[GCore.iActiveLanguage]);
		var jValueAdder = gThis._WriteValueAdder();
		for (var i in gThis.m_oAttributes[gThis.m_sActiveAttribute].values) {
			gThis.m_jValues.append(gThis._WriteValue(gThis.m_sActiveAttribute, i));
		}
		gThis.m_jValues.append(jValueAdder);
	};
	
	gThis._WriteAttributes = function() {
		gThis.m_jAttributes.empty();
		var jAttributeAdder = gThis._WriteAttributeAdder();
		for (var i in gThis.m_aSelectedAttributes) {
			gThis.m_jAttributes.append(gThis._WriteAttribute(gThis.m_aSelectedAttributes[i]));
		}
		gThis.m_jAttributes.append(jAttributeAdder);
	};
	
	gThis.Update = function() {
		gThis._WriteAttributes();
		gThis._WriteValues();
		gThis._InitializeEditorEvents();
		gThis.m_jField.empty();
		for (var i in gThis.m_aSelectedAttributes) {
			var oAttribute = gThis.m_oAttributes[gThis.m_aSelectedAttributes[i]];
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + i + ']" value="' + oAttribute.id + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][id]" value="' + oAttribute.id + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][name]" value="' + (oAttribute.name[GCore.iActiveLanguage]).replace('"',"''") + '"/>');
			for (var j in oAttribute.values) {
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][values][' + j + '][id]" value="' + oAttribute.values[j].id + '"/>');
				gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][values][' + j + '][type]" value="' + oAttribute.values[j].type + '"/>');
				for (var l in GCore.aoLanguages) {
					gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[editor][' + i + '][values][' + j + '][name][' + l + ']" value="' + (oAttribute.values[j].name[l]).replace('"',"''") + '"/>');
				}
			}
		}
	};
	
	gThis._InitializeEditorEvents = function() {
		gThis.m_jAttributes.find('input').keydown(GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis._OnAttributeAdd($(this));
				gThis.m_jAttributes.find('input').focus();
			}
		}));
		
		gThis.m_jValues.find('input').keydown(GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis._OnValueAdd($(this));
				gThis.m_jValues.find('input').focus();
			}
		}));
	};
	
	gThis._OnValueAdd = function(jInput) {
		if (jInput.val() == '') {
			return false;
		}
		var sName = [];
		for (var l in GCore.aoLanguages) {
			if(l == GCore.iActiveLanguage){
				sName[l] = jInput.val();
			}else{
				sName[l] = '';
			}
		}
		gThis._AddValue(gThis.m_sActiveAttribute, {
			name: sName
		});
		gThis.Update();
		return true;
	};
	
	gThis._OnAttributeAdd = function(jInput) {
		if (jInput.val() == '') {
			return false;
		}
		var sName = [];
		for (var l in GCore.aoLanguages) {
			if(l == GCore.iActiveLanguage){
				sName[l] = jInput.val();
			}else{
				sName[l] = 'test';
			}
		}
		gThis._AddAttribute({
			name: sName
		});
		gThis.Update();
		return true;
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jField = $('<div/>');
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jAttributes = $('<ul/>');
		gThis.m_jValues = $('<ul/>');
		gThis.m_jNode.append($('<div class="' + gThis._GetClass('Attributes') + '"/>').append('<h3>' + GForm.Language.technical_attribute_editor_attributes + '</h3>').append(gThis.m_jAttributes));
		gThis.m_jNode.append($('<div class="' + gThis._GetClass('Values') + '"/>').append('<h3>' + GForm.Language.technical_attribute_editor_values + '</h3>').append(gThis.m_jValues));
		gThis.Update();
		var sLastActive = GCookie('edited-technical-data-attribute-' + gThis.m_oOptions.sSetId);
		if(sLastActive != undefined && sLastActive > 0 && gThis.m_oAttributes[sLastActive] != undefined){
			if(gThis.m_oOptions.asDefaults != undefined && gThis.m_oOptions.asDefaults.length && $.inArray(sLastActive, gThis.m_oOptions.asDefaults) == -1){
				if(gThis.m_oOptions.asDefaults[0] != undefined){
					gThis.m_sActiveAttribute = gThis.m_oOptions.asDefaults[0];
					gThis.EditAttribute(gThis.m_sActiveAttribute);
				}
			}else{
				if(gThis.m_oOptions.asDefaults != undefined && gThis.m_oOptions.asDefaults[0] != undefined){
					gThis.m_sActiveAttribute = sLastActive;
					gThis.EditAttribute(gThis.m_sActiveAttribute);
				}
			}
		}
	};
	
	gThis.Populate = function(mValue) {
		gThis.m_aSelectedAttributes = mValue;
		gThis.Update();
	};
	
	gThis.Reset = function() {
		gThis.m_oAttributes = {};
		for (var i in gThis.m_oOptions.aoAttributes) {
			gThis._AddAttribute(GCore.Duplicate(gThis.m_oOptions.aoAttributes[i], true));
		}
		gThis.m_sActiveAttribute = 0;
		gThis.Update();
	};
	
}, oDefaults);
/*
* CONDITIONS
*/

var GFormCondition = function(sCondition, mArgument) {
	
	var gThis = this;
	
	gThis.m_sType = sCondition;
	gThis.m_mArgument = mArgument;
	
	gThis.Evaluate = function(mValue) {
		switch(gThis.m_sType) {
			
			// EQUALS
			case GFormCondition.EQUALS:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return false;
				}
				else if (gThis.m_mArgument instanceof Array) {
					return $.inArray(mValue, gThis.m_mArgument) != -1;
				}
				else {
					return mValue == gThis.m_mArgument;
				}
				break;
			
			// GE
			case GFormCondition.GE:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return false;
				}
				else {
					return mValue >= gThis.m_mArgument;
				}
				break;
			
			// LE
			case GFormCondition.LE:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return false;
				}
				else {
					return mValue <= gThis.m_mArgument;
				}
				break;
			
			// NOT
			case GFormCondition.NOT:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return !gThis.m_mArgument.Evaluate(mValue);
				}
				else {
					return false;
				}
				break;
			
			// AND
			case GFormCondition.AND:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return true;
				}
				else if (gThis.m_mArgument instanceof Array) {
					for (var i = 0; i < gThis.m_mArgument.length; i++) {
						if (!(gThis.m_mArgument[i] instanceof GFormCondition) || !gThis.m_mArgument[i].Evaluate(mValue)) {
							return false;
						}
					}
					return true;
				}
				else {
					return false;
				}
				break;
			
			// OR
			case GFormCondition.OR:
				if (gThis.m_mArgument instanceof GFormCondition) {
					return true;
				}
				else if (gThis.m_mArgument instanceof Array) {
					for (var i = 0; i < gThis.m_mArgument.length; i++) {
						if (!(gThis.m_mArgument[i] instanceof GFormCondition)) {
							return false;
						}
						if (gThis.m_mArgument[i].Evaluate(mValue)) {
							return true;
						}
					}
					return true;
				}
				else {
					return false;
				}
				break;
			
		};
		return true;
	};
	
};

GFormCondition.EQUALS = '=';
GFormCondition.GE = '>=';
GFormCondition.LE = '<=';
GFormCondition.NOT = '!';
GFormCondition.AND = '&&';
GFormCondition.OR = '||';


/*
* HIDDEN
*/

var oDefaults = {
	sName: '',
	oClasses: {
		sFieldClass: 'field-hidden'
	},
	sFieldType: 'hidden',
	sDefault: '',
	aoRules: [],
	aoDependencies: [],
	sComment: ''
};

var GFormHidden = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jNode.append(gThis._AddField());
	};
	
	gThis.Validate = function(bNoRequests, sRepetition) {
		return true;
	};
	
	gThis._AddField = function(sId) {
		var jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="' + gThis.GetName(sId) + '" id="' + gThis.GetId(sId) + '"/>');
		gThis.m_jField = jField;
		return jField;
	};
	
}, oDefaults);

/*
* ORDER EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-order-editor',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition',
		sHiddenClass: 'hidden',
		sButtonClass: 'button',
		sTriggerClass: 'trigger'
	},
	oImages: {
		sAddIcon: '_images_panel/icons/buttons/add.png',
		sDeselectIcon: '_images_panel/icons/datagrid/delete.png'
	},
	aoOptions: [],
	sDefault: '',
	aoRules: [],
	sComment: '',
	fLoadProducts: GCore.NULL,
	bAdvancedEditor: false
};

var GFormOrderEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadProducts;
	gThis.m_fProcessProduct;
	gThis.m_jDatagrid;
	gThis.m_jSelectedDatagrid;
	gThis.m_jTrigger;
	gThis.m_gDatagrid;
	gThis.m_gSelectedDatagrid;
	gThis.m_gDataProvider;
	gThis.m_bFirstLoad = true;
	
	gThis.m_oRequest = {};
	
	gThis.m_iCounter = 0;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
		var aValues = [];
		var jValues = gThis.m_jField.find('input');
		for (var i = 0; i < jValues.length / 4; i++) {
			aValues.push({
				id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]:eq(' + i + ')').val(),
				quantity: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[quantity]"]:eq(' + i + ')').val(),
				price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[price]"]:eq(' + i + ')').val(),
				variant: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[variant]"]:eq(' + i + ')').val(),
				trackstock: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[trackstock]"]:eq(' + i + ')').val(),
				stock: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[stock]"]:eq(' + i + ')').val(),
				weight: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[weight]"]:eq(' + i + ')').val(),
			});
		}
		return aValues;
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
		for (var i in mValue) {
			if (mValue[i]['id'] == undefined) {
				continue;
			}
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][idproduct]" value="' + mValue[i]['idproduct'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][quantity]" value="' + mValue[i]['quantity'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][previousquantity]" value="' + mValue[i]['previousquantity'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][trackstock]" value="' + mValue[i]['trackstock'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][stock]" value="' + mValue[i]['stock'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][sellprice]" value="' + mValue[i]['sellprice'] + '"/>');
			gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName() + '[' + mValue[i]['id'] + '][variant]" value="' + mValue[i]['variant'] + '"/>');
		}
	};
	
	gThis._OnSelect = function(gDg, sId) {
		if (gThis.m_bRepeatable) {
			var oSelectedRow = GCore.Duplicate(gDg.GetRow(sId));
			gThis._AddRow(oSelectedRow);
			gDg.ClearSelection();
			gThis.m_gSelectedDatagrid.LoadData();
		}
	};
	
	gThis._AddRow = function(oSelectedRow) {
		GAlert.DestroyAll();
		GMessage('Dodano wybrany produkt do zamówienia.');
		oSelectedRow = $.extend({
			id: 'new-' + (gThis.m_iCounter++),
			quantity: 1,
			variant: '',
			sellprice: '0.00'
		}, oSelectedRow);
		gThis.m_gDataProvider.AddRow(oSelectedRow);
		return oSelectedRow;
	};
	
	gThis._OnDeselect = function(gDg, sId) {
	};
	
	gThis._OnChange = function(eEvent) {
		if (gThis.m_bRepeatable) {
			gThis.m_jField.empty();
		}
		var asIds = [];
		for (var i in eEvent.rows) {
			if (eEvent.rows[i].id != undefined) {
				asIds.push(eEvent.rows[i]);
			}
		}
		gThis.SetValue(asIds);
	};
	
	gThis._PrepareNode = function() {
		gThis.m_oOptions.oParsedFilterData = {};
		for (var i in gThis.m_oOptions.oFilterData) {
			$.globalEval('var oParsed = [' + gThis.m_oOptions.oFilterData[i] + '];');
			gThis.m_oOptions.oParsedFilterData[i] = $.extend({}, oParsed);
		}
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		gThis.m_jDatagridWrapper = $('<div class="existing-products"/>');
		if (gThis.m_bRepeatable) {
			gThis.m_jField = $('<div/>');
			gThis.m_jDatagrid = $('<div/>');
			gThis.m_jDatagridWrapper.append(gThis.m_jDatagrid);
			gThis.m_jSelectedDatagrid = $('<div class="selected-products"/>');
			gThis.m_jNode.append(gThis.m_jDatagridWrapper);
			gThis.m_jNode.append(gThis.m_jSelectedDatagrid);
		}
		else {
			gThis.m_jField = $('<input type="hidden" name="' + gThis.GetName() + '"/>');
			gThis.m_jDatagrid = $('<div/>');
			gThis.m_jNode.append(gThis.m_jDatagridWrapper);
		}
		gThis.m_jNode.append(gThis.m_jField);
		gThis.m_jDatagridWrapper.addClass(gThis._GetClass('Hidden'));
		gThis.m_jTrigger = $('<p class="' + gThis._GetClass('Trigger') + '"/>');
		var jA = $('<a href="#" class="' + gThis._GetClass('Button') + '"/>');
		jA.append('<span><img src="' + gThis._GetImage('AddIcon') + '" alt=""/>' + GForm.Language.product_select_add + '</span>');
		jA.click(GEventHandler(function(eEvent) {
			var jImg = gThis.m_jTrigger.find('a span img');
			if (gThis.m_jDatagridWrapper.hasClass(gThis._GetClass('Hidden'))) {
				gThis.m_jDatagridWrapper.css('display', 'none').removeClass(gThis._GetClass('Hidden'));
			}
			if (!gThis.m_jDatagridWrapper.get(0).bHidden) {
				gThis.m_jDatagridWrapper.get(0).bHidden = true;
				gThis.m_jTrigger.find('a span').empty().append(jImg).append(GForm.Language.product_select_close_add);
			}
			else {
				gThis.m_jDatagridWrapper.get(0).bHidden = false;
				gThis.m_jTrigger.find('a span').empty().append(jImg).append(GForm.Language.product_select_add);
			}
			gThis.m_jDatagridWrapper.slideToggle(250);
			return false;
		}));
		gThis.m_jTrigger.append(jA);
		gThis.m_jNode.append(gThis.m_jTrigger);
	};
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (!gThis.m_gDatagrid) {
			return;
		}
		gThis.m_jField.empty();
		gThis.m_oOptions.asDefaults = GCore.Duplicate(mValue);
		gThis.SetValue(mValue);
		gThis.m_gSelectedDatagrid.LoadData();
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._InitDatagrid();
			if (gThis.m_bRepeatable) {
				gThis._InitSelectedDatagrid();
				gThis.Populate(gThis.m_oOptions.asDefaults);
			}
			else {
				gThis.Populate(gThis.m_oOptions.sDefault);
			}
			gThis.m_bShown = true;
		}
	};
	
	gThis._ProcessProduct = function(oProduct) {
		return oProduct;
	};
	
	gThis._ProcessSelectedProduct = function(oProduct) {
		oProduct = gThis.m_fProcessProduct(oProduct);
		if (oProduct.thumb != '') {
			oProduct.name = '<a title="" href="' + oProduct.thumb + '" class="show-thumb"><img src="' + GCore.DESIGN_PATH + '_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" /></a> '+ oProduct.name + ((oProduct.ean != '') ? '<br /><small>EAN: ' + oProduct.ean + '</small>' : '');
		}else{
			oProduct.name = '<img style="opacity: 0.2;vertical-align: middle;" src="' + GCore.DESIGN_PATH + '_images_panel/icons/datagrid/details.png" style="vertical-align: middle;" /> '+ oProduct.name + ((oProduct.ean != '') ? '<br /><small>EAN: ' + oProduct.ean + '</small>' : '');
		}
		return oProduct;
	};
	
	gThis._InitColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT,
				width: GF_Datagrid.WIDTH_AUTO,
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			appearance: {
				width: 90
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_weight = new GF_Datagrid_Column({
			id: 'weight',
			caption: 'Waga',
			appearance: {
				width: 70
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_producer = new GF_Datagrid_Column({
			id: 'producer',
			caption: GForm.Language.product_select_producer,
			appearance: {
				width: 150
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: gThis.m_oOptions.oParsedFilterData['producer'],
			}
		});
		
		var column_category = new GF_Datagrid_Column({
			id: 'categoriesname',
			caption: GForm.Language.product_select_categories,
			appearance: {
				width: 200,
				align: GF_Datagrid.ALIGN_LEFT
			},
			filter: {
				type: GF_Datagrid.FILTER_TREE,
				filtered_column: 'ancestorcategoryid',
				options: gThis.m_oOptions.oParsedFilterData['categoryid'],
				load_children: gThis.m_oOptions.fLoadCategoryChildren
			}
		});
		
		return [
			column_id,
			column_name,
			column_category,
			column_producer,
			column_price,
			column_weight,
		];
		
	};
	
	gThis._InitAdvancedColumns = function() {
		
		var column_id = new GF_Datagrid_Column({
			id: 'id',
			caption: GForm.Language.product_select_id,
			appearance: {
				width: 40,
				visible: false
			}
		});
		
		var column_idproduct = new GF_Datagrid_Column({
			id: 'idproduct',
			caption: GForm.Language.product_select_product_id,
			appearance: {
				width: 40,
				visible: false
			}
		});
		
		var column_name = new GF_Datagrid_Column({
			id: 'name',
			caption: GForm.Language.product_select_name,
			appearance: {
				align: GF_Datagrid.ALIGN_LEFT
			}
		});
		
		var column_barcode = new GF_Datagrid_Column({
			id: 'barcode',
			caption: GForm.Language.product_select_barcode,
			appearance: {
				width: 60,
				visible: false
			}
		});
		
		var column_variant = new GF_Datagrid_Column({
			id: 'variant',
			caption: GForm.Language.product_select_variant,
			selectable: true,
			appearance: {
				width: 140
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'sellprice',
			caption: GForm.Language.product_select_price,
			editable: true,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_RIGHT
			}
		});
		
		var column_price_gross = new GF_Datagrid_Column({
			id: 'sellprice_gross',
			caption: GForm.Language.product_select_price_gross,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_weight = new GF_Datagrid_Column({
			id: 'weight',
			caption: 'Waga',
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_weight_total = new GF_Datagrid_Column({
			id: 'weight_total',
			caption: 'Waga w sumie',
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER,
				visible: false
			}
		});
		
		var column_quantity = new GF_Datagrid_Column({
			id: 'quantity',
			caption: GForm.Language.product_select_quantity,
			editable: true,
			appearance: {
			width: 50
		}
		});
		
		var column_stock = new GF_Datagrid_Column({
			id: 'stock',
			caption: GForm.Language.product_variants_editor_stock,
			appearance: {
				width: 80
			}
		});
		
		var column_net_subsum = new GF_Datagrid_Column({
			id: 'net_subsum',
			caption: GForm.Language.product_select_net_subsum,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_vat = new GF_Datagrid_Column({
			id: 'vat',
			caption: GForm.Language.product_select_vat,
			appearance: {
				width: 50,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_vat_value = new GF_Datagrid_Column({
			id: 'vat_value',
			caption: GForm.Language.product_select_vat_value,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_subsum = new GF_Datagrid_Column({
			id: 'subsum',
			caption: GForm.Language.product_select_subsum,
			appearance: {
				width: 70,
				align: GF_Datagrid.ALIGN_CENTER
			}
		});
		
		var column_variant_options = new GF_Datagrid_Column({
			id: 'variant_options',
			caption: 'Warianty',
			appearance: {
				width: 70,
				visible: false
			}
		});
		
		return [
			column_id,
			column_idproduct,
			column_name,
			column_barcode,
			column_variant,
			column_price,
			column_price_gross,
			column_weight,
			column_weight_total,
			column_quantity,
			column_stock,
			column_net_subsum,
			column_vat,
			column_vat_value,
			column_subsum,
			column_variant_options
		];
		
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_fProcessProduct = gThis._ProcessProduct;
		gThis.m_fLoadProducts = gThis.m_oOptions.fLoadProducts;
		var aoColumns = gThis._InitColumns();
		
    var oOptions = {
			id: gThis.GetId(),
			appearance: {
				column_select: false
			},
			mechanics: {
				rows_per_page: 15,
				key: 'idproduct',
				only_one_selected: !gThis.m_bRepeatable,
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: gThis.m_fLoadProducts,
				process: gThis.m_fProcessProduct,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect//,
			},
			columns: aoColumns
    };
    
    gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		
	};
	
	gThis._Deselect = function(iDg, mId) {
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			gThis.m_gDataProvider.DeleteRow(mId[i]);
		}
		gThis.m_gSelectedDatagrid.ClearSelection();
		gThis.m_gSelectedDatagrid.LoadData();
	};
	
	gThis._CalculateRow = function(oRow) {
		oRow.quantity = isNaN(parseFloat(oRow.quantity)) ? 0 : parseFloat(oRow.quantity);
		oRow.sellprice = oRow.sellprice.replace(/,/, '.');
		oRow.sellprice = isNaN(parseFloat(oRow.sellprice)) ? 0 : parseFloat(oRow.sellprice);
		oRow.vat = isNaN(parseFloat(oRow.vat)) ? 0 : parseFloat(oRow.vat);
		var fPrice = parseFloat(oRow.sellprice);
		oRow.net_subsum = oRow.quantity * oRow.sellprice;
		oRow.weight_total = oRow.quantity * oRow.weight;
		oRow.sellprice_gross = oRow.sellprice * (1 + (oRow.vat / 100));
		oRow.sellprice_gross = oRow.sellprice_gross.toFixed(2);
		oRow.vat_value = oRow.net_subsum * (oRow.vat / 100);
		oRow.subsum = (oRow.net_subsum + oRow.vat_value).toFixed(2);
		oRow.sellprice = oRow.sellprice.toFixed(4);
		oRow.net_subsum = oRow.net_subsum.toFixed(2);
		oRow.weight_total = oRow.weight_total.toFixed(2);
		oRow.vat = oRow.vat.toFixed(2) + '%';
		oRow.vat_value = oRow.vat_value.toFixed(2);
		return oRow;
	};
	
	gThis._CalculateTotal = function(aoRows) {
		var net_subsum = 0;
		var vat_value = 0;
		var subsum = 0;
		var weight = 0;
		for (var i in aoRows) {
			net_subsum += parseFloat(aoRows[i].net_subsum);
			vat_value += parseFloat(aoRows[i].vat_value);
			subsum += parseFloat(aoRows[i].subsum);
			weight += parseFloat(aoRows[i].weight_total);
		}
		return {
			name: 'Suma',
			net_subsum: net_subsum.toFixed(2),
			vat_value: vat_value.toFixed(2),
			subsum: subsum.toFixed(2),
			weight: weight.toFixed(2),
		};
	};
	
	gThis._OnRowChange = GEventHandler(function(eEvent) {
		if ((eEvent.modified_row.variant != eEvent.previous_row.variant) && (eEvent.modified_row.variant != '')) {
			eval('var aoVariants = ' + eEvent.modified_row.variant__options + ';');
			for (var i in aoVariants) {
				if (aoVariants[i].id == eEvent.modified_row.variant) {
					eEvent.modified_row.sellprice = aoVariants[i].options.price;
					eEvent.modified_row.weight = aoVariants[i].options.weight;
					eEvent.modified_row.stock = aoVariants[i].options.stock;
					eEvent.modified_row.thumb = aoVariants[i].options.thumb;
					eEvent.modified_row.ean = aoVariants[i].options.ean;
				}
			}
		}
	});
	
	gThis._InitSelectedDatagrid = function() {
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'id',
			preProcess: gThis._CalculateRow,
			additional_rows: [
				new GF_Datagrid_Row({
					id: 'total',
					className: 'total',
					source: gThis._CalculateTotal,
					caption: GForm.Language.product_select_sum
				})
			],
			event_handlers: {
				change: gThis._OnChange,
				row_change: gThis._OnRowChange
			}
		}, []);
		
		var aoColumns = gThis._InitAdvancedColumns();
		
		var gActionDeselect = new GF_Action({
			img: gThis._GetImage('DeselectIcon'),
			caption: GForm.Language.product_select_deselect,
			action: gThis._Deselect
		});
		
    var oOptions = {
			id: gThis.GetId() + '_selected',
			appearance: {
				column_select: false,
				footer: false,
				filter: false
			},
			mechanics: {
				rows_per_page: 99999,
				key: 'id',
				no_column_modification: true,
				persistent: false
			},
			event_handlers: {
				load: function(oRequest, sResponseHandler) {
					if (gThis.m_bFirstLoad) {
						gThis.m_bFirstLoad = false;
						gThis._LoadSelected(oRequest, sResponseHandler);
					}
					else {
						gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
					}
				},
				update_row: function(sId, oRow) {
					gThis.m_gDataProvider.UpdateRow(sId, oRow);
					gThis.m_gSelectedDatagrid.LoadData();
				},
				process: gThis._ProcessSelectedProduct,
				loaded: gThis.m_oOptions.fOnChange
			},
			columns: aoColumns,
			row_actions: [
				gActionDeselect
			],
			context_actions: [
				gActionDeselect
			],
			group_actions: [
				gActionDeselect
			]
    };
		
		gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);
		
	};
	
	gThis.AddProducts = function(mIds) {
		if (!(mIds instanceof Array)) {
			if (mIds == undefined) {
				return;
			}
			mIds = [mIds];
		}
		var oRequest = GCore.Duplicate(gThis.m_oRequest, true);
		oRequest.where = [{
			column: 'idproduct',
			value: mIds,
			operator: 'IN'
		}];
		gThis.m_fLoadProducts(oRequest, GCallback(function(eEvent) {
			for (var j in eEvent.rows) {
				gThis._AddRow(eEvent.rows[j]);
			}
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
	gThis._LoadSelected = function(oRequest, sResponseHandler) {
		gThis.m_oRequest = oRequest;
		var asDefaults = [];
		for (var i in gThis.m_oOptions.asDefaults) {
			asDefaults.push(gThis.m_oOptions.asDefaults[i].idproduct);
		}
		oRequest.where = [{
			column: 'idproduct',
			value: asDefaults,
			operator: 'IN'
		}];
		gThis.m_fLoadProducts(oRequest, GCallback(function(eEvent) {
			var aoRows = [];
			for (var i in gThis.m_oOptions.asDefaults) {
				var sId = gThis.m_oOptions.asDefaults[i].idproduct;
				for (var j in eEvent.rows) {
					if (eEvent.rows[j].idproduct == sId) {
						aoRows.push($.extend(true, {id: i}, eEvent.rows[j], gThis.m_oOptions.asDefaults[i]));
						break;
					}
				}
			}
			gThis.m_gDataProvider.ChangeData(aoRows);
			gThis.m_gSelectedDatagrid.LoadData();
		}));
	};
	
}, oDefaults);


/*
* PRODUCT VARIANTS EDITOR
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-product-variants-editor',
		sFieldSpanClass: 'field',
		sGroupClass: 'group',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting'
	},
	oImages: {
		sDeleteIcon: '_images_panel/icons/datagrid/delete.png',
		sSaveIcon: '_images_panel/icons/datagrid/save.png',
		sAddIcon: '_images_panel/icons/datagrid/add.png'
	},
	aoOptions: [],
	asDefaults: '',
	aoRules: [],
	sComment: '',
	fGetSetsForCategories: GCore.NULL,
	fGetCartesian: GCore.NULL,
	fGetAttributesForSet: GCore.NULL,
	fGetValuesForAttribute: GCore.NULL,
	fAddAttribute: GCore.NULL,
	fAddValue: GCore.NULL,
	sCategoryField: '',
	sPriceField: '',
	sVatField: '',
	sCurrency: 'PLN'
};

var GFormProductVariantsEditor = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bShown = false;
	
	gThis.m_fLoadProducts;
	gThis.m_fProcessProduct;
	gThis.m_jDatagrid;
	gThis.m_gDatagrid;
	gThis.m_gDataProvider;
	gThis.m_jSetSelect;
	gThis.m_jSetSelectNode;
	gThis.m_jSetEditor;
	gThis.m_jSetEditorLabel;
	gThis.m_jVariantEditor;
	gThis.m_jVariantEditorWrapper;
	gThis.m_jVariantEditorOptions;
	gThis.m_aoSets = [];
	gThis.m_aoAttributes = [];
	gThis.m_bFirstLoad = true;
	gThis.m_aoVariants = [];
	gThis.m_sEditedVariant = GCore.NULL;
	gThis.m_mDataToPopulate;
	
	gThis.GetValue = function(sRepetition) {
		if (gThis.m_jField == undefined) {
			return '';
		}
	};
	
	gThis.SetValue = function(mValue, sRepetition) {
		if (gThis.m_jField == undefined) {
			return;
		}
	};
	
	gThis._OnSelect = function(gDg, sId) {
		gThis._InitVariantEditor(sId, gDg.GetRow(sId));
	};
	
	gThis._OnDeselect = function(gDg, sId) {
		gThis.SaveVariant();
		gThis.m_sEditedVariant = GCore.NULL;
		gThis.m_jVariantEditor.empty();
		gThis.m_jVariantEditorOptions.find('.save').fadeOut(150);
	};
	
	gThis._UpdatePrice = function(sModifier, fModifierValue) {
		if (sModifier == undefined) {
			sModifier = $('#' + gThis.GetId() + '__modifier_type option:selected').text();
		}
		if (fModifierValue == undefined) {
			fModifierValue = parseFloat($('#' + gThis.GetId() + '__modifier_value').val().replace(/,/, '.'));
		}
		else {
			fModifierValue = parseFloat(('' + fModifierValue).replace(/,/, '.'));
		}
		var fBasePrice = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sPriceField).GetValue().replace(/,/, '.'));
		fModifierValue = isNaN(fModifierValue) ? 0 : fModifierValue;
		fBasePrice = isNaN(fBasePrice) ? 0 : fBasePrice;
		var fPrice = 0;
		switch (sModifier) {
			case '%':
				fPrice = fBasePrice * (fModifierValue / 100);
				break;
			case '+':
				fPrice = fBasePrice + fModifierValue;
				break;
			case '-':
				fPrice = fBasePrice - fModifierValue;
				break;
			case '=':
				fPrice = fModifierValue;
				break;
		}
		$('#' + gThis.GetId() + '__net_price').val(fPrice.toFixed(4)).attr("disabled", "disabled");
		var iVatId = parseInt(gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jNode.find('option:selected').val());
		var fVatvalue = 0;
		if (gThis.m_oOptions.aoVatValues[iVatId] != undefined) {
			fVatvalue = parseFloat(gThis.m_oOptions.aoVatValues[iVatId]);
		}
		fVatvalue = isNaN(fVatvalue) ? 0 : fVatvalue;
		var fGrossPrice = fPrice * (1 + fVatvalue / 100);
		$('#' + gThis.GetId() + '__gross_price').val(fGrossPrice.toFixed(4)).attr("disabled", "disabled");
	};
	
	gThis._UpdateModificatorValue = function(fPrice) {
		var sModifier = $('#' + gThis.GetId() + '__modifier_type option:selected').text();
		if (fPrice == undefined) {
			fPrice = parseFloat($('#' + gThis.GetId() + '__net_price').val().replace(/,/, '.'));
		}
		else {
			fPrice = parseFloat(('' + fPrice).replace(/,/, '.'));
		}
		var fBasePrice = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sPriceField).GetValue().replace(/,/, '.'));
		fPrice = isNaN(fPrice) ? 0 : fPrice;
		fBasePrice = isNaN(fBasePrice) ? 0 : fBasePrice;
		var fModifierValue = 0;
		if (fBasePrice > 0) {
			switch (sModifier) {
				case '%':
					fModifierValue = (fPrice / fBasePrice) * 100;
					break;
				case '+':
					fModifierValue = fPrice - fBasePrice;
					break;
				case '-':
					fModifierValue = fBasePrice - fPrice;
					break;
				case '=':
					fModifierValue = fPrice;
					break;
			}
		}
		$('#' + gThis.GetId() + '__modifier_value').val(fModifierValue.toFixed(4));
	};
	
	
	gThis.Validation = GEventHandler(function(eEvent) {
		var fValue = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
		if (isNaN(fValue) || fValue < 0) {
			fValue = 0;
		}
		$(eEvent.currentTarget).val(fValue.toFixed(4));
	});
	
	gThis._InitVariantEditor = function(sId, oVariant) {
		if (oVariant == undefined) {
			oVariant = gThis._GetDefaultVariant(sId);
		}
		gThis.m_sEditedVariant = sId;
		gThis.m_jVariantEditorOptions.find('.save').fadeIn(150);
		gThis.m_jVariantEditor.empty().css('display', 'none');
		
		var jSpecification = $('<div class="specification"/>');
		
		gThis.m_jVariantEditor.append(jSpecification);
		
		var fBasePrice = parseFloat(gThis.m_gForm.GetField(gThis.m_oOptions.sPriceField).GetValue().replace(/,/, '.'));
		fBasePrice = isNaN(fBasePrice) ? 0 : fBasePrice;
		var jBasePrice = $('<input type="text" disabled="disabled" value="' + fBasePrice.toFixed(4) + '"/>').css('cursor', 'default');
		jSpecification.append($('<div class="field-text"/>').append('<label>' + GForm.Language.product_variants_editor_variant_editor_base_net_price + '</label>').append($('<span class="field"/>').append(jBasePrice)));
		
		var iVatId = parseInt(gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jNode.find('option:selected').val());
		var fVatvalue = 0;
		if (gThis.m_oOptions.aoVatValues[iVatId] != undefined) {
			fVatvalue = parseFloat(gThis.m_oOptions.aoVatValues[iVatId]);
		}
		var fGrossPrice = fBasePrice * (1 + fVatvalue / 100);
		var jBaseGrossPrice = $('<input type="text" disabled="disabled" value="' + fGrossPrice.toFixed(4) + '"/>').css('cursor', 'default');
		jSpecification.append($('<div class="field-text"/>').append('<label>' + GForm.Language.product_variants_editor_variant_editor_base_gross_price + '</label>').append($('<span class="field"/>').append(jBaseGrossPrice)));
		
		var jModifierType = $('<select id="' + gThis.GetId() + '__modifier_type"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		for (var i = 0; i < gThis.m_oOptions.aoSuffixes.length; i++) {
			jModifierType.append('<option' + ((gThis.m_oOptions.aoSuffixes[i]['symbol'] == oVariant.modifier_type) ? ' selected="selected"' : '') + ' value="' + gThis.m_oOptions.aoSuffixes[i].id + '">' + gThis.m_oOptions.aoSuffixes[i].symbol + '</option>');
		}
		jSpecification.append($('<div class="field-select"/>').append('<label for="' + gThis.GetId() + '__modifier_type">' + GForm.Language.product_variants_editor_variant_editor_modifier_type + '</label>').append($('<span class="field"/>').append(jModifierType)));
		jModifierType.GSelect();
		jModifierType.change(GEventHandler(function(eEvent) {
			gThis._UpdatePrice();
		}));
		
		var jModifierValue = $('<input type="text" id="' + gThis.GetId() + '__modifier_value"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__modifier_value">' + GForm.Language.product_variants_editor_variant_editor_modifier_value + '</label>').append($('<span class="field"/>').append(jModifierValue)));
		
		var fHandler = GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
			setTimeout(function() {
				gThis._UpdatePrice(undefined, $(eEvent.currentTarget).val());
			}, 5);
		});
		
		jModifierValue.val(oVariant.modifier_value).keypress(fHandler).blur(fHandler).blur(gThis.Validation);
		
		var jPrice = $('<input type="text" id="' + gThis.GetId() + '__net_price"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__price">' + GForm.Language.product_variants_editor_variant_editor_net_price + '</label>').append($('<span class="field"/>').append(jPrice)));
		
		var jGrossPrice = $('<input type="text" id="' + gThis.GetId() + '__gross_price"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__price">' + GForm.Language.product_variants_editor_variant_editor_gross_price + '</label>').append($('<span class="field"/>').append(jGrossPrice)));
		
		fHandler = GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
			setTimeout(function() {
				var fNetPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
				fNetPrice = isNaN(fNetPrice) ? 0 : fNetPrice;
				var iVatId = parseInt(gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jNode.find('option:selected').val());
				var fVatvalue = 0;
				if (gThis.m_oOptions.aoVatValues[iVatId] != undefined) {
					fVatvalue = parseFloat(gThis.m_oOptions.aoVatValues[iVatId]);
				}
				fVatvalue = isNaN(fVatvalue) ? 0 : fVatvalue;
				var fGrossPrice = fNetPrice * (1 + fVatvalue / 100);
				jGrossPrice.val(fGrossPrice.toFixed(4));
				gThis._UpdateModificatorValue(fNetPrice);
			}, 5);
		});
		jPrice.keypress(fHandler).blur(fHandler).blur(gThis.Validation);
		
		fHandler = GEventHandler(function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
			setTimeout(function() {
				var fGrossPrice = parseFloat($(eEvent.currentTarget).val().replace(/,/, '.'));
				fGrossPrice = isNaN(fGrossPrice) ? 0 : fGrossPrice;
				var iVatId = parseInt(gThis.m_gForm.GetField(gThis.m_oOptions.sVatField).m_jNode.find('option:selected').val());
				var fVatvalue = 0;
				if (gThis.m_oOptions.aoVatValues[iVatId] != undefined) {
					fVatvalue = parseFloat(gThis.m_oOptions.aoVatValues[iVatId]);
				}
				fVatvalue = isNaN(fVatvalue) ? 0 : fVatvalue;
				var fNetPrice = fGrossPrice / (1 + fVatvalue / 100);
				jPrice.val(fNetPrice.toFixed(4));
				gThis._UpdateModificatorValue(fNetPrice);
			}, 5);
		});
		jGrossPrice.keypress(fHandler).blur(fHandler).blur(gThis.Validation);
		
		var jStock = $('<input type="text" id="' + gThis.GetId() + '__stock"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__stock">' + GForm.Language.product_variants_editor_variant_editor_stock + '</label>').append($('<span class="field"/>').append(jStock)));
		jStock.val(oVariant.stock);
		
		jStock.keypress(function(eEvent){
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
		});
		
		var jSymbol = $('<input type="text" id="' + gThis.GetId() + '__symbol"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__symbol">Symbol</label>').append($('<span class="field"/>').append(jSymbol)));
		jSymbol.val(oVariant.symbol);
		
		jSymbol.keypress(function(eEvent){
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
		});
		
		var jDeletable = $('<input type="hidden" id="' + gThis.GetId() + '__deletable"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append(jDeletable);
		jDeletable.val(oVariant.deletable);
		
		var jStatusType = $('<select id="' + gThis.GetId() + '__status"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jStatusType.append('<option' + ((1 == oVariant.status) ? ' selected="selected"' : '') + ' value="' + 1 + '">Aktywny</option>');
		jStatusType.append('<option' + ((0 == oVariant.status) ? ' selected="selected"' : '') + ' value="' + 0 + '">Nieaktywny</option>');
		jSpecification.append($('<div class="field-select"/>').append('<label for="' + gThis.GetId() + '__status">Status</label>').append($('<span class="field"/>').append(jStatusType)));
		jStatusType.GSelect();
		
		var jAvailablity = $('<select id="' + gThis.GetId() + '__availablity"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		for (var i in gThis.m_oOptions.aoAvailablity) {
			jAvailablity.append('<option' + ((i == oVariant.availablity) ? ' selected="selected"' : '') + ' value="' + i + '">' + gThis.m_oOptions.aoAvailablity[i] + '</option>');
		}
		jSpecification.append($('<div class="field-select"/>').append('<label for="' + gThis.GetId() + '__status">Dostępność</label>').append($('<span class="field"/>').append(jAvailablity)));
		jAvailablity.GSelect();
		
		var jWeight = $('<input type="text" id="' + gThis.GetId() + '__weight"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jSpecification.append($('<div class="field-text"/>').append('<label for="' + gThis.GetId() + '__weight">Waga</label>').append($('<span class="field"/>').append(jWeight)));
		jWeight.val(oVariant.weight);
		
		jWeight.keypress(function(eEvent){
			if (eEvent.keyCode == 13) {
				eEvent.preventDefault();
				eEvent.stopImmediatePropagation();
				gThis.SaveVariant();
			}
		});
		
		var jPhoto = $('<div class="attribute-photos" />');
		jPhoto.append('<h3>Wybierz zdjęcie wariantu</h3><input type="hidden" id="' + gThis.GetId() + '__photo" value="'+ oVariant.photo +'" />');
		for (var i = 0; i < gThis.m_oOptions.aoPhotos.length; i++) {
			//jPhoto.append('<option' + ((gThis.m_oOptions.aoPhotos[i].id == oVariant.photo) ? ' selected="selected"' : '') + ' value="' + gThis.m_oOptions.aoPhotos[i].id + '">' + gThis.m_oOptions.aoPhotos[i].name + '</option>');
			jPhoto.append('<img' + ((gThis.m_oOptions.aoPhotos[i].id == oVariant.photo) ? ' class="selected"' : '') + ' id="'+ gThis.m_oOptions.aoPhotos[i].id +'" src="'+ gThis.m_oOptions.aoPhotos[i].thumb +'" />');
		}
		jSpecification.append(jPhoto);
		
		jPhoto.find('img').click(function(){
			if($(this).hasClass('selected')){
				jPhoto.find('img').removeClass('selected');
				var photoid = 0;
			}else{
				jPhoto.find('img').removeClass('selected');
				$(this).addClass('selected');
				var photoid = $(this).attr('id');
			}
			$('#' + gThis.GetId() + '__photo').val(photoid);
		});
		
		var asExistingAttributes = [];
		var jAttributes = $('<ul class="attributes"/>');
		for (var i in oVariant) {
			if (i.substr(0, 10) != 'attribute_') {
				continue;
			}
			asExistingAttributes.push(i.substr(10));
			jAttributes.append(gThis.AddEditorAttribute(i.substr(10), oVariant[i]));
		}
		
		var jNew = gThis._GetNewAttributeSelector(asExistingAttributes);
		if (jNew != GCore.NULL) {
			jAttributes.append(jNew);
		}
		
		gThis.m_jVariantEditor.append(jAttributes);
		
		gThis.m_jVariantEditor.slideDown(200);
		gThis._UpdatePrice();
	};
	
	gThis._GetNewAttributeSelector = function(asExistingAttributes) {
		var jLi = $('<li class="field-select new"/>');
		var jSelect = $('<select id="' + gThis.GetId() + '__attribute_new"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});;
		
		jSelect.append('<option value="">' + GForm.Language.product_variants_editor_choose_attribute + '</option>');
		var j = 0;
		for (var i = 0; i < gThis.m_aoAttributes.length; i++) {
			if ($.inArray(gThis.m_aoAttributes[i].id, asExistingAttributes) != -1) {
				continue;
			}
			jSelect.append('<option value="' + gThis.m_aoAttributes[i].id + '">' + gThis.m_aoAttributes[i].name + '</option>');
			j++;
		}
		if (j == 0) {
			return GCore.NULL;
		}
		jLi.append($('<span class="field"/>').append(jSelect));
		jSelect.GSelect();
		jSelect.change(function() {
			var sAttributeId = $(this).find('option:selected').attr('value');
			var sAttributeName = $(this).find('option:selected').text();
			if (sAttributeId == '') {
				return;
			}
			$(this).closest('ul').append(gThis.AddEditorAttribute(sAttributeId));
			var asExistingAttributes = gThis._MakeExistingAttributesList($(this));
			var jNew = gThis._GetNewAttributeSelector(asExistingAttributes);
			if (jNew != GCore.NULL) {
				$(this).closest('ul').append(jNew);
			}
			$(this).closest('li').remove();
		});
		return jLi;
	};
	
	gThis._MakeExistingAttributesList = function(jContext) {
		var asExistingAttributes = [];
		var jSelects = jContext.closest('ul').find('li select[name^="attribute_"]');
		for (var i = 0; i < jSelects.length; i++) {
			asExistingAttributes.push(jSelects.eq(i).attr('name').substr(10));
		}
		return asExistingAttributes;
	};
	
	gThis.AddEditorAttribute = function(sAttributeId, sValue) {
		var sAttributeName = '';
		for (var i in gThis.m_aoAttributes) {
			if (gThis.m_aoAttributes[i].id == sAttributeId) {
				sAttributeName = gThis.m_aoAttributes[i].name;
				aoValues = gThis.m_aoAttributes[i].values;
			}
		}
		var jLi = $('<li class="field-select"/>');
		var jSelect = $('<select id="' + gThis.GetId() + '__attribute_' + sAttributeId + '" name="attribute_' + sAttributeId + '"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});;
		
		for (var i = 0; i < aoValues.length; i++) {
			jSelect.append('<option' + ((aoValues[i].name == sValue) ? ' selected="selected"' : '') + ' value="' + aoValues[i].id + '">' + aoValues[i].name + '</option>');
		}
		jLi.append('<label for="' + gThis.GetId() + '__attribute_' + sAttributeId + '">' + sAttributeName + '</label>');
		jLi.append($('<span class="field"/>').append(jSelect));
		jSelect.GSelect();
		
		var jDelete = $('<a href="#" class="delete"/>');
		jDelete.click(function() {
			var jUl = $(this).closest('ul');
			jUl.children('.new').remove();
			$(this).closest('li').remove();
			var asExistingAttributes = gThis._MakeExistingAttributesList(jUl);
			var jNew = gThis._GetNewAttributeSelector(asExistingAttributes);
			if (jNew != GCore.NULL) {
				jUl.append(jNew);
			}
			return false;
		});
		jLi.append(jDelete.append('<img src="' + gThis._GetImage('DeleteIcon') + '" alt=""/>'));
		
		return jLi;
	};
	
	
	gThis.AddVariant = function(oVariant) {
		if (oVariant == undefined) {
			var sId = 'new-' + gThis.m_sRepetitionCounter++;
			gThis.m_gDataProvider.AddRow(gThis._GetDefaultVariant(sId));
		}
		
		return sId;
	};
	
	gThis.SaveVariant = function() {
		if (gThis.m_sEditedVariant == GCore.NULL) {
			return;
		}
		var oRow = {
			idvariant: gThis.m_sEditedVariant,
			modifier_type: $('#' + gThis.GetId() + '__modifier_type option:selected').text(),
			modifier_type_id: $('#' + gThis.GetId() + '__modifier_type option:selected').attr('value'),
			modifier_value: $('#' + gThis.GetId() + '__modifier_value').val(),
			photo: $('#' + gThis.GetId() + '__photo').val(),
			availablity: $('#' + gThis.GetId() + '__availablity').val(),
			stock: $('#' + gThis.GetId() + '__stock').val().replace(/,/, '.'),
			symbol: $('#' + gThis.GetId() + '__symbol').val(),
			status: $('#' + gThis.GetId() + '__status').val(),
			weight: $('#' + gThis.GetId() + '__weight').val().replace(/,/, '.'),
		};
		var jSelects = gThis.m_jVariantEditor.find('.attributes li select[name^="attribute_"]');
		for (var i = 0; i < jSelects.length; i++) {
			var sAttributeId = jSelects.eq(i).attr('name').substr(10);
			var sValueId = jSelects.eq(i).find('option:selected').attr('value');
			var sValueName = jSelects.eq(i).find('option:selected').text();
			oRow['attribute_' + sAttributeId] = sValueName;
			oRow['attributeid_' + sAttributeId] = sValueId;
		}
		gThis.m_gDataProvider.UpdateRow(gThis.m_sEditedVariant, oRow);
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		
		gThis.m_jField = $('<div/>');
		
		gThis._PrepareSetSelect();
		gThis._PrepareSetEditor();
		
		gThis.m_jDatagrid = $('<div/>');
		gThis.m_jNode.append(gThis.m_jDatagrid);
		gThis.m_jNode.append(gThis.m_jField);
		
	};
	
	gThis._PrepareVariantEditor = function() {
		gThis.m_jVariantEditor = $('<div class="variant-editor"/>');
		gThis.m_jVariantEditorWrapper = $('<div class="variant"/>');
		gThis.m_jVariantEditorWrapper.css('display', 'none');
		gThis.m_jVariantEditorWrapper.append(gThis.m_jVariantEditor);
		gThis.m_jVariantEditorOptions = $('<ul class="options"/>');
		gThis.m_jVariantEditorWrapper.append(gThis.m_jVariantEditorOptions);
		var jAdd = $('<a class="add button" href="#"/>');
		jAdd.append('<span><img src="' + gThis._GetImage('AddIcon') + '" alt=""/>' + GForm.Language.product_variants_editor_add_variant + '</span>');
		var jSave = $('<a class="save button" href="#"/>');
		jSave.append('<span><img src="' + gThis._GetImage('SaveIcon') + '" alt=""/>' + GForm.Language.product_variants_editor_save_variant + '</span>');
		var jGenerate = $('<a class="add button" href="#"/>');
		jGenerate.append('<span><img src="' + gThis._GetImage('AddIcon') + '" alt=""/>Generuj automatycznie</span>');
		if(gThis.m_oOptions.bAllowGenerate == 1){
			gThis.m_jVariantEditorOptions.append($('<li/>').append(jGenerate));
		}
		gThis.m_jVariantEditorOptions.append($('<li/>').append(jAdd));
		gThis.m_jVariantEditorOptions.append($('<li/>').append(jSave));
		gThis.m_jNode.append(gThis.m_jVariantEditorWrapper);
		jAdd.click(GEventHandler(function(eEvent) {
			var sId = gThis.AddVariant();
			gThis.m_gDatagrid.m_asSelected = [sId];
			gThis.SaveVariant();
			gThis._InitVariantEditor(sId);
			return false;
		}));
		jSave.click(GEventHandler(function(eEvent) {
			gThis.SaveVariant();
			return false;
		})).css('display', 'none');
		
		jGenerate.click(GEventHandler(function(eEvent) {
			var aoAttributes = [];
			$(".generate:checked").each(function() {
				aoAttributes.push(this.value);
			});
			if(aoAttributes.length){
				if(gThis.m_aoVariants.length > 0){
					var title = 'Automatyczne wygenerowanie wariantów';
					var msg = 'Wybranie tej opcji usunie wszystkie dotychczasowe dotychczasowe warianty i wygeneruje kombinacje na nowo. Czy jesteś pewien ?';
					var params = {};
					var func = function(p) {
						gThis.m_gDataProvider.ChangeData();
						gThis.m_gDatagrid.LoadData();
						gThis._GenerateCartesian(aoAttributes);
					};
			   		new GF_Alert(title, msg, func, true, params);
				}else{
					gThis._GenerateCartesian(aoAttributes);
				}
			}else{
				GError('Nie wybrano cech', 'Aby wygenerować automatycznie warianty musisz wybrać cechy zaznaczając checkbox obok ich nazwy.');
			}
			return false;
		}));
	};
	
	gThis._GenerateCartesian = function(aoAttributes){
		var sSetId = $('#variants_pane__variants__set').find('option:selected').val();
		gThis.m_oOptions.fGetCartesian({
			setid: sSetId,
			ids: aoAttributes
		}, GCallback(gThis._OnVariantsGenerated));
	};
	
	gThis._OnVariantsGenerated = GEventHandler(function(eEvent) {
		
		$.each(eEvent.variants, function(k,key){
			var sId = 'new-' + gThis.m_sRepetitionCounter++;
			var oRow = {
				idvariant: sId,
				modifier_type: '%',
				modifier_type_id: 1,
				modifier_value: 100,
				stock: 0,
				photo: 0,
				availablity: 0,
				symbol: '',
				status: 1,
				deletable: 1,
				weight: 0
			};
			$.each(key, function(a, attribute){
				oRow['attribute_' + attribute.sAttributeId] = attribute.sValueName;
				oRow['attributeid_' + attribute.sAttributeId] = attribute.sValueId;
			});
			gThis.m_gDataProvider.AddRow(oRow);
			gThis.m_gDatagrid.m_asSelected = [sId];
		});
		gThis.m_gDatagrid.LoadData();
		gThis.Update();
	});
	
	gThis._PrepareSetSelect = function() {
		jSetSelectField = $('<div class="field-select"/>');
		jSetSelectField.append('<label for="' + gThis.GetId() + '__set">' + GForm.Language.product_variants_editor_set_for_this_product + ' <small>' + GForm.Language.product_variants_editor_set_for_this_product_suffix + '</small></label>');
		gThis.m_jSetSelectNode = $('<span class="repetition"/>');
		jSetSelectField.append(gThis.m_jSetSelectNode);
		gThis._CreateSetSelectNode([]);
		gThis.m_jNode.append(jSetSelectField);
	};
	
	gThis._CreateSetSelectNode = function(aoOptions) {
		gThis.m_jSetSelect = $('<select id="' + gThis.GetId() + '__set" name="' + gThis.GetName() + '[set]"/>');
		gThis.m_jSetSelectNode.empty().append($('<span class="field"/>').append(gThis.m_jSetSelect));
		for (var i = 0; i < aoOptions.length; i++) {
			gThis.m_jSetSelect.append('<option' + ((aoOptions[i].id == gThis.m_oOptions.sSet) ? ' selected="selected"' : '') + ' value="' + aoOptions[i].id + '"' + (aoOptions[i].current_category ? ' class="strong"' : '') + '>' + aoOptions[i].name + '</option>');
		}
	};
	
	gThis.LoadSets = function() {
		gThis.m_jSetEditorLabel.css('display', 'none');
		gThis.m_jSetEditor.add(gThis.m_jDatagrid).add(gThis.m_jVariantEditorWrapper).animate({
			opacity: 0
		}, 250);
		var jWaiting = $('<span class="' + gThis._GetClass('Waiting') + '"/>');
		gThis.m_jSetSelect.closest('.' + gThis._GetClass('FieldSpan')).parent().find('.' + gThis._GetClass('Waiting')).remove();
		gThis.m_jSetSelect.closest('.' + gThis._GetClass('FieldSpan')).parent().append(jWaiting);
		jWaiting.css('display', 'none').fadeIn(250);
		var jCategories = $(gThis.m_gForm).find('input[name*="[' + gThis.m_oOptions.sCategoryField + '][]"]');
		var asCategories = [];
		for (var i = 0; i < jCategories.length; i++) {
			asCategories.push(jCategories.eq(i).val());
		}
		gThis.m_oOptions.fGetSetsForCategories({
			id: asCategories
		}, GCallback(gThis.OnSetsLoaded));
	};
	
	gThis.OnSetsLoaded = GEventHandler(function(eEvent) {
		gThis.m_aoSets = eEvent.sets;
		gThis.ReplaceSetSelect();
		gThis.m_jSetEditorLabel.fadeIn(250);
	});
	
	gThis.ReplaceSetSelect = function() {
		gThis._CreateSetSelectNode(gThis.m_aoSets);
		gThis.m_jSetSelect.GSelect();
		gThis.m_jSetSelect.closest('.' + gThis._GetClass('FieldSpan')).parent().find('.' + gThis._GetClass('Waiting')).fadeOut(250, function() {$(this).remove();});
		gThis.m_jSetSelect.change(GEventHandler(function() {
			gThis.LoadAttributes();
		})).change();
	};
	
	gThis._PrepareSetEditor = function() {
		gThis.m_jSetEditor = $('<ul class="set-editor"/>');
		gThis.m_jSetEditor.css('display', 'none');
		gThis.m_jSetEditorLabel = $('<h3/>');
		gThis.m_jSetEditorLabel.append('<span>' + GForm.Language.product_variants_editor_availble_attributes + '</span>');
		gThis.m_jSetEditorLabel.css('display', 'none');
		gThis.m_jNode.append(gThis.m_jSetEditorLabel);
		gThis.m_jNode.append(gThis.m_jSetEditor);
	};
	
	gThis.ReplaceSetEditor = function() {
		gThis.m_jSetEditor.empty();
		for (var i = 0; i < gThis.m_aoAttributes.length; i++) {
			var jAttribute = $('<li/>');
			jAttribute.append('<h4>' + gThis.m_aoAttributes[i].name + '</h4>');
			var jUl = $('<ul/>');
			for (var j = 0; j < gThis.m_aoAttributes[i].values.length; j++) {
				var oValue = gThis.m_aoAttributes[i].values[j];
				var jValue = $('<li><input type="checkbox" class="generate" value="'+ oValue.id + '" /> ' + oValue.name + '</li>');
				jUl.append(jValue);
			}
			var jSaveValue = $('<a class="save" href="#"/>').append('<img src="' + gThis._GetImage('SaveIcon') + '" alt="' + GForm.Language.product_variants_editor_save_value + '" title="' + GForm.Language.product_variants_editor_save_value + '"/>');
			var jValueField = $('<input class="value" type="text"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
			jValueField.bind('keydown', function(eEvent) {
				if (eEvent.keyCode == 13) {
					eEvent.stopImmediatePropagation();
					eEvent.preventDefault();
					$(this).closest('li').find('.save').trigger('click');
				}
			});
			jValueField.get(0).sAttributeId = gThis.m_aoAttributes[i].id;
			var jValueFieldWrapper = $('<span class="field-text"/>').append($('<span class="field">').append(jValueField)).hide();
			var jAddValue = $('<a class="add" href="#"/>').append('<img src="' + gThis._GetImage('AddIcon') + '" alt="' + GForm.Language.product_variants_editor_add_value + '" title="' + GForm.Language.product_variants_editor_add_value + '"/>');
			jAddValue.click(function() {
				$(this).closest('li').find('.add').hide();
				$(this).closest('li').find('.field-text').show();
				$(this).closest('li').find('.save').show();
				$(this).closest('li').find('.value').focus();
				return false;
			});
			jSaveValue.click(function() {
				var sValue = $(this).closest('li').find('.value').val();
				if ((sValue != undefined) && sValue.length) {
					gThis.AddValue(sValue, $(this).closest('li').find('.value').get(0).sAttributeId, gThis.m_jSetSelect.find('option:selected').attr('value'));
				}
				$(this).closest('li').find('.value').val('');
				$(this).closest('li').find('.field-text').hide();
				$(this).closest('li').find('.save').hide();
				$(this).closest('li').find('.add').show();
				$(this).closest('li').find('.add').focus();
				return false;
			}).hide();
			jUl.append($('<li class="new"/>').append(jAddValue).append(jValueFieldWrapper).append(jSaveValue));
			jAttribute.append(jUl);
			gThis.m_jSetEditor.append(jAttribute);
		}
		var jSaveAttribute = $('<a class="save" href="#"/>').append('<img src="' + gThis._GetImage('SaveIcon') + '" alt="' + GForm.Language.product_variants_editor_save_attribute + '" title="' + GForm.Language.product_variants_editor_save_attribute + '"/>');
		var jAttributeField = $('<input class="attribute" type="text"/>').focus(function() {$(this).closest('.field').addClass('focus');}).blur(function() {$(this).closest('.field').removeClass('focus');});
		jAttributeField.bind('keydown', function(eEvent) {
			if (eEvent.keyCode == 13) {
				eEvent.stopImmediatePropagation();
				eEvent.preventDefault();
				$(this).closest('h4').find('.save').trigger('click');
			}
		});
		var jAttributeFieldWrapper = $('<span class="field-text"/>').append($('<span class="field">').append(jAttributeField)).hide();
		var jAddAttribute = $('<a class="add" href="#"/>').append('<img src="' + gThis._GetImage('AddIcon') + '" alt="' + GForm.Language.product_variants_editor_add_attribute + '" title="' + GForm.Language.product_variants_editor_add_attribute + '"/>');
		jAddAttribute.click(function() {
			$(this).closest('h4').find('.add').hide();
			$(this).closest('h4').find('.field-text').show();
			$(this).closest('h4').find('.save').show();
			$(this).closest('h4').find('.attribute').focus();
			return false;
		});
		jSaveAttribute.click(function() {
			var sAttribute = $(this).closest('h4').find('.attribute').val();
			if ((sAttribute != undefined) && sAttribute.length) {
				gThis.AddAttribute(sAttribute, gThis.m_jSetSelect.find('option:selected').attr('value'));
			}
			$(this).closest('h4').find('.attribute').val('');
			$(this).closest('h4').find('.field-text').hide();
			$(this).closest('h4').find('.save').hide();
			$(this).closest('h4').find('.add').show();
			$(this).closest('h4').find('.add').focus();
			return false;
		}).hide();
		gThis.m_jSetEditor.append($('<li/>').append($('<h4/>').append(jAddAttribute).append(jAttributeFieldWrapper).append(jSaveAttribute)));
	};
	
	gThis.AddAttribute = function(sAttribute, sSet) {
		gThis.m_jSetEditor.animate({opacity: .5}, 250);
		gThis.m_jDatagrid.animate({opacity: .5}, 250);
		gThis.m_jVariantEditorWrapper.animate({opacity: .5}, 250);
		(!gThis.m_jSetEditorLabel.find('.' + gThis._GetClass('Waiting')).length) && gThis.m_jSetEditorLabel.append($('<span class="' + gThis._GetClass('Waiting') + '"/>').css('display', 'none').fadeIn(150));
		gThis.m_oOptions.fAddAttribute({
			attribute: sAttribute,
			set: sSet
		}, GCallback(gThis.OnAttributeAdded));
	};
	
	gThis.OnAttributeAdded = GEventHandler(function(eEvent) {
		gThis.LoadAttributes();
	});
	
	gThis.AddValue = function(sValue, sAttribute, sSet) {
		gThis.m_jSetEditor.animate({opacity: .5}, 250);
		gThis.m_jDatagrid.animate({opacity: .5}, 250);
		gThis.m_jVariantEditorWrapper.animate({opacity: .5}, 250);
		(!gThis.m_jSetEditorLabel.find('.' + gThis._GetClass('Waiting')).length) && gThis.m_jSetEditorLabel.append($('<span class="' + gThis._GetClass('Waiting') + '"/>').css('display', 'none').fadeIn(150));
		gThis.m_oOptions.fAddValue({
			value: sValue,
			attribute: sAttribute,
			set: sSet
		}, GCallback(gThis.OnValueAdded));
	};
	
	gThis.OnValueAdded = GEventHandler(function(eEvent) {
		gThis.LoadAttributes();
	});
	
	gThis.LoadAttributes = function() {
		gThis.m_jSetEditor.animate({opacity: .5}, 250);
		gThis.m_jDatagrid.animate({opacity: .5}, 250);
		gThis.m_jVariantEditorWrapper.animate({opacity: .5}, 250);
		(!gThis.m_jSetEditorLabel.find('.' + gThis._GetClass('Waiting')).length) && gThis.m_jSetEditorLabel.append($('<span class="' + gThis._GetClass('Waiting') + '"/>').css('display', 'none').fadeIn(150));
		var sSetId = gThis.m_jSetSelect.find('option:selected').attr('value');
		gThis.m_oOptions.fGetAttributesForSet({
			id: sSetId
		}, GCallback(gThis.OnAttributesLoaded));
	};
	
	gThis.OnAttributesLoaded = new GEventHandler(function(eEvent) {
		gThis.m_aoAttributes = eEvent.attributes;
		if (gThis.m_mDataToPopulate != undefined) {
			gThis.Populate(gThis.m_mDataToPopulate);
			delete gThis.m_mDataToPopulate;
		}
		gThis.ReplaceSetEditor();
		gThis._InitDatagrid();
		gThis.m_jSetEditorLabel.find('.' + gThis._GetClass('Waiting')).fadeOut(150, function() {$(this).remove();});
		gThis.m_jSetEditor.slideDown(250);
		gThis.m_jVariantEditorWrapper.slideDown(250);
		gThis.m_jSetEditor.animate({opacity: 1}, 250);
		gThis.m_jDatagrid.animate({opacity: 1}, 250);
		gThis.m_jVariantEditorWrapper.animate({opacity: 1}, 250);
	});
	
	gThis.OnReset = function() {
		gThis.m_bFirstLoad = true;
	};
	
	gThis.Populate = function(mValue) {
		if (mValue instanceof Object) {
			if (!gThis.m_aoAttributes.length) {
				gThis.m_mDataToPopulate = mValue;
			}
			var aoData = [];
			for (var i in mValue) {
				var sSuffixSymbol = '%';
				for (var j in gThis.m_oOptions.aoSuffixes) {
					if (gThis.m_oOptions.aoSuffixes[j].id == mValue[i].suffix) {
						sSuffixSymbol = gThis.m_oOptions.aoSuffixes[j].symbol;
					}
				}
				var oVariant = {
					idvariant: mValue[i].idvariant,
					modifier_type_id: mValue[i].suffix,
					modifier_type: sSuffixSymbol,
					modifier_value: mValue[i].modifier,
					stock: mValue[i].stock,
					availablity: mValue[i].availablity,
					photo: mValue[i].photo,
					symbol: mValue[i].symbol,
					status: mValue[i].status,
					deletable: mValue[i].deletable,
					weight: mValue[i].weight,
				};
				if (mValue[i]['attributes'] != undefined) {
					for (var j in mValue[i]['attributes']) {
						oVariant['attributeid_' + j] = mValue[i]['attributes'][j];
						for (var k in gThis.m_aoAttributes) {
							if (gThis.m_aoAttributes[k].id == j) {
								for (var l in gThis.m_aoAttributes[k]['values']) {
									if (gThis.m_aoAttributes[k]['values'][l].id == mValue[i]['attributes'][j]) {
										oVariant['attribute_' + j] = gThis.m_aoAttributes[k]['values'][l].name;
									}
								}
							}
						}
					}
				}
				aoData.push(oVariant);
			}
			gThis.m_aoVariants = aoData.slice(0);
			gThis.Update();
		}
		if (!(gThis.m_gDataProvider instanceof GF_Datagrid_Data_Provider)) {
			return;
		}
		gThis.Update();
	};
	
	gThis.OnShow = function() {
		if (!gThis.m_bShown) {
			gThis._PrepareVariantEditor();
			gThis.Populate(gThis.m_oOptions.asDefaults);
			gThis.m_jSetSelect.GSelect();
			gThis.m_bShown = true;
		}
		else {
			if (gThis.m_gDataProvider instanceof GF_Datagrid_Data_Provider) {
				gThis.m_gDataProvider._ProcessAllRows();
				gThis.m_gDatagrid.LoadData();
			}
		}
		gThis.LoadSets();
	};
	
	gThis._InitColumns = function(aoAttributeColumns) {
		
		if (aoAttributeColumns == undefined) {
			aoAttributeColumns = [];
		}
		
		var column_id = new GF_Datagrid_Column({
			id: 'idvariant',
			caption: GForm.Language.product_variants_editor_id,
			appearance: {
				width: 40,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_deletable = new GF_Datagrid_Column({
			id: 'deletable',
			caption: GForm.Language.product_variants_editor_id,
			appearance: {
				width: 40,
				visible: false
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_stock = new GF_Datagrid_Column({
			id: 'stock',
			caption: GForm.Language.product_variants_editor_stock,
			appearance: {
				width: 50
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_symbol = new GF_Datagrid_Column({
			id: 'symbol',
			caption: 'Symbol',
			appearance: {
				width: 170
			},
			filter: {
				type: GF_Datagrid.FILTER_INPUT
			}
		});
		
		var column_status = new GF_Datagrid_Column({
			id: 'status',
			caption: 'Status',
			appearance: {
				width: 40
			},
			filter: {
				type: GF_Datagrid.FILTER_SELECT,
				options: [
					{id: '', caption: ''}, {id: '1', caption: 'Aktywny'}, {id: '0', caption: 'Nieaktywny'}
				],
			}
		});
		
		var column_weight = new GF_Datagrid_Column({
			id: 'weight',
			caption: 'Waga',
			appearance: {
				width: 60
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var column_modifier = new GF_Datagrid_Column({
			id: 'modifier',
			caption: GForm.Language.product_variants_editor_modifier,
			appearance: {
				width: 70
			}
		});
		
		var column_price = new GF_Datagrid_Column({
			id: 'price',
			caption: GForm.Language.product_variants_editor_price,
			appearance: {
				width: 70
			},
			filter: {
				type: GF_Datagrid.FILTER_BETWEEN
			}
		});
		
		var aoColumns = [column_id];
		aoColumns = aoColumns.concat(aoAttributeColumns, [
			column_stock,
			column_symbol,
			column_status,
			column_weight,
			column_modifier,
			column_price,
			column_deletable
		]);
		
		return aoColumns;
		
	};
	
	gThis.DeleteVariant = function(iDg, mId) {
		
		if (!(mId instanceof Array)) {
			mId = [mId];
		}
		for (var i = 0; i < mId.length; i++) {
			var oRow = gThis.m_gDataProvider.GetRow(mId);
			if(oRow.deletable == 1){
				if (mId == gThis.m_sEditedVariant) {
					gThis.m_sEditedVariant = GCore.NULL;
					gThis.m_jVariantEditor.empty();
					gThis.m_jVariantEditorOptions.find('.save').fadeOut(150);
				}
				gThis.m_gDataProvider.DeleteRow(mId[i]);
			}else{
				GError('Nie można skasować wariantu', 'Wybrany wariant występuje w zamówieniach. Możesz go tylko wyłączyć aby nie był wyświetlany w sklepie.');
			}
		}
		gThis.m_gDatagrid.LoadData();
	};
	
	gThis._GetDefaultVariant = function(sId) {
		var sSuffixId = '';
		for (var i in gThis.m_oOptions.aoSuffixes) {
			if (gThis.m_oOptions.aoSuffixes[i].symbol == '%') {
				sSuffixId = gThis.m_oOptions.aoSuffixes[i].id;
			}
		}
		return {
			idvariant: sId,
			modifier_type: '%',
			modifier_type_id: sSuffixId,
			modifier_value: '100.00',
			stock: '0',
			symbol: '',
			status: 1,
			deletable: 1,
			weight: 0
		}
	};
	
	gThis.AddVariant = function(oVariant) {
		if (oVariant == undefined) {
			var sId = 'new-' + gThis.m_sRepetitionCounter++;
			gThis.m_gDataProvider.AddRow(gThis._GetDefaultVariant(sId));
		}
		else {
			var sId = oVariant.idvariant;
			gThis.m_gDataProvider.AddRow(oVariant);
		}
		gThis.m_gDatagrid.LoadData();
		return sId;
	};
	
	gThis.Update = function() {
		gThis.m_jField.empty();
		if (gThis.m_gDataProvider instanceof GF_Datagrid_Data_Provider) {
			gThis.m_aoVariants = gThis.m_gDataProvider.GetData();
		}
		for (var i = 0; i < gThis.m_aoVariants.length; i++) {
			var oVariant = gThis.m_aoVariants[i];
			gThis.m_jField.append('<input value="' + oVariant['modifier_type_id'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][suffix]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['modifier_value'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][modifier]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['stock'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][stock]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['symbol'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][symbol]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['status'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][status]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['deletable'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][deletable]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['weight'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][weight]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['availablity'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][availablity]" type="hidden"/>');
			gThis.m_jField.append('<input value="' + oVariant['photo'] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][photo]" type="hidden"/>');
			for (var j in oVariant) {
				if (j.substr(0, 12) != 'attributeid_') {
					continue;
				}
				gThis.m_jField.append('<input value="' + oVariant[j] + '" name="' + gThis.GetName() + '[' + oVariant.idvariant + '][attributes][' + j.substr(12) + ']" type="hidden"/>');
			}
		}
	};
	
	gThis._PrepareColumnsFromAttributes = function() {
		var aoColumns = [];
		for (var i = 0; i < gThis.m_aoAttributes.length; i++) {
			var oAttribute = gThis.m_aoAttributes[i];
			var aoValues = [];
			for (var j = 0; j < oAttribute['values'].length; j++) {
				aoValues.push({
					id: oAttribute['values'][j]['name'],
					caption: oAttribute['values'][j]['name']
				});
			}
			aoColumns.push(new GF_Datagrid_Column({
				id: 'attribute_' + oAttribute['id'],
				caption: oAttribute['name'],
				appearance: {
					visible: i < 4
				},
				filter: {
					type: GF_Datagrid.FILTER_SELECT,
					options: [{id: '', caption: ''}].concat(aoValues)
				}
			}));
		}
		return aoColumns;
	};
	
	gThis.PreProcessVariants = function(oRow) {
		var sBasePrice = $(gThis.m_gForm).find('input[name*="[' + gThis.m_oOptions.sPriceField + ']"]').val();
		var iBasePrice = 0;
		if (!isNaN(parseFloat(sBasePrice)) && (parseFloat(sBasePrice) >= 0)) {
			iBasePrice = parseInt('' + (parseFloat(sBasePrice) * 100));
		}
		oRow.base_price = (iBasePrice / 100).toFixed(4);
		var iPrice = iBasePrice;
		oRow.modifier_value = (!isNaN(parseFloat(oRow.modifier_value))) ? parseFloat(oRow.modifier_value).toFixed(4) : '0.00';
		switch (oRow.modifier_type) {
			case '=':
				if (!isNaN(parseFloat(oRow.modifier_value)) && (parseFloat(oRow.modifier_value) >= 0)) {
					iPrice = parseInt('' + (parseFloat(oRow.modifier_value) * 100));
					oRow.modifier = '=' + parseFloat(oRow.modifier_value).toFixed(4);
				}
				break;
			case '%':
				if (!isNaN(parseFloat(oRow.modifier_value))) {
					iPrice = parseInt('' + (iPrice * (parseFloat(oRow.modifier_value) / 100)));
					oRow.modifier = parseFloat(oRow.modifier_value).toFixed(4) + '%';
				}
				break;
			case '+':
				if (!isNaN(parseFloat(oRow.modifier_value))) {
					iPrice += parseInt('' + (parseFloat(oRow.modifier_value) * 100));
					oRow.modifier = '+' + parseFloat(oRow.modifier_value).toFixed(4);
				}
				break;
			case '-':
				if (!isNaN(parseFloat(oRow.modifier_value))) {
					iPrice -= parseInt('' + (parseFloat(oRow.modifier_value) * 100));
					oRow.modifier = '-' + parseFloat(oRow.modifier_value).toFixed(4);
				}
				break;
		}
		var iDifference = iPrice - iBasePrice;
		oRow.price = (iPrice / 100).toFixed(4);
		return oRow;
	};
	
	gThis.ProcessVariant = function(oRow) {
		oRow.status = (oRow.status == 1) ? 'Aktywny':'Nieaktywny';
		return oRow;
	};
	
	gThis._InitDatagrid = function() {
		
		gThis.m_jDatagrid.empty().attr('class', '');
		
		gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
			key: 'idvariant',
			preProcess: gThis.PreProcessVariants,
			event_handlers: {
				change: GEventHandler(function(rows) {
					gThis.Update();
				})
			}
		}, gThis.m_aoVariants);
		
		var aoAttributeColumns = gThis._PrepareColumnsFromAttributes();
		
		var aoColumns = gThis._InitColumns(aoAttributeColumns);
		
    var oOptions = {
			id: gThis.GetId() + '_variants',
			mechanics: {
				rows_per_page: 150,
				key: 'idvariant',
				only_one_selected: true,
				persistent: false
			},
			event_handlers: {
				load: GEventHandler(function(oRequest, sResponseHandler) {
					gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
				}),
				process: gThis.ProcessVariant,
				delete_row: gThis.DeleteVariant,
				delete_group: gThis.DeleteVariant,
				select: gThis._OnSelect,
				deselect: gThis._OnDeselect
			},
			columns: aoColumns,
			row_actions: [
				GF_Datagrid.ACTION_DELETE
			],
			group_actions: [
				GF_Datagrid.ACTION_DELETE
			]
    };
		gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);
		
		gThis.Update();
		
	};
	
}, oDefaults);

/*
* REPETITION LANGUAGE
*/

var oDefaults = {
	sName: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	oImages: {
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	aoLanguages: [],
	aoFields: [],
	agFields: []
};

var GFormRepetitionLanguage = GCore.ExtendClass(GFormContainer, function(options) {
	
	var gThis = this;
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div class="' + gThis._GetClass('Repetition') + '"/>');
		var jFlag = $('<a href="#" class="flag-repetition" tabindex="-1"/>');
		$.each(options.aoLanguages,function(l,language){
			if(language.sValue == options.sName)
			{
			jFlag.append('<img src="' + GCore.DESIGN_PATH+"_images_common/icons/languages/"+language.sFlag + '" alt="' + language.sLabel + '" title="' + language.sLabel + '"/>');	
			}
		});
		
		gThis.m_jNode.append(jFlag);
		gThis.m_jNode.append(gThis.RenderChildren());
	};
	
}, oDefaults);

/*
* REPETITION
*/

var oDefaults = {
	sName: '',
	oClasses: {
		sRepetitionClass: 'GFormRepetition',
		sDeleteButtonClass: 'delete-repetition'
	},
	oImages: {
		sDelete: '_images_panel/buttons/small-delete.png',
		sAdd: '_images_panel/buttons/small-add.png'
	},
	aoFields: [],
	agFields: []
};

var GFormRepetition = GCore.ExtendClass(GFormContainer, function() {
	
	var gThis = this;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div class="' + gThis._GetClass('Repetition') + '"/>');
		var jDelete = $('<a href="#" class="' + gThis._GetClass('DeleteButton') + '"/>');
		jDelete.append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.delete_repetition + '" title="' + GForm.Language.delete_repetition + '"/>');
		gThis.m_jNode.append(jDelete);
		gThis.m_jNode.append(gThis.RenderChildren());
	};
	
}, oDefaults);


/*
* RIGHTS TABLE
*/

var oDefaults = {
	sName: '',
	sLabel: '',
	oClasses: {
		sFieldClass: 'field-rights-table',
		sFieldSpanClass: 'field',
		sPrefixClass: 'prefix',
		sSuffixClass: 'suffix',
		sFocusedClass: 'focus',
		sInvalidClass: 'invalid',
		sRequiredClass: 'required',
		sWaitingClass: 'waiting',
		sFieldRepetitionClass: 'repetition'
	},
	sDefault: '',
	aoRules: [],
	sComment: '',
	asControllers: [],
	asActions: []
};

var GFormRightsTable = GCore.ExtendClass(GFormField, function() {
	
	var gThis = this;
	
	gThis.m_bDontCheck = false;
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jNode.append(jLabel);
		gThis.m_jNode.append($('<span class="' + gThis._GetClass('FieldRepetition') + '"/>').append(gThis._PrepareTable()));
	};
	
	gThis._PrepareTable = function() {
		var jTr;
		var i;
		var j;
		var jTable = $('<table cellspacing="0"/>');
		var jHead = $('<thead/>');
		jTr = $('<tr/>');
		jTr.append('<th>&nbsp;</th>');
		for (i = 0; i < gThis.m_oOptions.asActions.length; i++) {
			jTr.append('<th>' + gThis.m_oOptions.asActions[i].name + '</th>');
		}
		jTr.append('<th class="all">' + GForm.Language.all_actions + '</th>');
		jHead.append(jTr);
		jTable.append(jHead);
		var jBody = $('<tbody/>');
		for (i = 0; i < gThis.m_oOptions.asControllers.length; i++) {
			jTr = $('<tr/>');
			jTr.append('<th scope="row">' + gThis.m_oOptions.asControllers[i].name + '</th>');
			for (j = 0; j < gThis.m_oOptions.asActions.length; j++) {
				jTr.append('<td><input type="checkbox" value="1" name="' + gThis.GetName() + '[' + gThis.m_oOptions.asControllers[i].id + '][' + gThis.m_oOptions.asActions[j].id + ']"/></td>');
			}
			jTr.append('<td class="all"><input type="checkbox"/></td>');
			jBody.append(jTr);
		}
		jTr = $('<tr class="all"/>');
		jTr.append('<th scope="row">' + GForm.Language.all_controllers + '</th>');
		for (j = 0; j < gThis.m_oOptions.asActions.length; j++) {
			jTr.append('<td><input type="checkbox"/></td>');
		}
		jTr.append('<td>&nbsp;</td>');
		jBody.append(jTr);
		jTable.append(jBody);
		return jTable;
	};
	
	gThis._Initialize = function() {
		gThis.m_jNode.find('tbody td, tbody th').hover(function() {
			$(this).closest('tr').addClass('active');
			var iIndex = $(this).closest('tr').find('td, th').index($(this).closest('td, th'));
			if (iIndex > 0) {
				$(this).closest('table').find('tr').each(function() {
					$(this).find('td, th').eq(iIndex).addClass('active');
				});
			}
		}, function() {
			$(this).closest('tr').removeClass('active');
			var iIndex = $(this).closest('tr').find('td, th').index($(this).closest('td, th'));
			if (iIndex > 0) {
				$(this).closest('table').find('tr').each(function() {
					$(this).find('td, th').eq(iIndex).removeClass('active');
				});
			}
		});
		gThis.m_jNode.find('td.all input').click(function() {
			if ($(this).is(':checked')) {
				$(this).closest('tr').checkCheckboxes();
			}
			else {
				$(this).closest('tr').unCheckCheckboxes();
			}
		});
		gThis.m_jNode.find('tr.all input').click(function() {
			var iIndex = $(this).closest('tr').find('td, th').index($(this).closest('td, th'));
			if ($(this).is(':checked')) {
				$(this).closest('table').find('tr').each(function() {
					$(this).find('td, th').eq(iIndex).checkCheckboxes();
				});
			}
			else {
				$(this).closest('table').find('tr').each(function() {
					$(this).find('td, th').eq(iIndex).unCheckCheckboxes();
				});
			}
		});
		gThis.m_jNode.find('input').not('.all input').click(gThis.OnInputChange).change(gThis.OnInputChange);
	};
	
	gThis.OnInputChange = function() {
		gThis._CheckHorizontal.apply(this);
		gThis._CheckVertical.apply(this);
	};
	
	gThis._CheckHorizontal = function() {
		if (gThis.m_bDontCheck) {
			return;
		}
		if ($(this).closest('tr').find('td:not(.all), th').find('input:not(:checked)').length) {
			$(this).closest('tr').find('.all').unCheckCheckboxes();
		}
		else {
			$(this).closest('tr').find('.all').checkCheckboxes();
		}
	};
	
	gThis._CheckVertical = function() {
		if (gThis.m_bDontCheck) {
			return;
		}
		var iIndex = $(this).closest('tr').find('td').index($(this).closest('td, th'));
		var jTrs = $(this).closest('table').find('tbody tr:not(.all)');
		var iLength = 0;
		for (var i = 0; i < jTrs.length; i++) {
			iLength += jTrs.eq(i).find('td:eq(' + iIndex + ') input:not(:checked)').length;
			if (iLength) {
				break;
			}
		}
		if (iLength) {
			$(this).closest('table').find('tbody tr.all td:eq(' + iIndex + ')').unCheckCheckboxes();
		}
		else {
			$(this).closest('table').find('tbody tr.all td:eq(' + iIndex + ')').checkCheckboxes();
		}
	};
	
	gThis.Populate = function(mData) {
		gThis.m_bDontCheck = true;
		for (var iController in mData) {
			for (var iAction in mData[iController]) {
				if (mData[iController][iAction]) {
					gThis.m_jNode.find('input[name="' + gThis.GetName() + '[' + iController + '][' + iAction + ']"]').parent().checkCheckboxes();
				}
			}
		}
		gThis.m_bDontCheck = false;
		gThis.OnShow();
	};
	
	gThis.OnReset = function() {
		gThis.m_bDontCheck = true;
		gThis.m_jNode.unCheckCheckboxes();
		gThis.m_bDontCheck = false;
		gThis.OnShow();
	};
	
	gThis.OnShow = function() {
		gThis.m_jNode.find('tbody tr:not(.all)').each(function() {
			$(this).find('td:eq(0) input:checked').each(gThis._CheckHorizontal);
		});
		gThis.m_jNode.find('tbody tr:eq(0)').each(function() {
			$(this).find('td:not(.all) input:checked').each(gThis._CheckVertical);
		});
	};
	
}, oDefaults);

/*
* LOADING
* Adds a loading indicator for the desired DOM element.
*/

var oDefaults = {
	oClasses: {
	},
	sBackground: '#fff',
	fOpacity: .75,
	iZIndex: 1001
};

var GLoading = function() {
	
	var gThis = this;
	
	gThis.m_jOverlay;
	gThis.m_jIcon;
	
	gThis._Constructor = function() {
		gThis.m_jOverlay = $('<div class="GLoading"/>').css({
			display: 'block',
			position: 'absolute',
			left: $(gThis).offset().left,
			top: $(gThis).offset().top,
			width: $(gThis).width(),
			height: $(gThis).height(),
			zIndex: gThis.m_oOptions.iZIndex,
			opacity: 0,
			background: gThis.m_oOptions.sBackground
		});
		gThis.m_jIcon = $('<div class="GLoading_icon"/>').css({
			display: 'block',
			position: 'absolute',
			left: $(gThis).offset().left,
			top: $(gThis).offset().top,
			width: $(gThis).width(),
			height: $(gThis).height(),
			zIndex: gThis.m_oOptions.iZIndex,
			opacity: 0
		});
		$('body').append(gThis.m_jOverlay).append(gThis.m_jIcon);
		gThis.m_jOverlay.animate({
			duration: 500,
			opacity: gThis.m_oOptions.fOpacity
		});
		gThis.m_jIcon.animate({
			duration: 500,
			opacity: 1
		});
		$(gThis).resize(GEventHandler(function(eEvent) {
			gThis.m_jOverlay.css({
				width: $(gThis).width(),
				height: $(gThis).height()
			});
			gThis.m_jIcon.css({
				width: $(gThis).width(),
				height: $(gThis).height()
			});
		}));
	};
	
	gThis.StopLoading = function() {
		gThis.m_jOverlay.stop(true, true).animate({
			duration: 500,
			opacity: 0
		}, function() {
			$(this).remove();
		});
		gThis.m_jIcon.stop(true, true).animate({
			duration: 500,
			opacity: 0
		}, function() {
			$(this).remove();
		});
		$(gThis).removeClass('GLoading');
	};
	
	gThis._Constructor();
	
};

GLoading.Stop = function(jNode) {
	return jNode.get(0).StopLoading();
};

GLoading.RemoveAll = function() {
	$('.GLoading, GLoading_icon').remove();
};

new GPlugin('GLoading', oDefaults, GLoading);

/*
* SEARCH
* Live Search 
*/

var oDefaults = {
	oClasses: {
	},
	sBackground: '#fff',
	fOpacity: .75,
	iZIndex: 1001,
	iDuration: 200,
	sDefaultText: '',
	sPlaceholder: 'live-search-results'
};

var GSearch = function() {
	
	var gThis = this;
	gThis._Constructor = function() {
		gThis.m_oOptions.sViewUrl = GCore.sAdminUrl+'mainside/confirm/';
		gThis.m_jInput = $(this);
		gThis.sLastValue = gThis.m_jInput.val();
		gThis.m_jInput.attr('autocomplete','off');
		gThis.m_jLiveSearch = $('<div>').attr('id',gThis.m_oOptions.sPlaceholder).appendTo(document.body).hide().slideUp(0);
		$(document.body).click(function(event){
			var clicked = $(event.target);
			if(!(clicked.is('#'+gThis.m_oOptions.sPlaceholder) || clicked.parents('#' + gThis.m_oOptions.sPlaceholder).length || clicked.is('input'))){
				gThis.m_jLiveSearch.slideUp(gThis.m_oOptions.iDuration);
			}
		});
		gThis.OnFocus();
		gThis.OnBlur();
		gThis.OnClick();
		
		gThis.m_jInput.typeWatch({callback: function(){
			gThis.OnTypingFinished();
		}});
	};
	
	gThis.RepositionLiveSearch = function() {
		var liveSearchPaddingBorderHoriz = parseInt(gThis.m_jLiveSearch.css('paddingLeft'), 10) + parseInt(gThis.m_jLiveSearch.css('paddingRight'), 10) + parseInt(gThis.m_jLiveSearch.css('borderLeftWidth'), 10) + parseInt(gThis.m_jLiveSearch.css('borderRightWidth'), 10);
		var tmpOffset = gThis.m_jInput.offset();
		var inputDim = {
			left: tmpOffset.left,
			top: tmpOffset.top,
			width: gThis.m_jInput.outerWidth(),
			height: gThis.m_jInput.outerHeight()
		};
		
		inputDim.topPos = inputDim.top + inputDim.height;
		inputDim.totalWidth = inputDim.width - liveSearchPaddingBorderHoriz;

		gThis.m_jLiveSearch.css({
			position:	'absolute',
			left:	inputDim.left+'px',
			top:	inputDim.topPos+'px',
			width:	inputDim.totalWidth+'px'
		});
	};
	
	gThis.ShowLiveSearch = function() {
		gThis.RepositionLiveSearch();	
		$(window).unbind('resize', gThis.RepositionLiveSearch).bind('resize', gThis.RepositionLiveSearch);
		gThis.m_jLiveSearch.slideDown(gThis.m_oOptions.iDuration);
	};
	
	gThis.HideLiveSearch = function() {
		gThis.m_jLiveSearch.slideUp(gThis.m_oOptions.iDuration);
	};
	
	gThis.OnFocus = function() {
		gThis.m_jInput.focus(function() {
			if(gThis.m_jInput.val() == gThis.m_oOptions.sDefaultText) $(this).val("");
		});
		if (gThis.m_jInput.val() != ''){
			if (gThis.m_jLiveSearch.html() == ''){
				gThis.sLastValue = '';
				gThis.m_jInput.keyup();
			}else{
				setTimeout(gThis.ShowLiveSearch(),1);
			}
		}
	};
	
	gThis.OnClick = function() {
		gThis.m_jInput.click(function(){
			if(gThis.m_jLiveSearch.html() != '') {
				setTimeout(gThis.ShowLiveSearch(),1);
			}	
		});
	};
	
	gThis.OnBlur = function() {
		gThis.m_jInput.blur(function() {
			if(gThis.m_jInput.val() == '') $(this).val(gThis.m_oOptions.sDefaultText);
		});
		if(gThis.m_jLiveSearch.html() != ''){
			gThis.ShowLiveSearch();
		}
	};
	
	gThis.OnTypingFinished = function() {
		if(gThis.sLastValue != gThis.m_jInput.val() && gThis.m_jInput.val() != '' && gThis.m_jInput.val() != gThis.m_oOptions.sDefaultText && gThis.m_jInput.val().length > 2){
			gThis.LoadResults();
		}
	};
	
	gThis.LoadResults = function() {
		gThis.sLastValue = gThis.m_jInput.val();
		$.get(gThis.m_oOptions.sViewUrl + Base64.encode(gThis.m_jInput.val()), function (data){
			if (data.length && gThis.sLastValue.length) {
				gThis.m_jLiveSearch.html(data);
				gThis.ShowLiveSearch();
			}else{
				gThis.HideLiveSearch();
			}
		});
	};
	
	gThis._Constructor();
	
};

new GPlugin('GSearch', oDefaults, GSearch);