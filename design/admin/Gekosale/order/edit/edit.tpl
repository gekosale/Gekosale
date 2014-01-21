{% extends "layout.tpl" %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/icons/modules/orders-edit.png" alt=""/>{% trans %}TXT_EDIT_ORDER{% endtrans %} {{ order.order_id }} ({{ order.view }}) <br /><small>z dnia {{ order.order_date }}</small></h2>
<ul class="possibilities">
	<li><a href="{{ URL }}order" class="button return" title="{% trans %}TXT_RETURN_TO_PREVIOUS_SCREEN{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_ORDER_LIST{% endtrans %}" alt="{% trans %}TXT_ORDER_LIST{% endtrans %}"/></span></a></li>
	{% if order.previous > 0 %}<li><a href="{{ URL }}order/edit/{{ order.previous }}" class="button previous-order"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-green.png" title="{% trans %}TXT_PREV_ORDER{% endtrans %}" alt="{% trans %}TXT_PREV_ORDER{% endtrans %}"/>{% trans %}TXT_PREV_ORDER{% endtrans %}</span></a></li>{% endif %}
	{% if order.next > 0 %}<li><a href="{{ URL }}order/edit/{{ order.next }}" class="button next"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-right-green.png" title="{% trans %}TXT_NEXT_ORDER{% endtrans %}" alt="{% trans %}TXT_NEXT_ORDER{% endtrans %}"/>{% trans %}TXT_NEXT_ORDER{% endtrans %}</span></a></li>{% endif %}
	<li><a href="{{ URL }}order/confirm/{{ order.order_id }}" class="button print"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/print.png" title="{% trans %}TXT_NEXT_ORDER{% endtrans %}" alt="{% trans %}TXT_NEXT_ORDER{% endtrans %}"/>{% trans %}TXT_PRINT{% endtrans %}</span></a></li>
	<li><a href="#order" id="save_order" rel="submit" class="button" title="{% trans %}TXT_SAVE{% endtrans %}"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/check.png" alt=""/>{% trans %}TXT_SAVE{% endtrans %}</span></a></li>
</ul>
<script type="text/javascript">
	
		/*<![CDATA[*/
			GCore.OnLoad(function() {
				$('.view-order').GTabs();
			});
		/*]]>*/
	
</script>
<div class="view-order GForm">
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_VIEW_ORDER_BASIC_DATA{% endtrans %}</span></legend>
		{{ form }}
		
		<div class="layout-two-columns">
		 	
			<div class="column">
				<h3><span><strong>Zmień status</strong></span></h3>
				{{ statusChange }}
			</div>
		 	
			<div class="column">
				<h3><span><strong>Dodaj notkę</strong></span></h3>
				{{ addNotes }}
			</div>
			
		</div>
		
	</fieldset>
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_VIEW_ORDER_INVOICES{% endtrans %}</span></legend>
		
		{% if order.invoices|length > 0 %}
		<ul class="changes-detailed">
		{% for invoice in order.invoices %}
		<li>
			<h4><span>{{ invoice.symbol }} - <em>{{ invoice.invoicedate }}</em> <a href="{{ URL }}invoice/view/{{ invoice.idinvoice }},0">ORYGINAŁ</a> | <a href="{{ URL }}invoice/view/{{ invoice.idinvoice }},1">KOPIA</a></span></h4>
			{% if invoice.comment !='' %}<p>{% trans %}TXT_COMMENT{% endtrans %}: <strong>{{ invoice.comment }}</strong></p>{% endif %}
			<p>{% trans %}TXT_MATURITY{% endtrans %}: <strong>{{ invoice.paymentduedate }}</strong></p>
			<p>{% trans %}TXT_SALES_PERSON{% endtrans %}: <strong>{{ invoice.salesperson }}</strong></p>
			<p>{% trans %}TXT_TOTAL_PAYED{% endtrans %}: <strong>{{ invoice.totalpayed }}</strong></p>
		</li>
		{% endfor %}
		</ul>
		{% endif %}
		
		<p class="information">
			<a href="{{ URL }}invoice/add/{{ order.order_id }},1" class="button"><span>{% trans %}TXT_ADD_INVOICE_PRO{% endtrans %}</span></a>
			<a href="{{ URL }}invoice/add/{{ order.order_id }},2" class="button"><span>{% trans %}TXT_ADD_INVOICE_VAT{% endtrans %}</span></a>
		</p>
		
	</fieldset>
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_VIEW_ORDER_SHIPMENTS{% endtrans %}</span></legend>
		
		{% if order.shipments|length > 0 %}
		<ul class="changes-detailed">
		{% for shipment in order.shipments %}
		<li>
			<h4><span>{{ shipment.model }} ({{ shipment.guid }}: {{ shipment.packagenumber }}) - <em>{{ shipment.adddate }}</em>
        </li>
		{% endfor %}
		</ul>
		{% endif %}
		<p class="information">
			<a href="{{ URL }}shipment/add/elektronicznynadawca,{{ order.order_id }}" class="button"><span>{% trans %}TXT_ADD_SHIPMENT{% endtrans %} (ElektronicznyNadawca)</span></a>
			<a href="{{ URL }}shipment/add/dpd,{{ order.order_id }}" class="button"><span>{% trans %}TXT_ADD_SHIPMENT{% endtrans %} (DPD)</span></a>
		</p>
	</fieldset>
    
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_VIEW_ORDER_HISTORY{% endtrans %}</span></legend>
		
		{% if order.order_history|length > 0 %}
		<ul class="changes-detailed">
			{% for change in order.order_history %}
			<li>
				<h4><span>{{ change.date }} - <em>{% if change.inform == 1 %}{% trans %}TXT_VIEW_ORDER_CLIENT_INFORMED{% endtrans %}{% else %}{% trans %}TXT_VIEW_ORDER_CLIENT_NOT_INFORMED{% endtrans %}{% endif %}</em></span></h4>
				{% if change.orderstatusname is defined %}<p>status: <strong>{{ change.orderstatusname }}</strong></p>{% endif %}
				{% if change.content is defined %}<p>Komentarz: <strong>{{ change.content }}</strong></p>{% endif %}
				{% if change.user != '' %}<p class="author">{% trans %}TXT_VIEW_ORDER_CHANGE_AUTHOR{% endtrans %}: <strong>{{ change.user }}</strong></p>{% endif %}
			</li>
			{% endfor %}
		</ul>
		{% else %}
			<p class="information">{% trans %}TXT_VIEW_ORDER_NO_RECORDED_HISTORY{% endtrans %}</p>
		{% endif %}	
	</fieldset>
	
	{% if order.order_files|length > 0 %}
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_VIEW_ORDER_FILES{% endtrans %}</span></legend>
			{% for file in order.order_files %}
				<p class="information"><a href="{{ file.path }}" target="_blank">{{ file.path }}</a></p>
			{% endfor %}
	</fieldset>
	{% endif %}
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_ORDER_NOTE{% endtrans %}</span></legend>
		
		{% if orderNotes|length > 0 %}
		<ul class="changes-detailed">
			{% for ordernote in orderNotes %}
			<li>
				<h4><span>{{ ordernote.adddate }}</span></h4>
				{% if ordernote.content is defined %}<p>Komentarz: <strong>{{ ordernote.content }}</strong></p>{% endif %}
				<p class="author">{% trans %}TXT_VIEW_ORDER_CHANGE_AUTHOR{% endtrans %}: <strong>{{ ordernote.user }}</strong></p>
			</li>
			{% endfor %}
		{% else %}
        	<p class="information">{% trans %}TXT_ORDER_NOTES_EMPTY{% endtrans %}</p>
		{% endif %}		
		</ul>
	</fieldset>
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_CLIENT_ORDER_HISTORY{% endtrans %}</span></legend>
		
		{% if clientOrderHistory|length > 0 %}
		<ul class="changes-detailed">
			{% for history in clientOrderHistory %}
			<li>
				<h4><span>{{ history.adddate }}</span></h4>
				<p>Nr. zamówienia:  <strong><a href="{{ URL }}order/edit/{{ history.idorder }}">#{{ history.idorder }}</a></strong></p>
				<p class="author">{% trans %}TXT_ALL_ORDERS_PRICE{% endtrans %}: <strong>{{ history.globalprice }}</strong>{{ currencysymbol }}</p>
			</li>
			{% endfor %}
		{% else %}
        	<p class="information">{% trans %}TXT_ORDER_HISTORY_EMPTY{% endtrans %}</p>
		{% endif %}	
		</ul>
	</fieldset>
	
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_CUSTOMER_OPINION{% endtrans %}</span></legend>
		{% if order.customeropinion is not empty %}
			<p class="information">{{ order.customeropinion }}</p>
		{% else %}
			<p class="information">{% trans %}TXT_CUSTOMER_OPINION_NO_EXIST{% endtrans %}</p>
		{% endif %}
	</fieldset>
	{% if order.giftwrap == 1 %}
	<fieldset class="no-margin">
		<legend><span>{% trans %}TXT_GIFTWRAP{% endtrans %}</span></legend>
		<h4>Treść dedykacji:</h4>
		<p class="information">{{ order.giftwrapmessage }}</p>
	</fieldset>
	{% endif %}
</div>

<script type="text/javascript">
	
		/*<![CDATA[*/
			
			var RecalculateOrder = function(eEvent, bWithDeliveryMethodsUpdate) {
				var fNetValue = parseFloat($('.field-order-editor .selected-products tr.total .GF_Datagrid_Col_net_subsum span').text());
				var fVatValue = parseFloat($('.field-order-editor .selected-products tr.total .GF_Datagrid_Col_vat_value span').text());
				var fWeight = parseFloat($('.field-order-editor .selected-products tr.total .GF_Datagrid_Col_weight span').text());
				fNetValue = isNaN(fNetValue) ? 0 : fNetValue;
				fVatValue = isNaN(fVatValue) ? 0 : fVatValue;
				fWeight = isNaN(fWeight) ? 0 : fWeight;
				var gSelectedDatagrid = $('.field-order-editor').get(0).gNode.m_gSelectedDatagrid;
				var aoProducts = [];
				for (var i in gSelectedDatagrid.m_aoRows) {
					aoProducts.push({
						id: gSelectedDatagrid.m_aoRows[i].idproduct,
						variant: gSelectedDatagrid.m_aoRows[i].variant,
						quantity: gSelectedDatagrid.m_aoRows[i].quantity,
						price: gSelectedDatagrid.m_aoRows[i].price
					});
				};
				$('#additional_data__summary_data__total_net_total').val(fNetValue.toFixed(2));
				$('#additional_data__summary_data__total_vat_value').val(fVatValue.toFixed(2));
				$('#pricenetto').val(fNetValue.toFixed(2));
				$('#pricebrutto').val((fNetValue + fVatValue).toFixed(2));
				
				if ((bWithDeliveryMethodsUpdate != undefined) && bWithDeliveryMethodsUpdate) {					
					xajax_GetDispatchMethodForPrice({
						products: aoProducts,
						idorder: {{ order.id }},
						net_total: (fNetValue).toFixed(2),
						gross_total: (fNetValue + fVatValue).toFixed(2),
						weight_total: (fWeight).toFixed(2),
					}, GCallback(function(oResponse) {
						$('#order').get(0).GetField('delivery_method').ExchangeOptions(oResponse.options);
					}));
				}
				xajax_CalculateDeliveryCost({
					products: aoProducts,
					idorder: {{ order.id }},
					weight: parseFloat($('.field-order-editor .selected-products tr.total .GF_Datagrid_Col_weight span').text()),
					price_for_deliverers: $('#pricebrutto').val(),
					net_total: $('#pricenetto').val(),
					delivery_method: $('#additional_data__payment_data__delivery_method').val(),
					rules_cart: $('#additional_data__payment_data__rules_cart').val(),
					currency: $('#currencyid').val()
				}, GCallback(function(oResponse) {
					var fDeliveryValue = parseFloat(oResponse.cost);
					fDeliveryValue = isNaN(fDeliveryValue) ? 0 : fDeliveryValue;
					var fCouponValue = parseFloat(oResponse.coupon);
					fCouponValue = isNaN(fCouponValue) ? 0 : fCouponValue;
					$('#additional_data__summary_data__total_delivery').val(fDeliveryValue.toFixed(2));
					if(oResponse.rulesCart.discount != undefined) {
						var sSymbol =  oResponse.rulesCart.symbol;
						var fDiscount = parseFloat(oResponse.rulesCart.discount);
						var fOldTotal = parseFloat(fNetValue + fVatValue + fDeliveryValue - fCouponValue);
						switch (sSymbol) {
							case '%':
								fNewTotal = fOldTotal * (fDiscount / 100);
								break;
							case '+':
								fNewTotal = fOldTotal + fDiscount;
								break;
							case '-':
								fNewTotal = fOldTotal - fDiscount;
								break;
							case '=':
								fNewTotal = fDiscount;
								break;
						}
						$('#additional_data__summary_data__total_total').val((fNewTotal).toFixed(2));
					} else {
						$('#additional_data__summary_data__total_total').val((fNetValue + fVatValue + fDeliveryValue - fCouponValue).toFixed(2));
					}
					
					$('#additional_data__summary_data__total_delivery').val(fDeliveryValue.toFixed(2));
					$('#coupon').val(fCouponValue.toFixed(2));
				}));
			};
			
			var OnProductListChanged = GEventHandler(function(eEvent) {
				var gSelectedDatagrid = $('.field-order-editor').get(0).gNode.m_gSelectedDatagrid;
				if(gSelectedDatagrid.m_aoRows.length){
					RecalculateOrder(eEvent, true);
				}
				gSelectedDatagrid.m_jBody.find('.show-thumb').mouseenter(GTooltip.ShowThumbForThis).mouseleave(GTooltip.HideThumbForThis);
			});
			
			$(document).ready(function() {
				{% if order.isallegro == 1 %}
				GMessage('To zamówienie zostało zaimportowane automatycznie z Allegro');
				{% endif %}
				$('#additional_data__payment_data__delivery_method').live('change',RecalculateOrder);
				$('#additional_data__payment_data__rules_cart').change(RecalculateOrder);
				$("<input />").attr({type:'hidden',name:'coupon',id:'coupon',value:'0'}).appendTo($("#order"));
				$("<input />").attr({type:'hidden',name:'pricebrutto',id:'pricebrutto'}).appendTo($("#order"));
				$("<input />").attr({type:'hidden',name:'pricenetto',id:'pricenetto'}).appendTo($("#order"));
				$("<input />").attr({type:'hidden',name:'currencyid',id:'currencyid',value:'{{ currencyid }}'}).appendTo($("#order"));
			});	

		/*]]>*/
	
</script>
{% endblock %}