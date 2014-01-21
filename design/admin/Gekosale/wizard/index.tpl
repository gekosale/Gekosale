{% if wizard_disabled == false %}
<div class="wizard-pane position-fixed" id="{{ stickyid }}">
	<div id="steps-bar">
		<ul class="steps">
			<li class="products step1"><a href="{{ URL }}product#step1">Produkty</a></li>
			<li class="template step2"><a href="{{ URL }}pagescheme#step2">Styl graficzny</a></li>
			<li class="cms step3"><a href="{{ URL }}contentcategory#step3">Treść</a></li>
			<li class="payment step4"><a href="{{ URL }}paymentmethod#step4">Płatności</a></li>
			<li class="shipping step5"><a href="{{ URL }}dispatchmethod#step5">Dostawa</a></li>
			<li class="domain step6 last"><a href="{{ URL }}view/edit/{{ viewid }}#url_pane">Domena</a></li>
		</ul>
	</div>
	
	<ul class="tools">
		<li><a class="ac-close" href="{{ URL }}wizard/close">Zamknij przewodnik</a></li>
	</ul>
</div>

<script type="text/javascript">
	for(var i = {{ wizard_step|default(0) }}; i <= 6; i++) {
		
		$('.steps li.step'+i).addClass('active');
	
	}
	
/*	if(location.hash == '#step1') { 
		$('#navigation li:eq(1) ul').slideDown(150);
	
	}*/
	
	$('.sticky-progress').css('bottom', '30px');
</script>
{% endif %}