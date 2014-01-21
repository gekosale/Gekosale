{% extends "layoutbox.tpl" %}
{% block content %}
<h2 class="category-title">{{ box.heading }}</h2>
{% if currentCategory.description != '' or currentCategory.shortdescription !='' or currentCategory.photo != '' %}
<div class="category-description">
	{% if currentCategory.photo != '' %}
	<img src="{{ currentCategory.photo }}" alt="{{ currentCategory.name }}" />
	{% endif %}
	<div class="caption">
	{% if currentCategory.description !='' %}
		{{ currentCategory.description }}
	{% else %}
		{{ currentCategory.shortdescription }}
	{% endif %}
	</div>
</div>
{% endif %}
<div class="category-options">
	<form class="form-horizontal">
    	<fieldset>
        	<div class="control-group">
            	<label class="control-label" for="sort">Sortuj wg:</label>
                <div class="controls">
                	<select id="sort" class="input-medium">
                    {% for key, sort in sorting %} 
	    				<option value="?sort={{ key }}&order=asc" {% if key == orderBy and orderDir == 'asc' %}selected{% endif %}>{{ sort }} - {% trans %}TXT_ASC{% endtrans %}</option> 
	    				<option value="?sort={{ key }}&order=desc" {% if key == orderBy and orderDir == 'desc' %}selected{% endif %}>{{ sort }} - {% trans %}TXT_DESC{% endtrans %}</option> 
	    			{% endfor %}
                    </select>
                </div>
            </div>
		</fieldset>
	</form>
    <div class="category-view">
    	<span>Widok:</span>
        <a href="?view=1" class="list {% if view == 1 %}active{% endif %}"></a>
        <a href="?view=0" class="box {% if view == 0 %}active{% endif %}"></a>
	</div>
    <a href="#" class="pull-right">Zobacz wszystkie produkty <i class="icon-arrow-down-blue"></i></a>
    <div class="clearfix"></div>
</div>
{% include 'pagination.tpl' %} 
{% include 'products.tpl' %}
{% include 'pagination.tpl' %}
{% endblock %}