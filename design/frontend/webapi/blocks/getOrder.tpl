<div class="page-header">
	<h1 id="getOrder">getOrder <small>pobieranie informacji o zamówieniu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$id = 1;
$response = $client->getOrder($id);
$client->debug($response);
</pre>

<h2>Przyjmowane parametry</h2>
<table class="table table-bordered table-striped">
<thead>
<tr>
<th style="width: 100px;">Nazwa</th>
<th style="width: 50px;">Typ</th>
<th>Opis</th>
</tr>
</thead>
<tbody>
<tr>
<td>id</td>
<td>int</td>
<td>ID zamówienia w sklepie</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>


<pre>Array
(
    [header] => Array
        (
            [orderid] => 1
            [orderdate] => 2013-06-30 10:09:22
            [clientid] => 1
            [currencysymbol] => PLN
            [currencyrate] => 1
            [orderstatusid] => 7
            [orderstatusname] => Oczekuje na płatność
            [comments] => 
            [viewid] => 3
        )

    [client] => Array
        (
            [id] => 1
            [firstname] => Jan
            [surname] => Kowalski
            [email] => kowalski@wellcommerce.pl
            [clientgroupname] => Grupa brązowa
            [clientgroupid] => 10
        )

    [billing] => Array
        (
            [firstname] => Jan
            [surname] => Kowalski
            [city] => Polska
            [postcode] => 00-001
            [phone] => 555666777
            [street] => Testowa 1
            [streetno] => 19e
            [placeno] => 00-001
            [country] => Poland
            [companyname] => 
            [email] => kowalski@wellcommerce.pl
            [nip] => 
        )

    [shipping] => Array
        (
            [firstname] => Jan
            [surname] => Kowalski
            [city] => Polska
            [postcode] => 00-001
            [phone] => 555666777
            [street] => Testowa 1
            [streetno] => 19e
            [placeno] => 00-001
            [country] => Poland
            [companyname] => 
            [email] => kowalski@wellcommerce.pl
            [nip] => 
        )

    [products] => Array
        (
            [0] => Array
                (
                    [productid] => 683
                    [ean] => 683
                    [barcode] => 
                    [name] => Majtki wyszczuplające ELITE FORTE
                    [net_price] => 40.6504
                    [quantity] => 1.0000
                    [net_subtotal] => 40.65040000
                    [vat] => 23.00
                    [vat_value] => 9.35
                    [subtotal] => 50.00
                    [attributes] => Array
                        (
                        )

                )

        )

    [footer] => Array
        (
            [delivery] => Array
                (
                    [name] => Poczta Polska
                    [symbol] => 
                    [net_price] => 12
                    [quantity] => 1
                    [net_subtotal] => 12
                    [vat] => 23.00
                    [vat_value] => 2.76
                    [subtotal] => 14.76
                )

            [payment] => Array
                (
                    [name] => Pobranie
                    [net_price] => 10.00
                    [quantity] => 1
                    [net_subtotal] => 10.00
                    [vat] => 23.00
                    [vat_value] => 2.30
                    [subtotal] => 12.30
                )

        )

)
</pre>
<h2>Opis zwracanych danych</h2>
Informacje zostaną uzupełnione w dokumentacji do wersji 1.0.1