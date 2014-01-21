<!DOCTYPE html>
<html lang="{{ languageCode }}">
<head>
    {% include 'meta.tpl' %}
	<link rel="shortcut icon" href="{{ DESIGNPATH }}_images_frontend/core/logos/{{ FAVICON }}"/>
    <link rel="stylesheet" href="{{ css_asset('css/bootstrap.css') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/mixins.less') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/application.less') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ css_asset('css/smoothness/jquery-ui-1.8.21.custom.css') }}" type="text/css"/>
    <link rel="stylesheet/less" href="{{ css_asset('css/scheme.less') }}" type="text/css"/>
    <link rel="stylesheet" href="{{ css_asset('css/style.css') }}" type="text/css"/>

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- fav and touch icons -->
    <link rel="shortcut icon" href="ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-144-precomposed.png" sizes="144x144" >
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-114-precomposed.png" sizes="114x114">
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-72-precomposed.png" sizes="72x72">
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-57-precomposed.png">
    {% include 'javascript.tpl' %}
    <!--[if IE]>
    <link rel="stylesheet" href="{{ css_asset('css/ie.css') }}" type="text/css"/>
    <![endif]-->
    
	{{ analyticsjs }}

	<script type="text/javascript">
		new GCore({
			iCookieLifetime: 30,
			sDesignPath: '{{ DESIGNPATH }}',
			sAssetsPath: '{{ ASSETSPATH }}',
			sController: '{{ CURRENT_CONTROLLER }}',
			sCartRedirect: '{{ cartredirect }}'
		});

		$(document).ready(function(){
			$('#product-search').submit(function(){
				var query = Base64.encode($('#product-search-phrase').val());
				var url = '{{ path('frontend.productsearch') }}/' + query;
				window.location.href = url;
				return false;
			});
		});
	</script>
	{{ xajax }}
	{% if error is defined %}
	<script type="text/javascript">
		$(document).ready(function(){
			GError('{{ error }}');
		});
	</script>
	{% endif %}
</head>

<body>
    <div class="container">
    	{% block header %}{% endblock %}