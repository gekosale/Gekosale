{% extends "layoutbox.tpl" %}
{% block content %}
<div class="tabbable tabs-below">
	<div class="tab-content">
    	<div class="tab-pane fade active in" id="A">
        	<div id="slideshow-{{ box.id }}" class="carousel slide">
            	<div class="carousel-inner">
            		{% for slide in slideshow %}
            		<div class="item {% if loop.first %}active{% endif %}">
                    	 <a href="{{ slide.url }}"><img src="{{ DESIGNPATH }}{{ slide.image }}" alt=""></a>
                        <div class="carousel-caption">
	                        <h4>{{ slide.caption }}</h4>
                    	</div>
                    </div>
            		{% endfor %}
				</div>
                <a class="left carousel-control" href="#slideshow-{{ box.id }}" data-slide="prev">‹</a>
                <a class="right carousel-control" href="#slideshow-{{ box.id }}" data-slide="next">›</a>
			</div>
		</div>
	</div>
</div>
{% endblock %}