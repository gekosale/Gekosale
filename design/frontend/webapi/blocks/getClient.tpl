<div class="page-header">
	<h1 id="getClient">getClient <small>pobieranie informacji o kliencie</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$id = 1;
$response = $client->getClient($id);
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
<td>ID klienta w sklepie</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>



<pre>Array
(
    [firstname] => Jan
    [surname] => Kowalski
    [idclientaddress] => 2
    [phone] => 555666777
    [phone2] => 
    [street] => Testowa 1
    [streetno] => 19e
    [postcode] => 00-001
    [placename] => Polska
    [placeno] => 00-001
    [nip] => 
    [companyname] => 
    [email] => kowalski@wellcommerce.pl
    [countryid] => 261
    [billing_address] => Array
        (
            [idclientaddress] => 1
            [firstname] => Jan
            [surname] => Kowalski
            [companyname] => 
            [nip] => 
            [street] => Testowa 1
            [streetno] => 19e
            [placeno] => 00-001
            [placename] => Polska
            [postcode] => 00-001
            [countryid] => 261
            [clienttype] => 0
        )

    [shipping_address] => Array
        (
            [idclientaddress] => 2
            [firstname] => Jan
            [surname] => Kowalski
            [companyname] => 
            [nip] => 
            [street] => Testowa 1
            [streetno] => 19e
            [placeno] => 00-001
            [placename] => Polska
            [postcode] => 00-001
            [countryid] => 261
            [clienttype] => 0
        )

)
</pre>
<h2>Opis zwracanych danych</h2>
Informacje zostaną uzupełnione w dokumentacji do wersji 1.0.1