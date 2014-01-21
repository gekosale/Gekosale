<p>Witaj <strong style="color:#231f20;">{{ orderhistory.firstname }} {{ orderhistory.surname }}</strong></p>
<p>{% trans %}TXT_STATUS{% endtrans %}: <strong>{{ orderhistory.orderstatusname }}</strong></p>
<p>{% trans %}TXT_COMMENT{% endtrans %}: {{ orderhistory.content }}</p>