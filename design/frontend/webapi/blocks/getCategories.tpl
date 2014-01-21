<div class="page-header">
	<h1 id="getCategories">getCategories <small>pobieranie informacji o kategoriach</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$response = $client->getCategories();
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
            [id] => 750
            [parentid] => 
            [distinction] => 0
            [enable] => 1
            [photos] => Array
                (
                    [small] => {{ URL }}design/_gallery/_140_140_3/1100.jpg
                    [normal] => {{ URL }}design/_gallery/_300_300_3/1100.jpg
                    [large] => {{ URL }}design/_gallery/_600_600_3/1100.jpg
                    [orginal] => {{ URL }}design/_gallery/_orginal/1100.jpg
                )

            [translation] => Array
                (
                    [1] => Array
                        (
                            [name] => ĹťywnoĹÄ
                            [shortdescription] => <p>
	Jedzenie â pokarm, ĹźywnoĹÄ, to, co siÄ wprowadza do organizmu, by zapewniÄ mu odpowiednie substancje do dziaĹania i odbudowy (inaczej: jadĹo, pokarm); takĹźe czynnoĹÄ spoĹźywania pokarmu.</p>
                            [description] => 
                            [link] => {{ URL }}kategoria/zywnosc
                            [seo] => zywnosc
                            [keywordtitle] => 
                            [keyword] => 
                            [keyworddescription] => 
                        )

                )

        )

    [1] => Array
        (
            [id] => 751
            [parentid] => 
            [distinction] => 2
            [enable] => 1
            [photos] => Array
                (
                    [small] => {{ URL }}design/_gallery/_140_140_3/1.png
                    [normal] => {{ URL }}design/_gallery/_300_300_3/1.png
                    [large] => {{ URL }}design/_gallery/_600_600_3/1.png
                    [orginal] => {{ URL }}design/_gallery/_orginal/1.png
                )

            [translation] => Array
                (
                    [1] => Array
                        (
                            [name] => Kosmetyki
                            [shortdescription] => 
                            [description] => 
                            [link] => {{ URL }}kategoria/kosmetyki
                            [seo] => kosmetyki
                            [keywordtitle] => 
                            [keyword] => 
                            [keyworddescription] => 
                        )

                )

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
<td>ID kategorii w sklepie</td>
</tr>
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
	<tr>
	<td>photos</td>
	<td>array</td>
	<td>Tablica zawierająca informacje o zdjęciach kategorii<br /><br />
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
		<td>small</td>
		<td>string</td>
		<td>Adres najmniejszego zdjęcia</td>
		</tr>
		<tr>
		<td>normal</td>
		<td>string</td>
		<td>Adres normalnego zdjęcia</td>
		</tr>
		<tr>
		<td>large</td>
		<td>string</td>
		<td>Adres dużego zdjęcia</td>
		</tr>
		<tr>
		<td>orginal</td>
		<td>string</td>
		<td>Adres zdjęcia oryginalnego</td>
		</tr>
		</tbody>
		</table>
	</td>
	</tr>
</tbody>
</table>