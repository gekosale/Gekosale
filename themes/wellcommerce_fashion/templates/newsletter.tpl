<div class="newsletter">
	<h3 class="font">Biuletyn okazji {{ SHOP_NAME }}</h3>
	<p>{% trans %}TXT_NEWSLETTER_INFO{% endtrans %}</p>
	<form class="form-inline" id="signup-newsletter" action="">
		<div class="control-group">
			<div class="input-prepend input-append">
				<span class="add-on"><i class="icon-envelope"></i></span><input type="text" class="span3" placeholder="{% trans %}TXT_EMAIL{% endtrans %}" id="email-newsletter" name="email-newsletter"><button class="btn" type="submit">{% trans %}TXT_SIGNUP{% endtrans %}</button>
			</div>
		</div>
		<div class="control-group">
			<label class="checkbox">
				<input type="checkbox"	id="privacy-newsletter" name="privacy-newsletter" value="1">&nbsp;{% trans %}TXT_NEWSLETTER_ACCEPT{% endtrans %} <a href="{{ path('frontend.conditions') }}" target="_blank">{% trans %}TXT_NEWSLETTER_CONDITIONS{% endtrans %}</a>
			</label>
		</div>
		<div class="control-group green">
			<span class="green help-block" id="success"></span>
		</div>
		<div class="control-group error">
			<span class="error help-block" id="error"></span>
		</div>
		<div class="control-group" id="errors"></div>
	</form>
</div>
<script type="text/javascript">
GCore.OnLoad(function() {
	$('#privacy-newsletter').click(function(){
		$('#success').text('');
		$('#error').text('');
	});

	$('#signup-newsletter').validate({
		errorLabelContainer: $("#errors"),
		errorElement: 'span',
		errorClass: 'error',
		wrapClass: 'help-block',
		rules: {
			"email-newsletter":{
				"required":true,
				"email":true
			},
			"privacy-newsletter":{
				"required":true
			}
		},
		messages: {
			"email-newsletter":{
				"required":"{% trans %}ERR_EMPTY_EMAIL{% endtrans %}",
				"email": "{% trans %}ERR_WRONG_EMAIL{% endtrans %}"
			},
			"privacy-newsletter":{
				"required":"{% trans %}TXT_NEWSLETTER_ACCEPT_CONDITIONS{% endtrans %}"
			}
		},
		highlight: function(label) {
			$('#errors').addClass('error');
		},
		submitHandler: function(form) {
			$('#success').text('');
			$('#error').text('');
			xajax_addNewsletter($('#email-newsletter').val(), $('#privacy-newsletter').is(':checked'));
		}

	});
});
</script>
