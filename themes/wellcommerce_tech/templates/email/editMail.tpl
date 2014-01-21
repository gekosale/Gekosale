<p>{% trans %}TXT_EMAIL_CHANGE_CONTENT{% endtrans %}</p>
<p>
<b>{% trans %}TXT_CLIENT{% endtrans %}: </b><br/>
{% trans %}TXT_FIRSTNAME{% endtrans %}: {{ clientdata.firstname }}<br>
{% trans %}TXT_SURNAME{% endtrans %}: {{ clientdata.surname }}<br> 
{% trans %}TXT_OLD_EMAIL{% endtrans %}: {{ clientdata.email }}<br>
{% trans %}TXT_NEW_EMAIL{% endtrans %}: {{ EMAIL_NEW }}<br>
</p>