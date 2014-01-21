{% extends "layoutbox.tpl" %} {% block content %}
<div id="homeSlideTab" class="tabbable tabs-below">
	<div class="tab-content">
		<div class="tab-pane fade active in" id="category-27">
			<div id="myCarousel" class="carousel slide">
			{{ products }}
			</div>
		</div>
	</div>
	<div class="tabs">
		<ul class="nav nav-tabs bottom-tabs">
			{% for category in showcasecategories %}
			<li {% if category.id == 0%}class="active"{% endif %}><a href="#" data-toggle="tab" data-id="{{ category.id }}">{{ category.caption }}</a><span class="ico"></span></li>
			{% endfor %}
		</ul>
	</div>
</div>
{% endblock %}
