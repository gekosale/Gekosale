{% extends "layoutbox.tpl" %}
{% block content %}
{% if collectionData|length > 0 %}
<h1 class="category-title">{{ producer.name }} - {{ collectionData.name }}</h1>
	{% if collectionData.photo != '' or collectionData.description != '' %}
	<div class="category-description">
		{% if collectionData.photo != '' %}
		<img style="float: right;right: 0;position: absolute;" src="{{ collectionData.photo }}" alt="{{ collectionData.name }}" title="{{ collectionData.name }}" />
		{% endif %}
		{% if collectionData.description != '' %}
		<div class="caption">
		{{ collectionData.description }}
		</div>
		{% endif %}
	</div>
	{% endif %}
{% else %}
<h1 class="category-title">{{ producer.name }}</h1>
{% if producer.photo != '' or producer.description != '' %}
<div class="category-description">
	{% if producer.photo != '' %}
	<img style="float: right;right: 0;position: absolute;" src="{{ producer.photo }}" alt="{{ producer.name }}" title="{{ producer.name }}" />
	{% endif %}
	{% if producer.description != '' %}
	<div class="caption">
	{{ producer.description }}
	</div>
	{% endif %}
</div>
{% endif %}
{% endif %}
{% if dataset.rows|length > 0 %}
<div class="category-options">
	<form class="form-horizontal">
    	<fieldset>
        	<div class="control-group">
            	<label class="control-label" for="sort">Sortuj wg:</label>
                <div class="controls">
                	<select id="sort" class="input-medium" onchange="location.href=this.value">
                    {% for sort in sorting %}
	    				<option value="{{ sort.link }}" {% if sort.active %}selected{% endif %}>{{ sort.label }}</option>
	    			{% endfor %}
                    </select>
                </div>
            </div>
		</fieldset>
	</form>
    <div class="category-view">
    	<span>Widok:</span>
    	{% for switch in viewSwitcher %}
        <a href="{{ switch.link }}#sort" class="{% if switch.type == 1 %}list{% else%}box{% endif %} {% if switch.active == 1 %}active{% endif %}"></a>
        {% endfor %}
	</div>
    <div class="clearfix"></div>
</div>
{% include 'pagination.tpl' %}
{% include 'products.tpl' %}
{% include 'pagination.tpl' %}
{% else %}
<div class="alert alert-block alert-error">
{% trans %}ERR_EMPTY_PRODUCT_LIST{% endtrans %}
</div>
{% endif %}
{% endblock %}