{% for group in technicalData %}
<h2>{{ group.name }}</h2>
<table class="table">
	<tbody>
	{% for spec in group.attributes if spec.value != '' %}
		<tr>
			<td>{{ spec.name }}</td>
			<th>
			{% if spec.type == 5 %}
				{% if spec.value == 1 %}{% trans %}TXT_YES{% endtrans %}{% else %}{% trans %}TXT_NO{% endtrans %}{% endif %}
			{% else %}
				{{ spec.value }}
			{% endif %}
			</th>
		</tr>
		{% endfor %}
	</tbody>
</table>
{% endfor %}