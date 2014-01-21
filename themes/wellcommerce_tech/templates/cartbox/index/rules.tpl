{% if checkRulesCart is defined %}
	{% set hasRules = 0 %}
	{% for rule in checkRulesCart if rule.type == 0 %}
		{% set hasRules = 1 %}
	{% endfor %}
	
	{% if hasRules %}
	<div class="alert alert-block alert-info">
		{% for rule in checkRulesCart if rule.type == 0 %}
		<h4>
			{% if rule.name != '' and rule.description != '' %}
			<strong>{{ rule.name }}</strong><br />
			{{ rule.description }}
			{% else %}
			<strong>{% trans %}TXT_MEET_CONDITION_FOR_DISCOUNT{% endtrans %} {% if rule.discount is defined %}{{ rule.discount }}{% endif %} {% if rule.freeshipping == 1 %}{% trans %}TXT_RULESCART_GET_FREE_SHIPPING{% endtrans %}{% endif %}</strong> 
			{% for condition in rule.conditions %}
			<br />{{ condition }}
			{% endfor %}
			{% endif %}
		</h4>
		{% endfor %}
	</div>
	{% endif %}
{% endif %}