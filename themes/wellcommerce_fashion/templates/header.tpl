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
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-144-precomposed.png" sizes="144x144" >
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-114-precomposed.png" sizes="114x114">
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-72-precomposed.png" sizes="72x72">
    <link rel="apple-touch-icon-precomposed" href="{{ ASSETSPATH }}ico/apple-touch-icon-57-precomposed.png">
	<!--[if IE]>
    <link rel="stylesheet" href="{{ css_asset('css/ie.css') }}" type="text/css"/>
    <![endif]-->
    {% include 'javascript.tpl' %}
</head>

<body>
	<div id="fb-root"></div>
    <div class="container">
	{% if newsletterButton %}
    	<a id="btn_rightNewsletter" rel="#o_newsletter" href="{{ path('frontend.newsletter') }}" title="Newsletter"><sub>Newsletter</sub></a>
	{% endif %}
        <header class="header">
            <div class="row">
                <div class="span5">
                    <h1><a href="{{ path('frontend.home') }}" title="{{ SHOP_NAME }}">{{ SHOP_NAME }}</a></h1>
                </div>
                <div class="span7">
                    <div class="row pull-right">
                    	{% if currencies|length > 1 %}
                        <div class="span2">
                            <form class="form-inline nomargin pull-right">
                                <fieldset>
                                    <label class="control-label" for="waluta">{% trans %}TXT_KIND_OF_CURRENCY{% endtrans %}:</label>
                                    <select class="span1" id="waluta" onchange="xajax_changeCurrency(this.value);">
                                    {% for currency in currencies %}
					 					<option value="{{ currency.id }}" {% if currency.selected == 1 %}selected="selected"{% endif %}>{{ currency.name }}</option>
									{% endfor %}
                                    </select>
                                </fieldset>
                            </form>
                        </div>
                        {% endif %}
                        {% if languageFlag|length > 1 %}
                        <div class="span2">
                            <div class="change">
                                <span class="lang">{% trans %}TXT_LANGUAGE{% endtrans %}:</span>
                                {% for language in languageFlag %}
                                <a href="#" onclick="xajax_changeLanguage({{ language.id }});" title="{{ language.name }}" class="lang {% if language.active == 1 %}active{% endif %}"><img src="{{ DESIGNPATH }}_images_common/icons/languages/{{ language.icon }}" alt="{{ language.name }}"></a>
                                {% endfor %}
                            </div>
                        </div>
                        {% endif %}
                        <div class="span3 nomargin">
                            <ul class="nav nav-pills nomargin pull-right">
                            {% if clientdata is not empty %}
                            	<li><a class="dropdown-toggle" href="{{ path('frontend.clientorder') }}" title="">{% if client.firstname is defined %}{{ client.firstname }} {{ client.surname }}{% else %}{{ clientdata.firstname }} {{ clientdata.surname }}{% endif %}</a></li>
                                <li><a href="{{ path('frontend.logout') }}" title="{% trans %}TXT_LOGOUT{% endtrans %}">{% trans %}TXT_LOGOUT{% endtrans %}</a></li>
                            {% else %}
                                <li id="loginTop">
                                    <a class="dropdown-toggle" href="{{ path('frontend.clientlogin') }}" title="">{% trans %}TXT_LOGIN_PROCESS{% endtrans %} <b class="caret"></b></a>
                                    <div id="loginTopContent">
                                        <h4 class="font">{% trans %}TXT_REGISTRATION{% endtrans %}</h4>
                                        {% include 'quicklogin.tpl' %}
                                    </div>
                                </li>
                                <li><a href="{{ path('frontend.registration') }}" title="">{% trans %}TXT_REGISTRATION{% endtrans %}</a></li>
                                {% endif %}
                            </ul>
                        </div>
                    </div>
                    <div class="row pull-right">
                    	{% if defaultcontact is not empty %}
                        <div class="span3 contact nomargin"><a href="{{ path('frontend.contact') }}" title="{{ defaultcontact.name }}" class="email">{{ defaultcontact.email }}</a></div>
                        <div class="span2 phone nomargin">
                            <h3 class="font">{{ defaultcontact.phone }}</h3>
                            {% if defaultcontact.businesshours != '' %}<span>{{ defaultcontact.businesshours }}</span>{% endif %}
                        </div>
                        {% endif %}
                        <div class="span3 nomargin pull-right">
                            <div id="topBasket">
							{{ cartpreview }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {% include 'navbar.tpl' %}
		{% include 'breadcrumb.tpl' %}
