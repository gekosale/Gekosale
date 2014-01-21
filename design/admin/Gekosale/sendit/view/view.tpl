{% extends "layout.tpl" %}
{% block stylesheet %}
{{ parent() }}

<style type="text/css">

   .clear{ clear: both }

   #sendit
   {
       background: none repeat scroll 0 0 #FFFFFF;
       border: 1px solid #B7B7B7;
       padding: 17px 20px 2px;
   }
   #sendit div
   {
       margin: 30px 0;
   }

   #sendit input, #sendit select
    {
       background: none repeat scroll 0 0 transparent;
       border: medium none;
       font-size: 1em;
       height: 14px;
       padding: 3px 4px;
       background: url("{{ DESIGNPATH }}_images_panel/backgrounds/field.png") repeat-x scroll 0 -1px #FFFFFF;
       border-color: #9F9F9F #E7E7E7 #E7E7E7 #9F9F9F;
       border-image: none;
       border-radius: 3px 3px 3px 3px;
       border-style: solid;
       border-width: 1px;

   }
   #sendit select
   {
        height: 20px;
       width: 302px;

   }
   #sendit .col2  input
   {
       width: 292px;
   }
   #sendit .margin-form
   {

       height: 20px;
       position: relative;
       width: 292px;
       margin: 0 0 0 135px;
   }
   #sendit label
   {
       padding: 0 15px 0 0;
       text-align: right;
       line-height: 22px;
       font-weight: bold;
       float: left;
       width: 120px
   }
   #sendit .row
   {
        margin: 6px 0;
   }
   #sendit div.col2
   {
       float: left;
       margin: 10px 20px;
       width: 45%;
   }
   #sendit .product,#sendit .notify
   {
       margin: 10px 20px;
   }
   #sendit h2
   {
        clear: both;
       width: 100%;
   }
   #sendit ul
    {
       list-style: none;
   }
   #sendit ul li
   {
       padding: 5px 0;
   }
   #sendit .product
    {
       float: left;
   }
   #sendit ul.sendit_service label
   {
      float: none;
   }
   span.red
    {
       color: red;
   }
   span.req
   {
       color: #ff9500;
   }
   div#summary div div table.table tbody tr.sum
   {
       background: #e7e7e7;
   }
   div#summary div div table.table tbody tr.sumBrutto
   {
       background: #f0feec;

   }
   div#summary div div table.table tbody tr.sumConfirm
   {
       background: #d5ffda;
   }
   div#summary div div table.table tbody tr.sumBrutto td
   {
       font-weight: bold;
       font-size: 20px;
   }
   div#summary div div table.table tbody tr.sum td.col2, div#summary div div table.table tbody tr.sumBrutto td.col2
   {
       text-align: right;
   }

   div#summary div div table.table tr th,table#sendit_orders tr th {
       background: -moz-linear-gradient(center top , #F9F9F9, #ECECEC) repeat-x scroll left top #ECECEC;
       color: #333333;
       font-size: 13px;
       padding: 4px 6px;
       text-align: left;
       text-shadow: 0 1px 0 #FFFFFF;
   }

   div#summary div div table.table tr td, table#sendit_orders tr td {
       border-bottom: 1px solid #CCCCCC;
       color: #333333;
       font-size: 12px;
       padding: 4px 4px 4px 6px;
   }
   div#summary div div table.table, table#sendit_orders
    {
       border: 1px solid #CCCCCC;
   }
   .center
    {
       text-align: center !important;
   }
   #sendit .error {
       background: 6px 6px #FFBABA;
       border: 1px solid #CC0000;
       color: #D8000C;
   }

    #sendit .conf {
    background: 6px 6px #DFF2BF;
    border: 1px solid #4F8A10;
    color: #4F8A10;
    }
    #sendit .error, #sendit .conf {
    border-radius: 3px 3px 3px 3px;
    color: #383838;
    font-size: 12px;
    font-weight: normal;
    line-height: 20px;
    margin: 0 0 10px;
    min-height: 28px;
    padding: 13px 5px 5px 40px;
}
   div#summary div div.col2 table.table thead tr th.title
    {
       font-size: 20px;
       padding: 10px;
   }
   .button,  #sendit a.button{
       background: -moz-linear-gradient(center top , #F9F9F9, #E3E3E3) repeat scroll 0 0 transparent;
       border-color: #CCCCCC #BBBBBB #A0A0A0;
       border-radius: 3px 3px 3px 3px;
       border-style: solid;
       border-width: 1px;
       color: #000000;
       margin: 0;
       outline: medium none;
       padding: 3px 8px;
       text-align: center;
       text-shadow: 0 1px 0 #FFFFFF;
       vertical-align: middle;
       white-space: nowrap;
   }
   .button[disabled="disabled"], .button.disabled {
       color: #8C8C8C !important;
   }
   input.button[disabled="disabled"]:hover, input.button[disabled="disabled"].disabled:hover {
       background-color: #FFF6D3;
   }
   .button:hover {
       border: 1px solid #939393;
   }
   #content h2 img
   {
       height: auto;
       width: auto;
       margin: auto;
       float: none;
   }
   a.alert_email
   {
       display: block;
       background: url('{{ DESIGNPATH }}_images_frontend/core/icons/alerts.png') no-repeat;
       width: 20px;
       height: 16px;
       margin: 10px 17px;
   }
   a.alert_sms
   {
       display: block;
       background: url('{{ DESIGNPATH }}_images_frontend/core/icons/alerts.png') 0 -16px no-repeat;
       width: 16px;
       height: 21px;
       margin: 10px 17px;
   }
   div.notify
   {
       margin: 10px;
   }
   div.notify table tbody tr td
   {
       border-bottom: 1px dotted #D7D7D7;
       font-size: 12px;
       font-weight: bold;
   }
   div.notify table tbody tr td input
   {
       margin: 0 0 0 5px;
   }
    div#sendit div.clear div.notify table thead tr th
   {
        font-size: 14px;
    }
    div#sendit div.more_info ul.sendit_form li label
   {
     line-height: normal;
   }
    div#sendit div.more_info input[type="text"]
    {
        width: 200px;
    }
   div#loader
    {
        background: url("{{ DESIGNPATH }}_images_frontend/core/icons/ajax-loader.gif") no-repeat;
        height: 32px;
        width: 32px;
        display: block;
        margin: 40px auto;
    }


</style>

{% endblock %}
{% block content %}
<h2><img src="{{ DESIGNPATH }}_images_panel/logos/sendit.png" alt=""/> zamówienie nr {{ order.order_id }}</h2>
<ul class="possibilities">
	<li><a href="{{ URL }}sendit" class="button return"><span><img src="{{ DESIGNPATH }}_images_panel/icons/buttons/arrow-left-gray.png" title="{% trans %}TXT_ORDER_LIST{% endtrans %}" alt="{% trans %}TXT_ORDER_LIST{% endtrans %}"/></span></a></li>
</ul>


<div class="clear"></div>
<div id="sendit">
{% if sendit_error != '' %}
    <div class="clear"></div>
    <div class="error">{{ sendit_error }}</div>
    <div class="clear"></div>
{% endif %}
<table id="sendit_orders" class="table" width="100%" cellspacing="0" cellpadding="0">
    <thead>
    <tr>
        <th>Nr zlecenia</th>
        <th>Cena</th>
        <th>Kurier</th>
        <th>List przewozowy</th>
        <th>Protokół odbioru</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
    <div id="form_sendit" style="display: none">
        <h2>Informacje adresowe</h2>
        <div>
            <p>Pola oznaczone <span class="req">*</span> są obowiązkowe.</p>
            <div class="col2">
                <h3>Dane nadawcy</h3>
                <div class="row">
                    <label>Nazwa<span class="req"> *</span></label>
                    <div class="margin-form">
                        <input class="field-text" type="text" name="senderName" id="senderName" value="{{ senderName }}" />
                   </div>
               </div>

                <div class="row">
                    <label>Ulica i nr domu<span class="req"> *</span></label>
                    <div class="margin-form">
                        <input type="text" name="senderStreet" id="senderStreet" value="{{ senderStreet }}" />
                   </div>
               </div>

                <div class="row">
                    <label>Kod pocztowy<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderZip" id="senderZip" value="{{ senderZip }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Miasto<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderCity" id="senderCity" value="{{ senderCity }}" />
                   </div>
               </div>
                <div class="row">

                    <label>Kraj<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderCountry" id="senderCountry" value="Polska"
                               readonly="readonly "/>
                   </div>
               </div>
                <div class="row">
                    <label>Telefon<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderPhone" id="senderPhone" value="{{ senderPhone }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Email<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderEmail" id="senderEmail" value="{{ senderEmail }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Osoba kontaktowa<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="senderPerson" id="senderPerson" value="{{ senderPerson }}" />
                   </div>
               </div>
           </div>

            <div class="col2">
                <h3>Dane odbiorcy</h3>
                <div class="row">
                    <label>Nazwa<span class="req"> *</span></label>
                    <div class="margin-form">
                        <input class="field-text" type="text" name="receiverName" id="receiverName" value="{{ receiverName }}" />
                   </div>
               </div>

                <div class="row">
                    <label>Ulica i nr domu<span class="req"> *</span></label>
                    <div class="margin-form">
                        <input type="text" name="receiverStreet" id="receiverStreet" value="{{ receiverStreet }}" />
                   </div>
               </div>

                <div class="row">
                    <label>Kod pocztowy<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="receiverZip" id="receiverZip" value="{{ receiverZip }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Miasto<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="receiverCity" id="receiverCity" value="{{ receiverCity }}" />
                   </div>
               </div>
                <div class="row">

                    <label>Kraj<span class="req"> *</span></label>

                    <div class="margin-form">
                       <select name="receiver_country" id="receiver_country" class="GSelect">
                        {foreach from=$country_list item=name key=key}
                            <option value="{{ key }}" {% if receiverCountryCode == key %}selected="selected"{% endif %}>{{ name }}</option>
                        {/foreach}
                        </select>
                   </div>
               </div>
                <div class="row">
                    <label>Telefon<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="receiverPhone" id="receiverPhone" value="{{ receiverPhone }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Email<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="receiverEmail" id="receiverEmail" value="{{ receiverEmail }}" />
                   </div>
               </div>
                <div class="row">
                    <label>Osoba kontaktowa<span class="req"> *</span></label>

                    <div class="margin-form">
                        <input type="text" name="receiverPerson" id="receiverPerson" value="{{ receiverPerson }}" />
                   </div>
               </div>
           </div>
       </div>

        <div class="clear"></div>

        <div>
            <h2>Informacje o przesyłce</h2>

            <div class="product">
                <h3>Ilość paczek standardowych</h3>
                <ul class="sendit_form">
                    <li>
                        <label for="kPK">kopertowych: </label><input name="kPK" id="kPK" class="parcel" type="text" maxlength="3"
                                                                             value="{{ kPK }}">
                    </li>
                    <li>
                        <label for="kP5">do 5kg: </label><input name="kP5" id="kP5" class="parcel" type="text" value="{{ kP5 }}"  maxlength="3">
                    </li>
                    <li>
                        <label for="kP10">do 10kg: </label><input name="kP10" id="kP10" class="parcel" type="text"  maxlength="3"
                                                                          value="{{ kP10 }}">
                    </li>
                    <li>
                        <label for="kP20">do 20kg: </label><input name="kP20" id="kP20" class="parcel" type="text"  maxlength="3"
                                                                          value="{{ kP20 }}">
                    </li>
                    <li>
                        <label for="kP30">do 30kg: </label><input name="kP30" id="kP30" class="parcel" type="text"  maxlength="3"
                                                                          value="{{ kP30 }}">
                    </li>
                    <li>
                        <label for="kP50">do 50kg: </label><input name="kP50" id="kP50" class="parcel" type="text"  maxlength="3"
                                                                          value="{{ kP50 }}">
                    </li>
                    <li>
                        <label for="kP70">do 70kg: </label><input name="kP70" id="kP70" class="parcel" type="text"  maxlength="3"
                                                                          value="{{ kP70 }}">
                    </li>

                </ul>
           </div>


            <div class="product">
                <h3>Ilość paczek niestandardowych</h3>
                <ul class="sendit_form">
                    <li>
                        <label for="nstd_kPK">kopertowych: </label><input name="nstd_kPK" id="nstd_kPK" class="parcel"  maxlength="3"
                                                                                  type="text" value="0">
                    </li>
                    <li>
                        <label for="nstd_kP5">do 5kg: </label><input name="nstd_kP5" id="nstd_kP5" class="parcel" type="text"  maxlength="3"
                                                                             value="0">
                    </li>
                    <li>
                        <label for="nstd_kP10">do 10kg: </label><input name="nstd_kP10" id="nstd_kP10" class="parcel"  maxlength="3"
                                                                               type="text" value="0">
                    </li>
                    <li>
                        <label for="nstd_kP20">do 20kg: </label><input name="nstd_kP20" id="nstd_kP20" class="parcel"  maxlength="3"
                                                                               type="text" value="0">
                    </li>
                    <li>
                        <label for="nstd_kP30">do 30kg: </label><input name="nstd_kP30" id="nstd_kP30" class="parcel"  maxlength="3"
                                                                               type="text" value="0">
                    </li>
                    <li>
                        <label for="nstd_kP50">do 50kg: </label><input name="nstd_kP50" id="nstd_kP50" class="parcel"  maxlength="3"
                                                                               type="text" value="0">
                    </li>
                    <li>
                        <label for="nstd_kP70">do 70kg: </label><input name="nstd_kP70" id="nstd_kP70" class="parcel"  maxlength="3"
                                                                               type="text" value="0">
                    </li>

                </ul>
           </div>

            <div class="product">
                <h3>Palety</h3>
                <ul class="sendit_form">
                    <li>
                        <label for="palletWeight">waga: </label><input name="palletWeight" id="palletWeight" class="pallet"
                                                                               type="text" value="0" {% if receiverCountryCode != 'PL'  %}
                                                                               disabled="disabled" {% endif %}   maxlength="10" >
                    </li>
                    <li>
                        <label for="palletHeight">wysokość: </label><input name="palletHeight" id="palletHeight" class="pallet"
                                                                                   type="text"
                                                                                   value="0" {% if receiverCountryCode != 'PL'  %}
                                                                                   disabled="disabled" {% endif %}   maxlength="10">
                    </li>
                </ul>
           </div>
       </div>
        <div class="clear"></div>

        <div class="notify">
            <h3>Powiadomienia</h3>
            <table>
                <thead>
                <tr>
                    <th style="width: 180px"></th>
                    <th colspan="2">Nadawca</th>
                    <th style="width: 20px"></th>
                    <th colspan="2">Odbiorca</th>
                </tr>
                <tr>
                    <th></th>
                    <th><a href="" class="alert_email sender" title="Zaznacz wszystkie e-maile dla nadawcy"></a></th>
                    <th><a href="" class="alert_sms sender" title="Zaznacz wszystkie SMS-y dla nadawcy"></a></th>
                    <th></th>
                    <th><a href="" class="alert_email receiver" title="Zaznacz wszystkie e-maile dla odbiorcy"></a></th>
                    <th><a href="" class="alert_sms receiver" title="Zaznacz wszystkie SMS-y dla odbiorcy"></a></th>

                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Odbiór od nadawcy</td>
                    <td><input class="notify" name="ReceiveSenderEmail" id="ReceiveSenderEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="ReceiveSenderSMS" id="ReceiveSenderSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                    <td></td>
                    <td><input class="notify" name="ReceiveReceiverEmail" id="ReceiveReceiverEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="ReceiveReceiverSMS" id="ReceiveReceiverSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                </tr>
                <tr>
                    <td>Wydanie kurierowi<br/>Odbiór w terminalu</td>
                    <td><input class="notify" name="CourierSenderEmail" id="CourierSenderEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="CourierSenderSMS" id="CourierSenderSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                    <td></td>
                    <td><input class="notify" name="CourierReceiverEmail" id="CourierReceiverEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="CourierReceiverSMS" id="CourierReceiverSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                </tr>
                <tr>
                    <td>Awizowanie</td>
                    <td><input class="notify" name="AwizoSenderEmail" id="AwizoSenderEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="AwizoSenderSMS" id="AwizoSenderSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                    <td></td>
                    <td><input class="notify" name="AwizoReceiverEmail" id="AwizoReceiverEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="AwizoReceiverSMS" id="AwizoReceiverSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                </tr>
                <tr>
                    <td>Doręczenie</td>
                    <td><input class="notify" name="DeliverSenderEmail" id="DeliverSenderEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="DeliverSenderSMS" id="DeliverSenderSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                    <td></td>
                    <td><input class="notify" name="DeliverReceiverEmail" id="DeliverReceiverEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="DeliverReceiverSMS" id="DeliverReceiverSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                </tr>
                <tr>
                    <td>Odmowa przyjęcia</td>
                    <td><input class="notify" name="RefuseSenderEmail" id="RefuseSenderEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="RefuseSenderSMS" id="RefuseSenderSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                    <td></td>
                    <td><input class="notify" name="RefuseReceiverEmail" id="RefuseReceiverEmail" type="checkbox"></td>
                    <td><input class="notify sms" name="RefuseReceiverSMS" id="RefuseReceiverSMS"
                               type="checkbox" {% if receiverCountryCode != 'PL'  %} disabled="disabled" {% endif %} ></td>
                </tr>
                </tbody>
            </table>

        </div>
        <div class="clear"></div>

        <div class="more_info">
            <h3>Informacje dodatkowe</h3>

            <ul class="sendit_form">
                <li>
                    <label for="saleDocId">Numer dokumentu sprzedaży: </label><input name="saleDocId" id="saleDocId" type="text"
                                                                                             value="" maxlength="35">
                </li>
                <li>
                    <label for="packageContent">Zawartość przesyłki: </label><input name="packageContent" id="packageContent"
                                                                                            type="text" value="" maxlength="35">
                </li>
                <li>
                    <label for="protocol">Automatycznie generuj protokół odbioru: </label><input name="protocol" id="protocol"
                                                                                                         type="checkbox" value="">
                </li>
            </ul>
        </div>


        <div id ="services"></div>
        <div class="clear"></div>
        <div id ="summary"></div>
        <div class="clear"></div>

        <button class="button" type="button" id="checkService">Sprawdź dostępność usług</button>
        <button class="button" type="button" id="rate" disabled="disabled">Wyceń przesyłkę</button>
    </div>
    <div class="sendit_buttons">
        <button class="button" type="button" name="senditForm" id="senditForm">Zamów kuriera</button>
        <button class="button" type="button" name="senditUpdate" id="senditUpdate">Odśwież statusy</button>
    </div>
</div>
<script type="text/javascript">

    /*<![CDATA[*/

    rate = false;
    $(document).ready(function() {
        updateStatus()

        $('input.pallet').keyup(function (){
            if($(this).val() != '')
            {
                var val = parseInt($(this).val());
                if( isNaN(val))
                    val = 0;
                $(this).val(val);
                $( 'input.parcel').val(0);
            }
            $('div#summary').html('');
            $('#rate').attr("disabled","disabled");
            $('#rate').addClass('disabled');
            if ( rate )
                rate.abort();
        });
        $('input.parcel').keyup(function (){
            if($(this).val() != '')
            {
                var val = parseInt($(this).val());
                if( isNaN(val))
                    val = 0;
                $(this).val(val);
                $('input.pallet').val(0);
            }
            $('div#summary').html('');
            $('#rate').attr("disabled","disabled");
            $('#rate').addClass('disabled');
            if ( rate )
                rate.abort();
        });
        $('input.parcel, input.pallet').focusout(function (){
            if($(this).val() == '')
                $(this).val(0);
        });


        $('input.pallet, input.parcel, #senderZip, #receiver_country, #receiverZip').change(function (){
            $('#services').html('');
            $('#rate').attr('disabled','disabled');
            $('#rate').addClass('disabled');
            $('div#summary').html('');
            if ( rate )
                rate.abort();
        });
        $('#receiver_country').change(function (){
            if($(this).val() != "PL")
            {
                $('input.sms').attr('disabled','disabled').removeAttr('checked');
                $('input#palletWeight, input#palletHeight').val(0).attr('disabled','disabled');
            }
            else
            {
                $('input.sms').removeAttr('disabled');
                $('input#palletWeight, input#palletHeight').removeAttr('disabled');
            }
        });
        $('#INS').live('change',function() {
            if( $(this).is(':checked'))
            {
                $('#ins_value').show();
                $('span#ins_value input').keyup(function (){
                    if($(this).val() != '')
                    {
                        var val = parseFloat($(this).val());
                        if( isNaN(val))
                            val = 0;
                        $(this).val(val);
                    }
                    console.log($(this).val());
                });
            }
            else
            {
                $('#ins_value').hide();
            }
        });
        $('#COD').live('change',function() {
            if( $(this).is(':checked'))
            {
                $('#cod_value').show();
                $('span#cod_value input').keyup(function (){
                    if($(this).val() != '')
                    {
                        var val = parseFloat($(this).val());
                        if( isNaN(val))
                            val = 0;
                        $(this).val(val);
                    }
                    console.log($(this).val());
                });
            }
            else
            {
                $('#cod_value').hide();
            }
        });
        $('#senditForm').toggle(function(){
            $('div#form_sendit').slideDown('slow');
            $('#senditForm').text('Schowaj formularz');
        },function (){
            $('div#form_sendit').slideUp('slow');
            $('#senditForm').text('Zamów kuriera');
        });
        $('div#sendit div.error').delay(5000).hide('slow');

        $('div.sendit_buttons #senditUpdate').click(updateStatus);
        $('#checkService').click(function (){
            $('#services').html('<div id="loader"></div>');
            $('#summary').html('');
            if ( rate )
                rate.abort();
            var pallet = 0;
            if( $('#palletWeight').val() > 0 || $('#palletHeight').val() > 0)
                pallet = 1;

            xajax_checkService({
                sender_zip: $('#senderZip').val(),
                receiver_zip: $('#receiverZip').val(),
                receiver_country: $('#receiver_country').val(),
                pallet: pallet
            }, GCallback(function(oResponse) {
             if(oResponse.content != undefined) //ok
            {
                $('#services').html(oResponse.content);
                $('#rate').removeAttr('disabled');
                $('#rate').removeClass('disabled');



                $('input.parcel, input.pallet, input.notify, input.terms, input.services').change(function() {
                    $('div#summary').html('');
                    if ( rate )
                        rate.abort();
                });
            }
            else if(oResponse.faultstring != undefined)
                $('#services').html('<div class="error">'+oResponse.faultstring+'</div>');
            else
                $('#services').html('<div class="error">Błąd połączenia, spróbuj jeszcze raz</div>');
            }));

        });
        $('a.alert_email.sender').click(function (){
            if($('input#ReceiveSenderEmail').attr('checked') && $('input#CourierSenderEmail').attr('checked') && $('input#AwizoSenderEmail').attr('checked') && $('input#DeliverSenderEmail').attr('checked') && $('input#RefuseSenderEmail').attr('checked'))
            {
                $('input#ReceiveSenderEmail, input#CourierSenderEmail, input#AwizoSenderEmail, input#DeliverSenderEmail, input#RefuseSenderEmail').removeAttr('checked');
            }
            else
            {
                $('input#ReceiveSenderEmail, input#CourierSenderEmail, input#AwizoSenderEmail, input#DeliverSenderEmail, input#RefuseSenderEmail').attr('checked','checked');
            }
            return false;
        });
        $('a.alert_sms.sender').click(function (){
            if( $('#receiver_country').val() == "PL" )
            {
                if($('input#ReceiveSenderSMS').attr('checked') && $('input#CourierSenderSMS').attr('checked') && $('input#AwizoSenderSMS').attr('checked') && $('input#DeliverSenderSMS').attr('checked') && $('input#RefuseSenderSMS').attr('checked'))
                {
                    $('input#ReceiveSenderSMS, input#CourierSenderSMS, input#AwizoSenderSMS, input#DeliverSenderSMS, input#RefuseSenderSMS').removeAttr('checked');
                }
                else
                {
                    $('input#ReceiveSenderSMS, input#CourierSenderSMS, input#AwizoSenderSMS, input#DeliverSenderSMS, input#RefuseSenderSMS').attr('checked','checked');
                }
            }
            return false;
        });
        $('a.alert_email.receiver').click(function (){
            if($('input#ReceiveReceiverEmail').attr('checked') && $('input#CourierReceiverEmail').attr('checked') && $('input#AwizoReceiverEmail').attr('checked') && $('input#DeliverReceiverEmail').attr('checked') && $('input#RefuseReceiverEmail').attr('checked'))
            {
                $('input#ReceiveReceiverEmail, input#CourierReceiverEmail, input#AwizoReceiverEmail, input#DeliverReceiverEmail, input#RefuseReceiverEmail').removeAttr('checked');
            }
            else
            {
                $('input#ReceiveReceiverEmail, input#CourierReceiverEmail, input#AwizoReceiverEmail, input#DeliverReceiverEmail, input#RefuseReceiverEmail').attr('checked','checked');

            }
            return false;
        });
        $('a.alert_sms.receiver').click(function (){
            if( $('#receiver_country').val() == 'PL' )
            {
                if($('input#ReceiveReceiverSMS').attr('checked') && $('input#CourierReceiverSMS').attr('checked') && $('input#AwizoReceiverSMS').attr('checked') && $('input#DeliverReceiverSMS').attr('checked') && $('input#RefuseReceiverSMS').attr('checked'))
                {
                    $('input#ReceiveReceiverSMS, input#CourierReceiverSMS, input#AwizoReceiverSMS, input#DeliverReceiverSMS, input#RefuseReceiverSMS').removeAttr('checked');
                }
                else
                {
                    $('input#ReceiveReceiverSMS, input#CourierReceiverSMS, input#AwizoReceiverSMS, input#DeliverReceiverSMS, input#RefuseReceiverSMS').attr('checked','checked');
                }
            }
            return false;
        });

        $('#rate').click(function (){

            $('div#summary').html('<div id="loader"></div>');
            if ( rate )
                rate.abort();

            rate = xajax_rate({
                sender_postcode: $('#senderZip').val(),
                senderEmail: $('#senderEmail').val(),
                senderName: $('#senderName').val(),
                senderStreet: $('#senderStreet').val(),
                senderCity: $('#senderCity').val(),
                senderPhoneNumber: $('#senderPhone').val(),
                senderContactPerson: $('#senderPerson').val(),

                receiver_postcode: $('#receiverZip').val(),
                receiver_country: $('#receiver_country').val(),
                receiverEmail: $('#receiverEmail').val(),
                receiverName: $('#receiverName').val(),
                receiverStreet: $('#receiverStreet').val(),
                receiverCity: $('#receiverCity').val(),
                receiverPhoneNumber: $('#receiverPhone').val(),
                receiverContactPerson: $('#receiverPerson').val(),

                kPK: $('#kPK').val(),
                kP5: $('#kP5').val(),
                kP10: $('#kP10').val(),
                kP20: $('#kP20').val(),
                kP30: $('#kP30').val(),
                kP50: $('#kP50').val(),
                kP70: $('#kP70').val(),

                nstd_kPK: $('#nstd_kPK').val(),
                nstd_kP5: $('#nstd_kP5').val(),
                nstd_kP10: $('#nstd_kP10').val(),
                nstd_kP20: $('#nstd_kP20').val(),
                nstd_kP30: $('#nstd_kP30').val(),
                nstd_kP50: $('#nstd_kP50').val(),
                nstd_kP70: $('#nstd_kP70').val(),

                palletWeight: $('#palletWeight').val(),
                palletHeight: $('#palletHeight').val(),

                ReceiveSenderEmail: $('#ReceiveSenderEmail').is(':checked'),
                ReceiveSenderSMS: $('#ReceiveSenderSMS').is(':checked'),
                ReceiveReceiverEmail: $('#ReceiveReceiverEmail').is(':checked'),
                ReceiveReceiverSMS: $('#ReceiveReceiverSMS').is(':checked'),

                CourierSenderEmail: $('#CourierSenderEmail').is(':checked'),
                CourierSenderSMS: $('#CourierSenderSMS').is(':checked'),
                CourierReceiverEmail: $('#CourierReceiverEmail').is(':checked'),
                CourierReceiverSMS: $('#CourierReceiverSMS').is(':checked'),

                AwizoSenderEmail: $('#AwizoSenderEmail').is(':checked'),
                AwizoSenderSMS: $('#AwizoSenderSMS').is(':checked'),
                AwizoReceiverEmail: $('#AwizoReceiverEmail').is(':checked'),
                AwizoReceiverSMS: $('#AwizoReceiverSMS').is(':checked'),

                DeliverSenderEmail: $('#DeliverSenderEmail').is(':checked'),
                DeliverSenderSMS: $('#DeliverSenderSMS').is(':checked'),
                DeliverReceiverEmail: $('#DeliverReceiverEmail').is(':checked'),
                DeliverReceiverSMS: $('#DeliverReceiverSMS').is(':checked'),

                RefuseSenderEmail: $('#RefuseSenderEmail').is(':checked'),
                RefuseSenderSMS: $('#RefuseSenderSMS').is(':checked'),
                RefuseReceiverEmail: $('#RefuseReceiverEmail').is(':checked'),
                RefuseReceiverSMS: $('#RefuseReceiverSMS').is(':checked'),


                saleDocId: $('#saleDocId').val(),
                packageContent: $('#packageContent').val(),

                term: $('input.terms:checked').val(),
                COD: $('#COD').is(':checked'),
                INS: $('#INS').is(':checked'),
                ROD: $('#ROD').is(':checked'),
                SRE: $('#SRE').is(':checked'),
                SSE: $('#SSE').is(':checked'),
                BYH: $('#BYH').is(':checked'),
                H24: $('#H24').is(':checked'),
                cod_value: $('span#cod_value input').val(),
                ins_value: $('span#ins_value input').val()
            }, GCallback(function(oResponse) {
                if(oResponse.content != undefined)
                {
                    $('#summary').html(oResponse.content);
                    $('#summary tr.sumConfirm td button.button').click(function (){
                        confirmOrder($(this).val());
                    });
                }
                else if(oResponse.faultstring != undefined)
                    $('#summary').html('<div class="error">'+oResponse.faultstring+'</div>');
                else
                    $('#summary').html('<div class="error">Błąd połączenia, spróbuj jeszcze raz</div>');
//                console.log(oResponse);
            }));

        });

    });
    function confirmOrder(courier)
    {
        $('div#summary').html('<div id="loader"></div>');
        rate = xajax_confirmOrder({

            courier: courier,
            id_order: {{ order.order_id }},


            sender_postcode: $('#senderZip').val(),
            senderEmail: $('#senderEmail').val(),
            senderName: $('#senderName').val(),
            senderStreet: $('#senderStreet').val(),
            senderCity: $('#senderCity').val(),
            senderPhoneNumber: $('#senderPhone').val(),
            senderContactPerson: $('#senderPerson').val(),

            receiver_postcode: $('#receiverZip').val(),
            receiver_country: $('#receiver_country').val(),
            receiverEmail: $('#receiverEmail').val(),
            receiverName: $('#receiverName').val(),
            receiverStreet: $('#receiverStreet').val(),
            receiverCity: $('#receiverCity').val(),
            receiverPhoneNumber: $('#receiverPhone').val(),
            receiverContactPerson: $('#receiverPerson').val(),

            kPK: $('#kPK').val(),
            kP5: $('#kP5').val(),
            kP10: $('#kP10').val(),
            kP20: $('#kP20').val(),
            kP30: $('#kP30').val(),
            kP50: $('#kP50').val(),
            kP70: $('#kP70').val(),

            nstd_kPK: $('#nstd_kPK').val(),
            nstd_kP5: $('#nstd_kP5').val(),
            nstd_kP10: $('#nstd_kP10').val(),
            nstd_kP20: $('#nstd_kP20').val(),
            nstd_kP30: $('#nstd_kP30').val(),
            nstd_kP50: $('#nstd_kP50').val(),
            nstd_kP70: $('#nstd_kP70').val(),

            palletWeight: $('#palletWeight').val(),
            palletHeight: $('#palletHeight').val(),

            ReceiveSenderEmail: $('#ReceiveSenderEmail').is(':checked'),
            ReceiveSenderSMS: $('#ReceiveSenderSMS').is(':checked'),
            ReceiveReceiverEmail: $('#ReceiveReceiverEmail').is(':checked'),
            ReceiveReceiverSMS: $('#ReceiveReceiverSMS').is(':checked'),

            CourierSenderEmail: $('#CourierSenderEmail').is(':checked'),
            CourierSenderSMS: $('#CourierSenderSMS').is(':checked'),
            CourierReceiverEmail: $('#CourierReceiverEmail').is(':checked'),
            CourierReceiverSMS: $('#CourierReceiverSMS').is(':checked'),

            AwizoSenderEmail: $('#AwizoSenderEmail').is(':checked'),
            AwizoSenderSMS: $('#AwizoSenderSMS').is(':checked'),
            AwizoReceiverEmail: $('#AwizoReceiverEmail').is(':checked'),
            AwizoReceiverSMS: $('#AwizoReceiverSMS').is(':checked'),

            DeliverSenderEmail: $('#DeliverSenderEmail').is(':checked'),
            DeliverSenderSMS: $('#DeliverSenderSMS').is(':checked'),
            DeliverReceiverEmail: $('#DeliverReceiverEmail').is(':checked'),
            DeliverReceiverSMS: $('#DeliverReceiverSMS').is(':checked'),

            RefuseSenderEmail: $('#RefuseSenderEmail').is(':checked'),
            RefuseSenderSMS: $('#RefuseSenderSMS').is(':checked'),
            RefuseReceiverEmail: $('#RefuseReceiverEmail').is(':checked'),
            RefuseReceiverSMS: $('#RefuseReceiverSMS').is(':checked'),


            saleDocId: $('#saleDocId').val(),
            packageContent: $('#packageContent').val(),
            protocol:  $('#protocol').is(':checked'),

            term: $('input.terms:checked').val(),
            COD: $('#COD').is(':checked'),
            INS: $('#INS').is(':checked'),
            ROD: $('#ROD').is(':checked'),
            SRE: $('#SRE').is(':checked'),
            SSE: $('#SSE').is(':checked'),
            BYH: $('#BYH').is(':checked'),
            H24: $('#H24').is(':checked'),
            cod_value: $('span#cod_value input').val(),
            ins_value: $('span#ins_value input').val()
        }, GCallback(function(oResponse) {
                if(oResponse.content != undefined)
                {
                    $('#summary').html('<div class="conf">'+oResponse.content+'</div>');
                    updateStatus();
                }
                else if(oResponse.faultstring != undefined)
                    $('#summary').html('<div class="error">'+oResponse.faultstring+'</div>');
                else
                    $('#summary').html('<div class="error">Błąd połączenia, spróbuj jeszcze raz</div>');

        }));

    }
    function updateStatus()
    {
        $('table#sendit_orders.table tbody').html('<tr><td colspan="6"><div id="loader"></div></td></tr>');
        xajax_updateStatus({

            id_order: {{ order.order_id }}

        }, GCallback(function(oResponse) {
            $('table#sendit_orders.table tbody').html(oResponse.content);
            if(oResponse.count > 0 )
                $('div.sendit_buttons #senditUpdate').show();
            if(oResponse.error != undefined && oResponse.error != '')
            {
                if( $('table#sendit_orders.table').parent().find('div.error').length > 0 )
                    $('table#sendit_orders.table').parent().find('div.error').html(oResponse.error);
                else
                    $('table#sendit_orders.table').parent().prepend('<div class="error">'+oResponse.error+'</div>');

                $('div#sendit div.error').delay(5000).hide('slow');
            }


        }));

    }
    /*]]>*/

</script>

{% endblock %}