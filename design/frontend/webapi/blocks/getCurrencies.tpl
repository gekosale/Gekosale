<div class="page-header">
	<h1 id="getCurrencies">getCurrencies <small>pobieranie listy walut</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$response = $client->getCurrencies();
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
<td>-</td>
<td>-</td>
<td>-</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>

<pre>Array
(
    [0] => Array
        (
            [idcurrency] => 28
            [currencyname] => złoty
            [currencysymbol] => PLN
            [decimalseparator] => .
            [thousandseparator] => 
            [positivepreffix] =>  
            [positivesuffix] =>  PLN
            [negativepreffix] => -
            [negativesuffix] =>  PLN
            [decimalcount] => 2
        )

    [1] => Array
        (
            [idcurrency] => 200
            [currencyname] => euro
            [currencysymbol] => EUR
            [decimalseparator] => ,
            [thousandseparator] => 
            [positivepreffix] => EUR 
            [positivesuffix] => 
            [negativepreffix] => EUR 
            [negativesuffix] => 
            [decimalcount] => 2
        )

)
</pre>
<h2>Opis zwracanych danych</h2>
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
<td>idcurrency</td>
<td>int</td>
<td>ID waluty w sklepie</td>
</tr>
<tr>
<td>currencyname</td>
<td>string</td>
<td>Nazwa waluty</td>
</tr>
<tr>
<td>currencysymbol</td>
<td>string</td>
<td>Symbol waluty</td>
</tr>
<tr>
<td>decimalseparator</td>
<td>string</td>
<td>Separator dziesiętny</td>
</tr>
<tr>
<td>thousandseparator</td>
<td>string</td>
<td>Separator tysięcy</td>
</tr>
<tr>
<td>positivepreffix</td>
<td>string</td>
<td>Preffix dla wartości dodatnich</td>
</tr>
<tr>
<td>positivesuffix</td>
<td>string</td>
<td>Suffix dla wartości dodatnich</td>
</tr>
<tr>
<td>negativepreffix</td>
<td>string</td>
<td>Preffix dla wartości ujemnych</td>
</tr>
<tr>
<td>negativesuffix</td>
<td>string</td>
<td>Suffix dla wartości ujemnych</td>
</tr>
<tr>
<td>decimalcount</td>
<td>int</td>
<td>Ilość miejsc dziesiętnych</td>
</tr>
</tbody>
</table>