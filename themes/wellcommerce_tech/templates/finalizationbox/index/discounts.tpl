{% for s in summary %}
<tr>
	<td colspan="4" class="alignright">{{ s.label }}</td>
	<td colspan="2" class="center">{{ s.value }}</td>
</tr>
{% endfor %}