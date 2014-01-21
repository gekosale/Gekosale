<div class="page-header">
	<h1 id="getOrders">getOrders <small>pobieranie informacji o zamówieniach w sklepie</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$request = Array(
	'starting_from' => 0
);
$response = $client->getOrders($request);
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
<td>starting_from</td>
<td>int</td>
<td>ID ostatniego zamówienia od jakiego ma być pobierana lista</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>



<pre>Array
(
    [0] => Array
        (
            [id] => 1
            [date] => 2013-06-30 10:09:22
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
<td>ID zamówienia w sklepie do wykorzystania w metodzie <a href="#getOrder">getOrder</a></td>
</tr>
<tr>
<td>date</td>
<td>datetime</td>
<td>Data złożenia zamówienia w formacie Y-m-d H:i:s</td>
</tr>
</tbody>
</table>