$(document).ready(function() {

	$.datepicker.regional['pl'] = GFormDate.Language;
	$.datepicker.setDefaults($.datepicker.regional['pl']);
	$('.block').GBlock();
	$('.box').GBox();
	$('select').GSelect();
	$('#search').GSearch(); 
	$('#message-bar').GMessageBar();
	$('.simple-stats .tabs').tabs({fx: {opacity: 'toggle',duration: 75}});

	if (!$.browser.flash){
		GAlert('Brak obsługi Flash', 'Twoja przeglądarka nie obsługuje technologii Flash. Jest ona wymagana do prawidłowego działania systemu.')
	}
	
	$('.scrollable-tabs').GScrollableTabs();
	GCore.Init();	
	$('.order-notes').tabs();
	$('.sticky-progress').GSticky();
	$('#navigation > li > ul > li > ul > li.active').parent().parent().parent().parent().addClass('active');
	$('#navigation > li > ul > li.active').parent().parent().addClass('active');
	$('#navigation > li > ul > li.active').parent().addClass('active');
	
	
	// Ukrywanie i pokazywanie odnosnika "Zwin wszystkie"
	// -------------------------------------------------------------------------
	$('.expand-all a').each(function() {
		var $this = $(this);
		
		if ( $this.css('visibility') == 'hidden' ) {
			$this.parent().hide();
		}
		else {
			$this.parent().show();
		}
	});
	
	$('.expand-all a').mouseup(function() {
		var $this = $(this);
		
		if ( $this.parent().next('.tree-wrapper').find('expanded').length == 0 ) {
			$this.parent().hide();
		}
		else {
			$this.parent().show();
		}
	});
	
	$('.tree-wrapper .expander').live('click', function() {
		var $this = $(this).parents('.tree-wrapper').prev('.expand-all').children();
		
		if ( $this.css('visibility') == 'hidden' ) {
			$this.parent().hide();
		}
		else {
			$this.parent().show();
		}
	});
	
	
	// Opoznienie dla dropdown menu glownego
	// -------------------------------------------------------------------------
	$('#navigation li').hoverIntent({
		interval: 100,
		over: function() {
			$('> ul', this).addClass('down').slideDown();
		},
		timeout: 300,
		out: function() {
			$('> ul', this).removeClass('down').slideUp();
		}
	});

	
});
