jQuery.fn.checkCheckboxes = function(filter, returnChecked)
{
	filter = filter || "*";
	returnChecked = returnChecked || false;
	var returnWhat = jQuery([]);
	this.each(
		function()
		{
			var checked = jQuery("input:checkbox", this).filter(filter).each(
				function()
				{
					this.checked = true;
					$(this).change();
				}
			).filter(":checked");
			returnWhat = checked;
		}
	);
	if(!returnChecked)
	{
		returnWhat = this;
	}
	return returnWhat;
};

jQuery.fn.unCheckCheckboxes = function(filter, returnUnChecked)
{
	filter = filter || "*";
	returnUnChecked = returnUnChecked || false;
	var returnWhat = jQuery([]);
	this.each(
		function()
		{
			var unChecked = jQuery("input:checkbox", this).filter(filter).each(
				function()
				{
					this.checked = false;
					$(this).change();
				}
			).filter(":not(:checked)");
			returnWhat = unChecked;
		}
	);
	if(!returnUnChecked)
	{
		returnWhat = this;
	}
	return returnWhat;
};
