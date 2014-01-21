{% extends "layoutbox.tpl" %}
{% block content %}
<div class="vertical-block category-filter">
	<div class="block-head">{{ box.heading }}</div>
    <div class="block-content">
    	<form class="form" method="post" action="">
    		<input type="hidden" name="layered_submitted" value="1" />
			<fieldset>
    			<legend>Cena:</legend>
		        <div class="control-group">
		        	<label class="control-label" style="display: inline-block;">Od:</label> <input style="display: inline-block;" class="input-mini2" type="text" name="priceFrom" value="{{ priceFrom }}">&nbsp;&nbsp;
		        	<label class="control-label" style="display: inline-block;">Do:</label> <input style="display: inline-block;" class="input-mini2" type="text" name="priceTo" value="{{ priceTo }}">
		        </div>
		        <div class="form-actions form-actions-clean pull-right">
		        	<button class="btn btn-primary">Pokaż</button>
		        </div>
			</fieldset>
			{% for group in groups %}
        	<fieldset>
				<legend>{{ group.name }}:</legend>
                <div class="controls checkbox-list">
                	{% for attribute in group.attributes %}
                	<label class="checkbox">
                    	<input class="active" type="checkbox" name="attribute[]" value="{{ attribute.id }}" {% if attribute.active %}checked="checked"{% endif %}>
                    	<a href="{{ attribute.link }}" title="{{ attribute.name }}">{{ attribute.name }}</a>
                    </label>
					{% endfor %}                  
				</div>
				<div class="form-actions form-actions-clean pull-right">
		        	<button class="btn btn-primary">Pokaż</button>
		        </div>
			</fieldset>
			{% endfor %}  
			
			{% for name, group in technicaldatagroups %}
        	<fieldset>
				<legend>{{ name }}:</legend>
                <div class="controls checkbox-list">
                	{% for attribute in group %}
                	<label class="checkbox">
                    	<input class="active" type="checkbox" name="attribute[]" value="{{ attribute.id }}" {% if attribute.active %}checked="checked"{% endif %}>
                    	<a href="{{ attribute.link }}" title="{{ attribute.name }}">{{ attribute.name }}</a>
                    </label>
					{% endfor %}                  
				</div>
				<div class="form-actions form-actions-clean pull-right">
		        	<button class="btn btn-primary">Pokaż</button>
		        </div>
			</fieldset>
			{% endfor %}  
			
        	{% if producers|length > 0 %}
        	<fieldset>
				<legend>Producenci:</legend>
                <div class="controls checkbox-list">
                	{% for producer in producers %}
                	<label class="checkbox">
                    	<input class="active" type="checkbox" name="producer[]" value="{{ producer.id }}" {% if producer.active %}checked="checked"{% endif %}>
                    	<a href="{{ producer.link }}" title="{{ producer.name }}">{{ producer.name }}</a>
                    </label>
					{% endfor %}                  
				</div>
				<div class="form-actions form-actions-clean pull-right">
		        	<button class="btn btn-primary">Pokaż</button>
		        </div>
			</fieldset>
			{% endif %}               
		</form>
    </div>
</div>


{% endblock %}