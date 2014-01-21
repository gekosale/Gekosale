<p>{% trans %}TXT_ADDRESS_ADD_CONTENT{% endtrans %}</p>
<p>
<b>{% trans %}TXT_NEW_ADDRESS{% endtrans %}:</b><br/>
{% trans %}TXT_FIRSTNAME{% endtrans %}: {{ address.firstname }}<br>
{% trans %}TXT_SURNAME{% endtrans %}: {{ address.surname }}<br>
{% trans %}TXT_PLACENAME{% endtrans %}: {{ address.placename }}<br>
{% trans %}TXT_POSTCODE{% endtrans %}: {{ address.postcode }}<br>
{% trans %}TXT_STREET{% endtrans %}: {{ address.street }} <br>
{% trans %}TXT_STREETNO{% endtrans %}: {{ address.streetno }}<br>
</p>