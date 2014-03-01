/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /**
* GF()
* 
* Groups elemetary methods that are used widely in the whole GexoFramework.
* The class mainly consists of static methods availble to call from
* any place in the application.
*/
function GF() {};

/**
* const GF.NULL - Null equivalent.
*/
GF.NULL = -9999;

/**
* GF.ExtendClass(sType, fChild[, oChildStatic])
* 
* Function that realises the classical inheritance model.
* 
* Treats the current class as the base class and extends it with the one
* passed as the second argument. The first argument specifies the child
* class' name. The third argument, if defined, is an object consisting
* of the child class' static members.
* 
* @return function - New class implementing both source classes.
* @param sType string - Name of the new class.
* @param fChild function - The definition of the derived class.
* @param fChildStatic object - An object consisting of the child class'
*  static members. It should be built of name-definition pairs.
* @param bNoBaseConstructor bool - If set to true, the base class'
*  constructor isn't invoked when derived object is created.
*/
GF.ExtendClass = function(sType, fChild, oChildStatic, bNoBaseConstructor) {
	var fBase = this;
	var fExtended = function() {
		var aBaseArguments = [sType];
		for (var i = 0; i < arguments.length; i++) {
			aBaseArguments.push(arguments[i]);
		}
		if (bNoBaseConstructor != undefined && bNoBaseConstructor) {
			return fChild.apply(this, arguments);
		}
		var result = fBase.apply(this, aBaseArguments);
		if (result === false) {
			return result;
		}
		return fChild.apply(this, arguments);
	};
	fExtended.GF_ImplementMembers(fBase.prototype);
	fExtended.GF_ImplementStaticMembers(fBase, sType);
	fExtended.GF_ImplementStaticMembers(oChildStatic);
	return fExtended;
};

/**
* GF.ImplementMembers(oProto)
* 
* Implements all the proto members as the current class' member functions.
* 
* @param oProto object - An object consisting of the class' methods.
*/
GF.ImplementMembers = function(oProto) {
  for(var i in oProto) {
		this.prototype[i] = oProto[i];
	}
};

/**
* GF.ImplementStaticMembers(oProto, sType)
* 
* Implements all the proto members as the current class' static members.
* 
* The function is prepared to cope with the GetInstance-like methods of GF_Instance
* class. It overrides the standard argument list so that the second argument
* is always the calling class' name.
* 
* @param oProto object - An object consisting of the class' static members.
* @param sType string - Name of the derived class.
*/
GF.ImplementStaticMembers = function(oProto, sType) {
	for(var i in oProto) {
		if (i == 'GetInstance') {
			this[i] = function(oNode) {
				return GF_Instance.GetInstance(oNode, sType);
			}
		}
		else if (i == 'GetCurrentInstance') {
			this[i] = function(oNode) {
				return GF_Instance.GetCurrentInstance(oNode, sType);
			}
		}
		else {
			this[i] = oProto[i];
		}
	}
};

/**
* GF.NewConstructor(fConstructor)
* 
* Prepares a safe constructor that catches all the thrown exceptions during
* its evaluation. It is recommended that all newly created constructors should
* be processed using this method. Otherwise you should manually ensure to 
* correctly apply exception catching.
* 
* @return function - Safe object constructor.
* @param fConstructor function - A function pointer to the base constructor.
*/
GF.NewConstructor = function(fConstructor) {
	var fSafeConstructor = function(jTarget, oOptions) {
		try {
			return fConstructor.apply(this, arguments);
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			this.RemoveInstance();
			return false;
		}
	};
	return fSafeConstructor;
};

/**
* GF.NewEventHandler(fHandler)
* 
* Prepares a safe event handler that catches all the thrown exceptions. It is
* recommended that all newly created event handlers should be processed using
* this method. Otherwise you should manually ensure to correctly apply
* exception catching.
* 
* @return function - Safe event handler.
* @param fHandler function - A function pointer to the base event handler.
*/
GF.NewEventHandler = function(fHandler) {
	var fSafeHandler = function(eEvent) {
		try {
			return fHandler.apply(this, arguments);
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			return false;
		}
	};
	return fSafeHandler;
};

/**
* GF.NewSafeMethod(fMethod)
* 
* Prepares a safe method that catches all the thrown exceptions. It is
* recommended that all newly created methods that can be invoked externally
* should be processed using this method. Otherwise you should manually 
* ensure to correctly apply exception catching.
* 
* @return function - Safe method.
* @param fHandler function - A function pointer to the base method.
*/
GF.NewSafeMethod = function(fMethod) {
	var fSafeHandler = function(eEvent) {
		try {
			return fMethod.apply(this, arguments);
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			return false;
		}
	};
	return fSafeHandler;
};

/**
* GF.GetClasses(jNode)
* 
* Returns an array of the node's classes.
* 
* @return array - Classname array.
* @param jNode jQuery - Target DOM node.
*/
GF.GetClasses = function(jNode) {
	return jNode.attr('class').split(' ');
};

/**
* GF.GetChunkFromClass(jNode, sNeedle)
* 
* Looks int he node's class string for a chunk that starts with sNeedle.
* When found, returns the remaining part of such chunk.
* 
* @return string - Remaining part of the found chunk or an empty string.
* @param jNode jQuery - Node which classes are subject to search.
* @param sNeedle string - Beginning of the chunk that we want to find.
*/
GF.GetChunkFromClass = function(jNode, sNeedle) {
	var aClasses = GF.GetClasses(jNode);
	for (var i in aClasses) {
		if (aClasses[i].substr(0, sNeedle.length) == sNeedle) {
			return aClasses[i].substr(sNeedle.length);
		}
	}
	GF_Debug.Warning('Desired string (' + sNeedle + ') was not found in any of the node\'s classes (class string: "' + jNode.attr('class') + '").');
	return '';
};

/**
* GF.GetIdFromFieldName(sFieldName)
* 
* Extracts the field's id from its name. It's used to process and identify fields
* that names are in an array-form, e.g. "fieldName[123]". The function returns "123"
* in such case. It's safe to use it with multi dimensional arrays - only the last
* id will be returned.
* 
* @return string - Element's id.
* @param sFieldName string - The field's name from which the id needs to be extracted.
*/
GF.GetIdFromFieldName = function(sFieldName) {
	var aIds = sFieldName.match(/\[[^\]]+\]/g);
	var sLastId = aIds.pop();
	return sLastId.substr(1, sLastId.length - 2);
};

/**
* GF.CheckboxesCheck(jContext)
* 
* Checks all the descendant checkboxes of the jContext node.
* 
* @param jContext jQuery - Predecessor of the checkboxes to check.
*/
GF.CheckboxesCheck = function(jContext) {
	jContext.checkCheckboxes();
};

/**
* GF.CheckboxesUncheck(jContext)
* 
* Unchecks all the descendant checkboxes of the jContext node.
* 
* @param jContext jQuery - Predecessor of the checkboxes to uncheck.
*/
GF.CheckboxesUncheck = function(jContext) {
	jContext.unCheckCheckboxes();
};

/**
* GF.StopPropagation(eEvent)
* 
* A generic event handler that stops the event propagation.
* 
* @param eEvent EventObject - jQuery's event object.
*/
GF.StopPropagation = function(eEvent) {
	eEvent.stopImmediatePropagation();
	eEvent.stopPropagation();
	return true;
};

/**
* GF.PreventDefault(eEvent)
* 
* A generic event handler that prevents the browser from invoking default action.
* 
* @param eEvent EventObject - jQuery's event object.
*/
GF.PreventDefault = function(eEvent) {
	eEvent.preventDefault();
	return true;
};

/**
* GF.InArray(aArray, mValue)
* 
* Checks whether the mValue element exists in the aArray.
* 
* @return bool - True if the value exists in array or false if not.
* @param aArray array - Array of values.
* @param mValue mixed - Value that we want to find.
*/
GF.InArray = function(aArray, mValue) {
	for (var i in aArray) {
		if (aArray[i] == mValue) {
			return true;
		}
	}
	return false;
};

/**
* GF.DeleteFromArray(aArray, mValue)
* 
* Deletes safely the mValue from aArray.
* 
* @return array - The modified array.
* @param aArray array - Array of values.
* @param mValue mixed - Value that we want to delete.
*/
GF.DeleteFromArray = function(aArray, mValue) {
	while(true) {
		var n = aArray.length;
		var j = 0;
		for (var i in aArray) {
			if (aArray[i] == mValue) {
				aArray.splice(i, 1);
			}
			j++;
		}
		if (j == n) {
			break;
		}
	}
	return aArray;
};

/**
* GF.DeleteFromArrayAt(aArray, mIndex)
* 
* Deletes safely the mIndex element from aArray.
* 
* @return array - The modified array.
* @param aArray array - Array of values.
* @param mIndex mixed - Index of element that we want to delete.
*/
GF.DeleteFromArrayAt = function(aArray, mIndex) {
	while(true) {
		var n = aArray.length;
		var j = 0;
		for (var i in aArray) {
			if (i == mIndex) {
				aArray.splice(i, 1);
			}
			j++;
		}
		if (j == n) {
			break;
		}
	}
	return aArray;
};

GF.Clone = function(mSource) {
	if (mSource == null || typeof(mSource) != 'object') {
		return mSource;
	}
	var mTemp = new mSource.constructor();
	for (var sKey in mSource) {
		mTemp[sKey] = GF.Clone(mSource[sKey]);
	}
	return mTemp;
};

/**
* GF.CountMembers(oObject)
* 
* Counts the enumerable members of oObject.
* 
* @return int - Number of members.
* @param oObject Object - Object which members we want to count.
*/
GF.CountMembers = function(oObject) {
	var iCount = 0;
	for (var i in oObject) {
		iCount++;
	}
	return iCount;
};

/**
* GF.MoneyFormat(nValue)
* 
* Returns a string formatted according to the currently selected currency.
* 
* @return string - Formatted value.
* @param nValue Number - Amount to format.
*/
GF.MoneyFormat = function(nValue) {
	return nValue.toFixed(2);
};

/**
* GF.ParseMoney(sValue)
* 
* Returns a floating point number according to the currency value passed as a parameter.
* 
* @return Number - Parsed value.
* @param sValue string - Unparsed value.
*/
GF.ParseMoney = function(sValue) {
	return parseFloat(sValue.replace(/,/, '.'));
};

/* Function prototype extensions */

Function.prototype.GF_Extend = GF.ExtendClass;
Function.prototype.GF_ImplementMembers = GF.ImplementMembers;
Function.prototype.GF_ImplementStaticMembers = GF.ImplementStaticMembers;

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 function GF_Base() {
	
	/* Declarations */
	
	this.GF_Base;       // GF_Base([...])
	
	/* Definitions */
	
	/**
	* GF_Base([...])
	* 
	* @return bool - False on error.
	*/
	this.GF_Base = function() {
		try {
			
		}
		catch (exception) {
			return false;
		}
	};
	
	/* Constructor call */
	
	return this.GF_Base();
	
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /**
* GF_Debug()
* 
* Groups the methods needed by the debug section of Framework.
*/
function GF_Debug() {};

/**
* Error reporting constants.
* @const GF_Debug.LEVEL_ALL int - Report all messages.
* @const GF_Debug.LEVEL_ERRORS_ONLY int - Report errors only.
* @const GF_Debug.LEVEL_NONE int - Verbose mode.
*/
GF_Debug.LEVEL_ALL = 0;
GF_Debug.LEVEL_ERRORS_ONLY = 1;
GF_Debug.LEVEL_NONE = 2;

/**
* @var GF_Debug.s_iLevel int - Current error reporting level. May be set
*  externally at any moment.
*/
GF_Debug.s_iLevel = GF_Debug.LEVEL_NONE;

/**
* @var GF_Debug.s_asMessages array - Vector of pending messages.
*/
GF_Debug.s_asMessages = [];

/**
* GF_Debug.Error(sMessage)
* 
* Signals that an error has occured. It processes the message and
* reports it to the user if the reporting level is adequate.
* It also raises an exception with the message specified.
* 
* @param sMessage string - The error description.
*/
GF_Debug.Error = function(sMessage) {
	GF_Debug.s_asMessages.push({
		level: 2,
		message: sMessage
	});
	if (GF_Debug.s_iLevel <= GF_Debug.LEVEL_ERRORS_ONLY) {
		GF_Debug.ShowMessage(sMessage);
	}
	throw new GF_Exception(sMessage);
};

/**
* GF_Debug.Warning(sMessage)
* 
* Signals that a warning has occured. It processes the message and
* reports it to the user if the reporting level is adequate.
* 
* @param sMessage string - The warning description.
*/
GF_Debug.Warning = function(sMessage) {
	GF_Debug.s_asMessages.push({
		level: 1,
		message: sMessage
	});
	if (GF_Debug.s_iLevel == GF_Debug.LEVEL_ALL) {
		GF_Debug.ShowMessage(sMessage);
	}
};

/**
* GF_Debug.ShowMessage(sMessage)
* 
* Reprots a message to the user. In it's simple form just shows
* an alert box on the screen.
* 
* It can be freely overriden with a custom function.
* 
* @param sMessage string - The message to display.
*/
GF_Debug.ShowMessage = function(sMessage) {
	GAlert(GF_Debug.Language.error_occured, sMessage, {
		iType: GAlert.TYPE_ERROR
	});
};

/**
* GF_Debug.HandleException(xException)
* 
* Handles an exception in an appropriate way.
* 
* @return void
* @param xException mixed - An exception.
*/
GF_Debug.HandleException = function(xException) {
	if (xException instanceof GF_Exception) {
		
	}
	else {
		if (GF_Debug.level <= GF_Debug.LEVEL_ERRORS_ONLY) {
			alert('Non-GF exception:\n' + xException);
		}
	}
	throw xException;
};

/**
* GF_Exception(sMessage)
* 
* General GexoFramework exception.
* 
* @param sMessage string - The exception's description.
*/
function GF_Exception(sMessage) {};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_Debug.Language = {
	error_occured: 'Wystąpił błąd!'
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 function GF_Config(options) {
	
	this.GF_Config = function(options) {
	};
	
	this._Process = function(options) {
		this._ProcessNode(options, this);
		this._PostProcess();
	};
	
	this._ProcessNode = function(options, context) {
		for (var i in options) {
			if ((options[i] instanceof Object) && !(options[i] instanceof Function) && !(options[i] instanceof Array) && !(options[i] instanceof $)) {
				if (context[i] != undefined) {
					if (GF.CountMembers(context[i]) == 0) {
						context[i] = options[i];
					}
					else {
						this._ProcessNode(options[i], context[i]);
					}
				}
				else {
					GF_Debug.Warning('Encountered an attempt to override a non-existing parameter group (' + i + ') in a GF_Config derived class.');
				}
			}
			else {
				if (context[i] != undefined) {
					context[i] = options[i];
				}
				else {
					GF_Debug.Warning('Encountered an attempt to override a non-existing parameter (' + i + ') in a GF_Config derived class.');
				}
			}
		}
	};
	
	this._PostProcess = function() {
	};
	
	/* Constructor call */
	
	return this.GF_Config(options);
	
}

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /**
* GF_Instance(sType, jTarget[, oOptions])
*
* Base class for all GF instances. Every object should extend this class
* (using the GF.ExtendClass method which is availble as a Function extension
* named Extend).
* 
* Implements basic object creation procedures, aggregation and common methods
* for object selection.
* 
* @return bool - False on error.
* @param type string - Name of the child class.
* @param target jQuery - Target which is being used to initialize the newly
*  created object.
* @param options object - Configuration options object.
*/
function GF_Instance(sType, jTarget, oOptions) {
	
	/* Declarations */
	
	this.m_iId;             // Instance's id
	this.m_jTarget;         // Instance's DOM element
	this.m_sType;           // Final type of the object
	
	this.GF_Instance;       // GF_Instance(sType, jTarget[, oOptions])
	this.InitInstance;      // InitInstance(target)
	this.RemoveInstance;    // RemoveInstance()
	this.RefreshTarget;     // RefreshTarget()
	this.IsInstanceOf;      // IsInstanceOf(sType)
	
	/* Definitions */
	
	/**
	* GF_Instance(sType, jTarget[, oOptions])
	*
	* The GF_Instance constructor. Checks if all the necessary parameters are set
	* and invokes the object creation. If any exception raises, it skips the object
	* creation and returns false.
	* 
	* When target points to more than one DOM element, this constructor invokes
	* multiple object creation and returns an array of newly created objects.
	* 
	* @return bool - False on error.
	* @param type string - Name of the child class.
	* @param target jQuery - Target which is being used to initialize the newly
	*  created object.
	* @param options object - Configuration options object.
	*/
	this.GF_Instance = function(sType, jTarget, options) {
		try {
			
			if ((sType == undefined) || !sType) {
				GF_Debug.Error('No instance type specified. GF object creation failed.');
			}
			
			this.m_sType = sType;
			
			if (!(jTarget instanceof $)) {
				GF_Debug.Error('Unknown type of target (' + typeof jTarget + '). Expected a jQuery object. GF object creation failed.');
			}
			if (jTarget.length < 1) {
				GF_Debug.Warning('Target emerged to be an empty jQuery object. GF object creation omitted.');
				return false;
			}
			if (jTarget.length > 1) {
				var a = [];
				eval('var f = ' + sType + ';');
				for (var i = 0; i < jTarget.length; i++) {
					a.push(new f(jTarget.eq(i), oOptions));
				}
				return a;
			}
			this.InitInstance(jTarget);
			
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			this.RemoveInstance();
			return false;
		}
	};
		
	/**
	* InitInstance(jTarget)
	*
	* Initializes the DOM element passed as the argument. Prepares the name, id and
	* adds necessary CSS classes.
	* 
	* Also adds the newly created object to the instances vector.
	* 
	* @return void
	* @param jTarget jQuery - Target DOM element.
	*/
	this.InitInstance = function(jTarget) {
		this.m_iId = GF_Instance.s_aoInstances.push(this) - 1;
		this.m_jTarget = jTarget;
		this.m_jTarget.addClass('GF-instance-' + this.m_iId).addClass('GF-instance').addClass(sType);
	};
	
	/**
	* RemoveInstance()
	* 
	* Removes this instance from the instances vector.
	* 
	* @return void
	*/
	this.RemoveInstance = function() {
		if ((this.m_iId != undefined) && (GF_Instance.s_aoInstances[this.m_iId] != undefined)) {
			if (this.m_jTarget) {
				this.m_jTarget.remove();
			}
			GF.DeleteFromArray(GF_Instance.s_aoInstances, this.m_iId);
		}
	};
	
	/**
	* RefreshTarget()
	* 
	* Refreshes the m_jTarget property using the actual instance id.
	*/
	this.RefreshTarget = function() {
		this.m_jTarget = $('.GF-instance-' + this.m_iId);
	};
	
	/**
	* IsInstanceOf(sType)
	* 
	* Checks whether current object is an istance of the given class.
	* 
	* @return bool
	* @param sType string - Type that we want to match
	*/
	this.IsInstanceOf = function(sType) {
		return this.m_sType == sType;
	};
	
	/* Constructor call */
	
	return this.GF_Instance(sType, jTarget, oOptions);
	
};

/**
* @var GF_Instance.s_aoInstances array - Keeps all the instances for further retrieval.
*/
GF_Instance.s_aoInstances = [];

/**
* GF_Instance.GetInstance(jNode[, sType])
* 
* Returns an object of a GF_Instance derived class associated with the DOM
* node passed as an argument. If type is specified, it looks for an object
* of that type. Otherwise it returns the first GF_Instance encountered.
* 
* It is recommended to use GetInstace() straight from derived classes.
* It's not necessary then to pass the second argument - it's being passed
* automatically.
* 
* @return GF_Instance|bool - Associated object or false if not found.
* @param jNode jQuery - A DOM node being the search subject.
* @param sType string - Optional name of the object type desired.
*/
GF_Instance.GetInstance = function(jNode, sType) {
	var iId;
	if (jNode.hasClass('GF-instance') && ((sType == undefined) || jNode.hasClass(sType))) {
		iId = GF_Instance.GetId(GF.GetClasses(jNode));
		if (iId >= 0) {
			return GF_Instance.ReturnInstance(iId);
		}
		GF_Debug.Message('Encoutered a .GF-instance node without id.');
	}
	var jParents = jNode.parents('.GF-instance' + ((sType == undefined) ? '' : '.' + sType));
	for (var i = 0; i < jParents.length; i++) {
		jNode = jParents.eq(i);
		iId = GF_Instance.GetId(GF.GetClasses(jNode));
		if (iId >= 0) {
			return GF_Instance.ReturnInstance(iId);
		}
		GF_Debug.Message('Encoutered a .GF-instance node without id.');
	}
	GF_Debug.Error('Instance not found!');
	return false;
};

/**
* GF_Instance.GetId(aClasses)
* 
* Returns the GF_Instance id from an array of CSS classes.
* 
* @return int - Id.
* @param aClasses array - CSS classname array.
*/
GF_Instance.GetId = function(aClasses) {
	for (var i in aClasses) {
		if (aClasses[i].substr(0, 12) == 'GF-instance-') {
			return aClasses[i].substr(12);
		}
	}
	return -1;
};

/**
* GF_Instance.ReturnInstance(iId)
* 
* Returns an instance with the specified id. It checks first if such an instance
* does exist. It's recommended to use GetInstace rather than call this function
* directly.
* 
* @return GF_Instance|bool - Desired instance or false on failiure.
* @param id int - Id of the desired object.
*/
GF_Instance.ReturnInstance = function(iId) {
	if (GF_Instance.s_aoInstances[iId] == undefined) {
		GF_Debug.Error('Tried to access a non-existing instance (' + iId + ').');
		return false;
	}
	return GF_Instance.s_aoInstances[iId];
};

/**
* GF_Instance.GetCurrentInstance(mContext, type)
* 
* Returns the derived class of GF_Instance instance corresponding to the context.
* It may be the context itself if called from an appropriate object or a GetInstace
* result if context is a DOM node.
* 
* It is recommended to use GetInstace() straight from derived classes.
* It's not necessary then to pass the second argument - it's being passed
* automatically.
* 
* It is commonly used in event handlers across the framework.
* 
* @return GF_Instance|bool - Desired object or false on error.
* @param mContext mixed - Pointer to object that is used to begin search.
* @param type string - Optional name of the object type desired.
*/
GF_Instance.GetCurrentInstance = function(mContext, sType) {
	eval('var bIsProperType = ((mContext instanceof ' + sType + ') ? true : false);');
	if (bIsProperType) {
		return mContext;
	}
	else if (mContext.nodeType != undefined) {
		eval('var instance = ' + sType + '.GetInstance($(mContext));');
		return instance;
	}
	else {
		GF_Debug.Error('Couldn\'t get current instance of type ' + sType + '. Unrecognized context type (' + typeof mContext + ')');
		return false;
	}
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /**
* GF_Autosuggest(target, options)
* 
* Manages the Autosuggest form field. It's a text field with a convinient,
* dynamically updatet suggestions list.
* 
* Possible options (* indicates a required property):
* - source * - function - pointer to a JavaScript function which returns a 
*   suggestions set for the specified request. It should acquire two
*   parameters: one being a request object and the second which is a name of
*   the function that should be invoked when the results are ready.
* - minLength - int - minimal number of characters that need to by typed
*   before suggestions will appear (default = 3).
* - delay - int - the dealy in miliseconds after which the request for
*   suggestions will be sent (default = 500).
* - maxSuggestions - int - maximal number of suggestions to appear
*   (default = 5).
* 
* @return bool - False on error.
* @param target jQuery - Target which is being used to initialize the newly
*  created object.
* @param options object - Configuration options object.
*/
GF_Autosuggest = GF_Instance.GF_Extend('GF_Autosuggest', function(target, options) {
	
	/* Declarations */
	
	this.m_sName;                 // Name of the suggestions field
	this.m_fSource;               // Pointer to the suggestion source function
	this.m_iMinLength = 3;        // Minimal number of characters to request suggestions
	this.m_iDelay = 500;          // Delay in ms of suggestion show
	this.m_iMaxSuggestions = 5;   // Maximal number of suggestions shown
	
	this.m_jInput;                // jQuery object pointing to the input field
	this.m_aSuggestions = [];     // Array of suggestions
	this.m_iCurrentSuggestion = 0;// Currently focused suggestion
	this.m_bPending = false;      // Request is being processed
	
	this.GF_Autosuggest;          // GF_Autosuggest(target, options)
	this.InitializeEvents;        // InitializeEvents()
	this.FocusInput;              // FocusInput(event)
	this.BlurInput;               // BlurInput(event)
	this.ClickedInput;            // ClickedInput(event)
	this.ChangeActiveSuggestion;  // ChangeActiveSuggestion(event)
	this.SuggestionChosen;        // SuggestionChosen(newSuggestion)
	this.KeyDown;                 // KeyDown(event)
	this.KeyPressed;              // KeyPressed(event)
	this.LoadSuggestions;         // LoadSuggestions(suggestions)
	this.ShowSuggestions;         // ShowSuggestions()
	this.CreateSuggestions;       // CreateSuggestions()
	this.DeleteSuggestions;       // DeleteSuggestions()
	this.ExpandSuggestions;       // ExpandSuggestions()
	this.RetractSuggestions;      // RetractSuggestions()
	
	/* Definitions */
	
	/**
	* GF_Autosuggest(target, options)
	* 
	* The GF_Autosuggest constructor. Overrides all the default configuration values
	* with the passed options and inovkes necessary initializers.
	* 
	* @return bool - False on error.
	* @param target jQuery - Target which is being used to initialize the newly
	*  created object.
	* @param options object - Configuration options object.
	*/
	this.GF_Autosuggest = GF.NewConstructor(function(target, options) {
		this.m_jInput = this.m_jTarget.find('input');
		this.m_sName = this.m_jInput.attr('name');
		this.m_fSource = options.source;
		this.m_iMinLength = (options.minLength != undefined) ? options.minLength : this.m_iMinLength;
		this.m_iDelay = (options.delay != undefined) ? options.delay : this.m_iDelay;
		this.m_iMaxSuggestions = (options.maxSuggestions != undefined) ? options.maxSuggestions : this.m_iMaxSuggestions;
		this.InitializeEvents();
	});
	
	/**
	* InitializeEvents()
	* 
	* Binds all the necessary event handlers.
	* 
	* @return void
	*/
	this.InitializeEvents = function() {
		this.m_jInput.blur(this.BlurInput).focus(this.FocusInput).click(this.ClickedInput).keypress(this.KeyPressed).keydown(this.KeyDown);
		$('#autosuggest-suggestions li').live('click', this.SuggestionChosen).live('mouseover', this.ChangeActiveSuggestion);
	};
	
	/**
	* FocusInput(event)
	* 
	* Handles the Focus event for input field.
	* 
	* Changes the field's name to disable the browser's built-in autosuggest,
	* changes the active field indicator and passes command to the GetSuggestions() method.
	* 
	* @return bool - Always true.
	*/
	this.FocusInput = GF.NewEventHandler(function(event) {
		var autosuggest = GF_Autosuggest.GetCurrentInstance(this);
		autosuggest.m_jInput.attr('name', autosuggest.m_sName + '_' + Math.floor(Math.random() * 1000000));
		GF_Autosuggest.active = autosuggest.m_iId;
		if (!GF_Autosuggest.enabled) {
			GF_Autosuggest.enabled = true;
			return true;
		}
		GF_Autosuggest.GetSuggestions();
		return true;
	});
	
	/**
	* BlurInput(event)
	* 
	* Handles the Blur event for input field.
	* 
	* Restores the proper field name, unsets the active field indicator and
	* initiates suggestion box hiding.
	* 
	* @return bool - Always true.
	*/
	this.BlurInput = GF.NewEventHandler(function(event) {
		var autosuggest = GF_Autosuggest.GetCurrentInstance(this);
		autosuggest.m_jInput.attr('name', autosuggest.m_sName);
		GF_Autosuggest.last = GF_Autosuggest.active;
		GF_Autosuggest.active = -1;
		autosuggest.RetractSuggestions();
		return true;
	});
	
	/**
	* ClickedInput(event)
	* 
	* Handles the Click event for input field.
	* 
	* Invokes suggestions loading.
	* 
	* @return bool - Always true.
	*/
	this.ClickedInput = GF.NewEventHandler(function(event) {
		if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length) {
			GF_Autosuggest.GetSuggestions();
		}
		return true;
	});
	
	/**
	* ChangeActiveSuggestion([newSuggestion])
	* 
	* Handles the MouseOver event for suggestion box items.
	* 
	* @return bool - Always true.
	* @param newSuggestion int - New suggestion's id.
	*/
	this.ChangeActiveSuggestion = GF.NewEventHandler(function(newSuggestion) {
		if (this instanceof GF_Autosuggest) {
			var autosuggest = this;
			autosuggest.m_iCurrentSuggestion = newSuggestion;
		}
		else {
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
			autosuggest.m_iCurrentSuggestion = $('#autosuggest-suggestions li').index(this);
		}
		$('#autosuggest-suggestions li').removeClass('active');
		$('#autosuggest-suggestions li:eq(' + autosuggest.m_iCurrentSuggestion + ')').addClass('active');
		return true;
	});
	
	/**
	* SuggestionChosen([event])
	* 
	* Chooses the currently active suggestion. May be invoked as an event handler.
	* 
	* @return bool - Always true.
	*/
	this.SuggestionChosen = GF.NewEventHandler(function(event) {
		if (GF_Autosuggest.active >= 0)
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
		else if (GF_Autosuggest.last >= 0)
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.last);
		else
			return true;
		autosuggest.m_jInput.val($('#autosuggest-suggestions li:eq(' + autosuggest.m_iCurrentSuggestion + ')').text()).change();
		GF_Autosuggest.enabled = false;
		autosuggest.m_jInput.focus();
		return true;
	});
	
	/**
	* KeyDown(e)
	* 
	* Handles the KeyDown event for input field.
	* 
	* Handles arrows, enter and escape as well as backspace and delete keys.
	* Takes action appropriate to the key pressed.
	* 
	* For Opera it handles all the character keys.
	* 
	* @return bool - Appropriate state according to the key pressed.
	*/
	this.KeyDown = GF.NewEventHandler(function(e) {
		switch (e.keyCode) {
			case 38: //up
				if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length)
					return false;
				var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
				autosuggest.ChangeActiveSuggestion(Math.max(0, autosuggest.m_iCurrentSuggestion - 1));
				return false;
			case 40: //down
				if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length) {
					GF_Autosuggest.GetSuggestions();
					return false;
				}
				else {
					var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
					autosuggest.ChangeActiveSuggestion(Math.min(autosuggest.m_aSuggestions.length - 1, autosuggest.m_iCurrentSuggestion + 1));
				}
				return false;
			case 13: //enter
				if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length)
					return true;
				var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
				autosuggest.SuggestionChosen();
				autosuggest.RetractSuggestions();
				return false;
			case 27: //esc
				if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length)
					return true;
				var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
				autosuggest.RetractSuggestions();
				return false;
		}
		if (window.opera || e.keyCode == 8 || e.keyCode == 46) {
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
			if (!autosuggest.pending)
				setTimeout(GF_Autosuggest.GetSuggestions, autosuggest.m_iDelay);
			return true;
		}
	});
	
	/**
	* KeyPressed(e)
	* 
	* Handles the KeyPress event for input field.
	* 
	* Handles enter key as well as normal character keys.
	* 
	* @return bool - Appropriate state according to the key pressed.
	*/
	this.KeyPressed = GF.NewEventHandler(function(e) {
		if (e.keyCode == 13) {
			if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length)
				return true;
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
			autosuggest.SuggestionChosen();
			autosuggest.RetractSuggestions();
			return false;
		}
		if (e.charCode) {
			var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
			if (!autosuggest.pending)
				setTimeout(GF_Autosuggest.GetSuggestions, autosuggest.m_iDelay);
			return true;
		}
	});
	
	/**
	* LoadSuggestions(suggestions)
	* 
	* Initiates the newly acquired suggestion list display.
	* 
	* Checks whether user still expects the list to appear and if so, invokes
	* the ShowSuggestions function with the array of suggestions.
	* 
	* @return void
	* @param suggestions object - response returned by server, including the whole
	*  new suggestion list.
	*/
	this.LoadSuggestions = function(suggestions) {
		this.m_aSuggestions = suggestions.s;
		if (this.m_aSuggestions.length && (GF_Autosuggest.active >= 0) && (this.m_jInput.val() == suggestions.q)) {
			this.ExpandSuggestions();
			this.ShowSuggestions();
		}
		else {
			this.RetractSuggestions();
		}
	};
	
	/**
	* ShowSuggestions()
	* 
	* Displays the suggestions list. This method bases on the object's m_aSuggestions
	* vector and doesn't need any parameters. It expects that the suggestions list
	* (even if empty) already exists and exchanges the list items.
	* 
	* @return void
	*/
	this.ShowSuggestions = function() {
		this.m_iCurrentSuggestion = 0;
		var ul = GF_Autosuggest.suggestions.find('ul');
		ul.empty();
		for (var i in this.m_aSuggestions) {
			ul.append('<li' + ((i == this.m_iCurrentSuggestion) ? ' class="active"' : '') + '>' + this.m_aSuggestions[i].name + '</li>');
		}
	};
	
	/**
	* CreateSuggestions()
	* 
	* Creates the suggestions list DOM structure and appends it to the document.
	* 
	* @return void
	*/
	this.CreateSuggestions = function() {
		GF_Autosuggest.suggestions = $('<div id="autosuggest-suggestions"></div>');
		GF_Autosuggest.suggestions.append('<div class="ne"></div>');
		GF_Autosuggest.suggestions.append('<div class="nw"></div>');
		GF_Autosuggest.suggestions.append('<div class="se"></div>');
		GF_Autosuggest.suggestions.append('<div class="sw"></div>');
		GF_Autosuggest.suggestions.append('<ul></ul>');
		GF_Autosuggest.suggestions.css('display', 'none');
		$('body').append(GF_Autosuggest.suggestions);
	};
	
	/**
	* DeleteSuggestions()
	* 
	* Deletes the suggestion list from the document's DOM.
	* 
	* @return void
	*/
	this.DeleteSuggestions = function() {
		if ((GF_Autosuggest.suggestions instanceof jQuery) && GF_Autosuggest.suggestions.length)
			GF_Autosuggest.suggestions.remove();
		GF_Autosuggest.suggestions = GF.NULL;
	};
	
	/**
	* ExpandSuggestions()
	* 
	* Expands the suggestion list at the correct position below the input field.
	* If the list doesn't exist it invokes its creation.
	* 
	* @return void
	*/
	this.ExpandSuggestions = function() {
		var autosuggest = GF_Autosuggest.GetCurrentInstance(this);
		if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length) {
			autosuggest.CreateSuggestions();
			GF_Autosuggest.suggestions.css('width', autosuggest.m_jInput.width() + 12);
			GF_Autosuggest.suggestions.css('left', autosuggest.m_jInput.offset().left - 1);
			GF_Autosuggest.suggestions.css('top', autosuggest.m_jInput.offset().top + autosuggest.m_jInput.height() + 14);
			GF_Autosuggest.suggestions.fadeIn(200);
		}
	};
	
	/**
	* RetractSuggestions()
	* 
	* Retracts the suggestion list.
	* 
	* @return void
	*/
	this.RetractSuggestions = function() {
		if (!(GF_Autosuggest.suggestions instanceof jQuery) || !GF_Autosuggest.suggestions.length)
			return;
		var autosuggest = GF_Autosuggest.GetCurrentInstance(this);
		GF_Autosuggest.suggestions.fadeOut(200, autosuggest.DeleteSuggestions);
	};
	
	/* Constructor call */
	
	return this.GF_Autosuggest(target, options);
	
}, {
	
	enabled: true,           // Is the autosuggest enabled? It's temporarily disabled when user presses ESC.
	active: -1,              // The active autosuggest.
	last: -1,                // The last autosuggest that was selected - useful when handling the Blur event.
	suggestions: GF.NULL,    // jQuery pointer to the suggestion list.
	
	/**
	* GF_Autosuggest.GetSuggestions()
	* 
	* Requests suggestions for the currently active autosuggest using
	* its source function.
	* 
	* It sends a two-parameter request to the source function:
	* - request object:
	*   > id - autosuggest Id,
	*   > q - query typed by the user,
	*   > n - max. number of suggestions expected.
	* - name of the JS function to invoke when the answer is ready.
	* 
	* @return void
	*/
	GetSuggestions: function() {
		if (GF_Autosuggest.active < 0) {
			return;
		}
		var autosuggest = GF_Autosuggest.ReturnInstance(GF_Autosuggest.active);
		var query = autosuggest.m_jInput.val();
		if (query.length < autosuggest.m_iMinLength) {
			autosuggest.RetractSuggestions();
			return;
		}
		if (autosuggest.pending)
			return;
		autosuggest.pending = true;
		autosuggest.m_fSource({
			id: autosuggest.m_iId,
			q: query,
			n: autosuggest.m_iMaxSuggestions
		}, 'GF_Autosuggest.ProcessSuggestions');
	},
	
	/**
	* GF_Autosuggest.ProcessSuggestions(data)
	* 
	* Method that passes the response to the proper autosuggest object.
	* 
	* It should be called by the server/source function when the response
	* is ready. The response should be passed as its argument.
	* 
	* Response should consist of following attributed:
	* - id - id of the autosuggest (same as acquired in the request)
	* - q - the query for which the suggestions are
	* - s - array of objects for each of the suggestions:
	*   > name - the name that will be displayed in the list
	* 
	* @param data object - Response object.
	*/
	ProcessSuggestions: function(data) {
		if (GF_Autosuggest.ReturnInstance(data.id) != undefined) {
			var autosuggest = GF_Autosuggest.ReturnInstance(data.id);
			autosuggest.pending = false;
			autosuggest.LoadSuggestions(data);
			if (data.q != autosuggest.m_jInput.val()) {
				GF_Autosuggest.GetSuggestions();
			}
		}
	}
	
});

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_Autosuggest.Language = {
}

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_ConflictResolver = GF_Instance.GF_Extend('GF_ConflictResolver', function(jTarget, oOptions) {
	
	/* Declarations */
	
	this.m_fUpdateFunction;
	this.m_fRetryFunction;
	this.m_fCancelFunction;
	this.m_aConflictGroups;
	
	this.GF_ConflictResolver;
	this.Update;
	this.Cancel;
	this.Retry;
	this.Rewrite;
	
	this._ProcessData;
	
	/* Definitions */
	
	this.GF_ConflictResolver = GF.NewConstructor(function(jTarget, oOptions) {
		GF_ConflictResolver.s_dInstance = this;
		this.m_fUpdateFunction = oOptions.update_function;
		this.m_fRetryFunction = oOptions.retry_function;
		this.m_fCancelFunction = oOptions.cancel_function;
		this.m_aConflictGroups = [];
		this.Update();
	});
	
	this.Update = GF.NewEventHandler(function() {
		this.m_fUpdateFunction({
			id: this.m_iId
		}, 'GF_ConflictResolver.ProcessIncomingData');
	});
	
	this._ProcessData = function(oData) {
		this.m_aConflictGroups = oData.conflict_groups;
		this.Rewrite();
	};
	
	this.Retry = GF.NewEventHandler(function(eEvent) {
		var dCr = GF_ConflictResolver.GetMain();
		if (dCr.m_aConflictGroups[eEvent.data.iGroup] != undefined) {
			dCr.m_fRetryFunction({
				model: dCr.m_aConflictGroups[eEvent.data.iGroup]['model'],
				method: dCr.m_aConflictGroups[eEvent.data.iGroup]['method'],
				input: dCr.m_aConflictGroups[eEvent.data.iGroup]['input']
			});
		}
		else {
			dCr.Update();
		}
		return false;
	});
	
	this.Cancel = GF.NewEventHandler(function(eEvent) {
		var dCr = GF_ConflictResolver.GetMain();
		if (dCr.m_aConflictGroups[eEvent.data.iGroup] != undefined) {
			dCr.m_fCancelFunction({
				model: dCr.m_aConflictGroups[eEvent.data.iGroup]['model'],
				method: dCr.m_aConflictGroups[eEvent.data.iGroup]['method'],
				input: dCr.m_aConflictGroups[eEvent.data.iGroup]['input']
			});
		}
		else {
			dCr.Update();
		}
		return false;
	});
	
	this.Rewrite = GF.NewSafeMethod(function() {
		if (this.m_aConflictGroups.length) {
			this.m_jTarget.empty();
			this.m_jTarget.append('<h2>' + GF_ConflictResolver.Language.conflict_resolving + '</h2>');
			this.m_jTarget.append('<p>' + GF_ConflictResolver.Language.conflicts_encountered + '</p>');
			var jConflictGroupsList = $('<ul></ul>');
			for (var i = 0; i < this.m_aConflictGroups.length; i++) {
				var jConflictGroup = $('<li></li>');
				jConflictGroup.append('<h3>' + 'Tu fajnie byloby napisac, ze user probowal wykonac taka, a nie inna operacje' + '</h3>');
				jConflictGroup.append($('<p></p>').append($('<a href="#">' + GF_ConflictResolver.Language.try_again + '</a>').bind('click', {iGroup: i}, this.Retry)));
				jConflictGroup.append($('<p></p>').append($('<a href="#">' + GF_ConflictResolver.Language.cancel + '</a>').bind('click', {iGroup: i}, this.Cancel)));
				var jConflictTablesList = $('<ul></ul>');
				for (var k in this.m_aConflictGroups[i].tables) {
					var jConflictTable = $('<li></li>');
					jConflictTable.append('<h4>' + this.m_aConflictGroups[i].tables[k]['message'] + '</h4>');
					jConflictTable.append($('<p><a href="/admin/' + this.m_aConflictGroups[i].tables[k]['controller'] + '">' + GF_ConflictResolver.Language.go_to_controller + '</a></p>'));
					var jConflictsList = $('<ul></ul>');
					for (var j = 0; j < this.m_aConflictGroups[i].tables[k]['conflicts']; j++) {
						var jConflict = $('<li></li>');
						for (var l in this.m_aConflictGroups[i].tables[k]['columns']) {
							jConflict.append(this.m_aConflictGroups[i].tables[k]['columns'][l]['values'][j] + ' ');
						}
						jConflictsList.append(jConflict);
					}
					jConflictTable.append(jConflictsList);
					jConflictTablesList.append(jConflictTable);
				}
				jConflictGroup.append(jConflictTablesList);
				jConflictGroupsList.append(jConflictGroup);
			}
			this.m_jTarget.append(jConflictGroupsList);
			this.m_jTarget.slideDown('fast');
		}
		else {
			this.m_jTarget.slideUp('fast', function() {
				$(this).empty();
			});
		}
	});
	
	/* Constructor call */
	
	return this.GF_ConflictResolver(jTarget, oOptions);
	
}, {
	
	s_dInstance: GF.NULL,
	
	GetMain: GF.NewSafeMethod(function() {
		return GF_ConflictResolver.s_dInstance;
	}),
	
	ProcessIncomingData: GF.NewEventHandler(function(oData) {
		if ((oData.id == undefined) || !(GF_Instance.s_aoInstances[oData.id] instanceof GF_ConflictResolver)) {
			GF_Debug.Error('The server has responded with an invalid conflict resolver id (' + oData.id + ').');
		}
		var dCr = GF_Instance.ReturnInstance(oData.id);
		dCr._ProcessData(oData);
	})
	
});

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_ConflictResolver.Language = {
	conflict_resolving: 'Rozwiązywanie konfliktów',
	conflicts_encountered: 'Próbowano dokonać operacji która jest niemożliwa z powodu wystąpienia konfliktów.',
	try_again: 'Spróbuj ponownie',
	cancel: 'Anuluj operację',
	go_to_controller: 'Przejdź do odpowiedniego modułu, aby rozwiązać problem'
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /*
* WYSKAKUJACY ALERT
* Po wywolaniu konstruktora, u uzytkownika pojawia sie na ekranie ladny alert.
* @deprecated
* @param String title Tytul errora
* @param String message Tresc errora
* @param Mixed ok Opcjonalny parametr - jesli true (domyslna wartosc), to pojawia sie przycisk OK. Jesli jest to wskaznik do funkcji JS, wowczas funkcja ta jest wywolywana po kliknieciu przez uzytkownika na przycisk OK.
* @param Mixed cancel Opcjonalny parametr - jesli true (domyslnie false), to pojawia sie przycisk Anuluj. Jesli jest to wskaznik do funkcji JS, wowczas funkcja ta jest wywolywana po kliknieciu przez uzytkownika na przycisk Anuluj.
* @param Mixed paramOk Opcjonalny parametr - jesli ustawiony, jest to parametr wywolania funkcji zdefiniowanej jako uruchamiana w momencie klikniecia OK.
* @param Mixed paramCancel Opcjonalny parametr - jesli ustawiony, jest to parametr wywolania funkcji zdefiniowanej jako uruchamiana w momencie klikniecia Anuluj.
*/

function GF_Alert(title, message, ok, cancel, paramOk, paramCancel) {

	this.m_sTitle = title;
	this.m_sMessage = message;
	this.m_cOk = ok;
	this.m_cCancel = cancel;
	this.m_mParamOk = paramOk;
	this.m_mParamCancel = paramCancel;

	this.m_iId = GF_Alert.instances.push(this) - 1;

	this.create = function() {
		// Portowanie starych wywolan alertow do nowego systemu.
		// W nowych przypadkach nalezy bezwzglednie uzywac pochodnych GAlert.
		
		if (this.m_cOk instanceof Function) {
			var mOkParams = this.m_mParamOk;
			var fCallbackOk = this.m_cOk;
			var fOk = function(eEvent) {
				fCallbackOk(mOkParams);
				GAlert.DestroyThis.apply($(this).closest('.message').get(0));
			};
		}
		else {
			var fOk = GAlert.DestroyThis;
		}
		if (this.m_cCancel instanceof Function) {
			var mCancelParams = this.m_mParamCancel;
			var fCallbackCancel = this.m_cCancel;
			var fCancel = function(eEvent) {
				fCallbackCancel(mCancelParams);
				GAlert.DestroyThis.apply($(this).closest('.message').get(0));
			};
		}
		else {
			var fCancel = GAlert.DestroyThis;
		}
		
		if (this.m_cOk && this.m_cCancel) {
			GWarning(this.m_sTitle, this.m_sMessage, {
				bAutoExpand: true,
				aoPossibilities: [
					{mLink: fOk, sCaption: GF_Alert.Language.ok},
					{mLink: fCancel, sCaption: GF_Alert.Language.cancel}
				]
			});
			return;
		}
		
		if (this.m_cOk) {
			GWarning(this.m_sTitle, this.m_sMessage, {
				bAutoExpand: true,
				aoPossibilities: [
					{mLink: fOk, sCaption: GF_Alert.Language.ok}
				]
			});
			return;
		}
		
		GWarning(this.m_sTitle, this.m_sMessage);
		return;

		if (!$('#alert-overlay').length) {
			$('body').append('<div id="alert-overlay"></div>');
		}
		$('#alert-overlay').fadeIn(250);
		$('#alert-overlay').append('<div class="alert" id="alert-' + (GF_Alert.instances.length - 1) + '"><h2>' + this.m_sTitle + '</h2><p>' + this.m_sMessage + '</p><ul class="close"><li class="ok"><a href="#">' + GF_Alert.Language.ok + '</a></li>' + (((typeof this.m_cCancel == 'function') || (this.m_cCancel)) ? ' <li class="cancel"><a href="#">' + GF_Alert.Language.cancel + '</a></li>' : '') + '</ul><div class="ending"></div></div>');
		$('#alert-overlay').css('height', $(document).height()).find('.close .ok a').click(function() {
			var id = $(this).parents('.alert').attr('id').split('-');
			GF_Alert.instances[id[1]].close();
			if (typeof GF_Alert.instances[id[1]].m_cOk == 'function') {
				if (GF_Alert.instances[id[1]].m_mParamOk != undefined) {
					GF_Alert.instances[id[1]].m_cOk(GF_Alert.instances[id[1]].m_mParamOk);
				}
				else {
					GF_Alert.instances[id[1]].m_cOk();
				}
			}
			delete GF_Alert.instances[id[1]];
			return false;
		}).parents('.alert').css('margin-top', - $('#alert-overlay .alert').height()).animate({marginTop: ($(window).scrollTop() + 200) + "px" }, {queue: false, duration: 500}).find('.close .cancel a').click(function() {
			var id = $(this).parents('.alert').attr('id').split('-');
			GF_Alert.instances[id[1]].close();
			if (typeof GF_Alert.instances[id[1]].m_cFalse == 'function') {
				if (GF_Alert.instances[id[1]].m_mParamCancel != undefined) {
					GF_Alert.instances[id[1]].m_cCancel(GF_Alert.instances[id[1]].m_mParamCancel);
				}
				else {
					GF_Alert.instances[id[1]].m_cCancel();
				}
			}
			delete GF_Alert.instances[id[1]];
			return false;
		});
		$(window).scroll(function() {
			$('#alert-overlay .alert').animate({
				marginTop: ($(window).scrollTop() + 200) + "px"
			}, {
				queue: false, duration: 500
			});
		});
		$('#alert-overlay').bind('keydown', GF_Alert.KeyPressed);
		$('#alert-overlay').find('.alert:first .ok a').focus();
	};

	this.close = function() {
		$('#alert-' + this.m_iId).remove();
		var j = 0;
		for (var i in GF_Alert.instances) {
			if (GF_Alert.instances[i] != undefined) {
				j++;
			}
		}
		if (j <= 1) {
			$('#alert-overlay').unbind('keydown', GF_Alert.KeyPressed);
			$('#alert-overlay').fadeOut('fast', function() {
				//$(this).remove();
			});
		}
	};

	this.create();

};

GF_Alert.KeyPressed = function(e) {
	switch (e.which) {
		case 13:
			$('#alert-overlay').find('.alert:first .ok a').click();
			e.stopImmediatePropagation();
			return false;
		case 27:
			if ($('#alert-overlay').find('.alert:first .cancel a').length)
				$('#alert-overlay').find('.alert:first .cancel a').click();
			else
				$('#alert-overlay').find('.alert:first .ok a').click();
			e.stopImmediatePropagation();
			return false;
	}
	return true;
};

GF_Alert.instances = new Array();

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_Alert.Language = {
	ok: 'OK',
	cancel: 'Anuluj'
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_ContextMenu = GF_Instance.GF_Extend('GF_ContextMenu', function(jTarget, oOptions) {
	
	/* Declarations */
	
	this.m_oOptions;
	this.m_bActive;
	this.m_cSup;
	this.m_cSub;
	
	this.GF_ContextMenu;
	this.Show;
	this.Hide;
	this.Close;
	
	this._CloseOther;
	this._CreateContextMenu;
	this._WriteAction;
	
	/* Definitions */
	
	this.GF_ContextMenu = GF.NewConstructor(function(jTarget, oOptions) {
		this.m_bActive = false;
		this.m_oOptions = new GF_ContextMenu_Config(oOptions);
		this._CreateContextMenu();
		this.Show();
	});
	
	this._CloseOther = function() {
		var jOther = $('.GF_ContextMenu');
		for (var i = 0; i < jOther.length; i++) {
			GF_ContextMenu.GetInstance(jOther.eq(i)).Close();
		}
	};
	
	this._CreateContextMenu = function() {
		var jMenu = this.m_jTarget;
		jMenu.append('<div class="w"></div>');
		jMenu.append('<div class="nw"></div>');
		jMenu.append('<div class="sw"></div>');
		jMenu.append('<div class="s"></div>');
		jMenu.append('<div class="se"></div>');
		jMenu.addClass('GF_ContextMenu');
		jMenu.css('display', 'none');
		var jUl = $('<ul></ul>');
		for (var i in this.m_oOptions.actions) {
			var oA = this.m_oOptions.actions[i];
			jUl.append(this._WriteAction(oA, this.m_oOptions.id));
		}
		jMenu.mouseout(this.Hide);
		jMenu.append(jUl);
		jMenu.css('left', this.m_oOptions.position.left).css('top', this.m_oOptions.position.top);
	};
	
	this._WriteAction = function(oA, sId) {
		var jLi = $('<li></li>');
		var jAnchor = $('<a href="#"><img src="' + oA.img + '" alt=""/>' + oA.caption + '</a>');
		if (GF.CountMembers(oA.values)) {
			var aoSubActions = [];
			for (var i in oA.values) {
				aoSubActions.push(new GF_Action({
					img: oA.img,
					action: oA.action,
					caption: oA.values[i],
					parameter: i
				}));
			}
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				var cCm = GF_ContextMenu.GetCurrentInstance(this);
				var cSub = GF_ContextMenu.Create({
					id: sId,
					actions: aoSubActions,
					position: {
						left: eEvent.pageX + 9,
						top: eEvent.pageY + 2
					}
				});
				cCm.m_cSub = cSub.m_jTarget;
				cSub.m_cSup = cCm;
				return false;
			}));
		}
		else {
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				if (oA.parameter != GF.NULL) {
					oA.action(sId, oA.parameter);
				}
				else {
					oA.action(sId);
				}
				return false;
			}));
			jAnchor.click(GF_ContextMenu.HandleHide);
		}
		jLi.append(jAnchor);
		return jLi;
	};
	
	this.Show = GF.NewEventHandler(function(eEvent) {
		var cCm = GF_ContextMenu.GetCurrentInstance(this);
		//cCm._CloseOther();
		this.m_bActive = true;
		GF_ContextMenu.s_iActive = this.m_iId;
		$('body').append(cCm.m_jTarget);
		cCm.m_jTarget.fadeIn(200);
	});
	
	this.Hide = GF.NewEventHandler(function(eEvent) {
		var cCm = GF_ContextMenu.GetCurrent();
		try {
			var jNewTarget = $(eEvent.relatedTarget);
		} catch(xException) {
			return true;
		};
		if (!cCm || !cCm.m_jTarget.length || jNewTarget.parents('.GF_ContextMenu').length || jNewTarget.hasClass('spacer') || (jNewTarget.parents().index(cCm.m_oOptions.parent) != -1) || (cCm.m_cSub && (jNewTarget.parents().index(cCm.m_cSub.m_jTarget) != -1))) {
			return true;
		}
		if (cCm.m_cSup) {
			GF_ContextMenu.s_iActive = cCm.m_cSup.m_iId;
		}
		else {
			GF_ContextMenu.s_iActive = -1;
		}
		cCm.m_jTarget.fadeOut(200, GF.NewEventHandler(function(eEvent) {
			$(this).remove();
			var cCm = GF_ContextMenu.GetCurrentInstance(this);
			if (cCm) {
				cCm.Close(eEvent);
			}
		}));
		return true;
	});
	
	this.Close = GF.NewEventHandler(function(eEvent) {
		var cCm = GF_ContextMenu.GetCurrentInstance(this);
		cCm.m_jTarget.remove();
		cCm.m_bActive = false;
	});
	
	/* Constructor call */
	
	return this.GF_ContextMenu(jTarget, oOptions);
	
}, {
	
	s_iActive: -1,
	
	Create: function(oOptions) {
		return new GF_ContextMenu($('<div></div>'), oOptions);
	},
	
	HandleHide: GF.NewEventHandler(function(eEvent) {
		if (GF_ContextMenu.s_iActive == -1) {
			return true;
		}
		return GF_ContextMenu.ReturnInstance(GF_ContextMenu.s_iActive).Hide(eEvent);
	}),
	
	GetCurrent: function() {
		try {
			if (GF_ContextMenu.s_iActive != -1) {
				return GF_ContextMenu.ReturnInstance(GF_ContextMenu.s_iActive);
			}
			return false;
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			return false;
		}
	},
	
	HideAll: function() {
		try {
			if (GF_ContextMenu.s_iActive != -1) {
				GF_ContextMenu.ReturnInstance(GF_ContextMenu.s_iActive).Close();
				GF_ContextMenu.s_iActive = -1;
			}
			return true;
		}
		catch (xException) {
			GF_Debug.HandleException(xException);
			return false;
		}
	}
	
});

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_ContextMenu_Config = GF_Config.GF_Extend('GF_ContextMenu_Config', function(options) {
	
	this.GF_ContextMenu_Config = GF.NewConstructor(function(options) {
		
		this.id = GF.NULL;
		this.parent = GF.NULL;
		this.actions = [];
		this.position = {
			left: 0,
			top: 0
		};
		
		this._Process(options);
		
	});
	
	this._PostProcess = function() {
	};
	
	/* Constructor call */
	
	return this.GF_ContextMenu_Config(options);
	
});

GF_Datagrid_Data_Provider = function(oOptions, aoData) {
	
	this.m_iId;
	this.m_oOptions;
	this.m_aoData;
	this.m_oRequest;
	this.m_aoLastFilter;
	this.m_sResponseHandler;
	
	this.GF_Datagrid_Data_Provider;
	this.Load;
	
	this.GF_Datagrid_Data_Provider = GF.NewConstructor(function(oOptions, aoData) {
		this.m_iId = GF_Datagrid_Data_Provider.s_adProviders.push(this) - 1;
		this.m_oOptions = oOptions;
		this.m_aoData = aoData;
		this._ProcessAllRows();
	});
	
	this.GetData = function() {
		return this.m_aoData;
	};
	
	this.Load = function(oRequest, sResponseHandler) {
		this.m_sResponseHandler = sResponseHandler;
		if (isNaN(oRequest.starting_from)) {
			oRequest.starting_from = 0;
		}
		if (this.m_oRequest) {
			this.m_aoLastFilter = this.m_oRequest.where;
		}
		this.m_oRequest = oRequest;
		if ((this.m_oOptions.event_handlers != undefined) && (this.m_oOptions.event_handlers.filter_change != undefined) && (this.m_oOptions.event_handlers.filter_change instanceof Function) && this.m_aoLastFilter && this._CheckIfFilterHasChanged(this.m_oRequest.where)) {
			this.m_oOptions.event_handlers.filter_change({
				provider_id: this.m_iId,
				request: this.m_oRequest,
				callback: 'GF_Datagrid_Data_Provider.ChangeData'
			});
		}
		else {
			this.ReturnData();
		}
	};
	
	this.ReturnData = function() {
		var oRequest = this.m_oRequest;
		var aoData = this.m_aoData.slice(0);
		var aoDataFiltered = this._FilterData(aoData, oRequest);
		var aoDataSorted = this._SortData(aoDataFiltered, oRequest);
		var aoDataChunked = this._ChunkData(aoDataSorted, oRequest);
		var oResponse = {
			data_id: oRequest['id'],
			rows_num: aoDataChunked.length,
			starting_from: (oRequest['starting_from'] ? oRequest['starting_from'] : 0),
			total: aoData.length,
			filtered: aoDataFiltered.length,
			rows: aoDataChunked,
			additional_rows: this._CalculateAdditionalRows(aoDataFiltered)
		};
		eval(this.m_sResponseHandler + '(oResponse);');
	};
	
	this._CheckIfFilterHasChanged = function(aoFilter) {
		if (this.m_aoLastFilter.length != aoFilter.length) {
			return true;
		}
		for (var i = 0; i < this.m_aoLastFilter.length; i++) {
			if ((this.m_aoLastFilter[i].operator != aoFilter[i].operator) || (this.m_aoLastFilter[i].value != aoFilter[i].value)) {
				return true;
			}
		}
		return false;
	};
	
	this.ChangeData = function(aoData) {
		if (aoData instanceof Array) {
			this.m_aoData = aoData;
		}
		else {
			this.m_aoData = [];
		}
		this._ProcessAllRows();
	};
	
	this.UpdateRow = function(sId, oNewRow) {
		for (var i in this.m_aoData) {
			if (this.m_aoData[i][this.m_oOptions.key] == sId) {
				var oPreviousRow = this.m_aoData[i];
				if (oNewRow != undefined) {
					this.m_aoData[i] = oNewRow;
					this.m_aoData[i] = this._ProcessRow(this.m_aoData[i]);
					this._InvokeEvent('row_change', {
						modified_row_id: sId,
						modified_row_key: i,
						modified_row: this.m_aoData[i],
						previous_row: oPreviousRow,
						rows: this.m_aoData
					});
					this.m_aoData[i] = this._ProcessRow(this.m_aoData[i]);
					this._InvokeEvent('change', {
						rows: this.m_aoData
					});
				}
				return this.m_aoData[i];
			}
		}
		GF_Debug.Error('Requested row (' + sId + ') was not found in the DG Data Provider when update was requested.');
	};
	
	this.DeleteRow = function(sId) {
		for (var i in this.m_aoData) {
			if (this.m_aoData[i][this.m_oOptions.key] == sId) {
				var oPreviousRow = this.m_aoData[i];
				this.m_aoData.splice(i, 1);
				this._InvokeEvent('row_delete', {
					deleted_row_id: sId,
					deleted_row_key: i,
					deleted_row: oPreviousRow,
					rows: this.m_aoData
				});
				this._InvokeEvent('change', {
					rows: this.m_aoData
				});
				return;
			}
		}
		GF_Debug.Error('Requested row (' + sId + ') was not found in the DG Data Provider when deletion was requested.');
	};
	
	this.AddRow = function(oRow) {
		oRow = this._ProcessRow(oRow);
		var sId = oRow[this.m_oOptions.key];
		if (sId == undefined) {
			GF_Debug.Warning('Tried to add a row without an id in the DG Data Provider.');
			return;
		}
		for (var i in this.m_aoData) {
			if (this.m_aoData[i][this.m_oOptions.key] == sId) {
				GF_Debug.Warning('Tried to add a row with an identical id (' + sId + ') that already exists in the DG Data Provider.');
				return;
			}
		}
		var iLength = this.m_aoData.push(oRow);
		this._InvokeEvent('row_add', {
			added_row_id: sId,
			added_row_key: iLength - 1,
			added_row: oRow,
			rows: this.m_aoData
		});
		this._InvokeEvent('change', {
			rows: this.m_aoData
		});
	};
	
	this.GetRow = function(sId) {
		for (var i in this.m_aoData) {
			if (this.m_aoData[i][this.m_oOptions.key] == sId) {
				return this.m_aoData[i];
			}
		}
		GF_Debug.Warning('Requested row (' + sId + ') was not found in the DG Data Provider.');
	};
	
	this._ProcessAllRows = function() {
		for (var i in this.m_aoData) {
			this.m_aoData[i] = this._ProcessRow(this.m_aoData[i]);
		}
	};
	
	this._ProcessRow = function(oRow) {
		if (this.m_oOptions.preProcess instanceof Function) {
			oRow = this.m_oOptions.preProcess(oRow);
		}
		return oRow;
	};
	
	this._CalculateAdditionalRows = function(aoRows) {
		if (!this.m_oOptions.additional_rows) {
			return [];
		}
		var aoAdditionalRows = [];
		for (var i in this.m_oOptions.additional_rows) {
			var oRow = {};
			if (this.m_oOptions.additional_rows[i].source instanceof Function) {
				oRow = this.m_oOptions.additional_rows[i].source(aoRows)
			}
			oRow._options = this.m_oOptions.additional_rows[i];
			aoAdditionalRows.push(oRow);
		}
		return aoAdditionalRows;
	};
	
	this._FilterData = function(aoRows, oRequest) {
		var aoFilteredRows = [];
		for (var i in aoRows) {
			var bSkipRow = false;
			for (var j in oRequest.where) {
				if (!oRequest.where[j]) break;
				var sValue = aoRows[i][oRequest.where[j].column];
				if (sValue == undefined) {
					bSkipRow = true;
					continue;
				}
				switch (oRequest.where[j].operator) {
					
					case 'GE':
						if (!isNaN(parseFloat(sValue)) && !isNaN(parseFloat(oRequest.where[j].value))) {
							if (parseFloat(sValue) < parseFloat(oRequest.where[j].value)) {
								bSkipRow = true;
							}
						}
						else {
							if (sValue < oRequest.where[j].value) {
								bSkipRow = true;
							}
						}
						break;
						
					case 'LE':
						if (!isNaN(parseFloat(sValue)) && !isNaN(parseFloat(oRequest.where[j].value))) {
							if (parseFloat(sValue) > parseFloat(oRequest.where[j].value)) {
								bSkipRow = true;
							}
						}
						else {
							if (sValue > oRequest.where[j].value) {
								bSkipRow = true;
							}
						}
						break;
						
					case 'LIKE':
						if (sValue.toUpperCase().indexOf(oRequest.where[j].value.substr(1, oRequest.where[j].value.length - 2).toUpperCase()) == -1) {
							bSkipRow = true;
						}
						break;
						
				}
				if (bSkipRow) {
					break;
				}
			}
			if (!bSkipRow) {
				aoFilteredRows.push(aoRows[i]);
			}
		}
		return aoFilteredRows;
	};
	
	this._SortData = function(aoRows, oRequest) {
		aoRows.sort(function(a, b) {
			if ((a[oRequest.order_by] == undefined) || (b[oRequest.order_by] == undefined)) {
				return 0;
			}
			if (!isNaN(parseFloat(a[oRequest.order_by])) && !isNaN(parseFloat(b[oRequest.order_by]))) {
				if (oRequest.order_dir == 'asc') {
					return (parseFloat(a[oRequest.order_by]) > parseFloat(b[oRequest.order_by])) ? 1 : -1;
				}
				else {
					return (parseFloat(a[oRequest.order_by]) > parseFloat(b[oRequest.order_by])) ? -1 : 1;
				}
			}
			var iMod = (oRequest.order_dir == 'asc') ? 1 : -1;
			for (var i = 0; i < Math.max(a[oRequest.order_by].length, b[oRequest.order_by].length); i++) {
				if (a[oRequest.order_by].charAt(i) == undefined) {
					return iMod;
				}
				if (b[oRequest.order_by].charAt(i) == undefined) {
					return -iMod;
				}
				if (parseInt(a[oRequest.order_by].toUpperCase().charCodeAt(i)) > parseInt(b[oRequest.order_by].toUpperCase().charCodeAt(i))) {
					return iMod;
				}
				if (parseInt(a[oRequest.order_by].toUpperCase().charCodeAt(i)) < parseInt(b[oRequest.order_by].toUpperCase().charCodeAt(i))) {
					return -iMod;
				}
			}
			return 0;
		});
		return aoRows;
	};
	
	this._ChunkData = function(aoRows, oRequest) {
		return aoRows.slice(oRequest.starting_from, oRequest.starting_from + oRequest.limit);
	};
	
	this._InvokeEvent = function(sEvent, oParams) {
		if ((this.m_oOptions.event_handlers != undefined) && (this.m_oOptions.event_handlers[sEvent] != undefined) && (this.m_oOptions.event_handlers[sEvent] instanceof Function)) {
			this.m_oOptions.event_handlers[sEvent](oParams);
		}
	};
	
	/* Constructor call */
	
	return this.GF_Datagrid_Data_Provider(oOptions, aoData);
	
};

GF_Datagrid_Data_Provider.s_adProviders = [];

GF_Datagrid_Data_Provider.ReturnInstance = function(iId) {
	if (GF_Datagrid_Data_Provider.s_adProviders[iId] != undefined) {
		return GF_Datagrid_Data_Provider.s_adProviders[iId];
	}
	return null;
};

GF_Datagrid_Data_Provider.ChangeData = function(oData) {
	if (oData.provider_id == undefined) {
		return;
	}
	var dProvider = GF_Datagrid_Data_Provider.ReturnInstance(oData.provider_id);
	dProvider.ChangeData(oData.data);
};

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 /**
* GF_Datagrid(jTarget, oOptions)
* 
* 
* 
* @return bool - False on error.
* @param jTarget jQuery - Target which is being used to initialize the newly
*  created object.
* @param oOptions object - Configuration options object.
*/
GF_Datagrid = GF_Instance.GF_Extend('GF_Datagrid', function(jTarget, oOptions) {
	
	/* Declarations */
	
	this.m_oOptions;            // Configuration options
	this.m_aoColumns;
	this.m_aiColumnsOrder;
	this.m_oColumnsLookup;
	this.m_aoRows;
	this.m_aoAdditionalRows;
	this.m_asSelected;
	this.m_jHeader;
	this.m_jBody;
	this.m_jAdditionalRows;
	this.m_jFooter;
	this.m_jInput;
	this.m_jModifyColumnsDialog;
	this.m_s_cContextMenu;
	this.m_aoAdditionalFilters;
	
	this.m_iPadding;
	this.m_iWidth;
	this.m_iTotalWidth;
	this.m_iScrollbarWidth;
	
	this.m_i_SpacerDrag;
	
	this.m_iFirstRecordId;
	this.m_iRows;
	this.m_iTotalRows;
	this.m_iFilteredRows;
	this.m_sOrderBy;
	this.m_iOrderDirection;
	this.m_iPage;
	this.m_iTotalPages;
	
	this.GF_Datagrid;               // GF_Datagrid(jTarget, oOptions)
	this.GetSelected;               // GetSelected()
	this.IsSelected;                // IsSelected(mRow)
	this.ClearSelection;            // ClearSelection([eEvent])
	this.LoadData;                  // LoadData()
	this.Rewrite;                   // Rewrite()
	this.SelectThisRow;             // SelectThisRow(eEvent)
	this.SelectRow;                 // SelectRow(sId)
	this.DeselectRow;               // DeselectRow(sId)
	this.SelectVisible;             // SelectVisible()
	this.DeselectVisible;           // DeselectVisible()
	this.GoToPage;                  // GoToPage(iPage)
	this.GoToNext;                  // GoToNext()
	this.GoToPrevious;              // GoToPrevious()
	this.GoToFirst;                 // GoToFirst()
	this.UpdateSelection;           // UpdateSelection()
	this.UpdateIndicators;          // UpdateIndicators()
	this.ToggleFilters;             // ToggleFilters([eEvent])
	this.GetRow;                    // GetRow(sId)
	this.GetCurrentRow;             // GetCurrentRow(jTr)
	this.ChangeAdditionalFilters;   // ChangeAdditionalFilters(aoFilters)
	this.GetRowIdByAttribute;       // GetRowIdByAttribute(sAttribute, mValue)
	
	this._ParseOptions;             // _ParseOptions(oOptions)
	this._ParseColumnOptions;       // _ParseColumnOptions()
	
	this._InitializeSpacers;        // _InitializeSpacers()
	this._SpacerDragInit;           // _SpacerDragInit(eEvent)
	this._SpacerDrag;               // _SpacerDrag(eEvent)
	this._SpacerDragStop;           // _SpacerDragStop(eEvent)
	
	this.WaitingScreenShow;        // WaitingScreenShow()
	this.WaitingScreenHide;        // WaitingScreenHide()
	
	this._ProcessData;              // _ProcessData(data)
	
	this._WriteRow;                 // _WriteRow(oR)
	this._WriteRowsActions;         // _WriteRowsActions(oR)
	this._WriteRowsAction;          // _WriteRowsAction(oA)
	this._ProcessRowsActions;       // _ProcessRowsActions(aoActions)
	this._WriteGroupActions;        // _WriteGroupActions()
	this._WriteGroupAction;         // _WriteGroupAction(oA)
	this._ProcessGroupActions;      // _ProcessGroupActions(aoActions)
	this._ShowSelection;            // _ShowSelection(jTr)
	
	this._ChangeOrder;              // _ChangeOrder(eEvent)
	
	this._GetNameForTd;             // _GetNameForTd(jTd)
	this._GetIdFromTr;              // _GetIdFromTr(jTr)
	this._GetTrForId;               // _GetTrForId(sId)
	this._GetColumnByName;          // _GetColumnByName(sId)
	this._GetConditionForField;     // _GetConditionForField(sId)
	
	this._CreateDatagrid;           // _CreateDatagrid()
	this._CreateHeader;             // _CreateHeader()
	this._WriteHeader               // _WriteHeader()
	this._CreateFilter;             // _CreateFilter()
	this._WriteFilters;             // _WriteFilters()
	this._WriteFilter;              // _WriteFilter(oF)
	this._CreateBody;               // _CreateBody()
	this._CreateFooter;             // _CreateFooter()
	this._CreateModifyColumnsDialog;// _CreateModifyColumnsDialog()
	
	this._InitializeWidths;         // _InitializeWidths()
	this._UpdateWidths;             // _UpdateWidths()
	this._UpdateHeights;            // _UpdateHeights()
	
	this._InitializeEvents;         // _InitializeEvents()
	this._InitializeRowEvents;      // _InitializeRowEvents(jTr)
	
	this._HandleGoToPage;           // _HandleGoToPage(eEvent)
	this._HandleGoToNext;           // _HandleGoToNext(eEvent)
	this._HandleGoToPrevious;       // _HandleGoToPrevious(eEvent)
	this._HandleGoToFirst;          // _HandleGoToFirst(eEvent)
	this._HandleToggleColumns;      // _HandleToggleModifyCol(eEvent)
	this._HandleCheckRowsCheckbox;  // _HandleCheckRowsCheckbox(eEvent)
	this._HandleToggleSelectVisible;// _HandleToggleSelectVisible(eEvent)
	this._HandleChangeColumns;      // _HandleChangeColumns(eEvent)
	this._HandleSaveColumns;        // _HandleSaveColumns(eEvent)
	this._HandleRestoreColumns;     // _HandleRestoreColumns(eEvent)
	this._HandleShowContextMenu;    // _HandleShowContextMenu(eEvent)
	this._HandleHideContextMenu;    // _HandleHideContextMenu(eEvent)
	
	this._NewRowEventHandler;       // _NewRowEventHandler(fHandler)
	
	/* Definitions */
	
	/**
	* GF_Datagrid(target, options)
	*
	* The GF_Datagrid constructor. Invokes all the necessary initialization
	* functions.
	* 
	* @return bool - False on error.
	* @param target jQuery - Target which is being used to initialize the newly
	*  created object.
	* @param options object - Configuration options object.
	*/
	this.GF_Datagrid = GF.NewConstructor(function(jTarget, oOptions) {
		
		this.RefreshTarget();
		
		this.m_aoColumns = [];
		this.m_aoRows = [];
		this.m_aoAdditionalRows = [];
		this.m_aiColumnsOrder = [];
		this.m_oColumnsLookup = {};
		this.m_oOptions = {};
		this.m_asSelected = [];
		
		this.m_iFirstRecordId = 0;
		this.m_iRows = 0;
		this.m_iTotalRows = 0;
		this.m_iFilteredRows = 0;
		this.m_sOrderBy = '';
		this.m_iOrderDirection = '';
		this.m_iPage = 0;
		this.m_iTotalPages = 0;
		
		this.m_bDontLoad = false;
		this.m_bFirstLoad = true;
		
		this.m_i_SpacerDrag = GF.NULL;
		
		this._ParseOptions(oOptions);
		
		this.m_sOrderBy = this.m_oOptions.mechanics.default_sorting;
		this.m_iOrderDirection = this._GetColumnByName(this.m_sOrderBy).sorting.default_order;
		
		this._CreateDatagrid();
		this._InitializeWidths();
		this._InitializeSpacers();
		this._InitializeEvents();
		this.m_oOptions.appearance.filter;
		
		this.LoadData();
		
	});
	
	this.GetSelected = function() {
		return this.m_asSelected;
	};
	
	this.IsSelected = function(mRow) {
		var sId;
		if (mRow instanceof $) {
			sId = this._GetIdFromTr(mRow);
		}
		else if ((typeof mRow == 'string') || (typeof mRow == 'number')) {
			sId = mRow;
		}
		else if (mRow instanceof Object) {
			sId = mRow[this.m_oOptions.mechanics.key];
		}
		else {
			GF_Debug.Error('Invalid argument passed to the datagrid\'s IsSelected method: ' + typeof oRow + '.');
		}
		for (var i in this.m_asSelected) {
			if (this.m_asSelected[i] == sId) {
				return true;
			}
		}
		return false;
	};
	
	this.ClearSelection = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var aiSelected = dDg.m_asSelected.concat([]);
		for (var i in aiSelected) {
			dDg.DeselectRow(aiSelected[i]);
		}
	});
	
	this.LoadData = function() {
		
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		
		if (dDg.m_bDontLoad) {
			return;
		}
		
		var aoColumns = [];
		var aConditions = [];
		
		dDg.WaitingScreenShow(function() {
			
			var dDg = GF_Datagrid.GetCurrentInstance(this);
			
			for (var i in dDg.m_aiColumnsOrder) {
				var oC = dDg.m_aoColumns[dDg.m_aiColumnsOrder[i]];
				aoColumns.push(oC.id);
				if (dDg.m_oOptions.appearance.filter) {
					aConditions = aConditions.concat(dDg._GetConditionForField(oC.id));
				}
			}
			
			aConditions = aConditions.concat(this.m_aoAdditionalFilters);
			
			var sCookie = sessionStorage.getItem('GF_Datagrid_State_' + Base64.encode(window.location.pathname + dDg.m_oOptions.id));
			
			var oRequest = {
				id: dDg.m_iId,
				from: aoColumns.join(', '),
				starting_from: (isNaN(dDg.m_iPage) ? 0 : dDg.m_iPage) * dDg.m_oOptions.mechanics.rows_per_page,
				limit: dDg.m_oOptions.mechanics.rows_per_page,
				order_by: dDg.m_sOrderBy,
				order_dir: (dDg.m_iOrderDirection == GF_Datagrid.SORT_DIR_ASC) ? 'asc' : 'desc',
				where: aConditions
			};
			
			if(this.m_oOptions.mechanics.persistent){
				if(this.m_bFirstLoad){
					if(sCookie){
						var oRequest =  $.parseJSON(Base64.decode(sCookie));
					}
					else if((window.location.hash).length){
						var oRequest =  $.parseJSON(Base64.decode(window.location.hash));
					}
				}
				
				var state = Base64.encode(JSON.stringify(oRequest));
				window.location.hash = state;
				
				sessionStorage.setItem('GF_Datagrid_State_' + Base64.encode(window.location.pathname + dDg.m_oOptions.id), state);
				
				this._ChangeOrderIndicator(oRequest.order_by,  oRequest.order_dir);
			}
			dDg.m_oOptions.event_handlers.load(oRequest, 'GF_Datagrid.ProcessIncomingData');
			this.m_bFirstLoad = false;
		});
		
	};
	
	this.Reset = function() {
		
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		
		dDg.m_jFilter.find('input, select').val('').parent().find('.faux span').text('');
		dDg.m_iPage = -1;
		dDg.GoToFirst();
		
	};
	
	this.Rewrite = function() {
		this.m_jBody.empty();
		this.m_jAdditionalRows.empty();
		for (var i in this.m_aoRows) {
			var oR = this.m_aoRows[i];
			var jTr = this._WriteRow(oR);
			jTr.addClass((i % 2) ? 'e' : 'o');
			this.m_jBody.append(jTr);
		}
		for (i in this.m_aoAdditionalRows) {
			oR = this.m_aoAdditionalRows[i];
			jTr = this._WriteAdditionalRow(oR);
			jTr.addClass((i % 2) ? 'e' : 'o');
			this.m_jAdditionalRows.append(jTr);
		}
		this._UpdateWidths();
		this._UpdateHeights();
		this.WaitingScreenHide();
		this.UpdateIndicators();
		if (this.m_oOptions.event_handlers.loaded instanceof Function) {
			this.m_oOptions.event_handlers.loaded(this);
		}
		this.UpdateSelection();
	};
	
	this.SelectThisRow = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var jTr = $(this).is('tr') ? $(this) : $(this).parents('tr:first');
		if (!jTr.length) {
			GF_Debug.Warning('Datagrid\'s SelectThisRow invoked neither on a TR element nor on any of its descendants. Selection aborted.');
			return false;
		}
		var sId = dDg._GetIdFromTr(jTr);
		if (dDg.m_oOptions.event_handlers.click_row instanceof Function) {
			if (!dDg.m_oOptions.event_handlers.click_row(dDg.m_iId, sId)) {
				return false;
			}
		}
		if (dDg.IsSelected(sId)) {
			dDg.DeselectRow(sId);
		}
		else {
			dDg.SelectRow(sId);
		}
		return false;
	});
	
	this.SelectRow = function(sId) {
		var jTr = this._GetTrForId(sId);
		if (GF.InArray(this.m_asSelected, sId)) {
			return false;
		}
		if (this.m_oOptions.mechanics.only_one_selected) {
			for (var i in this.m_asSelected) {
				this.DeselectRow(this.m_asSelected[i]);
			}
		}
		this.m_asSelected.push(sId);
		this._ShowSelection(jTr);
		if (this.m_oOptions.event_handlers.select instanceof Function) {
			this.m_oOptions.event_handlers.select(this, sId);
		}
		this.UpdateSelection();
	};
	
	this.DeselectRow = function(sId) {
		var jTr = this._GetTrForId(sId);
		GF.DeleteFromArray(this.m_asSelected, sId);
		jTr.removeClass('selected');
		GF.CheckboxesUncheck(jTr);
		if (this.m_oOptions.event_handlers.deselect instanceof Function) {
			this.m_oOptions.event_handlers.deselect(this, sId);
		}
		this.UpdateSelection();
	};
	
	this.SelectVisible = function() {
		for (var i in this.m_aoRows) {
			this.SelectRow(this.m_aoRows[i][this.m_oOptions.mechanics.key]);
		}
	};
	
	this.DeselectVisible = function() {
		for (var i in this.m_aoRows) {
			this.DeselectRow(this.m_aoRows[i][this.m_oOptions.mechanics.key]);
		}
	};
	
	this.GoToPage = function(iPage) {
		var iNewPage = Math.max(0, Math.min(this.m_iTotalPages - 1, iPage));
		if (iNewPage == this.m_iPage) {
			this.UpdateIndicators();
			return;
		}
		this.m_iPage = iNewPage;
		this.UpdateIndicators();
		this.LoadData();
	};
	
	this.GoToNext = function() {
		this.GoToPage(this.m_iPage + 1);
	};
	
	this.GoToPrevious = function() {
		this.GoToPage(this.m_iPage - 1);
	};
	
	this.GoToFirst = function() {
		this.GoToPage(0);
	};
	
	this.GoToLast = function() {
		this.GoToPage(this.m_iTotalPages - 1);
	};
	
	this.UpdateSelection = function() {
		var iSelected = 0;
		for (var i in this.m_aoRows) {
			if (this.IsSelected(this.m_aoRows[i])) {
				iSelected++;
			}
		}
		if (iSelected && (iSelected >= this.m_aoRows.length)) {
			if (this.m_jHeader && this.m_jHeader.length) {
				GF.CheckboxesCheck(this.m_jHeader.find('.GF_Datagrid_Col__select'));
			}
		}
		else {
			if (this.m_jHeader && this.m_jHeader.length) {
				GF.CheckboxesUncheck(this.m_jHeader.find('.GF_Datagrid_Col__select'));
			}
		}
		if (this.m_oOptions.event_handlers.selection_changed instanceof Function) {
			this.m_oOptions.event_handlers.selection_changed(this, this.m_asSelected);
		}
		this.UpdateIndicators();
		$('[name="' + this.m_oOptions.id + '"]').val(this.m_asSelected.join(',')).change();
	};
	
	this.UpdateIndicators = function() {
		if (this.m_jFooter) {
			this.m_jFooter.find('.GF_Datagrid_records_from').text((this.m_iFilteredRows == 0) ? 0 : this.m_iFirstRecordId + 1);
			this.m_jFooter.find('.GF_Datagrid_records_to').text(isNaN(this.m_iFirstRecordId + this.m_aoRows.length) ? 0 : (this.m_iFirstRecordId + this.m_aoRows.length));
			if (this.m_iFilteredRows == this.m_iTotalRows) {
				this.m_jFooter.find('.GF_Datagrid_records_total').text(this.m_iFilteredRows);
			}
			else {
				this.m_jFooter.find('.GF_Datagrid_records_total').html(this.m_iFilteredRows + ' <img src="' + GF_Datagrid.Files.warning_icon + '" alt="' + GF_Datagrid.Language.not_all_shown + (this.m_iTotalRows - this.m_iFilteredRows) + '" title="' + GF_Datagrid.Language.not_all_shown + (this.m_iTotalRows - this.m_iFilteredRows) + '"/>');
			}
			this.m_jFooter.find('.GF_Datagrid_selected_records').text(this.m_asSelected.length);
			this.m_jFooter.find('.GF_Datagrid_page').val(isNaN(this.m_iPage + 1) ? 1 : this.m_iPage + 1);
			if (this.m_asSelected.length) {
				this.m_jFooter.find('.GF_Datagrid_clear_selection').fadeIn('fast');
			}
			else {
				this.m_jFooter.find('.GF_Datagrid_clear_selection').fadeOut('fast');
			}
		}
		if (this.m_oOptions.external_indicators.total_rows != GF.NULL) {
			this.m_oOptions.external_indicators.total_rows.text(this.m_iFilteredRows);
		}
		if (this.m_oOptions.external_indicators.rows_from != GF.NULL) {
			this.m_oOptions.external_indicators.rows_from.text(this.m_iFirstRecordId + 1);
		}
		if (this.m_oOptions.external_indicators.rows_to != GF.NULL) {
			this.m_oOptions.external_indicators.rows_to.text(this.m_iFirstRecordId + this.m_aoRows.length);
		}
		if (this.m_oOptions.external_indicators.page != GF.NULL) {
			this.m_oOptions.external_indicators.page.text(this.m_iPage + 1);
		}
		if (this.m_oOptions.external_indicators.total_pages != GF.NULL) {
			this.m_oOptions.external_indicators.total_pages.text(this.m_iTotalPages);
		}
	};
	
	this.ToggleFilters = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.m_jFilter.find('td:not(.GF_Datagrid_Col__retract_filters) > span').slideToggle(500, GF.NewEventHandler(function(eEvent) {
			var dDg = GF_Datagrid.GetCurrentInstance(this);
			dDg._UpdateHeights();
		}));
		var sImgSrc = dDg.m_jFilter.find('.GF_Datagrid_Col__retract_filters img').attr('src');
		dDg.m_jFilter.find('.GF_Datagrid_Col__retract_filters img').attr('title', (sImgSrc != GF_Datagrid.Files.expand_filters_icon) ? GF_Datagrid.Language.expand_filters : GF_Datagrid.Language.retract_filters);
		dDg.m_jFilter.find('.GF_Datagrid_Col__retract_filters img').attr('alt', (sImgSrc != GF_Datagrid.Files.expand_filters_icon) ? GF_Datagrid.Language.expand_filters : GF_Datagrid.Language.retract_filters);
		dDg.m_jFilter.find('.GF_Datagrid_Col__retract_filters img').attr('src', (sImgSrc != GF_Datagrid.Files.expand_filters_icon) ? GF_Datagrid.Files.expand_filters_icon : GF_Datagrid.Files.retract_filters_icon);
		return false;
	});
	
	this.GetRow = function(sId) {
		for (var i in this.m_aoRows) {
			if (this.m_aoRows[i][this.m_oOptions.mechanics.key] == sId) {
				return this.m_aoRows[i];
			}
		}
		GF_Debug.Warning('Requested row (' + sId + ') was not found.');
		return false;
	};
	
	this.GetCurrentRow = function(jTr) {
		return this.GetRow(this._GetIdFromTr(jTr));
	};
	
	this.ChangeAdditionalFilters = GF.NewSafeMethod(function(aoFilters) {
		this.m_aoAdditionalFilters = aoFilters;
		this.LoadData();
	});
	
	this.GetRowIdByAttribute = GF.NewSafeMethod(function(sAttribute, mValue) {
		for (var i in this.m_aoRows) {
			if ((this.m_aoRows[i][sAttribute] == mValue)) {
				return this.m_aoRows[i][this.m_oOptions['mechanics']['key']];
			}
		}
		return false;
	});
	
	this.WaitingScreenShow = function(fCompletionHandler) {
		this.m_jTarget.children('.progress').remove();
		var jProgressBar = $('<div class="progress"><div class="bar"><div class="indicator"></div></div></div>');
		jProgressBar.css('display', 'none').css('top', this.m_jTarget.find('.header').height() - 1).css('bottom', (this.m_jFooter && this.m_jFooter.length) ? this.m_jFooter.get(0).clientHeight : 0 + 2);
		this.m_jTarget.append(jProgressBar);
		var dDg = this;
		jProgressBar.fadeIn(150, function() {
			fCompletionHandler.apply(dDg);
		});
	};
	
	this.WaitingScreenHide = function() {
		this.m_jTarget.children('.progress').fadeOut(300, GF.NewEventHandler(function(eEvent) {
			$(this).remove();
		}));
	};

//	this.WaitingScreenShow = function(fCompletionHandler) {
//		var dDg = this;
//		fCompletionHandler.apply(dDg);
//	};
//	
//	this.WaitingScreenHide = function() {
//		$(this).remove();
//	};
	
	this._ParseOptions = function(oOptions) {
		this.m_oOptions = new GF_Datagrid_Config(oOptions);
		
		if ((this.m_oOptions.id == GF.NULL) || !(this.m_oOptions.id.length)) GF_Debug.Error('Datagrid\'s id isn\'t set correctly. Datagrid initialization failed.');
		if ((this.m_oOptions.event_handlers.load == GF.NULL) || !(this.m_oOptions.event_handlers.load instanceof Function)) GF_Debug.Error('Datagrid\'s load function isn\'t set correctly. Datagrid initialization failed.');
		
		this._ParseColumnOptions();
		
		this._ProcessRowsActions(this.m_oOptions.row_actions);
		this._ProcessRowsActions(this.m_oOptions.context_actions);
		this._ProcessGroupActions(this.m_oOptions.group_actions);
		
		this.m_aoAdditionalFilters = this.m_oOptions.additional_filters;
		
	};
	
	this._ParseColumnOptions = function() {
		this.m_aoColumns = [];
		this.m_aiColumnsOrder = [];
		if (this.m_oOptions.appearance.column_select) {
			this.m_aoColumns.push(new GF_Datagrid_Column({
				id: '_select',
				caption: this.m_oOptions.mechanics.only_one_selected ? '&nbsp;' : '<input type="checkbox"/>',
				appearance: {
					width: 24,
					align: GF_Datagrid.ALIGN_CENTER
				},
				sorting: {
					allowed: false
				}
			}));
			this.m_aiColumnsOrder.push(0);
			this.m_oColumnsLookup[this.m_aoColumns[this.m_aoColumns.length - 1].id] = this.m_aoColumns.length - 1;
		}
		for (var i in this.m_oOptions.columns) {
			var j = this.m_aoColumns.push(GF.Clone(this.m_oOptions.columns[i])) - 1;
			if (this.m_oOptions.columns[i].appearance.visible) {
				this.m_aiColumnsOrder.push(j);
			}
			this.m_oColumnsLookup[this.m_aoColumns[this.m_aoColumns.length - 1].id] = this.m_aoColumns.length - 1;
		}
		if (this.m_oOptions.appearance.column_options && this.m_oOptions.row_actions.length) {
			this.m_aoColumns.push(new GF_Datagrid_Column({
				id: '_options',
				caption: GF_Datagrid.Language.options,
				appearance: {
					width: (this.m_oOptions.row_actions.length == 1) ? 30 : this.m_oOptions.row_actions.length * 20
				},
				sorting: {
					allowed: false
				}
			}));
			this.m_aiColumnsOrder.push(this.m_aoColumns.length - 1);
			this.m_oColumnsLookup[this.m_aoColumns[this.m_aoColumns.length - 1].id] = this.m_aoColumns.length - 1;
		}
		var sColumnsCookie = GCookie('GF_Datagrid_Columns_' + this.m_oOptions.id);
		if (sColumnsCookie) {
			this.m_aiColumnsOrder = JSON.parse(sColumnsCookie);
			var sWidthsCookie = GCookie('GF_Datagrid_Widths_' + this.m_oOptions.id);
			if (sWidthsCookie && sWidthsCookie.length) {
				var aiWidths = JSON.parse(sWidthsCookie);
				if (aiWidths.length == this.m_aiColumnsOrder.length) {
					for (var i in this.m_aiColumnsOrder) {
						this.m_aoColumns[this.m_aiColumnsOrder[i]].appearance.width = aiWidths[i];
					}
				}
			}
		}
	};
	
	
	this._InitializeSpacers = function() {
		this.m_jTarget.children('.spacer').remove();
		var iOffset = 0;
		for (var i in this.m_aiColumnsOrder) {
			var c = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			iOffset += c.appearance.width;
			if (i == this.m_aiColumnsOrder.length - 1) break;
			var jSpacer = $('<div class="spacer"><div class="head"></div></div>');
			if ((!this.m_oOptions.mechanics.no_column_resizing) && ((i != 0) || (!this.m_oOptions.appearance.column_select)) && ((!this.m_oOptions.appearance.column_options) || (i != this.m_aiColumnsOrder.length - 2))) {
				jSpacer.addClass('spacer-flexible').mousedown(this._SpacerDragInit);
			}
			if (this.m_jFooter) {
				this.m_jFooter.before(jSpacer);
			}
			else {
				this.m_jTarget.append(jSpacer);
			}
			jSpacer.css('left', iOffset - Math.ceil(parseInt(jSpacer.css('width')) / 2));
		}
	};
	
	this._SpacerDragInit = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		if (dDg.m_i_SpacerDrag != GF.NULL) {
			return false;
		}
		$(this).addClass('spacer-on');
		dDg.m_i_SpacerDrag = dDg.m_jTarget.find('.spacer').index($(this));
		return false;
	});
	
	this._SpacerDrag = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		if (dDg.m_i_SpacerDrag == GF.NULL) {
			return true;
		}
		var iMouseX = eEvent.clientX - dDg.m_jTarget.offset().left;
		var jS = dDg.m_jTarget.find('.spacer');
		var jPrevSpacer = jS.eq(dDg.m_i_SpacerDrag - 1);
		var jNextSpacer = jS.eq(dDg.m_i_SpacerDrag + 1);
		var jSpacer = jS.eq(dDg.m_i_SpacerDrag);
		if (jPrevSpacer.length) {
			if (jNextSpacer.length) {
				jSpacer.css('left', Math.min(parseInt(jNextSpacer.css('left')) - 2 * dDg.m_iPadding - 48, Math.max(parseInt(jPrevSpacer.css('left')) + 2 * dDg.m_iPadding + 48, iMouseX - Math.ceil(parseInt(jSpacer.css('width')) / 2))));
			}
			else {
				jSpacer.css('left', Math.min(parseInt(dDg.m_jTarget.get(0).clientWidth) - 2 * dDg.m_iPadding - 48, Math.max(parseInt(jPrevSpacer.css('left')) + 2 * dDg.m_iPadding + 48, iMouseX - Math.ceil(parseInt(jSpacer.css('width')) / 2))));
			}
		}
		else {
			if (jNextSpacer.length) {
				jSpacer.css('left', Math.min(parseInt(jNextSpacer.css('left')) - 2 * dDg.m_iPadding - 48, Math.max(48 + 2 * dDg.m_iPadding, iMouseX - Math.ceil(parseInt(jSpacer.css('width')) / 2))));
			}
			else {
				jSpacer.css('left', Math.min(parseInt(dDg.m_jTarget.get(0).clientWidth) - 2 * dDg.m_iPadding - 48, Math.max(48 + 2 * dDg.m_iPadding, iMouseX - Math.ceil(parseInt(jSpacer.css('width')) / 2))));
			}
		}
		var iPrevOffset = 0;
		var iCurOffset = 0;
		var iWidthLeft = parseInt(dDg.m_jTarget.get(0).clientWidth) - dDg.m_iScrollbarWidth;
		for (var i = 0; i < jS.length; i++) {
			var oC = dDg.m_aoColumns[dDg.m_aiColumnsOrder[i]];
			iCurOffset = parseInt(jS.eq(i).css('left')) + Math.ceil(parseInt(jS.eq(i).css('width')) / 2);
			oC.appearance.width = iCurOffset - iPrevOffset;
			iPrevOffset = iCurOffset;
			iWidthLeft -= oC.appearance.width;
		}
		var oC = dDg.m_aoColumns[dDg.m_aiColumnsOrder[i]];
		oC.appearance.width = iWidthLeft;
		dDg._UpdateWidths();
		return true;
	});
	
	this._SpacerDragStop = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.m_jTarget.find('.spacer:eq(' + dDg.m_i_SpacerDrag + ')').removeClass('spacer-on');
		dDg.m_i_SpacerDrag = GF.NULL;
		return false;
	});
	
	
	this._ProcessData = function(oData) {
		this.m_iRows = parseInt(oData.rows_num);
		this.m_iTotalRows = parseInt(oData.total);
		this.m_iFilteredRows = parseInt(oData.filtered);
		this.m_iFirstRecordId = parseInt(oData.starting_from);
		this.m_iTotalPages = Math.ceil(this.m_iFilteredRows / this.m_oOptions.mechanics.rows_per_page);
		this.m_iPage = Math.floor((this.m_iFirstRecordId / this.m_iFilteredRows) * this.m_iTotalPages);
		this.m_aoRows = oData.rows;
		this.m_aoAdditionalRows = oData.additional_rows ? oData.additional_rows : [];
		this.Rewrite();
	};
	
	
	this._WriteRow = function(oR) {
		var jTr = $('<tr></tr>');
		if (this.m_oOptions.event_handlers.process instanceof Function) {
			var oD = this.m_oOptions.event_handlers.process(GCore.Duplicate(oR));
		}
		else {
			var oD = GCore.Duplicate(oR);
		}
		for (var j in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[j]];
			var jTd;
			var mContent;
			if (oC.id == this.m_oOptions.mechanics.key) {
				jTd = $('<th scope="row"></th>');
			}
			else {
				jTd = $('<td></td>');
			}
			jTd.addClass('GF_Datagrid_Col_' + oC.id);
			switch (oC.appearance.align) {
				case GF_Datagrid.ALIGN_CENTER:
					jTd.addClass('c');
					break;
				case GF_Datagrid.ALIGN_LEFT:
					jTd.addClass('l');
					break;
				case GF_Datagrid.ALIGN_RIGHT:
					jTd.addClass('r');
					break;
			}
			var jSpan = $('<span></span>');
			switch (oC.id) {
				case '_select':
					mContent = '<input type="checkbox"/>';
					break;
				case '_options':
					mContent = this._WriteRowsActions(oR);
					break;
				default:
					if (oC.editable) {
						mContent = (oD[oC.id] != undefined) ? oD[oC.id] : '';
						jTd.addClass('editable');
						mContent = $('<input type="text" value="' + mContent + '" name="' + this.m_oOptions.id + '[' + oR[this.m_oOptions.mechanics.key] + '][' + oC.id + ']"/>');
						mContent.data('sPreviousValue', mContent.val());
					}
					else if (oC.selectable) {
						mContent = (oD[oC.id] != undefined) ? oD[oC.id] : '';
						jTd.addClass('selectable');
						var jSelect = $('<select name="' + this.m_oOptions.id + '[' + oR[this.m_oOptions.mechanics.key] + '][' + oC.id + ']"/>');
						var aoOptions = oC.filter.options;
						if ((aoOptions.length > 1) || ((aoOptions.length == 1) && (aoOptions[0].id != ''))) {
							for (var i in aoOptions) {
								jOption = $('<option value="' + aoOptions[i].id + '"' + ((aoOptions[i].id == mContent) ? ' selected="selected"' : '') + '/>');
								jOption.text(aoOptions[i].caption);
								if (aoOptions[i].options != undefined) {
									jOption.get(0).aoOptions = aoOptions[i].options;
								}
								jSelect.append(jOption);
							}
							mContent = $('<div class="field-select"/>').append($('<span class="field"/>').append(jSelect));
						}
						else {
							var aoOptions = $.parseJSON(oR[oC.id + '__options'].replace(/\\"/g, '"'));
							if ((aoOptions.length > 1) || ((aoOptions.length == 1) && (aoOptions[0].id != ''))) {
								for (var i in aoOptions) {
									jOption = $('<option value="' + aoOptions[i].id + '"' + ((aoOptions[i].id == mContent) ? ' selected="selected"' : '') + '/>');
									jOption.text(aoOptions[i].caption);
									if (aoOptions[i].options != undefined) {
										jOption.get(0).aoOptions = aoOptions[i].options;
									}
									jSelect.append(jOption);
								}
								mContent = $('<div class="field-select"/>').append($('<span class="field"/>').append(jSelect));
							}else{
								mContent = '';
							}
						}
						jSelect.data('sPreviousValue', jSelect.find('option:selected').attr('value'));
					}
					else {
						mContent = '' + ((oD[oC.id] != undefined) ? oD[oC.id] : '&nbsp;');
						if (!oC.appearance.no_title) {
							jSpan.attr('title', oC.caption + ': ' + mContent.replace(/ {2,}/, ' ').replace(/\&nbsp\;/, '-').replace(/<[^>]+>/, '').replace(/<\/?[^>]+>/gi, ''));
						}
					}
			}
			jTd.append(jSpan.append(mContent));
			if (j == 0) {
				jTd.append('<span style="display: none;" class="GF_Datagrid_Row_Id">' + oR[this.m_oOptions.mechanics.key] + '</span>');
			}
			if (this.IsSelected(oR)) {
				this._ShowSelection(jTr);
			}
			jTr.append(jTd);
		}
		this._InitializeRowEvents(jTr);
		return jTr;
	};
	
	this._WriteAdditionalRow = function(oR) {
		var jTr = $('<tr></tr>');
		var oD = oR;
		for (var j in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[j]];
			var jTd;
			var mContent;
			if (oC.id == this.m_oOptions.mechanics.key) {
				jTd = $('<th scope="row"></th>');
			}
			else {
				jTd = $('<td></td>');
			}
			jTd.addClass('GF_Datagrid_Col_' + oC.id);
			switch (oC.appearance.align) {
				case GF_Datagrid.ALIGN_CENTER:
					jTd.addClass('c');
					break;
				case GF_Datagrid.ALIGN_LEFT:
					jTd.addClass('l');
					break;
				case GF_Datagrid.ALIGN_RIGHT:
					jTd.addClass('r');
					break;
			}
			switch (oC.id) {
				case '_select':
					mContent = '&nbsp;';
					break;
				case '_options':
					mContent = '&nbsp;';
					break;
				default:
					mContent = (oD[oC.id] != undefined) ? oD[oC.id] : '&nbsp;';
			}
			var jSpan = $('<span></span>');
			jTd.append(jSpan.append(mContent));
			if (j == 0) {
				jTd.append('<span style="display: none;" class="GF_Datagrid_Row_Id">' + oR[this.m_oOptions.mechanics.key] + '</span>');
			}
			jTr.append(jTd);
		}
		if (oR._options.className) {
			jTr.addClass(oR._options.className);
		}
		if (oR._options.caption) {
			jTr.attr('title', oR._options.caption);
		}
		return jTr;
	};
	
	this._WriteRowsActions = function(oR) {
		var sId = oR[this.m_oOptions.mechanics.key];
		var jUl = $('<ul></ul>');
		if (GF.InArray(this.m_oOptions.no_actions_for_rows, sId)) {
			return jUl;
		}
		for (var i in this.m_oOptions.row_actions) {
			var oA = this.m_oOptions.row_actions[i];
			if ((oA.condition == GF.NULL) || ((typeof(oA.condition) == 'function') && oA.condition(oR))) {
				jUl.append(this._WriteRowsAction(oA, sId));
			}
		}
		return jUl;
	};
	
	this._WriteRowsAction = function(oA, sId) {
		var jLi = $('<li></li>');
		var jAnchor = $('<a href="#" title="' + oA.caption + '"><img src="' + oA.img + '" alt="' + oA.caption + '"/></a>');
		if (GF.CountMembers(oA.values)) {
			var aoSubActions = [];
			for (var i in oA.values) {
				aoSubActions.push(new GF_Action({
					img: oA.img,
					action: oA.action,
					caption: oA.values[i],
					parameter: i
				}));
			}
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				GF_ContextMenu.Create({
					id: sId,
					parent: $(this),
					actions: aoSubActions,
					position: {
						left: eEvent.pageX - 16,
						top: eEvent.pageY - 10
					}
				});
				return false;
			}));
		}
		else {
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				oA.action(sId);
				return false;
			}));
		}
		jLi.append(jAnchor);
		return jLi;
	};
	
	this._ProcessRowsActions = function(aoActions) {
		for (var i in aoActions) {
			var oA = aoActions[i];
			var bSkip = false;
			switch (oA) {
				case GF_Datagrid.ACTION_DELETE:
					if (!(this.m_oOptions.event_handlers.delete_row instanceof Function)) {
						GF_Debug.Error('Datagrid\'s delete row action is not set.');
					}
					oA = new GF_Action({
						img: GF_Datagrid.Files.delete_row_icon,
						caption: GF_Datagrid.Language.delete_row,
						action: this.m_oOptions.event_handlers.delete_row
					});
					break;
				case GF_Datagrid.ACTION_EDIT:
					if (!(this.m_oOptions.event_handlers.edit_row instanceof Function)) {
						GF_Debug.Error('Datagrid\'s edit row action is not set.');
					}
					oA = new GF_Action({
						img: GF_Datagrid.Files.edit_row_icon,
						caption: GF_Datagrid.Language.edit_row,
						action: this.m_oOptions.event_handlers.edit_row
					});
					break;
				case GF_Datagrid.ACTION_VIEW:
					if (!(this.m_oOptions.event_handlers.view_row instanceof Function)) {
						GF_Debug.Error('Datagrid\'s view row action is not set.');
					}
					oA = new GF_Action({
						img: GF_Datagrid.Files.view_row_icon,
						caption: GF_Datagrid.Language.view_row,
						action: this.m_oOptions.event_handlers.view_row
					});
					break;
			}
			if (bSkip) {
				continue;
			}
			aoActions[i] = this._ProcessAction(oA);
		}
		return aoActions;
	};
	
	this._WriteGroupActions = function() {
		var jUl = $('<ul></ul>');
		for (var i in this.m_oOptions.group_actions) {
			var oA = this.m_oOptions.group_actions[i];
			jUl.append(this._WriteGroupAction(oA));
		}
		return jUl;
	};
	
	this._WriteGroupAction = function(oA) {
		var jLi = $('<li></li>');
		var jAnchor = $('<a href="#" title="' + oA.caption + '"><img src="' + oA.img + '" alt="' + oA.caption + '"/></a>');
		var aIds = this.m_asSelected;
		if (GF.CountMembers(oA.values)) {
			var aoSubActions = [];
			for (var i in oA.values) {
				aoSubActions.push(new GF_Action({
					img: oA.img,
					action: oA.action,
					caption: oA.values[i],
					parameter: i
				}));
			}
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				if (!aIds.length) {
					new GF_Alert(oA.caption, GF_Datagrid.Language.no_rows_selected);
					return false;
				}
				GF_ContextMenu.Create({
					id: aIds,
					parent: $(this),
					actions: aoSubActions,
					position: {
						left: eEvent.pageX - 15,
						top: eEvent.pageY + 12
					}
				});
				return false;
			}));
		}
		else {
			jAnchor.click(GF.NewEventHandler(function(eEvent) {
				if (!aIds.length) {
					new GF_Alert(oA.caption, GF_Datagrid.Language.no_rows_selected);
					return false;
				}
				oA.action(aIds);
				return false;
			}));
		}
		jLi.append(jAnchor);
		return jLi;
	};
	
	this._ProcessGroupActions = function(aoActions) {
		for (var i in aoActions) {
			var oA = aoActions[i];
			switch (oA) {
				case GF_Datagrid.ACTION_DELETE:
					if (!(this.m_oOptions.event_handlers.delete_group instanceof Function)) {
						GF_Debug.Error('Datagrid\'s delete group action is not set.');
					}
					oA = new GF_Action({
						img: GF_Datagrid.Files.delete_group_icon,
						caption: GF_Datagrid.Language.delete_group,
						action: this.m_oOptions.event_handlers.delete_group
					});
					break;
			}
			aoActions[i] = this._ProcessAction(oA);
		}
		return aoActions;
	};
	
	this._ProcessAction = function(oA) {
		if (oA.processed) {
			return oA;
		}
		oA.processed = true;
		var dCurrentDg = this;
		var fOldAction = oA.action;
		var fNewAction = function(mIds, mParameter) {
			try {
				fOldAction(dCurrentDg.m_iId, mIds, mParameter);
			}
			catch (xException) {
				GF_Debug.HandleException(xException);
				return false;
			}
		};
		oA.action = fNewAction;
		return oA;
	};
	
	this._ShowSelection = function(jTr) {
		jTr.addClass('selected');
		GF.CheckboxesCheck(jTr);
	};
	
	
	this._ChangeOrder = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var sCol = dDg._GetNameForTd($(this));
		dDg.m_jHeader.find('.order_indicator').remove();
		if (dDg.m_sOrderBy == sCol) {
			dDg.m_iOrderDirection = (dDg.m_iOrderDirection == GF_Datagrid.SORT_DIR_ASC) ? GF_Datagrid.SORT_DIR_DESC : GF_Datagrid.SORT_DIR_ASC;
		}
		else {
			dDg.m_sOrderBy = sCol;
			dDg.m_iOrderDirection = dDg._GetColumnByName(sCol).sorting.default_order;
		}
		$(this).children('span').prepend('<img src="' + ((dDg.m_iOrderDirection == GF_Datagrid.SORT_DIR_ASC) ? GF_Datagrid.Files.order_asc : GF_Datagrid.Files.order_desc) + '" class="order_indicator"/>');
		dDg.LoadData();
	});
	
	this._ChangeOrderIndicator = function(column, direction) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.m_jHeader.find('.order_indicator').remove();
		$('th.GF_Datagrid_Col_' + column).children('span').prepend('<img src="' + ((direction == 'asc') ? GF_Datagrid.Files.order_asc : GF_Datagrid.Files.order_desc) + '" class="order_indicator"/>');
	};
	
	
	this._GetNameForTd = function(jTd) {
		if (!(jTd instanceof $)) {
			GF_Debug.Error('The argument passed to the datagrid\'s _GetNameForTd method was not a jQuery object: ' + typeof jTd + '.');
		}
		if (!jTd.is('th, td')) {
			var jOldTd = jTd;
			jTd = td.parents('td:first');
			if (!jTd.length) {
				jTd = jOldTd.parents('th:first');
				if (!jTd.length) {
					GF_Debug.Error('The argument passed to the datagrid\'s _GetNameForTd method was neither a td node nor one of its descendants.');
				}
			}
		}
		var sId = GF.GetChunkFromClass(jTd, 'GF_Datagrid_Col_');
		return sId;
	};
	
	this._GetIdFromTr = function(jTr) {
		if (!(jTr instanceof $)) {
			GF_Debug.Error('Invalid type of argument passed to the datagrid\'s _GetIdFromTr method: ' + typeof jTr + '. Should be a jQuery instance.');
		}
		jTr = jTr.is('tr') ? jTr : jTr.closest('tr');
		if (!jTr.length) {
			GF_Debug.Error('Invalid jQuery object passed to the datagrid\'s _GetIdFromTr method.');
		}
		return jTr.find('.GF_Datagrid_Row_Id').text();
	};
	
	this._GetTrForId = function(sId) {
		var jTr = this.m_jBody.find('tr .GF_Datagrid_Row_Id:contains("' + sId + '")');
		for (var i = 0; i < jTr.length; i++) {
			if (jTr.eq(i).text() == sId) {
				jTr = jTr.eq(i).parents('tr:first');
				break;
			}
		}
		return jTr;
	};
	
	this._GetColumnByName = function(sId) {
		if (this.m_oColumnsLookup[sId] == undefined) {
			GF_Debug.Warning('Column with the specified name (' + sId + ') was not found in the datagrid.');
		}
		return this.m_aoColumns[this.m_oColumnsLookup[sId]];
	}
	
	this._GetConditionForField = function(sId) {
		var oC = this._GetColumnByName(sId);
		var aConditions = [];
		switch (oC.filter.type) {
			
			case GF_Datagrid.FILTER_BETWEEN:
				var jField = this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id + ' input.from');
				if (!jField.length) {
					GF_Debug.Warning('Filter field for the specified column (' + oC.id + ') wasn\'t found.');
				}
				if (jField.val() == undefined) {
					GF_Debug.Error('Filter field for the specified column (' + oC.id + ') has an incorrect value.');
				}
				if (jField.val().length) {
					aConditions.push({
						column: ((oC.filter.filtered_column != undefined) && (oC.filter.filtered_column != GF.NULL)) ? oC.filter.filtered_column : oC.id,
						value: jField.val(),
						operator: 'GE'
					});
				};
				jField = this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id + ' input.to');
				if (!jField.length) {
					GF_Debug.Warning('Filter field for the specified column (' + oC.id + ') wasn\'t found.');
				}
				if (jField.val() == undefined) {
					GF_Debug.Error('Filter field for the specified column (' + oC.id + ') has an incorrect value.');
				}
				if (jField.val().length) {
					aConditions.push({
						column: ((oC.filter.filtered_column != undefined) && (oC.filter.filtered_column != GF.NULL)) ? oC.filter.filtered_column : oC.id,
						value: jField.val(),
						operator: 'LE'
					});
				};
				break;
			
			case GF_Datagrid.FILTER_INPUT:
			case GF_Datagrid.FILTER_AUTOSUGGEST:
				var jField = this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id + ' input');
				if (!jField.length) {
					GF_Debug.Warning('Filter field for the specified column (' + oC.id + ') wasn\'t found.');
				}
				if (jField.val() == undefined) {
					GF_Debug.Error('Filter field for the specified column (' + oC.id + ') has an incorrect value.');
				}
				if (jField.val().length) {
					aConditions.push({
						column: ((oC.filter.filtered_column != undefined) && (oC.filter.filtered_column != GF.NULL)) ? oC.filter.filtered_column : oC.id,
						value: '%' + jField.val() + '%',
						operator: 'LIKE'
					});
				}
				break;
			
			case GF_Datagrid.FILTER_SELECT:
				var jField = this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id + ' select');
				if (!jField.length) {
					GF_Debug.Warning('Filter field for the specified column (' + oC.id + ') wasn\'t found.');
				}
				if (jField.val() == undefined) {
					GF_Debug.Warning('Filter field for the specified column (' + oC.id + ') has an incorrect value.');
					break;
				}
				if (jField.val().length) {
					aConditions.push({
						column: ((oC.filter.filtered_column != undefined) && (oC.filter.filtered_column != GF.NULL)) ? oC.filter.filtered_column : oC.id,
						value: '%' + jField.val() + '%',
						operator: 'LIKE'
					});
				}
				break;
			
			case GF_Datagrid.FILTER_TREE:
				var jPanel = this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id + ' .GF_Datagrid_filter_tree').data('jPanel');
				if (jPanel == undefined) {
					break;
				}
				var asIds = this._BuildTreeConditions(jPanel.children('ul:first'));
				if (asIds.length) {
					aConditions.push({
						column: ((oC.filter.filtered_column != undefined) && (oC.filter.filtered_column != GF.NULL)) ? oC.filter.filtered_column : oC.id,
						value: asIds,
						operator: 'IN'
					});
				}
				break;
			
		}
		return aConditions;
	};
	
	this._BuildTreeConditions = function(jUl) {
		var asConditions = [];
		var jChildren = jUl.children('li');
		var iFields = jChildren.length;
		for (var i = 0; i < iFields; i++) {
			if (jChildren.eq(i).children('label').children('input').is(':checked')) {
				asConditions.push(jChildren.eq(i).data('sId'));
			}
			else {
				asConditions = asConditions.concat(this._BuildTreeConditions(jChildren.eq(i).children('ul:first')));
			}
		}
		return asConditions;
	};
	
	
	this._InitializeWidths = function() {
		this.m_iTotalWidth = this.m_jTarget.width();
		if (!this.m_oOptions.mechanics.no_column_modification || this.m_oOptions.appearance.max_height) {
			this.m_iScrollbarWidth = Math.max(26, this.m_iTotalWidth - this.m_jTarget.find('.body').get(0).clientWidth);
		}
		else {
			this.m_iScrollbarWidth = 0;
		}
		this.m_iWidth = this.m_iTotalWidth - this.m_iScrollbarWidth;
		if (this.m_jHeader && this.m_jHeader.length) {
			this.m_iPadding = (this.m_jHeader.find('th:first').get(0).clientWidth - this.m_jHeader.find('th:first span').get(0).clientWidth) / 2;
		}
		else {
			this.m_iPadding = 4;
		}
		
		var iWidthLeft = this.m_iWidth;
		var i;
		var iNumberOfAutoWidths = 0;
		for (i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			if (oC.appearance.width == GF_Datagrid.WIDTH_AUTO) {
				iNumberOfAutoWidths++;
				continue;
			}
			iWidthLeft -= oC.appearance.width;
		}
		for (i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			if (oC.appearance.width != GF_Datagrid.WIDTH_AUTO) {
				continue;
			}
			var iWidth = Math.floor(iWidthLeft / iNumberOfAutoWidths--);
			iWidthLeft -= iWidth;
			oC.appearance.width = iWidth;
		}
		
		if (iWidthLeft != 0) {
			
			iNumberOfColumnsLeft = this.m_aiColumnsOrder.length;
			if (this.m_oOptions.appearance.column_options) iNumberOfColumnsLeft--;
			if (this.m_oOptions.appearance.column_select) iNumberOfColumnsLeft--;
			
			for (var i in this.m_aiColumnsOrder) {
				var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
				if ((oC.id == '_options') || (oC.id == '_select')) {
					continue;
				}
				var iWidth = Math.floor((iWidthLeft / iNumberOfColumnsLeft--));
				iWidthLeft -= iWidth;
				oC.appearance.width += iWidth;
			}
			
			if (iWidthLeft != 0) {
				if (this.m_oOptions.appearance.column_options) {
					var oLastColumn = this.m_aoColumns[this.m_aiColumnsOrder[this.m_aiColumnsOrder.length - 2]];
				}
				else {
					var oLastColumn = this.m_aoColumns[this.m_aiColumnsOrder[this.m_aiColumnsOrder.length - 1]];
				}
				oLastColumn.appearance.width += iWidthLeft;
			}
			
		}
		
		var iWidth = 0;
		var bEqualizeWidths = false;
		for (var i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			if ((oC.id == '_options') || (oC.id == '_select')) {
				continue;
			}
			if (oC.appearance.width < 48) {
				bEqualizeWidths = true;
				break;
			}
		}
		
		if (bEqualizeWidths) {
			iWidthLeft = this.m_iWidth;
			iNumberOfColumnsLeft = this.m_aiColumnsOrder.length;
			for (i in this.m_aiColumnsOrder) {
				var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
				if ((oC.id == '_options') || (oC.id == '_select')) {
					iWidthLeft -= oC.appearance.width;
					iNumberOfColumnsLeft--;
					continue;
				}
				var iWidth = Math.floor(iWidthLeft / iNumberOfColumnsLeft--);
				iWidthLeft -= iWidth;
				oC.appearance.width = iWidth;
			}
			if (iWidthLeft != 0) {
				if (this.m_oOptions.appearance.column_options) {
					var oLastColumn = this.m_aoColumns[this.m_aiColumnsOrder[this.m_aiColumnsOrder.length - 2]];
				}
				else {
					var oLastColumn = this.m_aoColumns[this.m_aiColumnsOrder[this.m_aiColumnsOrder.length - 1]];
				}
				oLastColumn.appearance.width += iWidthLeft;
			}
		}
		
		this._UpdateWidths();
		this._UpdateHeights();
	};
	
	this._UpdateWidths = function() {
		this.m_oOptions.appearance.header && this.m_jHeader.find('.GF_Datagrid_Col__modify_columns').css('width', this.m_iScrollbarWidth);
		for (i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			this.m_jTarget.find('.GF_Datagrid_Col_' + oC.id).css('width', oC.appearance.width - 2 * this.m_iPadding);
			this.m_jTarget.find('.GF_Datagrid_Col_' + oC.id).children('span').css('width', oC.appearance.width - 2 * this.m_iPadding);
			if (!this.m_oOptions.appearance.max_height && (i == this.m_aiColumnsOrder.length - 1)) {
				this.m_jBody.find('.GF_Datagrid_Col_' + oC.id).css('width', oC.appearance.width - 2 * this.m_iPadding);
				this.m_jAdditionalRows.find('.GF_Datagrid_Col_' + oC.id).css('width', oC.appearance.width - 2 * this.m_iPadding);
				this.m_jBody.find('.GF_Datagrid_Col_' + oC.id).children('span').css('width', oC.appearance.width - 2 * this.m_iPadding + this.m_iScrollbarWidth);
				this.m_jAdditionalRows.find('.GF_Datagrid_Col_' + oC.id).children('span').css('width', oC.appearance.width - 2 * this.m_iPadding + this.m_iScrollbarWidth);
			}
			if (this.m_jFilter) {
				this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id).find('.GF_Datagrid_filter_input input, .GF_Datagrid_filter_autosuggest input').css('width', oC.appearance.width - 2 * this.m_iPadding - 6);
				this.m_jFilter.find('.GF_Datagrid_Col_' + oC.id).find('.GF_Datagrid_filter_between input').css('width', oC.appearance.width - 2 * this.m_iPadding - 4 - 30);
			}
			this.m_jBody.find('.GF_Datagrid_Col_' + oC.id + '.editable input').css('width', oC.appearance.width - 2 * this.m_iPadding - 6);
			this.m_jBody.find('.GF_Datagrid_Col_' + oC.id + '.selectable span.field').css('width', oC.appearance.width - 2 * this.m_iPadding - 2);
			this.m_jBody.find('.GF_Datagrid_Col_' + oC.id + ' .full-width').css('width', oC.appearance.width - 2 * this.m_iPadding - 2);
		}
	};
	
	this._UpdateHeights = function() {
		var jSpacers = this.m_jTarget.find('.spacer');
		for (var i = 0; i < jSpacers.length; i++) {
			var iHeight = this.m_jTarget.get(0).clientHeight - this.m_jFooter.get(0).clientHeight - (this.m_oOptions.appearance.footer ? 2 : 0);
			iHeight -= this.m_jAdditionalRows.get(0).clientHeight;
			for (var j in this.m_aoAdditionalRows) {
				var sColumnValue = this.m_aoAdditionalRows[j][this.m_aoColumns[this.m_aiColumnsOrder[i]].id];
				var sNextColumnValue = this.m_aoAdditionalRows[j][this.m_aoColumns[this.m_aiColumnsOrder[Math.min(this.m_aiColumnsOrder.length - 1, i + 1)]].id];
				if (((sColumnValue != undefined) && (sColumnValue != '')) || ((sNextColumnValue != undefined) && (sNextColumnValue != ''))) {
					iHeight += this.m_jAdditionalRows.get(0).clientHeight;
					break;
				}
			}
			jSpacers.eq(i).css('height', iHeight);
		}
		if (this.m_jHeader && this.m_jHeader.length) {
			this.m_jTarget.find('.spacer .head').css('height', this.m_jHeader.get(0).clientHeight);
			this.m_jHeader.find('.GF_Datagrid_Col__modify_columns span').css('height', this.m_jHeader.get(0).clientHeight).css('line-height', this.m_jHeader.get(0).clientHeight + 'px');
		}
	};
	
	
	this._CreateDatagrid = function() {
		this.m_oOptions.mechanics.create_input && (this.m_jInput = this.m_jTarget.append('<input type="hidden" name="' + this.m_oOptions.id + '"/>'));
		this.m_jTarget.append(this._CreateHeader());
		this.m_jTarget.append(this._CreateBody());
		this.m_jTarget.append(this._CreateAdditionalRows());
		this.m_jTarget.append(this._CreateFooter());
		if (!this.m_oOptions.appearance.footer) {
			this.m_jFooter.css('display', 'none');
		}
		if (!this.m_oOptions.appearance.filter) {
			this.m_jHeader.find('tbody').css('display', 'none');
		}
		if (!this.m_oOptions.appearance.header) {
			this.m_jHeader.css('display', 'none');
		}
	};
	
	this._CreateHeader = function() {
		var jHeader = $('<div class="header"></div>');
		var jTable = $('<table cellspacing="0"></table>');
		var jThead = $('<thead></thead>');
		var jTr = $('<tr></tr>');
		jHeader.append(jTable.append(jThead.append(jTr)));
		this.m_jHeader = jTr;
		this._WriteHeader();
		this.m_oOptions.appearance.filter && jTable.append(this._CreateFilter());
		return jHeader;
	};
	
	this._WriteHeader = function() {
		this.m_jHeader.empty();
		for (var i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			var jCell = $('<th class="GF_Datagrid_Col_' + oC.id + '"></th>');
			jCell.append('<span>' + oC.caption.replace(/ {2,}/, '<br/>') + '</span>');
			if (oC.sorting.allowed) {
				jCell.addClass('sortable');
			}
			if (oC.id == this.m_sOrderBy) {
				jCell.children('span').prepend('<img src="' + ((this.m_iOrderDirection == GF_Datagrid.SORT_DIR_ASC) ? GF_Datagrid.Files.order_asc : GF_Datagrid.Files.order_desc) + '" class="order_indicator"/>');
			}
			this.m_jHeader.append(jCell);
		}
		(!this.m_oOptions.mechanics.no_column_modification || this.m_oOptions.appearance.max_height) && this.m_jHeader.append('<th class="GF_Datagrid_Col__modify_columns"><span>' + (!this.m_oOptions.mechanics.no_column_modification ? '<a href="#"><img src="' + GF_Datagrid.Files.modify_columns_icon + '" alt="' + GF_Datagrid.Language.modify_columns + '" title="' + GF_Datagrid.Language.modify_columns + '"/></a>' : '') + '</span></th>');
	};
	
	this._CreateFilter = function() {
		var jTbody = $('<tbody></tbody>');
		var jTr = $('<tr></tr>');
		jTbody.append(jTr);
		this.m_jFilter = jTr;
		this._WriteFilters();
		return jTbody;
	};
	
	this._WriteFilters = function() {
		this.m_jFilter.empty();
		var oRequest = {};
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var sCookie = sessionStorage.getItem('GF_Datagrid_State_' + Base64.encode(window.location.pathname + dDg.m_oOptions.id));
		
		if(this.m_oOptions.mechanics.persistent && this.m_bFirstLoad){
			if(sCookie){
				var oRequest =  $.parseJSON(Base64.decode(sCookie));
			}
			else if((window.location.hash).length){
				var oRequest =  $.parseJSON(Base64.decode(window.location.hash));
			}
		}
		
		for (var i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			var default_value = '';
			var default_value_min = '';
			var default_value_max = '';
			var jCell = $('<td class="GF_Datagrid_Col_' + oC.id + '"></td>');
			if(oRequest.where != undefined){
				for (var i in oRequest.where) {
					var aF = oRequest.where[i];
					if(aF.column == oC.id && aF.operator == 'LIKE'){
						default_value = aF.value;
					}
					if(aF.column == oC.id && aF.operator == 'GE'){
						default_value_min = aF.value;
					}
					if(aF.column == oC.id && aF.operator == 'LE'){
						default_value_max = aF.value;
					}
				}
			}
			jCell.append(this._WriteFilter(oC.filter, default_value, default_value_min, default_value_max));
			this.m_jFilter.append(jCell);
		}
		(!this.m_oOptions.mechanics.no_column_modification || this.m_oOptions.appearance.max_height) && this.m_jFilter.append('<td class="GF_Datagrid_Col__retract_filters"><span><a href="#"><img src="' + GF_Datagrid.Files.retract_filters_icon + '" alt="' + GF_Datagrid.Language.retract_filters + '" title="' + GF_Datagrid.Language.retract_filters + '"/></a></span></td>');
	};
	
	this._WriteFilter = function(oF, default_value, default_value_min, default_value_max) {
		var dDg = this;
		var jFilter = $('<span></span>');
		switch (oF.type) {
			case GF_Datagrid.FILTER_INPUT:
				jFilter.addClass('GF_Datagrid_filter_input');
				var jField = $('<input type="text" value="' + default_value.replace(/%/g,"") + '"/>');
				jField.change(this.LoadData);
				jField.change(this._HandleGoToFirst);
				jField.keydown(this.PreventEnter);
				jFilter.append(jField);
				break;
			
			case GF_Datagrid.FILTER_AUTOSUGGEST:
				jFilter.addClass('GF_Datagrid_filter_autosuggest');
				var jField = $('<input type="text" value="' + default_value.replace(/%/g,"") + '"/>');
				jField.change(this.LoadData);
				jField.change(this._HandleGoToFirst);
				jField.keydown(this.PreventEnter);
				jFilter.append(jField);
				new GF_Autosuggest(jFilter, {
					source: oF.source,
					delay: this.m_oOptions.mechanics.autosuggest_delay,
					maxSuggestions: this.m_oOptions.mechanics.autosuggest_suggestions,
					minLength: this.m_oOptions.mechanics.autosuggest_min_length
				});
				break;
			
			case GF_Datagrid.FILTER_BETWEEN:
				jFilter.addClass('GF_Datagrid_filter_between');
				var jFieldFrom = $('<label><span>' + GF_Datagrid.Language.from + '</span><input type="text" class="from" value="' + default_value_min + '"/></label>');
				var jFieldTo = $('<label><span>' + GF_Datagrid.Language.to + '</span><input type="text" class="to" value="' + default_value_max + '"/></label>');
				jFieldFrom.change(this.LoadData);
				jFieldTo.change(this.LoadData);
				jFieldFrom.change(this._HandleGoToFirst);
				jFieldTo.change(this._HandleGoToFirst);
				jFieldFrom.keydown(this.PreventEnter);
				jFieldTo.keydown(this.PreventEnter);
				jFilter.append(jFieldFrom);
				jFilter.append(jFieldTo);
				break;
			
			case GF_Datagrid.FILTER_SELECT:
				jFilter.addClass('GF_Datagrid_filter_select');
				var jSelect = $('<select></select>');
				for (var i in oF.options) {
					if(default_value.replace(/%/g,"") == oF.options[i].id){
						jSelect.append('<option value="' + oF.options[i].id + '" selected>' + oF.options[i].caption + '</option>');
					}else{
						jSelect.append('<option value="' + oF.options[i].id + '">' + oF.options[i].caption + '</option>');
					}
					
				}
				jSelect.change(this.LoadData);
				jSelect.change(this._HandleGoToFirst);
				jFilter.append($('<span/>').append(jSelect));
				jSelect.GSelect();
				break;
			
			case GF_Datagrid.FILTER_TREE:
				jFilter.addClass('GF_Datagrid_filter_tree');
				var jPanel = $('<div class="GF_Datagrid_filter_tree_panel"/>');
				jFilter.data('jPanel', jPanel);
				var jList = $('<ul/>');
				jFilter.append(jList);
				jPanel.data('fLoadTree', oF.load_children);
				var jUl = $('<ul/>');
				jPanel.append(jUl);
				jPanel.GShadow();
				jPanel.data('jFilter', jFilter);
				jPanel.data('bExpanded', false);
				$('body').append(jPanel);
				jPanel.css('display', 'none');
				var jTriggerRetract = $('<a class="trigger-retract" href="#">' + GF_Datagrid.Language.filter_tree_retract + '</a>');
				var jTriggerReset = $('<a class="trigger-reset" href="#">' + GF_Datagrid.Language.filter_tree_cancel_all + '</a>');
				var jTriggerExpand = $('<a class="trigger-expand" href="#">' + GF_Datagrid.Language.filter_tree_expand + '</a>');
				jTriggerRetract.click(function(eEvent) {
					jTriggerExpand.click();
					return false;
				});
				jTriggerReset.click(function(eEvent) {
					jPanel.find('input:checked').each(function() {
						this.checked = false;
					});
					jPanel.find('input:first').triggerHandler('change');
					dDg._UpdateTreeList(jFilter);
					return false;
				});
				jTriggerExpand.click(function(eEvent) {
					jPanel.css({
						left: $(this).closest('td').offset().left - 1,
						top: $(this).closest('td').offset().top + $(this).closest('td').height() + 2,
						'min-width': $(this).closest('td').width() - 13
					});
					if (jPanel.data('bExpanded')) {
						jPanel.data('bExpanded', false);
						jPanel.slideUp(250);
						$(this).closest('td').removeClass('active');
					}
					else {
						jPanel.data('bExpanded', true);
						jPanel.slideDown(250);
						$(this).closest('td').addClass('active');
					}
					return false;
				});
				jFilter.append(jTriggerExpand).append(jTriggerReset);
				var oOptions;
				if (oF.options == undefined) {
					oOptions = [];
				}
				else if (oF.options instanceof Array) {
					oOptions = oF.options;
				}
				else {
					oOptions = oF.options[0];
				}
				var iLength = oOptions.length;
				for (var i = 0; i < iLength; i++) {
					var jLi = $('<li/>');
					jUl.append(jLi);
					this._WriteTreeItem.apply(this, [jLi, oOptions[i], false, oF]);
				}
				jPanel.append(jTriggerRetract);
				this._UpdateTreeList(jFilter, true);
				break;
			
			default:
				jFilter.html('&nbsp;');
			
		}
		return jFilter;
	};
	
	this._UpdateTreeList = function(jTree, bNoPositionUpdate) {
		var jList = jTree.find('ul:first');
		jList.empty();
		var jPanel = jTree.data('jPanel');
		if (jPanel == undefined) {
			return;
		}
		sList = this._BuildTreeList(jPanel.children('ul:first'));
		if (sList == '') {
			jList.append('<li class="none">' + GF_Datagrid.Language.filter_tree_empty + '</li>');
		}
		else {
			jList.html(sList);
		}
		var jLis = jList.find('li');
		if (jLis.length > 5) {
			jLis.slice(5).css('display', 'none');
			jList.append('<li>...</li>');
		}
		if (!bNoPositionUpdate) {
			jPanel.css({
				left: jTree.closest('td').offset().left - 1,
				top: jTree.closest('td').offset().top + jTree.closest('td').height() + 2,
				'min-width': jTree.closest('td').width() - 13
			});
		}
	};
	
	this._BuildTreeList = function(jUl) {
		var sList = '';
		var jChildren = jUl.children('li');
		var iFields = jChildren.length;
		for (var i = 0; i < iFields; i++) {
			if (jChildren.eq(i).children('label').children('input').is(':checked')) {
				var sFullName = '';
				var jParent = jChildren.eq(i).closest('li');
				do {
					sFullName = jParent.children('label:first').text() + ' &gt; ' + sFullName;
					jParent = jParent.parent().parent('li');
				} while(jParent.length);
				if (jChildren.eq(i).closest('li').data('bHasChildren')) {
					sList += '<li title="' + sFullName + '">' + sFullName + '*</li>';
				}
				else {
					sList += '<li title="' + sFullName.substr(0, sFullName.length - 6) + '">' + sFullName.substr(0, sFullName.length - 6) + '</li>';
				}
			}
			else {
				sList += this._BuildTreeList(jChildren.eq(i).children('ul:first'));
			}
		}
		return sList;
	};
	
	this._WriteTreeItem = function(jLi, oItem, bChecked, oFilter) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var jField = $('<input type="checkbox" value="1"' + (bChecked ? ' checked="checked"' : '') + '/>');
		jLi.data('sId', oItem.id);
		jLi.data('bLoaded', false);
		jLi.data('bHasChildren', oItem.hasChildren);
		/*if (gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').length) {
			jField.click();
			jField.attr('checked', 'checked');
		}*/
		jLi.append($('<label/>').append(jField).append(oItem.name));
		if (oItem.hasChildren) {
			var jExpander = $('<span class="expander"/>');
			jLi.prepend(jExpander);
			jExpander.click(function(eEvent) {
				dDg._OnTreeExpand.apply(this, [$.extend(eEvent, {
					dDg: dDg
				})]);
			});
			jLi.append('<ul><li><span class="waiting"/></li></ul>');
		}
		jField.change(function(eEvent) {
			var bChecked = $(this).is(':checked');
			if (bChecked) {
				$(this).closest('li').children('ul').find('input').each(function() {
					this.checked = true;
				});
			}
			else {
				$(this).parentsUntil('.GF_Datagrid_filter_tree_panel').filter('li').children('label').children('input:checked').each(function() {
					this.checked = false;
				});
				$(this).closest('li').find('input:checked').each(function() {
					this.checked = false;
				});
			}
			dDg.LoadData.apply(dDg);
			dDg._UpdateTreeList(jLi.closest('.GF_Datagrid_filter_tree_panel').data('jFilter'));
		});
	};
	
	this._OnTreeExpand = function(eEvent) {
		var dDg = eEvent.dDg;
		var sId = $(this).closest('li').data('sId');
		var bLoaded = $(this).closest('li').data('bLoaded');
		var bExpanded = $(this).closest('li').hasClass('expanded');
		var bChecked = $(this).closest('li').children('label').children('input').is(':checked');
		if (!bExpanded) {
			$(this).closest('li').addClass('expanded');
			if (!bLoaded) {
				var fHandler = $(this).closest('.GF_Datagrid_filter_tree_panel').data('fLoadTree');
				fHandler({
					parentId: sId
				}, GCallback(function(eEvent) {
					var jLi = eEvent.jParent;
					jLi.data('bLoaded', true);
					var jUl = jLi.children('ul');
					jUl.empty();
					var iLength = eEvent.aoItems.length;
					for (var i = 0; i < iLength; i++) {
						var jLi = $('<li/>');
						jUl.append(jLi);
						dDg._WriteTreeItem.apply(dDg, [jLi, eEvent.aoItems[i], bChecked]);
					}
				}, {
					jParent: $(this).closest('li')
				}));
			}
			else {
				
			}
		}
		else {
			$(this).closest('li').removeClass('expanded');
		}
	};
	
	this.PreventEnter = function(eEvent) {
		if (eEvent.keyCode == 13) {
			eEvent.preventDefault();
			eEvent.stopImmediatePropagation();
			$(this).change();
		}
	};
	
	this._CreateBody = function() {
		var jBody = $('<div class="body"></div>');
		var jTable = $('<table cellspacing="0"></table>');
		var jTbody = $('<tbody><tr><td></td></tr></tbody>');
		this.m_jBody = jTbody;
		jBody.append(jTable.append(jTbody));
		if (this.m_oOptions.appearance.max_height) {
			jBody.addClass('scrollable').css('max-height', this.m_oOptions.appearance.max_height);
		}
		return jBody;
	};
	
	this._CreateAdditionalRows = function() {
		var jBody = $('<div class="additional_rows"></div>');
		var jTable = $('<table cellspacing="0"></table>');
		var jTbody = $('<tbody><tr><td></td></tr></tbody>');
		this.m_jAdditionalRows = jTbody;
		jBody.append(jTable.append(jTbody));
		return jBody;
	};
	
	this._CreateFooter = function() {
		var jFooter = $('<div class="footer"></div>');
		this.m_jFooter = jFooter;
		if (this.m_oOptions.appearance.column_select) {
			var jSelectedRecords = $('<p class="selected-records"> ' + GF_Datagrid.Language.selected_records + ' <span class="GF_Datagrid_selected_records">0</span> <img class="GF_Datagrid_clear_selection" src="' + GF_Datagrid.Files.clear_selection_icon + '" title="' + GF_Datagrid.Language.clear_selection + '" alt="(' + GF_Datagrid.Language.clear_selection + ')" style="display: none;"/></p>');
			jSelectedRecords.prepend(this._WriteGroupActions());
			jFooter.append(jSelectedRecords);
		}
		var jPages = $('<p class="pages">' + GF_Datagrid.Language.choose_page + ' </p>');
		jPages.append('<img class="GF_Datagrid_go_to_first" src="' + GF_Datagrid.Files.first_page_icon + '" alt="(' + GF_Datagrid.Language.go_to_first_page + ')" title="' + GF_Datagrid.Language.go_to_first_page + '"/>');
		jPages.append('<img class="GF_Datagrid_go_to_prev" src="' + GF_Datagrid.Files.prev_page_icon + '" alt="(' + GF_Datagrid.Language.go_to_prev_page + ')" title="' + GF_Datagrid.Language.go_to_prev_page + '"/>');
		jPages.append('<input type="text" value="1" class="GF_Datagrid_page"/>');
		jPages.append('<img class="GF_Datagrid_go_to_next" src="' + GF_Datagrid.Files.next_page_icon + '" alt="(' + GF_Datagrid.Language.go_to_next_page + ')" title="' + GF_Datagrid.Language.go_to_next_page + '"/>');
		jPages.append('<img class="GF_Datagrid_go_to_last" src="' + GF_Datagrid.Files.last_page_icon + '" alt="(' + GF_Datagrid.Language.go_to_last_page + ')" title="' + GF_Datagrid.Language.go_to_last_page + '"/>');
		jFooter.append(jPages);
		var jRecordsShown = $('<p class="records-shown">' + GF_Datagrid.Language.records_shown + ' <span class="GF_Datagrid_records_from">0</span> - <span class="GF_Datagrid_records_to">0</span> ' + GF_Datagrid.Language.out_of + ' <span class="GF_Datagrid_records_total">0</span></p>');
		!this.m_oOptions.appearance.column_select && jRecordsShown.addClass('to-left');
		jFooter.append(jRecordsShown);
		return jFooter;
	};
	
	this._CreateModifyColumnsDialog = function() {
		var jDialog = $('<div></div>');
		jDialog.attr('id', 'GF_Modify_Columns_Datagrid_' + this.m_iId);
		jDialog.css('display', 'none');
		jDialog.addClass('GF_Datagrid_Modify_columns');
		var jUl = $('<ul></ul>');
		for (var i in this.m_aiColumnsOrder) {
			var oC = this.m_aoColumns[this.m_aiColumnsOrder[i]];
			if ((oC.id == '_options') || (oC.id == '_select')) {
				continue;
			}
			var jInput = $('<input value="' + this.m_aiColumnsOrder[i] + '" type="checkbox" checked="checked"/>');
			jInput.click(GF.NewEventHandler(function(eEvent) {
				if (!$(this).parents('ul:first').find('input:checkbox:checked').length) {
					eEvent.preventDefault();
					return false;
				}
				return true;
			})).change(this._HandleChangeColumns);
			jUl.append($('<li id="GF_Datagrid_Column_Order_' + this.m_aiColumnsOrder[i] + '">' + oC.caption + '</li>').prepend(jInput));
		}
		for (var i in this.m_aoColumns) {
			var oC = this.m_aoColumns[i];
			if (GF.InArray(this.m_aiColumnsOrder, i) || (oC.id == '_options') || (oC.id == '_select')) {
				continue;
			}
			var jInput = $('<input value="' + this.m_aiColumnsOrder[i] + '" type="checkbox"/>');
			jInput.click(GF.NewEventHandler(function(eEvent) {
				if (!$(this).parents('ul:first').find('input:checkbox:checked').length) {
					eEvent.preventDefault();
					return false;
				}
				return true;
			})).change(this._HandleChangeColumns);
			jUl.append($('<li id="GF_Datagrid_Column_Order_' + i + '">' + oC.caption + '</li>').prepend(jInput));
		}
		jUl.sortable({
			placeholder: 'placeholder',
			update: this._HandleChangeColumns
		});
		jDialog.append(jUl);
		jDialog.append('<p class="save"><a class="button" href="#" title="' + GF_Datagrid.Language.save_and_confirm_desc + '"><span><img src="' + GF_Datagrid.Files.save_icon + '" alt=""/>' + GF_Datagrid.Language.save_and_confirm + '</span></a></p>');
		jDialog.append('<p class="restore"><a class="button" href="#" title="' + GF_Datagrid.Language.restore_default_desc + '"><span><img src="' + GF_Datagrid.Files.make_default_icon + '" alt=""/>' + GF_Datagrid.Language.restore_default + '</span></a></p>');
		jDialog.append('<div class="shadow-l"></div>');
		jDialog.append('<div class="shadow-b"></div>');
		return jDialog;
	};
	
	
	this._InitializeEvents = function() {
		this.m_jTarget.keypress(GF.StopPropagation).keydown(GF.StopPropagation);
		this.m_jTarget.find('.header thead .GF_Datagrid_Col__modify_columns a').live('click', this._HandleToggleColumns);
		this.m_jTarget.find('.header thead .GF_Datagrid_Col__select input:checkbox').live('click', this._HandleToggleSelectVisible);
		this.m_jTarget.find('.header thead th.sortable').live('click', this._ChangeOrder);
		this.m_jTarget.find('.header tbody .GF_Datagrid_Col__retract_filters a').live('click', this.ToggleFilters);
		this.m_jTarget.mousemove(this._SpacerDrag).mouseup(this._SpacerDragStop);
		if (this.m_jFooter && this.m_jFooter.length) {
			this.m_jFooter.find('.GF_Datagrid_clear_selection').click(this.ClearSelection);
			this.m_jFooter.find('.GF_Datagrid_go_to_first').click(this._HandleGoToFirst);
			this.m_jFooter.find('.GF_Datagrid_go_to_prev').click(this._HandleGoToPrevious);
			this.m_jFooter.find('.GF_Datagrid_go_to_next').click(this._HandleGoToNext);
			this.m_jFooter.find('.GF_Datagrid_go_to_last').click(this._HandleGoToLast);
			this.m_jFooter.find('.GF_Datagrid_page').change(this._HandleGoToPage).keypress(this._HandleGoToPage);
		}
		$('.GF_Datagrid_Modify_columns .save a').live('click', this._HandleSaveColumns);
		$('.GF_Datagrid_Modify_columns .restore a').live('click', this._HandleRestoreColumns);
		$('.GF_Datagrid_Col__select input').live('mousedown', GF.PreventDefault);
	};
	
	this._InitializeRowEvents = function(jTr) {
		jTr.find('select').GSelect();
		jTr.click(this.SelectThisRow).mousedown(GF.PreventDefault);
		this.m_oOptions.context_actions.length &&	jTr.click(this._HandleShowContextMenu).mouseout(this._HandleHideContextMenu);
		(this.m_oOptions.event_handlers.view_row instanceof Function) && jTr.dblclick(this.HandleDoubleClick);
		jTr.find('.GF_Datagrid_Col__select input:checkbox').click(this._HandleCheckRowsCheckbox);
		jTr.find('input:text, select').mousedown(GF.StopPropagation).click(GF.StopPropagation).dblclick(GF.StopPropagation);
		jTr.find('.editable input').change(this.HandleEditableValueChange).keydown(this.PreventEnter);
		jTr.find('.selectable select').change(this.HandleSelectableValueChange).keydown(this.PreventEnter);
	};
	
	this._HandleGoToPage = GF.NewEventHandler(function(eEvent) {
		if ((eEvent.keyCode != undefined) && (eEvent.keyCode != 13)) {
			return true;
		}
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.GoToPage(parseInt($(this).val()) - 1);
		return true;
	});
	
	this._HandleGoToNext = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.GoToNext();
	});
	
	this._HandleGoToPrevious = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.GoToPrevious();
	});
	
	this._HandleGoToFirst = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.GoToFirst();
	});
	
	this._HandleGoToLast = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		dDg.GoToLast();
	});
	
	this._HandleToggleColumns = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		if (dDg.m_jModifyColumnsDialog && dDg.m_jModifyColumnsDialog.length) {
			dDg.m_jModifyColumnsDialog.slideUp(100, function() {
				dDg.m_jModifyColumnsDialog = GF.NULL;
				GOverlay.RemoveAll();
				$(this).remove();
			});
			$(this).closest('span').removeClass('active');
			return false;
		}
		dDg.m_jModifyColumnsDialog = dDg._CreateModifyColumnsDialog();
		dDg.m_jModifyColumnsDialog.css('left', $(this).closest('th').offset().left - 221 + $(this).closest('th').width());
		dDg.m_jModifyColumnsDialog.css('top', $(this).closest('th').offset().top + $(this).closest('th').height());
		dDg.m_jModifyColumnsDialog.GOverlay({
			fClick: GEventHandler(function(eEvent) {
				dDg.m_jTarget.find('.header thead .GF_Datagrid_Col__modify_columns a').click();
			}),
			fOpacity: .15
		});
		$('body').append(dDg.m_jModifyColumnsDialog);
		dDg.m_jModifyColumnsDialog.slideDown(150);
		$(this).closest('span').addClass('active');
		return false;
	});
	
	this._HandleCheckRowsCheckbox = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var jTr = $(this).parents('tr:first');
		eEvent.stopPropagation();
		var sId = dDg._GetIdFromTr(jTr);
		if (dDg.IsSelected(sId)) {
			dDg.DeselectRow(sId);
		}
		else {
			dDg.SelectRow(sId);
		}
		return true;
	});
	
	this._HandleToggleSelectVisible = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		if (!$(this).is(':checked')) {
			dDg.DeselectVisible();
		}
		else {
			dDg.SelectVisible();
		}
		return true;
	});
	
	this._HandleChangeColumns = GF.NewEventHandler(function(eEvent) {
		var jDialog = $(this).parents('.GF_Datagrid_Modify_columns');
		var sId = jDialog.attr('id');
		dDg = GF_Datagrid.ReturnInstance(parseInt(sId.substr(sId.lastIndexOf('_') + 1)));
		/*jDialog.slideUp('fast', function() {
			dDg.m_jModifyColumnsDialog = GF.NULL;
			$(this).remove();
		});*/
		dDg.m_aiColumnsOrder = [];
		if (dDg.m_oOptions.appearance.column_select) {
			dDg.m_aiColumnsOrder.push(dDg.m_oColumnsLookup['_select']);
		}
		var aoColumns = jDialog.find('ul').sortable('toArray');
		for (var i in aoColumns) {
			var iId = parseInt(aoColumns[i].substr(aoColumns[i].lastIndexOf('_') + 1));
			if (!jDialog.find('#GF_Datagrid_Column_Order_' + iId + ' input:checkbox:checked').length) {
				continue;
			}
			dDg.m_aiColumnsOrder.push(iId);
		}
		if (dDg.m_oOptions.appearance.column_options) {
			dDg.m_aiColumnsOrder.push(dDg.m_oColumnsLookup['_options']);
		}
		dDg.m_oOptions.appearance.header && dDg._WriteHeader();
		dDg.m_oOptions.appearance.filter && dDg._WriteFilters();
		dDg._InitializeWidths();
		dDg._InitializeSpacers();
		dDg.Rewrite();
		//dDg.LoadData();
		return true;
	});
	
	this._HandleSaveColumns = GF.NewEventHandler(function(eEvent) {
		var jDialog = $(this).parents('.GF_Datagrid_Modify_columns');
		var sId = jDialog.attr('id');
		dDg = GF_Datagrid.ReturnInstance(parseInt(sId.substr(sId.lastIndexOf('_') + 1)));
		
		/*jDialog.slideUp('fast', function() {
			dDg.m_jModifyColumnsDialog = GF.NULL;
			$(this).remove();
		});*/
		
		var aiWidths = [];
		for (var i in dDg.m_aiColumnsOrder) {
			aiWidths.push(dDg.m_aoColumns[dDg.m_aiColumnsOrder[i]].appearance.width);
		}
		
		if (dDg.m_oOptions.mechanics.save_column_modification) {
			GCookie('GF_Datagrid_Columns_' + dDg.m_oOptions.id, JSON.stringify(dDg.m_aiColumnsOrder), 7);
			GCookie('GF_Datagrid_Widths_' + dDg.m_oOptions.id, JSON.stringify(aiWidths), 7);
		}
		
		dDg.m_jTarget.find('.header thead .GF_Datagrid_Col__modify_columns a').click();
		
		return false;
	});
	
	this._HandleRestoreColumns = GF.NewEventHandler(function(eEvent) {
		var jDialog = $(this).parents('.GF_Datagrid_Modify_columns');
		var sId = jDialog.attr('id');
		var dDg = GF_Datagrid.ReturnInstance(parseInt(sId.substr(sId.lastIndexOf('_') + 1)));
		
		jDialog.slideUp('fast', function() {
			dDg.m_jModifyColumnsDialog = GF.NULL;
			$(this).remove();
		});
		GOverlay.RemoveAll();
		
		GCookie('GF_Datagrid_Columns_' + dDg.m_oOptions.id,null);
		GCookie('GF_Datagrid_Widths_' + dDg.m_oOptions.id,null);
		
		//dDg.m_oOptions.columns = dDg.m_oOptionsOriginal.columns;
		dDg._ParseColumnOptions();
		dDg.m_oOptions.appearance.header && dDg._WriteHeader();
		dDg.m_oOptions.appearance.filter && dDg._WriteFilters();
		dDg._InitializeWidths();
		dDg._InitializeSpacers();
		dDg.Rewrite();
		
		return false;
	});
	
	this._HandleShowContextMenu = GF.NewEventHandler(function(eEvent) {
		GF_ContextMenu.HideAll();
		var rClick = false;
		if (this.context) {
			var oThis = this.context;
			rClick = true;
		}
		else {
			var oThis = this;
		}
		var dDg = GF_Datagrid.GetCurrentInstance(oThis);
		if (!rClick && !$(oThis).hasClass('selected')) {
			GF_ContextMenu.HandleHide(eEvent);
			return false;
		}
		var sId = $(oThis).closest('tr').andSelf().find('.GF_Datagrid_Row_Id').text();
		dDg.SelectRow(sId);
		if (GF.InArray(dDg.m_oOptions.no_actions_for_rows, sId)) {
			return false;
		}
		if (dDg.m_oOptions.event_handlers.open_context_menu instanceof Function) {
			if (!dDg.m_oOptions.event_handlers.open_context_menu(dDg, sId)) {
				return false;
			}
		}
		var aActions = [];
		for (var i in dDg.m_oOptions.context_actions) {
			var oA = dDg.m_oOptions.context_actions[i];
			if ((oA.condition == GF.NULL) || ((typeof(oA.condition) == 'function') && oA.condition(dDg.GetRow(sId)))) {
				aActions.push(oA);
			}
		}
		new GF_ContextMenu.Create({
			id: sId,
			parent: $(oThis).closest('tr'),
			actions: aActions,
			position: rClick ? {
				left: eEvent.pageX - 16,
				top: eEvent.pageY - 10
			} : {
				left: eEvent.pageX + 10,
				top: eEvent.pageY + 3
			}
		});
		return false;
	});
	
	this._HandleHideContextMenu = GF.NewEventHandler(function(eEvent) {
		return GF_ContextMenu.HandleHide(eEvent);
	});
	
	this.HandleDoubleClick = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var sId = $(this).closest('tr').andSelf().find('.GF_Datagrid_Row_Id').text();
		if (false || dDg.m_oOptions.event_handlers.view_row instanceof Function) {
			dDg.m_oOptions.event_handlers.view_row(dDg.m_iId, sId);
		}
		return false;
	});
	
	this.HandleEditableValueChange = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var sId = $(this).closest('tr').andSelf().find('.GF_Datagrid_Row_Id').text();
		if (dDg.m_oOptions.event_handlers.update_row instanceof Function) {
			var oRow = dDg.GetRow(sId);
			var sColumn = $(this).attr('name').substring($(this).attr('name').lastIndexOf('[') + 1, $(this).attr('name').length - 1);
			var sPreviousValue = oRow[sColumn];
			oRow[sColumn] = $(this).val();
			dDg.m_oOptions.event_handlers.update_row(sId, oRow, sColumn, sPreviousValue);
			return true;
		}
	});
	
	this.HandleSelectableValueChange = GF.NewEventHandler(function(eEvent) {
		var dDg = GF_Datagrid.GetCurrentInstance(this);
		var sId = $(this).closest('tr').andSelf().find('.GF_Datagrid_Row_Id').text();
		if (dDg.m_oOptions.event_handlers.update_row instanceof Function) {
			var oRow = $.extend(true, {}, dDg.GetRow(sId));
			var sColumn = $(this).attr('name').substring($(this).attr('name').lastIndexOf('[') + 1, $(this).attr('name').length - 1);
			oRow[sColumn] = $(this).find('option:selected').attr('value');
			dDg.m_oOptions.event_handlers.update_row(sId, oRow, sColumn);
			return true;
		}
	});
	
	this._NewRowEventHandler = function(fHandler) {
		var fSafeHandler = GF.NewEventHandler(function(eEvent) {
			var dDg = GF_Datagrid.GetCurrentInstance(this);
			var jTr = $(this).is('tr') ? $(this) : $(this).parents('tr:first');
			if (!jTr.length) {
				GF_Debug.Warning('Datagrid\'s row event handler invoked neither on a TR element nor on any of its descendants. Action aborted.');
				return false;
			}
			var sId = dDg._GetIdFromTr(jTr);
			eEvent.stopImmediatePropagation();
			eEvent.preventDefault();
			return fHandler(dDg, sId);
		});
		return fSafeHandler;
	};
	
	/* Constructor call */
	
	return this.GF_Datagrid(jTarget, oOptions);
	
}, {
	
	s_dContextMenuFordDg: GF.NULL,
	s_sContextMenuForId: GF.NULL,
	s_cContextMenu: GF.NULL,
	
	SORT_DIR_ASC: 1,
	SORT_DIR_DESC: 2,
	
	ALIGN_LEFT: 1,
	ALIGN_CENTER: 2,
	ALIGN_RIGHT: 3,
	
	FILTER_NONE: 0,
	FILTER_INPUT: 1,
	FILTER_BETWEEN: 2,
	FILTER_SELECT: 3,
	FILTER_AUTOSUGGEST: 4,
	FILTER_TREE: 5,
	
	WIDTH_AUTO: 0,
	
	ACTION_EDIT: 1,
	ACTION_DELETE: 2,
	ACTION_VIEW: 3,
	
	RefreshAll: GF.NewSafeMethod(function() {
		for (var i in GF_Instance.s_aoInstances) {
			if (GF_Instance.s_aoInstances[i].IsInstanceOf('GF_Datagrid')) {
				GF_Instance.s_aoInstances[i].LoadData();
			}
		}
	}),
	
	ProcessIncomingData: GF.NewEventHandler(function(oData) {
		if ((oData.data_id == undefined) || !(GF_Instance.s_aoInstances[oData.data_id] instanceof GF_Datagrid)) {
			GF_Debug.Error('The server has responded with an invalid datagrid id (' + oData.data_id + ').');
		}
		var dDg = GF_Instance.ReturnInstance(oData.data_id);
		dDg._ProcessData(oData);
	})
	
});

/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_Datagrid_Config = GF_Config.GF_Extend('GF_Datagrid_Config', function(options) {
	
	this.GF_Datagrid_Config = GF.NewConstructor(function(options) {
		
		this.id = GF.NULL;
		this.appearance = {
			header: true,
			filter: true,
			footer: true,
			column_select: true,
			column_options: true,
			max_height: 0
		};
		this.mechanics = {
			rows_per_page: 50,
			key: GF.NULL,
			default_sorting: GF.NULL,
			right_click_menu: false,
			autosuggest_delay: 500,
			autosuggest_min_length: 1,
			autosuggest_suggestions: 5,
			only_one_selected: false,
			no_column_modification: false,
			no_column_resizing: false,
			create_input: false,
			save_column_modification: true,
			persistent: true
		};
		this.event_handlers = {
			load: GF.NULL,
			process: GF.NULL,
			select: GF.NULL,
			deselect: GF.NULL,
			click_row: GF.NULL,
			view_row: GF.NULL,
			selection_changed: GF.NULL,
			delete_row: GF.NULL,
			edit_row: GF.NULL,
			update_row: GF.NULL,
			delete_group: GF.NULL,
			open_context_menu: GF.NULL,
			loaded: GF.NULL
		};
		this.external_indicators = {
			rows_from: GF.NULL,
			rows_to: GF.NULL,
			total_rows: GF.NULL,
			page: GF.NULL,
			total_pages: GF.NULL
		};
		this.columns = [];
		this.row_actions = [];
		this.group_actions = [];
		this.context_actions = [];
		this.no_actions_for_rows = [];
		this.additional_filters = [];
		
		this._Process(options);
		
	});
	
	this._PostProcess = function() {
		if (this.mechanics.key == GF.NULL) {
			this.mechanics.key = this.columns[0].id;
		}
		if (this.mechanics.default_sorting == GF.NULL) {
			this.mechanics.default_sorting = this.mechanics.key;
		}
	};
	
	/* Constructor call */
	
	return this.GF_Datagrid_Config(options);
	
});

GF_Datagrid_Column = GF_Config.GF_Extend('GF_Datagrid_Column', function(options) {
	
	this.GF_Datagrid_Column = GF.NewConstructor(function(options) {
		
		this.id = GF.NULL;
		this.caption = GF.NULL;
		this.editable = false;
		this.selectable = false;
		this.appearance = {
			visible: true,
			width: GF_Datagrid.WIDTH_AUTO,
			align: GF_Datagrid.ALIGN_CENTER,
			no_title: false
		};
		this.sorting = {
			allowed: true,
			default_order: GF_Datagrid.SORT_DIR_ASC
		};
		this.filter = {
			type: GF_Datagrid.FILTER_NONE,
			filtered_column: GF.NULL,
			source: GF.NULL,
			options: [],
			load_children: GF.NULL
		};
		
		this._Process(options);
		
	});
	
	/* Constructor call */
	
	return this.GF_Datagrid_Column(options);
	
});

GF_Datagrid_Row = GF_Config.GF_Extend('GF_Datagrid_Row', function(options) {
	
	this.GF_Datagrid_Row = GF.NewConstructor(function(options) {
		
		this.id = GF.NULL;
		this.key = 'id';
		this.className = '';
		this.caption = '';
		this.source = GF.NULL;
		
		this._Process(options);
		
	});
	
	/* Constructor call */
	
	return this.GF_Datagrid_Row(options);
	
});

GF_Action = GF_Config.GF_Extend('GF_Action', function(options) {
	
	this.GF_Action = GF.NewConstructor(function(options) {
		
		this.img = GF.NULL;
		this.caption = GF.NULL;
		this.action = GF.NULL;
		this.values = {};
		this.parameter = GF.NULL;
		this.condition = GF.NULL;
		
		this._Process(options);
		
	});
	
	/* Constructor call */
	
	return this.GF_Action(options);
	
});


/**
 * Gekosale, Open Source E-Commerce Solution
 * http://www.gekosale.com
 *
 * Copyright (c) 2009 Gekosale
 *
 * This library is free software; you can redistribute it and/or 
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version. 
 * 
 */
 GF_Datagrid.Files = {
	modify_columns_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/modify-columns.png',
	retract_filters_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/filter-retract.png',
	expand_filters_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/filter-expand.png',
	order_asc: GCore.DESIGN_PATH + '_images_panel/datagrid/sort-asc.png',
	order_desc: GCore.DESIGN_PATH + '_images_panel/datagrid/sort-desc.png',
	delete_group_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/delete.png',
	delete_row_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/delete.png',
	edit_row_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/edit.png',
	view_row_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/details.png',
	first_page_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/page-first.png',
	prev_page_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/page-prev.png',
	next_page_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/page-next.png',
	last_page_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/page-last.png',
	clear_selection_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/clear-selection.png',
	save_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/save.png',
	make_default_icon: GCore.DESIGN_PATH + '_images_panel/icons/datagrid/make-default.png',
	warning_icon: GCore.DESIGN_PATH + '_images_panel/datagrid/warning.png'
};

GF_Slider = GF_Instance.GF_Extend('GF_Slider', function(jTarget, oOptions) {
	
	/* Declarations */
	
	this.GF_Slider;
	
	this.m_oOptions;
	this.m_jSlider;
	this.p_iValue;
	this.p_iMin;
	this.p_iMax;
	
	/* Definitions */
	
	this.GF_Slider = GF.NewConstructor(function(jTarget, oOptions) {
		var oDefaults = {
			min: 0,
			max: 200,
			range_min: 0,
			range_max: 200,
			value: 0,
			event_handlers: {
				drag_start: GF.NULL,
				drag_end: GF.NULL,
				change: GF.NULL
			}
		};
		this.m_oOptions = $.extend(oDefaults, oOptions);
		this.p_iMin = this.m_oOptions.min;
		this.p_iMax = this.m_oOptions.max;
		this._InitSlider();
		this.p_iValue = parseInt(this.m_jSlider.slider('value'));
		this.UpdateIndicators();
	});
	
	this._InitSlider = function() {
		this.m_jSlider = $('<div/>');
		this.m_jInput = $('<input type="text"/>');
		this.m_jInput.change(this._OnInputChange);
		this.m_jTarget.append(this.m_jSlider);
		this.m_jTarget.append($('<div class="input"/>').append($('<span/>').append(this.m_jInput)));
		this.m_jSlider.slider({
			range: 'min',
			min: this.m_oOptions.range_min,
			max: this.m_oOptions.range_max,
			value: this.m_oOptions.value,
			slide: this._OnSlide,
			start: this._OnStart,
			stop: this._OnStop
		});
	};
	
	this._OnSlide = GF.NewSafeMethod(function(e, oUi) {
		var sSlider = GF_Slider.GetCurrentInstance(this);
		var iPreviousValue = sSlider.p_iValue;
		var iNewValue = sSlider._Validate(parseInt(oUi.value));
		sSlider.p_iValue = iNewValue;
		sSlider.UpdateIndicators();
		if (iNewValue != iPreviousValue) {
			sSlider._InvokeEvent('change', {
				previous_value: iPreviousValue,
				value: iNewValue
			});
		}
		if (iNewValue != parseInt(oUi.value)) {
			sSlider.m_jSlider.slider('value', sSlider.p_iValue);
			return false;
		}
		return true;
	});
	
	this._OnStart = GF.NewSafeMethod(function(e, oUi) {
		var sSlider = GF_Slider.GetCurrentInstance(this);
		sSlider._InvokeEvent('drag_start', {
			value: sSlider.p_iValue
		});
		return true;
	});
	
	this._OnStop = GF.NewSafeMethod(function(e, oUi) {
		var sSlider = GF_Slider.GetCurrentInstance(this);
		sSlider._InvokeEvent('drag_end', {
			value: sSlider.p_iValue
		});
		return true;
	});
	
	this._OnInputChange = GF.NewEventHandler(function(e) {
		var sSlider = GF_Slider.GetCurrentInstance(this);
		sSlider.SetValue($(this).val());
	});
	
	this.UpdateIndicators = function() {
		this.m_jInput.val(this.p_iValue + '%');
	};
	
	this.SetValue = GF.NewSafeMethod(function(iValue) {
		iValue = parseInt(iValue);
		if (isNaN(iValue)) {
			this.UpdateIndicators();
			return false;
		}
		var iPreviousValue = this.p_iValue;
		this.p_iValue = this._Validate(iValue);
		this.m_jSlider.slider('value', this.p_iValue);
		this.UpdateIndicators();
		this._InvokeEvent('change', {
			previous_value: iPreviousValue,
			value: this.p_iValue
		});
		return true;
	});
	
	this._Validate = function(iValue) {
		return Math.max(this.p_iMin, Math.min(this.p_iMax, iValue));
	};
	
	this._InvokeEvent = function(sEvent, oParams) {
		if ((this.m_oOptions.event_handlers != undefined) && (this.m_oOptions.event_handlers[sEvent] != undefined) && (this.m_oOptions.event_handlers[sEvent] instanceof Function)) {
			this.m_oOptions.event_handlers[sEvent].call(this.m_jTarget.get(0), oParams);
		}
	};
	
	/* Constructor call */
	
	return this.GF_Slider(jTarget, oOptions);
	
}, {

});
