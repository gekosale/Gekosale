{% include 'header.tpl' %}
<section id="content" class="content layout-boxes">
	{% if message is defined %}
	<div class="alert alert-info">
		<h3>{{ message }}</h3>
	</div>
	{% endif %} 
	{{ pagescheme.content }}
</section>
{{ pagescheme.js }}
{% include 'footer.tpl' %}