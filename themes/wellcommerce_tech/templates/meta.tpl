<meta charset="utf-8">
<title>{% if metadata.keyword_title != '' %}{{ metadata.keyword_title }}{% endif %}{% if CURRENT_CONTROLLER != 'mainside' %} - {{ SHOP_NAME }}{% endif %}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="author" content="WellCommerce; http://www.wellcommerce.pl"/>
<meta name="description" content="{{ metadata.keyword_description }}"/>
<meta name="keywords" content="{{ metadata.keyword }}"/>
<meta name="revisit-after" content="1 Day" />
<meta http-equiv="content-language" content="{{ languageCode }}"/>
<meta http-equiv="X-UA-Compatible" content="IE=9" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{% if CURRENT_CONTROLLER == 'productcart' %}
<meta property="og:title" content="{% if metadata.keyword_title != '' %}{{ metadata.keyword_title }}{% endif %}{% if CURRENT_CONTROLLER != 'mainside' %} - {{ SHOP_NAME }}{% endif %}"/>
<meta property="og:url" content="{{ path('frontend.productcart', {"param": product.seo}) }}"/>
<meta property="og:image" content="{{ product.mainphoto.normal }}"/>
<meta property="og:type" content="product"/>
<meta property="og:site_name" content="{{ SHOP_NAME }}"/>
{% endif %}
{{ googleappstag }}
{{ additionalmeta }}