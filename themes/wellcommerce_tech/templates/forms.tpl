{% macro input(field, class) %}
{% autoescape true %}
<div class="control-group {% if field.errors|length %}error{% endif %}">
	<label class="control-label {% if field.attributes.required == 1 %}required{% endif %}" for="{{ field.attributes.id }}">{{ field.attributes.label }}</label>
    <div class="controls">
    	<input type="text" name="{{ field.attributes.name }}" class="{{ class }}" id="{{ field.attributes.id }}" value="{{ field.value }}" />
        {% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</div>
</div>
{% endautoescape %}
{% endmacro %}

{% macro hidden(field, class) %}
{% autoescape true %}
<input type="hidden" name="{{ field.attributes.name }}" id="{{ field.attributes.id }}" value="{{ field.value }}" />
{% endautoescape %}
{% endmacro %}

{% macro password(field, class) %}
{% autoescape true %}
<div class="control-group {% if field.errors|length %}error{% endif %}">
	<label class="control-label {% if field.attributes.required == 1 %}required{% endif %}" for="{{ field.attributes.id }}">{{ field.attributes.label }}</label>
    <div class="controls">
    	<input autocomplete="off" type="password" name="{{ field.attributes.name }}" class="{{ class }}" id="{{ field.attributes.id }}" value="{{ field.value }}" />
        {% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</div>
</div>
{% endautoescape %}
{% endmacro %}

{% macro checkbox(field, class) %}
{% autoescape true %}
<div class="control-group {% if field.errors|length %}error{% endif %}">
	<label class="checkbox">
		<input type="checkbox" name="{{ field.attributes.name }}" value="1" id="{{ field.attributes.id }}" {% if field.value == 1 %}checked="checked"{% endif %} /> {{ field.attributes.label|raw }}
		{% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</label>
</div>
{% endautoescape %}
{% endmacro %}

{% macro select(field, class) %}
{% autoescape true %}
<div class="control-group {% if field.errors|length %}error{% endif %}">
	<label class="control-label {% if field.attributes.required == 1 %}required{% endif %}" for="{{ field.attributes.id }}">{{ field.attributes.label }}</label>
	<div class="controls">
		<select class="{{ class }}" name="{{ field.attributes.name }}" id="{{ field.attributes.id }}">
			{% for key,option in field.attributes.options %}
			<option value="{{ key }}" {% if key == field.value %}selected="selected"{% endif %}>{{ option }}</option>
			{% endfor %}
		</select>
		{% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</div>
</div>
{% endautoescape %}
{% endmacro %}

{% macro radio(field, class) %}
{% autoescape true %}
<div class="control-group">
	<label class="control-label" for="{{ field.attributes.id }}">{{ field.attributes.label }}</label>
	<div class="controls">
	{% for key, option in field.attributes.options %}
		<label class="radio inline">
			<input type="radio"	id="{{ field.attributes.id }}_{{ key }}" name="{{ field.attributes.name }}" value="{{ key }}" {% if key == field.value %}checked="checked"{% endif %}> {{ option }}
		</label>
	{% endfor %}
	{% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</div>
</div>
{% endautoescape %}
{% endmacro %}

{% macro textarea(field, class) %}
{% autoescape true %}
<div class="control-group {% if field.errors|length %}error{% endif %}">
	<label class="control-label  {% if field.attributes.required == 1 %}required{% endif %}" for="{{ field.attributes.id }}">{{ field.attributes.label }}</label>
   	<div class="controls">
       	<textarea name="{{ field.attributes.name }}" id="{{ field.attributes.id }}" class="{{ class }}" rows="6">{{ field.attributes.value }}</textarea>
       	 {% if field.errors|length %}<span class="help-block pull-right">{{ field.errors|join }}</span>{% endif %}
	</div>
</div>
{% endautoescape %}
{% endmacro %}