$(document).ready(function() {
	$('.block').GBlock();
	$('.box').GBox();
	$('select').GSelect();
	$('#message-bar').GMessageBar();
	$('.simple-stats .tabs').tabs({fx: {opacity: 'toggle',duration: 75}});

	$('.scrollable-tabs').GScrollableTabs();
	GCore.Init();	
	$('.order-notes').tabs();
	$('.sticky-progress').GSticky();
	$('#navigation > li > ul > li > ul > li.active').parent().parent().parent().parent().addClass('active');
	$('#navigation > li > ul > li.active').parent().parent().addClass('active');
	$('#navigation > li > ul > li.active').parent().addClass('active');

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
