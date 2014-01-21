<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="pl"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="pl"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="pl"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="pl"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->
    <meta name="description" content="">
    <title>{{ SHOP_NAME }} :: porównywarka</title>
    <link rel="stylesheet" href="{{ css_asset('css/bootstrap.css') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/mixins.less') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/application.less') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/scheme.less') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ css_asset('css/compare.css') }}" type="text/css"/>
    {% include 'javascript.tpl' %}
</head>
<body class="page-comparison">

    <!-- header *********************************************************** -->
    <header id="header" class="header">
        <div class="wrapper clearfix">

             <h1><a href="{{ path('frontend.home') }}" title="{{ SHOP_NAME }}">{{ SHOP_NAME }}</a></h1>
            <nav class="menu-header clearfix right">
                <a class="link" href="#" onclick="xajax_deleteAllProductsFromCompare();">Usuń wszystkie</a>
                <a class="link" href="#" onclick="window.print();">Drukuj porównanie</a>
                <a class="link" href="{{ path('frontend.home') }}">Wróć do sklepu</a>
            </nav>

        </div>
    </header>
    <div id="main">
        <div class="wrapper clearfix">

            <table class="products table-comparison">
                <thead>
                    <tr>
                        <th>&nbsp;</th>
                        {% for item in products %}
                        <th>
                            <p class="title">{{ item.name }} <span class="close" onclick="xajax_deleteProductFromCompare({{ item.id }});"></span></p>
                            <figure>
                                <a href="{{ path('frontend.productcart', {"param": item.seo}) }}" title="">
                                    <img src="{{ item.photo }}" />
                                </a>
                            </figure>
                            <div id="addToCart" class="padding10">
	                        	{% if ( item.discountprice != NULL and item.discountprice != item.price ) %}
			        			<span class="price price-large padding10" id="changeprice" itemprop="price">{% if showtax == 0 %}{{ item.discountpricenetto|priceFormat }}{% else %}{{ item.discountprice|priceFormat }}{% endif %}</span>
			                	<span class="price price-small padding10" id="changeprice-old">{% if showtax == 0 %}{{ item.pricenetto|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</span>
								{% else %}
								<span class="price price-large padding10" id="changeprice" itemprop="price">{% if showtax == 0 %}{{ item.pricewithoutvat|priceFormat }}{% else %}{{ item.price|priceFormat }}{% endif %}</span>
								{% endif %}
	                            <a class="btn btn-primary btn-large" href="{{ path('frontend.productcart', {"param": item.seo}) }}" onclick="xajax_doQuickAddCart({{ item.id }});return false;" title="">Dodaj do koszyka</a>
                            </div>
                        </th>
                        {% endfor %}
                    </tr>
                    <tr>
                        <th>&nbsp;</th>
                        {% for item in products %}
                        <th>
		    				<div class="rate star pull-left readonly" itemprop="ratingValue" data-rating="{{ item.rating }}">{{ item.rating }}</div>
                        </th>
                        {% endfor %}
                    </tr>
                </thead>
                <tbody>
                {% for section, attributes in attributesTree %}
                    <tr class="tr-head"><td colspan="5">{{ section }}</td></tr>
                    {% for name, values in attributes %}
                    <tr>
                    	<th>{{ name }}</th>  
                    	{% for product in products %}
                    	<td>{{ values[product.id] }}</td>           
                    	{% endfor %}         
                    </tr>
                    {% endfor %}
				{% endfor %}
                </tbody>
            </table>

        </div>
    </div>
	<div id="basketModal" class="modal fade hide"></div>
    <footer id="footer">
        <div class="wrapper">

            <div class="copyrights clearfix">
                <p class="left">{{ "now"|date("Y") }} © <span>{{ SHOP_NAME }}</span> / {% trans %}TXT_ALL_RIGHT_RESERVER{% endtrans %}.</p>
                <p class="right"> <a href="http://wellcommerce.pl/" title="Sklep internetowy WellCommerce"><img src="{{ DESIGNPATH }}_images_common/logos/wellcommerce_footer.png" title="Sklep internetowy WellCommerce" /></a></p>
            </div>

        </div>
    </footer>
</body>
</html>