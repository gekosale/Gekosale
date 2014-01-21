<div class="page-header">
	<h1 id="getClients">getClients <small>pobieranie identyfikatorów klientów w sklepie</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$request = Array(
	'starting_from' => 0
);
$response = $client->getClients($request);
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
<td>ID ostatniego klienta od jakiego ma być pobierana lista</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>


<pre>Array
(
    [0] => Array
        (
            [id] => 1
        )

    [1] => Array
        (
            [id] => 2
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
<td>ID klienta w sklepie do wykorzystania w metodzie <a href="#getClient">getClient</a></td>
</tr>
</tbody>
</table>