      <tr>
        <td style="text-align:justify" align="left" valign="top"><font size="+1"><b>{% trans %}TXT_WELCOME{% endtrans %}</b></font>
        	<p>
				{% trans %}TXT_VIEW_ORDER_HISTORY{% endtrans %}
			</p>
        </td>
      </tr>
      <tr>
      	<td>
	      	<p>
		        <font color="#f6b900"><b>{% trans %}TXT_CLIENT{% endtrans %}: {{ orderhistory.firstname }} {{ orderhistory.surname }}</b></font><br/>
				{% trans %}TXT_COMMENT{% endtrans %} : {{ orderhistory.content }} <br> 
				{% trans %}TXT_STATUS{% endtrans %} : {{ orderhistory.orderstatusname }} <br>
	      	</p>
      	</td>
      </tr>