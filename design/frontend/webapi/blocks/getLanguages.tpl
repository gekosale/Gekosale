<div class="page-header">
	<h1 id="getLanguages">getLanguages <small>pobieranie listy języków</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$response = $client->getLanguages();
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
    [1] => Array
        (
            [id] => 1
            [flag] => pl_PL.png
            [weight] => 1
            [icon] => pl_PL.png
            [name] => Polski
            [active] => 1
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
<td>id</td>
<td>int</td>
<td>ID języka w sklepie</td>
</tr>
<tr>
<td>flag</td>
<td>string</td>
<td>Nazwa pliku z ikoną flagi dostepnego w {{ DESIGNPATH }}_images_common/icon/languages</td>
</tr>
<tr>
<td>weight</td>
<td>int</td>
<td>Kolejność języka w systemie</td>
</tr>
<tr>
<td>icon</td>
<td>string</td>
<td>Nazwa pliku z ikoną flagi dostepnego w {{ DESIGNPATH }}_images_common/icon/languages</td>
</tr>
<tr>
<td>name</td>
<td>string</td>
<td>Nazwa języka</td>
</tr>
<tr>
<td>active</td>
<td>int</td>
<td>Status aktywności języka</td>
</tr>
</tbody>
</table>