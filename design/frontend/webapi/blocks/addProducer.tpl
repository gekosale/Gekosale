<div class="page-header">
	<h1 id="addProducer">addProducer <small>dodanie producenta do sklepu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$request = Array(
	'translation' => Array(
		'1' => Array(
			'name' => 'Producent',
			'seo' => 'nowy-producent',
		)
	)
);

$response = $client->addProducer($request);

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
	<td>translation</td>
	<td>array</td>
	<td>Tablica zawierająca informacje o tłumaczeniach dotyczących producenta. Klucze główne to ID języka w sklepie<br /><br />
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
		<td>name</td>
		<td>string</td>
		<td>Nazwa producenta</td>
		</tr>
		<tr>
		<td>seo</td>
		<td>string</td>
		<td>Nazwa SEO producenta</td>
		</tr>
		</tbody>
		</table>
	</td>
	</tr>
</tbody>
</table>
    
<h2>Przykład zwracanych danych</h2>


<pre>Array
(
    [success] => 1
    [id] => 1
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
<td>success</td>
<td>int</td>
<td>Status operacji 1 oznacza poprawne dodanie producenta.</td>
</tr>
<tr>
<td>id</td>
<td>int</td>
<td>ID nowo dodanego producenta.</td>
</tr>
</tbody>
</table>