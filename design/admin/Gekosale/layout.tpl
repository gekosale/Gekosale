<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
	<head>
		<title>{{ SHOP_NAME }} - Panel administracyjny</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta http-equiv="Author" content="Verison; http://verison.pl"/>
		<meta http-equiv="Description" content="Panel administracyjny systemu sklepowego Gekosale."/>
		<meta name="robots" content="noindex, nofollow"/>
		<link rel="shortcut icon" href="favicon.ico"/>
		{% block stylesheet %}
		<link rel="stylesheet" href="{{ DESIGNPATH }}_fonts_panel/fonts.css" type="text/css"/>
		<link rel="stylesheet" href="{{ DESIGNPATH }}_css_panel/core/style.css" type="text/css"/>
		<link rel="stylesheet" href="{{ DESIGNPATH }}_js_libs/redactor/redactor.css" type="text/css"/>
		{% endblock %}
		{% block javascript %}
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-1.4.2.min.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/xajax/xajax_core.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.jqplugin.1.0.2.min.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/swf.packed.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.scrollTo.min.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.cookie.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-ui-1.7.2.custom.min.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.checkboxes.pack.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/json2.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/base64.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.hotkeys.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.onkeyup.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gekosale.js?v={{ appVersion }}"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/jquery.hoverIntent.min.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/redactor/redactor.min.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/redactor/pl.js"></script>
		{% endblock %}
		<script type="text/javascript">
			new GCore({
				iCookieLifetime: 30,
				sDesignPath: '{{ DESIGNPATH }}',
				iActiveView: '{{ view }}',
				iActiveLanguage: '{{ language }}',
				aoLanguages: {{ languages }},
				aoVatValues: {{ vatvalues }},
				sUrl: '{{ URL }}',
				sCurrentController: '{{ CURRENT_CONTROLLER }}',
				sCurrentAction: '{{ CURRENT_ACTION }}',
			});
		</script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/init.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gf.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/pl_PL.js"></script>
		<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/hotkeys.js"></script>
		<script type="text/javascript">
			GF_Debug.s_iLevel = GF_Debug.LEVEL_ALL;
		</script>
		{{ xajax }}
		{% if error is defined %}
		<script type="text/javascript">
			GError('{% trans %}TXT_ERROR_OCCURED{% endtrans %}', '{{ error }}');
		</script>
		{% endif %}
		{% if message is defined %}
		<script type="text/javascript">
		$(document).ready(function(){
			GMessage('{{ message }}');
		});
		</script>
		{% endif %}
	</head>
	<body>
		<div id="header">
			<div class="layout-container">
				<h1><a href="{{ URL }}mainside" accesskey="0" title="{% trans %}TXT_RETURN_TO_DESKTOP{% endtrans %}"><img src="{{ DESIGNPATH }}_images_panel/logos/logo.png" alt=""/></a></h1>
				<div id="livesearch">
					<input type="text" name="search" id="search" placeholder="{% trans %}TXT_SEARCH{% endtrans %}" />
				</div>
				<div id="top-menu">
					<ul>
						<li>
							<a href="{{ URL }}users/edit/{{ user_id }}">{% if user_name == ' ' %}{% trans %}TXT_ADMIN_ACCOUNT{% endtrans %}{% else %}{{ user_name }}{% endif %}</a> (<a href="{{ URL }}logout">{% trans %}TXT_LOGOUT{% endtrans %}</a>)
						</li>
						<li>
							<a href="{{ URL }}instancemanager">Przedłuż abonament</a>
						</li>
						<li>
							<a href="{{ URL }}instancemanager/view/account">Dane abonenta</a>
						</li>
						<li>
							<a target="_blank" href="http://wellcommerce.pl/zasoby/">{% trans %}TXT_HELP{% endtrans %}</a>
						</li>
						<li>
							<a href="{{ FRONTEND_URL }}" target="_blank" >{% trans %}TXT_HOME_PAGE{% endtrans %}</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div id="navigation-bar">
			<div class="layout-container">
				<div id="selectors" style="float: right; margin-top: 8px;"></div>
				<ul id="navigation">
				{% for block in menu %}
					{% if block.elements is empty %}
					<li {% if block.link == CURRENT_CONTROLLER %}class="active"{% endif %}>
						<a href="{{ URL }}{{ block.link }}" id="menu-{{ block.icon }}"><span class="icon {{ block.icon }}"></span>{{ block.name }}</a>
					</li>
					{% else %}
					<li>
						<a href="{{ URL }}{{ block.elements[0].link }}" id="menu-{{ block.icon }}"><span class="icon {{ block.icon }}"></span>{{ block.name }}</a>
							<ul>
							{% for element in block.elements %}
								<li {% if element.link == CURRENT_CONTROLLER %}class="active"{% endif %}>
									<a href="{{ URL }}{{ element.link }}">{{ element.name }}</a>
									{% if element.subelement is not empty %}
										<ul>
										{% for subelement in element.subelement %}
											<li>
												<a href="{{ URL }}{{ subelement.link }}">{{ subelement.name }}</a>
											</li>	
										{% endfor %}
										</ul>
									{% endif %}
								</li>	
							{% endfor %}
							</ul>
					</li>
					{% endif %}
				{% endfor %}
				</ul>
				
				<div id="boxShop">
					<span class="icon"></span>
					<div class="field-select">
						<select name="view-switcher" onchange="xajax_ChangeActiveView(this.value);">
							{% for view in views %}
							<option value="{{ view.id }}" {% if view.active %}selected="selected"{% endif %}>{{ view.name }}</option>
							{% endfor %}
						</select>
					</div>
				</div>
				
			</div>
		</div>
		<div id="message-bar"></div>
		<div id="content" class="layout-container"><div id="debug"></div>{% block content %}{% endblock %}</div>
		

        {% block contextmenu %} {% include "contextmenu/index.tpl" %} {% endblock %} 
		{% block sticky %}{% endblock %}
		{% block wizard %} 
		{% include "wizard/index.tpl" %} 
		{% endblock %}
		{% if CURRENT_CONTROLLER != 'exchange' %}
		<script>
		$(document).ready(function(){

			if(window.location.hash.length && $('a[href="'+ window.location.hash +'"]').text() != ''){

				setTimeout(function() {
					$('a[href="'+ window.location.hash +'"]').click();
					var id = $('form #' + window.location.hash).find('input:first-child').attr('id');
					$('#' + id).focus();
				}, 1000);
				
				
			}else{
				setTimeout(function() {
					var id = $('form').find('input[type="text"]:first-child').attr('id');
					$('#' + id).focus();
				}, 1000);
			}
			
			
			
		});
		</script>
		{% endif %}
	</body>
</html>