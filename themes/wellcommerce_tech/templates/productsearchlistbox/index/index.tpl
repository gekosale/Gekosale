{% extends "layoutbox.tpl" %}
{% block content %}
{% if dataset.rows|length > 0 %}
<h2 class="category-title">Wyniki wyszukiwania dla <strong>"{{ phrase }}"</strong></h2>
<span class="category-info">Dla "{{ phrase }}" znaleziono {{ dataset.total }} wyników.</span>
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
{% include 'products.tpl' %}
{% include 'pagination.tpl' %}
{% else %}
<p class="empty">{% trans %}TXT_EMPTY_SEARCH_RESULTS{% endtrans %}</p>
{% endif %}
{% endblock %}