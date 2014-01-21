{% if activelink is defined %}
<p><a href="{{ path('frontend.registration', {"param": activelink}) }}">{% trans %}TXT_ACTIVATE_CLIENT_ACCOUNT{% endtrans %}</a></p>
{% else %}
<p><strong style="color:#231f20;">{% trans %}TXT_REGISTER_USER_OK{% endtrans %}</strong></p>
{% endif %}
<p><strong>{% trans %}TXT_FIRSTNAME{% endtrans %}:</strong> {{ address.firstname }}</p>
<p><strong>{% trans %}TXT_SURNAME{% endtrans %}:</strong> {{ address.surname }}</p>
<p><strong>{% trans %}TXT_EMAIL{% endtrans %}:</strong> {{ address.email }}</p>
<p><strong>{% trans %}TXT_PHONE{% endtrans %}:</strong> {{ address.phone }}</p>
{% if address.phone2 != '' %}<p><strong>{% trans %}TXT_ADDITIONAL_PHONE{% endtrans %}:</strong> {{ address.phone2 }}</p>{% endif %}
