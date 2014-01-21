{% extends "layoutbox.tpl" %}
{% block content %}
<div class="vertical-block category-filter">
	<div class="block-head">{{ box.heading }}</div>
    <div class="block-content">
	{% if poll.idpoll is defined %}
		{% if check == 0 %}
			{% if poll.questions is not empty %}
				<h4 class="marginbt10">{{ poll.questions }}</h4>
				<form action="#" method="post" id="poll-{{ poll.idpoll }}">
					<fieldset>
						<div class="control-group">
							<div class="controls">
							{% for answer in poll.answers %}
								<label class="radio marginbt10"> 
									<input type="radio" name="poll" id="poll[{{ answer.votes }}]" value="1"/> {{ answer.name }}
								</label> 
							{% endfor %}	
							</div>
						</div>
						{% if clientdata is not empty %}
						<div class="form-actions form-actions-clean aligncenter">
				        	<button class="btn btn-primary">{% trans %}TXT_SEND{% endtrans %}</button>
				        </div>
				        <div id="pool-span"></div>
						{% else %}
							<p><a href="{{ path('frontend.clientlogin') }}">{% trans %}TXT_LOG_IN_TO_VOTE{% endtrans %}</a></p>
						{% endif %}
					</fieldset>
				</form>
				
				<script type="text/javascript">
					
						/*<![CDATA[*/
							
							var submitPollAnswer = GEventHandler(function(eEvent) {
								var jSelected = $(this).find('input[type=radio]:checked');
								if (!jSelected.length) {
									alert('Najpierw wybierz odpowiedź!');
									eEvent.stopImmediatePropagation();
									return false;
								}
								var aMatches = jSelected.attr('id').match(/poll\[([^\]]+)\]/);
								xajax_setAnswersChecked(aMatches[1], '{{ poll.idpoll }}');
								$(this).find('[type=submit], ul').fadeOut(150);
								$(this).find('.answers').fadeIn(150);
								eEvent.stopImmediatePropagation();
								return false;
							});
							
							GCore.OnLoad(function() {
								$('#poll-{{ poll.idpoll }}').unbind('submit', submitPollAnswer).bind('submit', submitPollAnswer);
							});
							
						/*]]>*/
					
				</script>
			{% endif %}
		{% else %}
			<h4>{{ poll.questions }}</h4>
			{% for answer in answers %}
				<p>{{ answer.name }}</p>
				<div class="progress progress-striped">
		  			<div class="bar" style="width: {{ answer.qty.percentage }}%;"></div>
				</div>
			{% endfor %}
		{% endif %}
	{% else %}
		<p>{% trans %}TXT_POLL_DOES_NOT_EXIST{% endtrans %}</p>
	{% endif %}	
	</div>
</div>
{% endblock %}