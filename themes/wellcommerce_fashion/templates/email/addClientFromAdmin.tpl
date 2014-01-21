<p>
<b>{% trans %}TXT_CLIENT{% endtrans %}:</b><br/>
{% trans %}TXT_FIRSTNAME{% endtrans %}: {{ personal_data.firstname }}<br>
{% trans %}TXT_SURNAME{% endtrans %}: {{ personal_data.surname }}<br>
{% trans %}TXT_LOG{% endtrans %}: {{ personal_data.email }}<br>
{% trans %}TXT_PASSWORD{% endtrans %}: {{ personal_data.password }}<br>
</p>
<p>
<b>{% trans %}TXT_ADDRESS{% endtrans %}:</b><br/>
{% trans %}TXT_PLACENAME{% endtrans %}: {{ address.placename }}<br>
{% trans %}TXT_POSTCODE{% endtrans %}: {{ address.postcode }}<br>
{% trans %}TXT_STREET{% endtrans %}: {{ address.street }} <br>
{% trans %}TXT_STREETNO{% endtrans %}: {{ address.streetno }}<br>
{% trans %}TXT_PHONE{% endtrans %}: {{ personal_data.phone }}<br>
{% if address.phone2 != '' %}<p><strong>{% trans %}TXT_ADDITIONAL_PHONE{% endtrans %}:</strong> {{ personal_data.phone2 }}</p>{% endif %}
</p>