<div class="page-header">
	<h1 id="deleteCategory">deleteCategory <small>skasowanie kategorii ze sklepu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$id = 706;
$response = $client->deleteCategory($id);
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
<td>ID kategorii do usunięcia. <br /><br /><strong>UWAGA: Kategorie podrzędne usuwane są automatycznie podczas kasowania kategorii nadrzędnej.</strong></td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>

<pre>Array
(
    [success] => 1
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
<td>Status operacji 1 oznacza poprawne usunięcie kategorii.</td>
</tr>
</tbody>
</table>