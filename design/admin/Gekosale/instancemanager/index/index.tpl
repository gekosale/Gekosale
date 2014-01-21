{% extends "layout.tpl" %}
{% block content %}
<script>
$(document).ready(function(){
	$('#invoice').find('.with-image').val('Zapłać teraz');
	var GetSummary = function(oData) {
		var gField = oData.gForm.GetField(oData.sFieldTarget);
		if (gField != undefined) {
			xajax_GetSummary({
				id: oData.sValue
			}, GCallback(function(eEvent) {
				var aoValues = [];
				for (var j in eEvent.data) {
					aoValues.push({
						sCaption: eEvent.data[j][0],
						sValue: eEvent.data[j][1]
					});
				}
				gField.ChangeItems(aoValues, eEvent.title);
			}));
		}
	};
});
</script>
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/configuration-list.png" alt=""/>{% trans %}TXT_INSTANCE_MANAGER{% endtrans %}</h2>
<div class="block">
	<h2>{{ client.domainname }}</h2>
	<ul class="instancemanager">
		<li>
			<strong>Uruchomiony:</strong> <span style="font-weight: 400;">{{ client.adddate }}</span>
		</li>
		<li>
			<strong>Następna płatność: <span style="font-weight: 400;">{{ client.billedfrom }} (<strong>{{ daysremaining }} dni</strong>)</span>
		</li>
		<li>
			<strong>Konto zweryfikowane: {% if client.verified == 1 %}<span style="color: #13aeb8;font-weight: 700;">Tak</span>{% else %}<span style="color: #C00A31;font-weight: 700;">Nie</span> (<a href="{{ URL }}{{ CURRENT_CONTROLLER }}/view/account">Zweryfikuj dane</a>){% endif %}<br />
		</li>
	</ul>
	<br />
	<br />
</div>
<div class="block">
	<h2>Przedłużenie usługi</h2>
	{{ payForm }}
</div>
<div class="block">
	<h2>Faktury</h2>
	<div class="invoice-list">
		<table cellpadding="0" cellspacing="0">
			<thead>
	   			<tr>
	     			<th>Numer</th>
	      			<th>Opis</th>
	      			<th>Status</th>
	      			<th>Data wystawienia</th>
	      			<th>Termin płatności</th>
	      			<th>Wartość brutto</th>
	      			<th>Opcje</th>
	   			</tr>
			</thead>
			<tbody>
			{% for key, invoice in invoices if invoice.invoice.id is defined %}
				<tr>
					<td style="width: 110px;">{{ invoice.invoice.fullnumber }}</td>
					<td>{{ invoice.invoice.description }}</td>
					<td style="width: 80px;">
					
					{% if invoice.invoice.paymentstate == 'unpaid' %}
						<span style="color: #C00A31;font-weight: 700;">Niezapłacona</span>
					{% endif %}
					
					{% if invoice.invoice.paymentstate == 'paid' %}
						<span style="color: #13aeb8;font-weight: 700;">Zapłacona</span>
					{% endif %}
					</td>
					
					<td style="width: 100px;">{{ invoice.invoice.disposaldate }}</td>
					<td style="width: 100px;">{{ invoice.invoice.paymentdate }}</td>
					<td style="width: 80px;">{{ invoice.invoice.total }}</td>
					<td style="width: 120px;"><a href="{{ URL }}{{ CURRENT_CONTROLLER }}/view/invoice,{{ invoice.invoice.id }}">Pobierz</a>{% if invoice.invoice.paymentstate == 'unpaid' %} | <a href="{{ URL }}{{ CURRENT_CONTROLLER }}/view/payment,{{ invoice.invoice.id }}">Opłać online</a>{% endif %}</td>
				</tr>
			{% endfor %}
			</tbody>
		</table>
		<br />
		<br />
	</div>
</div>
<div class="block">
	<h2>{{ client.productname }}</h2>
	{{ limitsForm }}
</div>

<style>
.instancemanager {
	margin: 0px;
}
.field-image.GFormNode {
	width: 50%;
	float: left;
}
.progress-bar {
	width: 530px;
	border-right: 1px solid rgb(81, 153, 4);
}

#invoice {
	font-weight: 400;
}
.GForm .form-navigation li.ui-tabs-selected a {
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow:  none;
}
</style>
{% endblock %}