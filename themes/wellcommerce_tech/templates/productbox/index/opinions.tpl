{% if opinionadded is defined %}
<div class="alert scroll alert-success scroll">
	<strong>{{ opinionadded }}</strong>
</div>
{% endif %}
<div class="well well-small product-details review">
	<h2>{% trans %}TXT_OPINION{% endtrans %}</h2>
    <div class="alert alert-block alert-success" id="review-alert">
    	<a href="#review" title="" class="btn btn-primary btn-large pull-right" data-toggle="collapse" data-target="#review"><i class="icon-inbox icon-white"></i> {% trans %}TXT_ADD_OPINION{% endtrans %}</a>
        <p><strong>Pomóż podjąć decyzję innym - wystaw ocenę i komentarz</strong></p>
        <p>Twoja ocena pomoże innym podjąć decyzję o wyborze odpowiedniego produktu.</p>
	</div>

    <form class="form-horizontal collapse" id="review" method="get" action="#">
    	<fieldset>
    		{% for r in range %}
        	<div class="control-group nomarginbt">
            	<label class="control-label">{{ r.name }}:</label>
                <div class="controls"><div class="star" data-rating="0" data-target="#rating-{{ r.id }}" style="cursor: pointer; width: 100px; "></div></div>
                <input type="hidden" name="{{ r.id }}" id="rating-{{ r.id }}">
            </div>
			{% endfor %}
            <div class="control-group nomarginbt">
            	<label class="control-label">Co sądzisz o produkcie?</label>
                <div class="controls">
                	<textarea class="span8" id="htmlopinion" name="htmlopinion" rows="4"></textarea>
               	</div>
            </div>
            <div class="hr"></div>
            <div class="control-group">
            	<label class="control-label">Wpisz imię/nick</label>
                <div class="controls">
                	<div class="input-inline"><input class="span12" type="text" name="nick" id="nick" value="{{ clientdata.firstname }}"></div>
                	{% if clientdata is empty %}
                	<span class="help-inline">lub <a href="{{ path('frontend.clientlogin') }}" title="">{% trans %}TXT_LOGIN{% endtrans %}</a> / <a href="{{ path('frontend.registration') }}" title="">{% trans %}TXT_REGISTER{% endtrans %}</a></span>
                	{% endif %}
                </div>
            </div>
            <div class="control-group error" id="review-errors" style="padding-left: 48px;"></div>
            <div class="form-actions form-actions-clean">
            	<button class="btn btn-primary btn-large"><i class="icon-inbox icon-white"></i> {% trans %}TXT_ADD_OPINION{% endtrans %}</button>
            </div>
		</fieldset>
	</form>
	{% if productreview|length > 0 %}
    <h3>{{ humanProductReviewCount }}</h3>
	{% autoescape true %}
    {% for opinion in productreview %}
	<div class="comment">
		<div class="rate-block">
			{% for range in opinion.ranges %}
			<h4>{{ range.name }}</h4>
			<div class="star readonly" data-rating="{{range.value }}" style="cursor: pointer; width: 100px; "></div>
			{% endfor %}
			<h3 class="nick">{{ opinion.nick }}</h4>
			<span class="date">{{ opinion.adddate }}</span>
		</div>
		<div class="text-block">
			{{ opinion.review }}
		</div>
		<div class="clearfix"></div>
	</div>
	{% endfor %}
	{% endautoescape %}
	{% endif %}
</div>

<script>
$(document).ready(function(){
	$('#review').on('show', function () {
		$('#review-alert').alert('close');
	});

	if($('.scroll').length != 0){
		$.scrollTo($('.scroll'));
	}

	$('#review').validate({
		errorLabelContainer: $("#review-errors"),
		errorElement: 'span',
		errorClass: 'error',
		wrapClass: 'help-block',
		rules: {
			"nick":{
				"required":true,
			},
			"htmlopinion":{
				"required":true,
			},
		},
		messages: {
			"nick":{
				"required":"{% trans %}ERR_OPINION_NICK_REQUIRED{% endtrans %}",
			},
			"htmlopinion":{
				"required":"{% trans %}ERR_FILL_AN_OPINION{% endtrans %}",
			},
		},
		highlight: function(label) {
			$('#errors').addClass('error');
		},
		submitHandler: function(form) {
			var params = {};
			var form = $('form#review').serializeArray();
			$.each(form, function(index,value) {
				params[value.name] = value.value;
			});
			return xajax_addOpinion({{ product.idproduct }}, params);
		}
	});
});
</script>