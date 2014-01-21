<p>Witamy serdecznie,<br/>
<p>
{% if newsletterlink is defined %}
{% trans %}TXT_CLIENT_REGISTRATION_NEWSLETTER{% endtrans %}<br/>
<a href="{{ path('frontend.newsletter', {"param": newsletterlink}) }}">{% trans %}TXT_ACTIVE_NEWSLETTER_LINK{% endtrans %}</a><br/></br>
<a href="{{ path('frontend.newsletter', {"param": unwantednewsletterlink}) }}">{% trans %}TXT_UNWANTED_ACTIVE_NEWSLETTER_LINK{% endtrans %}</a>
{% else %}
<a href="{{ path('frontend.newsletter', {"param": unwantednewsletterlink}) }}">{% trans %}TXT_UNWANTED_ACTIVE_NEWSLETTER_LINK{% endtrans %}</a>
{% endif %}
</p>
