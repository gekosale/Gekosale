<h2>{% trans %}TXT_FILES{% endtrans %}</h2>
<table class="table">
	<tbody>
		{% for file in files %}
		<tr>
			<td>{{ file.name }}</td>
			<th><a href="{{ URL }}redirect/view/{{ file.idfile }}">{% trans %}TXT_DOWNLOAD_FILE{% endtrans %}</a></th>
		</tr>
		{% endfor %}
	</tbody>
</table>