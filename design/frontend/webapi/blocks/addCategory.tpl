<div class="page-header">
	<h1 id="addCategory">addCategory <small>dodanie kategorii do sklepu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$request = Array(
	'parentid' => 750,
	'distinction' => 0,
	'enable' => 0,
	'translation' => Array(
		'1' => Array(
			'name' => 'Nowa kategoria 3',
			'shortdescription' => 'krótki opis',
			'description' => '<p>Opis</p>',
			'seo' => 'nowa-kategoria-3',
			'keywordtitle' => 'meta title',
			'keyword' => 'słowa kluczowe',
			'keyworddescription' => 'opis meta'
		)
	)
);

$response = $client->addCategory($request);

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
<td>parentid</td>
<td>int</td>
<td>ID kategorii nadrzędnej. Kategorie główne zwracają NULL</td>
</tr>
<tr>
<td>distinction</td>
<td>int</td>
<td>Kolejność kategorii w drzewie</td>
</tr>
<tr>
<td>enable</td>
<td>int</td>
<td>Status kategorii. 0 - wyłączona, 1 - włączona.</td>
</tr>
	<tr>
	<td>translation</td>
	<td>array</td>
	<td>Tablica zawierająca informacje o tłumaczeniach dotyczących kategorii. Klucze główne to ID języka w sklepie<br /><br />
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
		<td>Nazwa kategorii</td>
		</tr>
		<tr>
		<td>link</td>
		<td>string</td>
		<td>Pełny adres www do strony kategorii w sklepie</td>
		</tr>
		<tr>
		<td>seo</td>
		<td>string</td>
		<td>Nazwa SEO kategorii</td>
		</tr>
		<tr>
		<td>shortdescription</td>
		<td>string</td>
		<td>Krótki opis kategorii</td>
		</tr>
		<tr>
		<td>description</td>
		<td>string</td>
		<td>Opis kategorii</td>
		</tr>
		<tr>
		<td>keywordtitle</td>
		<td>string</td>
		<td>Tytuł META</td>
		</tr>
		<tr>
		<td>keyword</td>
		<td>string</td>
		<td>Słowa kluczowe META</td>
		</tr>
		<tr>
		<td>keyworddescription</td>
		<td>string</td>
		<td>Opis META</td>
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
    [id] => 771
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
<td>Status operacji 1 oznacza poprawne dodanie kategorii.</td>
</tr>
<tr>
<td>id</td>
<td>int</td>
<td>ID nowo dodanej kategorii.</td>
</tr>
</tbody>
</table>