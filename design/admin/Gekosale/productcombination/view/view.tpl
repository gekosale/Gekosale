<div id="main">
 <div id="content">
  {if isset($error)}
  	<p align="center" style="color:red">{$error}</p>
  {else}
   <ul class="tabs static">
    <li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}"><span>{% trans %}TXT_PRODUCTCOMBINATION{% endtrans %}</span></a></li>
    <li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/add"><span>{% trans %}TXT_ADD{% endtrans %}</span></a></li>
   </ul>
   <div class="block">
    <p>{% trans %}TXT_VIEW_FORM{% endtrans %}</p>
   </div>
    <ul class="tabs static">
     <li class="active"><h2><span>{% trans %}TXT_VIEW{% endtrans %}</span></h2></li>
     <li><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/edit/{$combination.id}"><span>{% trans %}TXT_EDIT{% endtrans %}</span></a></li>
    </ul>
    <div class="properties">
     <dl>   
    	 <dt>{% trans %}TXT_ID{% endtrans %}:</dt><dd>{$combination.id}</dd>
    	 <dt>{% trans %}TXT_PRODUCTCOMBINATION{% endtrans %}:</dt><dd>{$combination.name}</dd>
    	 <dt>{% trans %}TXT_SUFFIXTYPE{% endtrans %}:</dt><dd>{% trans %}{$combination.suffixname}{% endtrans %} ({$combination.symbol})</dd>
    	 <dt>{% trans %}TXT_VALUE{% endtrans %}:</dt><dd>{$combination.value}</dd>
    </dl> 
   </div>
    <div class="properties">
     <dl> 	 
       {section name=i loop=$combination.products}
       		<dt>{% trans %}TXT_PRODUCT{% endtrans %}:</dt>
       			<dd><strong>{$combination.products[i].productname}</strong>  
				{section name=key loop=$combination.products[i].productattributes}  
	       			{$combination.products[i].productattributes[key].attribute}
				{/section}
				</dd>
        	<dt>{% trans %}TXT_SHORTDESCRIPTION{% endtrans %}:</dt>
        		<dd>{$combination.products[i].shortdescription}</dd>
       		<dt>{% trans %}TXT_SELLPRICE{% endtrans %}:</dt>
        		<dd>{$combination.products[i].sellprice} {% trans %}TXT_CURRENCY{% endtrans %}</dd>
       		<dt>{% trans %}TXT_NUMBEROFITEM{% endtrans %}:</dt>
        		<dd>{$combination.products[i].numberofitems} {% trans %}TXT_QTY{% endtrans %}</dd>
       {/section}
   	 </dl> 
    </div> 
  {/if}
 </div>
</div>