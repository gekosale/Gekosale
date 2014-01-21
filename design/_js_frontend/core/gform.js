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
			var rRE = new RegExp(oRule.sFormat.substr(1, oRule.sFormat.length - 2));
			return gField.ValidationResult(rRE.test(mValue), oRule.sErrorMessage, iRepetition);
			
		case 'email':
			if(mValue != ''){
				return gField.ValidationResult(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum|pro)$/i.test(mValue), oRule.sErrorMessage, iRepetition);
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
			aKeys.sort();
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
	
	gThis.OnFocus = function(eEvent) {
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
			gThis._GetField(sRepetition).parent().find('input:checkbox').attr('checked','checked').change();
		}
		else {
			gThis._GetField(sRepetition).parent().find('input:checkbox').attr('checked','').change();
		}
	};
	
	gThis._PrepareNode = function() {
		gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));
		var jLabel = $('<label for="' + gThis.GetId() + '"/>');
		jLabel.text(gThis.m_oOptions.sLabel);
		if ((gThis.m_oOptions.sComment != undefined) && (gThis.m_oOptions.sComment.length)) {
			jLabel.append(' <small>' + gThis.m_oOptions.sComment + '</small>');
		}
		gThis.m_jField = $('<input type="' + gThis.m_oOptions.sFieldType + '" name="' + gThis.GetName() + '" id="' + gThis.GetId() + '" value="1"/>');
		var jRepetitionNode = $('<span class="' + gThis._GetClass('FieldRepetition') + '"/>');
		jRepetitionNode.append($('<span class="' + gThis._GetClass('FieldSpan') + '"/>').append(gThis.m_jField));
		var jError = $('<span class="' + gThis._GetClass('Required') + '"/>');
		jRepetitionNode.append(jError);
		gThis.m_jNode.append(jRepetitionNode);
		gThis.m_jNode.append(jLabel);
	};
	
	gThis.OnFocus = function(eEvent) {
		var jField = $(eEvent.currentTarget);
		jField.closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
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
		if(gThis.m_oOptions.sLabel != ''){
			gThis.m_jNode.append('<legend><span>' + gThis.m_oOptions.sLabel + '</span></legend>');
		}
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
		}
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
	sCssAttribute: ''
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
	};
	
	gThis.OnFocus = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).addClass(gThis._GetClass('Focused'));
	};
	
	gThis.OnBlur = function(eEvent) {
		$(eEvent.currentTarget).closest('.' + gThis._GetClass('FieldSpan')).removeClass(gThis._GetClass('Focused'));
	};
	
	gThis.Reset = function() {
		gThis.m_jField.val(gThis.m_oOptions.sDefault).change();
	};
	
	gThis._SetGlobal = function() {

		if (gThis.m_bRepeatable) {
				
			gThis.Populate(gThis.m_oOptions.asGlobals);
		}
		else {
			if(gThis.m_oOptions.sGlobal != undefined){
				
				gThis.m_jField.val(gThis.m_oOptions.sGlobal).change();
				
			}else{
				
				gThis.m_jField.val(0).change();
				
			}
		}
			
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
		gThis.m_jNode.append('<span class="' + gThis._GetClass('Button') + '"><span>' + ((gThis.m_oOptions.sIcon != '') ? '<img src="' + GCore.DESIGN_PATH + gThis.m_oOptions.sIcon + '" alt=""/>' : '') + '<input type="submit" name="' + gThis.GetName() + '" value="' + gThis.m_oOptions.sLabel + '"/></span></span>');
	};
	
}, oDefaults);

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
		sFieldRepetitionClass: 'repetition'
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
		gThis.m_jNode.find('input').parent().find('input:checkbox').attr('checked','').change();
	};
	
	gThis.Populate = function(mValue) {
		gThis.m_jNode.find('input:checkbox').attr('checked','').change();
		for (var i in mValue) {
			if (i == 'toJSON') {
				continue;
			}
			gThis.m_jNode.find('input[value="' + mValue[i] + '"]').parent().find('input:checkbox').attr('checked','checked').change();
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

GForm.Language = {
		form_data_invalid: 'Nie można wysłać formularza, ponieważ zawiera on niepoprawne informacje. Przed zapisaniem zmian należy je poprawić.',
		scroll_to_field: 'Przejdź do pola',
		close_alert: 'Zamknij alert',
		next: 'Dalej',
		previous: 'Wstecz',
		save: 'Zapisz',
		add_field_repetition: 'Dodaj nową wartość',
		remove_field_repetition: 'Usuń wartość',
		all_actions: 'wszystkie',
		all_controllers: 'wszystkie'
	};