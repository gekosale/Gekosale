<nav class="navbar">
    <div class="navbar-inner">
    	<div class="container">
        	<ul class="nav">
            	<li class="home {% if CURRENT_CONTROLLER == 'mainside' %}active{% endif %}"><a href="{{ path('frontend.home') }}"><span class="ico">{% trans %}TXT_MAINSIDE{% endtrans %}</span></a></li>
                <li class="divider-vertical"></li>
                {% if categories|length > 0 %}
                <li class="dropdown {% if CURRENT_CONTROLLER == 'categorylist' or CURRENT_CONTROLLER == 'productcart' %}active{% endif %}"><a href="{{ categories[0].link }}" class="dropdown-toggle">{% trans %}TXT_PRODUCTS{% endtrans %} <b class="caret"></b></a>
                	 <ul class="dropdown-menu">
						{% for category in categories %}
						<li><a href="{{ category.link }}">{{ category.label }}</a></li>
						{% endfor %}
                    </ul>
                </li>
                <li class="divider-vertical"></li>
                {% endif %}
                <li class="dropdown {% if CURRENT_CONTROLLER == 'productpromotion' %}active{% endif %}"><a href="{{ path('frontend.productpromotion') }}">{% trans %}TXT_PROMOTIONS{% endtrans %}</a></li>
                <li class="divider-vertical"></li>
                <li class="dropdown {% if CURRENT_CONTROLLER == 'productnews' %}active{% endif %}"><a href="{{ path('frontend.productnews') }}">{% trans %}TXT_NEW_PRODUCTS{% endtrans %}</a></li>
                {% for cat in contentcategory if cat.header == 1 %}
                <li class="divider-vertical"></li>
                <li class="dropdown">
                	<a href="{{ cat.link }}" class="dropdown-toggle">{{ cat.name }} <b class="caret"></b></a>
                    {% if cat.children is not empty %}
                    <ul class="dropdown-menu">
						{% for subcat in cat.children if subcat.header == 1 %}
						<li><a href="{{ subcat.link }}">{{ subcat.name }}</a></li>
						{% endfor %}
                    </ul>
                    {% endif %}
				</li>
                {% endfor %}
			</ul>
            <form id="product-search" class="navbar-search form-search pull-right" action="{{ path('frontend.productsearch') }}" method="post">
            	<div class="input-append">
                	<input id="product-search-phrase" type="text" class="search-query span2" placeholder="Szukaj produktu..."><button class="btn" type="submit"><i class="icon-search"></i></button>
				</div>
			</form>
		</div>
	</div>
</nav>