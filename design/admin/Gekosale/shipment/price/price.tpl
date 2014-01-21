{% extends "layout.tpl" %}
{% block content %}
<h2>{% trans %}TXT_SEND_ORDER_SHIPMENTS{% endtrans %}</h2>
<div class="block">

<table class="shipments">
    <thead>
        <tr>
            <th>
            {% trans %}TXT_SHIPMENT_ID{% endtrans %}
            </th>

            <th>
            {% trans %}TXT_ORDER_ID{% endtrans %}
            </th>

            <th>
            {% trans %}TXT_ADDRESS{% endtrans %}
            </th>
            <th>
            {% trans %}TXT_SHIPMENT_DATE{% endtrans %}
            </th>
            <th>
            {% trans %}TXT_WEIGHT{% endtrans %}
            </th>
            <th>
            {% trans %}TXT_DIMENSION{% endtrans %}
            </th>
            
            <th>
            {% trans %}TXT_COD_VALUE{% endtrans %}
            </th>
            <th>
            {% trans %}TXT_SHIPMENT_PRICE{% endtrans %}
            </th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
{% for shipPrice in shipments %}
<tr>
    <td class="id">{{ shipPrice.shipment.idshipment }}</td>
    <td class="id"><a href="/admin/order/edit/{{ shipPrice.order.order_id }}" target="_blank">{{ shipPrice.order.order_id }}</a></td>
    <td class="addr">{{ shipPrice.order.delivery_address.firstname }} {{ shipPrice.order.delivery_address.surname }}<br />
        {{ shipPrice.order.delivery_address.postcode }} {{ shipPrice.order.delivery_address.city }} <br />
        {{ shipPrice.order.delivery_address.street }} {{ shipPrice.order.delivery_address.streetno }}
    </td>
    <td class="date">
        {{ shipPrice.pricing.shipmentdate }}
    </td>

    <td class="weight">
        {{ "%.2f"|format(shipPrice.shipment.weight) }} kg
    </td>

    <td class="dim">
        {{ "%.2f"|format(shipPrice.shipment.width) }} cm x {{ "%.2f"|format(shipPrice.shipment.height) }} cm x  {{ "%.2f"|format(shipPrice.shipment.deep) }} cm
    </td>
    
    <td class="cod">
        {{ "%.2f"|format(shipPrice.shipment.codamount) }} zł
    </td>

    
    <td class="price">
        {{ "%.2f"|format(shipPrice.pricing.price) }} zł
    </td>
    
    <td class="oth">
    </td>
    
</tr>
{% endfor %}

<tr>
    <td colspan="6">&nbsp;</td>
    <td style="text-align: center;"><strong>{% trans %}TXT_TOTAL{% endtrans %}: </strong></td>
    <td style="text-align: center;">{{ "%.2f"|format(totalPrice)  }} zł</td>
</tr>   
</tbody>
</table>

<!-- <strong>{% trans %}TXT_IMPORTANT{% endtrans %}</strong> -->
<!-- <ul class="importants"> -->
<!--     <li>{% trans %}TXT_SHIPMENT_WITH_SUPERPACZKA{% endtrans %} <a href="/admin/shipment/packaging" target="_blank">{% trans %}TXT_READ_ABOUT_PACKAGES{% endtrans %}</a></li> -->
<!--     <li>{% trans %}TXT_SHIPMENTS_INVOICED_DEALED_BY_SUPERPACZKA{% endtrans %}</li> -->
<!-- </ul> -->


<!-- <div class="submit"> -->
<!--     <ul class="possibilities"> -->
<!--         <li></li> -->
<!--        <li style="text-align: right;"><input type="checkbox" value="1" name="superpaczka_agreement" id="superpaczka_agreement" /> <label for="superpaczka_agreement">{% trans %}TXT_ACCEPT_SUPERPACZKA_AGREEMENT{% endtrans %}</label> -->
<!--             <br/><a href="#" id="pay" class="button"><span>{% trans %}TXT_GOTO_PAYMENTS{% endtrans %}</span></a></li> -->
<!--     </ul> -->

<!-- </div> -->

</div>

    <script type="text/javascript">
            $('#pay').click(function(){
                if($('#superpaczka_agreement:checked').length <= 0){
                    alert('{% trans %}TXT_MUST_AGREE_SUPERPACZKA{% endtrans %}');
                    return false;
                }
                
                
                // goto superpaczka payments
                return false;
            });
    </script>
            
{% endblock %}


{% block sticky %}
{% include sticky %}
{% endblock %}