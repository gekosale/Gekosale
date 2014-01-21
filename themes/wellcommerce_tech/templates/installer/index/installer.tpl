<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl">
	<head>

		<!-- begin: Meta information -->
			<title>Gekosale Install</title>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<meta http-equiv="Author" content="Verison; http://www.gekosale.pl"/>
			<meta name="robots" content="noindex, nofollow"/>
			<link rel="shortcut icon" href="favicon.ico"/>
		<!-- end: Meta information -->

		<!-- begin: Stylesheet -->
			<link rel="stylesheet" href="{{ DESIGNPATH }}_css_panel/core/style.css" type="text/css"/>
		<!-- end: Stylesheet -->

		<!-- begin: JS libraries and scripts inclusion -->
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-1.4.2.min.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/xajax/xajax_core.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery-ui-1.7.2.custom.min.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.easing.1.3.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.scrollTo.min.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.cookie.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.dimensions.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.gradient.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.checkboxes.pack.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.resize.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/swfobject.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.swfobject.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/colorpicker.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/swfupload.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/swfupload.queue.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.swfupload.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/json2.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/base64.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/jquery.rightClick.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_libs/daterangepicker/js/daterangepicker.jQuery.js"></script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gekosale.js"></script>
			<script type="text/javascript">
				
					/*<![CDATA[*/
						new GCore({
							iCookieLifetime: 30,
							sDesignPath: '{{ DESIGNPATH }}',
							sUrl: '',
							sCurrentController: '',
							sCurrentAction: '',
						});
						$(document).ready(function() {

							$('.block').GBlock();
							$('.box').GBox();
							$('select').GSelect();
							
							$('#message-bar').GMessageBar();
							
							GCore.Init();	
							$('.installButton').unbind('click').bind('click',function(){
								$('#install').submit();
								if($('.warning').length){

								}else{
									$('#loading').GLoading();
								}
								return false;
							});
						});
					/*]]>*/
						GClientActivity.Language = {

								add_to_order: 'Dodaj do zamówienia'

							};

			 

							GFormDate.Language = {
								closeText: 'Zamknij',
								prevText: '&#x3c;Poprzedni',
								nextText: 'Następny&#x3e;',
								currentText: 'Dziś',
								monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
								'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
								monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
								'Lip','Sie','Wrz','Pa','Lis','Gru'],
								dayNames: ['Niedziela','Poniedziałek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
								dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
								dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
								dateFormat: 'yy-m-d', firstDay: 1,
								isRTL: false
							};

			 

							GException.Language = {
								exception_has_occured: 'Wystąpił błąd!'
							};

							GForm.Language = {
								form_data_invalid: 'Nie można wysłać formularza, ponieważ zawiera on niepoprawne informacje. Przed zapisaniem zmian należy je poprawić.',
								scroll_to_field: 'Przejdź do pola',
								close_alert: 'Zamknij alert',
								next: 'Dalej',
								previous: 'Wstecz',
								save: 'Zapisz',
							};

							GMessageBar.Language = {
								close_alert: 'OK',
								cancel: 'Anuluj',
								ok: 'OK',
								add: 'OK',
								yes: 'Tak',
								no: 'Nie'
							};

			 



				
			</script>
			<script type="text/javascript" src="{{ DESIGNPATH }}_js_panel/core/gf.js"></script>
			
			<script type="text/javascript">
				GF_Debug.s_iLevel = GF_Debug.LEVEL_ALL;
			</script>
		<!-- end: GexoFramework -->
		<style>
		
			#header {
			  min-height: 116px;
			  margin: 0px 0 0;
				position: relative;
			  background: #2a2b2c url({{ DESIGNPATH }}_images_installer/backgrounds/header.png) 30px 0 repeat-x;
			  color: #f3f3f3;
				z-index: 10;
			}
			
			#header:after {
			  content: '.'; 
			  display: block; 
			  height: 0; 
			  clear: both; 
			  visibility: hidden;
			}
			
			#header h1 {
			  display: inline;
			  padding: 13px 0;
			  line-height: 90px;
			  float: left;
			}
			
			#header h1 a {
			  display: block;
			  width: 650px;
			  height: 90px;
			  background: url({{ DESIGNPATH }}_images_installer/gekosale.png) 0 0 no-repeat;
			  color: #f3f3f3;
			  text-indent: -10000px;
			}
			
			.container {
				width: 966px;
				margin: auto;
			}
			
			#requirements .navigation,
			#installation .navigation {
				display: none;
			}
			
			.installButton {
				-moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
				-webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
				box-shadow:inset 0px 1px 0px 0px #ffffff;
				background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf) );
				background:-moz-linear-gradient( center top, #ededed 5%, #dfdfdf 100% );
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf');
				background-color:#ededed;
				-moz-border-radius:6px;
				-webkit-border-radius:6px;
				border-radius:6px;
				border:1px solid #dcdcdc;
				display:inline-block;
				color:#777777 !important;
				font-family:arial;
				font-size:18px;
				font-weight:bold;
				padding:12px 24px;
				text-decoration:none;
				text-shadow:1px 1px 0px #ffffff;
			}
			.installButton:hover {
				background:-webkit-gradient( linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed) );
				background:-moz-linear-gradient( center top, #dfdfdf 5%, #ededed 100% );
				filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed');
				background-color:#dfdfdf;
			}
			.installButton:active {
				position:relative;
				top:1px;
			}
			#installation .field-static-text {
				text-align: center;
				padding-top: 30px;
			}
		
		</style>
		{% if error is defined %}
			<script type="text/javascript">
				
					$(document).ready(function() {
						GMessage('TXT_ERROR_OCCURED', '{{ error }}');
					});
				
			</script>
		{% endif %}
	</head>
	<body class="welcome-screen">

		<div id="header">
			<div class="container">
				<h1><a href="http://www.gekosale.pl" target="_blank">Gekosale - Open-source e-commerce platform</a></h1>
			</div>
		</div>

		<!-- begin: Message bar -->
			<div id="message-bar">
				<h2 class="aural"></h2>
			</div>
		<!-- end: Message bar -->

		<!-- begin: Content -->
			<div id="content" class="layout-container">
				<div id="loading">
				{{ form }}
				</div>
			</div>
		<!-- end: Content -->

	</body>
</html>
