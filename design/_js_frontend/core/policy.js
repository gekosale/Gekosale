/*
* PRIVACY BAR
* GPrivacyBar
*/

var oDefaults = {
	oClasses: {},
	sPrivacyCookieName: 'privacy-bar-closed',
	iDefaultLifetime: 30,
	sUrl: '',
	sClose: 'Zamknij',
	sText1: 'Strona korzysta z plików cookie w celu realizacji usług zgodnie z <a href="{{ url }}" title="Polityka prywatności">Polityką prywatności</a>',
	sText2: 'Możesz określić warunki przechowywania lub dostępu do cookie w Twojej przeglądarce lub konfiguracji usługi.'
};

var GPrivacyBar = function() {
	
	var gThis = this;
	gThis.m_jBar;
	gThis.m_jCloseButton;
	gThis.m_jWrapper;
	
	gThis._Constructor = function() {
		if(GCookie(gThis.m_oOptions.sPrivacyCookieName)){
			return;
		}
		gThis.m_jBar = $('<div id="cookiesBar"/>');
		gThis.m_jCloseButton = $('<a id="cookiesBarClose" href="#" title="' + gThis.m_oOptions.sClose + '">' + gThis.m_oOptions.sClose + '</a>').click(gThis.OnClose);
		gThis.m_jWrapper = $('<div id="cookiesBarWrap"/>');
		
		gThis.m_jWrapper.append('<p>' + gThis.m_oOptions.sText1.replace("{{ url }}", gThis.m_oOptions.sUrl) + '</p>');

		gThis.m_jWrapper.append('<p>' + gThis.m_oOptions.sText2 + '</p>');
		
		gThis.m_jWrapper.append(gThis.m_jCloseButton);
		gThis.m_jBar.append(gThis.m_jWrapper);
		$(gThis).append(gThis.m_jBar);
	};
	
	gThis.OnClose = function(eEvent) {
		gThis.m_jBar.fadeOut();
		GCookie(gThis.m_oOptions.sPrivacyCookieName, 1, {
			expires: gThis.m_oOptions.iDefaultLifetime
		});
		eEvent.preventDefault();
	};
	
	gThis._Constructor();
	
};

new GPlugin('GPrivacyBar', oDefaults, GPrivacyBar);