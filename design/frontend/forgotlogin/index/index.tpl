<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
	<head>

		<!-- begin: Meta information -->
			<title>{{ SHOP_NAME }} Admin</title>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta http-equiv="Author" content="Verison; http://verison.pl"/>
			<meta http-equiv="Description" content="Panel administracyjny systemu sklepowego Gekosale."/>
			<meta name="robots" content="noindex, nofollow"/>
			<link rel="shortcut icon" href="favicon.ico"/>
			<link rel="stylesheet" href="{{ DESIGNPATH }}_css_panel/core/style.css" type="text/css"/>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-1.4.2.min.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/xajax/xajax_core.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.jqplugin.1.0.2.min.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/swf.packed.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.cookie.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-ui-1.7.2.custom.min.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.checkboxes.pack.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/json2.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/base64.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.hotkeys.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.onkeyup.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gekosale.js?v={{ appVersion }}"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/jquery.hoverIntent.min.js"></script>
			<script type="text/javascript">
				
					/*<![CDATA[*/
						new GCore({
							iCookieLifetime: 30,
							sDesignPath: '{{ DESIGNPATH }}',
							iActiveLanguage: '{{ language }}',
							iActiveView: '0',
							aoLanguages: {{ languages }},
							sUrl: '{{ URL }}',
							sCurrentController: '{{ CURRENT_CONTROLLER }}',
							sCurrentAction: '{{ CURRENT_ACTION }}',
						});
					/*]]>*/
				
			</script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/init.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gf.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/pl_PL.js"></script>
			
			<script type="text/javascript">
				GF_Debug.s_iLevel = GF_Debug.LEVEL_ALL;
			</script>
		<!-- end: GexoFramework -->
		
		{{ xajax }}
		
		{% if error is defined %}
			<script type="text/javascript">
				
				$(document).ready(function(){
					GError('{% trans %}TXT_ERROR_OCCURED{% endtrans %}', '{{ error }}');
				});
				
			</script>
		{% endif %}

		<script type="text/javascript">
		
			$(document).ready(function(){
				$("input[name='login']").focus();
			});
		
		</script>
	</head>
	<body class="welcome-screen">

		<!-- begin: Header -->
			<div id="header">
				
				<div class="layout-container">
					
					<h1><a href="{{ URL }}mainside" accesskey="0" title="{% trans %}TXT_RETURN_TO_DESKTOP{% endtrans %}"><img src="{{ DESIGNPATH }}_images_panel/logos/logo.png"></a></h1>
					
					<!-- begin: Shop name -->
						<div id="top-message">
							<p><a href="{{ URL }}">{{ SHOP_NAME }}</a></p>
						</div>
					<!-- end: Shop name -->
					
				</div>
				
			</div>
		<!-- end: Header -->

		<!-- begin: Message bar -->
			<div id="message-bar">

				<h2 class="aural">Wiadomości</h2>

			</div>
		<!-- end: Message bar -->

		<!-- begin: Content -->
			<div id="content" class="layout-container">
				
				{{ form }}

			</div>
		<!-- end: Content -->

	</body>
</html>
