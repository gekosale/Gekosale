
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{% trans %}MAIL_TITLE{% endtrans %}</title>
	<style type="text/css">
		body,td,th {
			font-size: 11px;
			color: #575656;
			font-family: Arial;
		}
		body {
			margin-left: 0px;
			margin-top: 0px;
			margin-right: 0px;
			margin-bottom: 0px;
		}
		a:link {
			color: #969696;
			text-decoration: none;
		}
		a:visited {
			text-decoration: none;
			color: #969696;
		}
		a:hover {
			text-decoration: none;
			color: #969696;
		}
		a:active {
			text-decoration: none;
			color: #969696;
		}
	</style>
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
    <td width="500" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td height="96" align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="96" align="left" valign="middle"><a href="#"><img src="http://gexo.tpl/design/_images_panel/logos/admin.png" alt=""/></td>
            <td width="60" align="left" valign="middle"><font color="#969696"><a href="#">{% trans %}TXT_CONTACT{% endtrans %}</a></font></td>
            <td width="79" align="left" valign="middle"><font color="#969696"><a href="#">{% trans %}TXT_YOUR_ACCOUNT{% endtrans %}</a></font></td>
            <td width="75" align="left" valign="middle"><font color="#969696"><a href="#">{% trans %}TXT_HELP{% endtrans %}</a></font></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td height="23" align="left" valign="top"><hr noshade="noshade" size="1" color="#e8e8e8"/></td>
      </tr>
      <tr>
        <td style="text-align:justify" align="left" valign="top"><font size="+2"><b>{% trans %}TXT_HEADER_NAME{% endtrans %}</b></font>
   		<br/>{% trans %}TXT_HEADER_INFO{% endtrans %}
        </td>
      </tr>
      <tr>
        <td height="38" align="left" valign="top"><img src="{{ DESIGNPATH }}_images_panel/icons/modules/checkpoint.png" alt=""/></td>
        <td height="38" align="left" valign="top"><img src="http://gexo.tpl/design/_images_panel/icons/modules/translation-list.png" alt=""/></td>
      </tr>
      <tr>
      <td height="38" align="left" valign="top"><img src="{{ DESIGNPATH }}_images_panel/logos/admin.png" alt=""/></td>
	      <td height="38" align="left" valign="top">111<img src="http://gexo.tpl/design/_images_panel/logos/admin.png" alt=""/></td>
      </tr>
      <tr>
        <td style="text-align:justify" align="left" valign="top"><font size="+1"><b>{% trans %}TXT_WELCOME{% endtrans %}</b></font>
        	<p>
        	 {{ active.firstname }}  {{ active.surname }}
			</p>
        </td>
      </tr>
      <tr>
        <td height="20" align="left" valign="top" style="text-align:justify">&nbsp;</td>
      </tr>
    </table>
   </td>
    <td>Ostatnie logowanie {{ active.lastLogged }}</td>
  </tr>
  <tr>
    <td height="10" bgcolor="#3d3d3d">&nbsp;</td>
    <td width="500" height="10" align="left" valign="top" bgcolor="#3d3d3d">&nbsp;</td>
    <td height="10" bgcolor="#3d3d3d">&nbsp;</td>
  </tr>
  <tr>
    <td height="70" bgcolor="#2c2c2c">&nbsp;</td>
    <td width="500" height="70" align="center" valign="middle" bgcolor="#2c2c2c">
    <font color="#b1b1b1">{% trans %}TXT_FOOTER_EMAIL{% endtrans %}</font></td>
    <td height="70" bgcolor="#2c2c2c">&nbsp;</td>
  </tr>
</table>
</body>
</html>