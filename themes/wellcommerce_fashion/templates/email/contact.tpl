<p>Nadawca: <strong style="color:#231f20;">{{ firstname }} {{ surname }}</strong></p>
<p>{% trans %}TXT_EMAIL{% endtrans %}: <a href="mailto:{{ phone }}">{{ email }}</a></p>
<p>{% trans %}TXT_PHONE{% endtrans %}: {{ phone }}</p>
<p>{% trans %}TXT_MESSAGE{% endtrans %}:<br /><br />{{ CONTACT_CONTENT }}</p>
{% if productLink is defined %}
<p><a href="{{ productLink }}">Zobacz produkt w sklepie</a></p>
{% endif %}