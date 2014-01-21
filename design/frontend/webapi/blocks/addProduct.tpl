<div class="page-header">
	<h1 id="addProduct">addProduct <small>dodanie produktu do sklepu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$request = Array(
    'producerid' => 65,
    'stock' => 999,
    'trackstock' => 1,
    'enable' => 1,
    'weight' => 1.25,
    'width' => 1.2,
    'height' => 0.6,
    'deepth' => 5.5,
    'ean' => 'EAN12345678',
    'delivelercode' => 'DEL12345678',
    'vat' => 23,
    'buyprice' => 100,
    'sellprice' => 199,
    'currency' => 'PLN',
    'promotion' => 1,
    'discountprice' => 159,
    'promotionstart' => '2013-12-01',
    'promotionend' => '2013-12-31',
    'translation' => Array(
        '1' => Array(
            'name' => 'Produkt Testowy',
            'seo' => 'produkt-testowy',
            'shortdescription' => 'Krótki opis',
            'description' => 'Opis',
            'longdescription' => 'Rozszerzony opis',
            'keywordtitle' => 'Tytuł meta',
            'keyword' => 'Słowa, kluczowe, produktu, testowego',
            'keyworddescription' => 'Opis meta'
        )
    ),
    'categories' => Array(
        750,
        751
    ),
    'statuses' => Array(
        5,
        16
    )
);

$response = $client->addProduct($request);

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
<td>producerid</td>
<td>int</td>
<td>ID producenta w sklepie</td>
</tr>
<tr>
<td>stock</td>
<td>int</td>
<td>Stan magazynowy</td>
</tr>
<tr>
<td>trackstock</td>
<td>int</td>
<td>Włączenie śledzenia magazynu 1/0</td>
</tr>
<tr>
<td>weight</td>
<td>float</td>
<td>Waga produktu w kg.</td>
</tr>
<tr>
<td>width</td>
<td>float</td>
<td>Szerokość produktu</td>
</tr>
<tr>
<td>height</td>
<td>float</td>
<td>Wysokość produktu</td>
</tr>
<tr>
<td>deepth</td>
<td>float</td>
<td>Głebokość produktu</td>
</tr>
<tr>
<td>deepth</td>
<td>float</td>
<td>Głebokość produktu</td>
</tr>
<tr>
<td>ean</td>
<td>string</td>
<td>Kod EAN produktu</td>
</tr>
<tr>
<td>ean</td>
<td>string</td>
<td>Kod EAN produktu</td>
</tr>
<tr>
<td>delivelercode</td>
<td>string</td>
<td>Kod dostawcy produktu</td>
</tr>
<tr>
<td>vat</td>
<td>int/float</td>
<td>Wysokość stawki VAT np. 23 lub 23.00</td>
</tr>
<tr>
<td>buyprice</td>
<td>float</td>
<td>Cena zakupu netto</td>
</tr>
<tr>
<td>sellprice</td>
<td>float</td>
<td>Cena sprzedaży netto</td>
</tr>
<tr>
<td>currency</td>
<td>string</td>
<td>Kod waluty domyślnej</td>
</tr>
<tr>
<td>promotion</td>
<td>int</td>
<td>Czy produkt posiada promocję 1/0</td>
</tr>
<tr>
<td>discountprice</td>
<td>float</td>
<td>Cena promocyjna netto</td>
</tr>
<tr>
<td>promotionstart</td>
<td>date</td>
<td>Data rozpoczęcia promocji</td>
</tr>
<tr>
<td>promotionend</td>
<td>date</td>
<td>Data zakończenia promocji</td>
</tr>
	<tr>
	<td>translation</td>
	<td>array</td>
	<td>Tablica zawierająca informacje o tłumaczeniach dotyczących produktu. Klucze główne to ID języka w sklepie<br /><br />
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
		<td>Nazwa produktu</td>
		</tr>
		<tr>
		<td>seo</td>
		<td>string</td>
		<td>Nazwa SEO produktu</td>
		</tr>
		<tr>
		<td>shortdescription</td>
		<td>string</td>
		<td>Krótki opis produktu</td>
		</tr>
		<tr>
		<td>description</td>
		<td>string</td>
		<td>Opis produktu</td>
		</tr>
		<tr>
		<td>longdescription</td>
		<td>string</td>
		<td>Rozszerzony opis produktu</td>
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
<tr>
<td>categories</td>
<td>array</td>
<td>Tablica zawierająca identyfikatory kategorii produktu</td>
</tr>
<tr>
<td>statuses</td>
<td>array</td>
<td>Tablica zawierająca identyfikatory statusów produktu</td>
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
<td>Status operacji 1 oznacza poprawne dodanie produktu.</td>
</tr>
<tr>
<td>id</td>
<td>int</td>
<td>ID nowo dodanego produktu.</td>
</tr>
</tbody>
</table>