<div class="page-header">
	<h1 id="getProducers">getProducers <small>pobieranie listy producentów</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$response = $client->getProducers();
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
            [id] => 2
            [translation] => Array
                (
                    [1] => Array
                        (
                            [producerid] => 2
                            [name] => Test
                            [link] => {{ URL }}producenci/test
                            [seo] => test
                            [description] => 
                            [keyword_title] => 
                            [keyword] => 
                            [keyword_description] => 
                        )

                )

            [photos] => Array
                (
                    [small] => {{ URL }}design/_gallery/_140_140_3/1.png
                    [normal] => {{ URL }}design/_gallery/_300_300_3/1.png
                    [large] => {{ URL }}design/_gallery/_600_600_3/1.png
                    [orginal] => {{ URL }}design/_gallery/_orginal/1.png
                )

        )

    [1] => Array
        (
            [id] => 52
            [translation] => Array
                (
                    [1] => Array
                        (
                            [producerid] => 52
                            [name] => Etnies
                            [link] => {{ URL }}producenci/etnies
                            [seo] => etnies
                            [description] => 
                            [keyword_title] => 
                            [keyword] => 
                            [keyword_description] => 
                        )

                )

            [photos] => Array
                (
                    [small] => {{ URL }}design/_gallery/_140_140_3/1.png
                    [normal] => {{ URL }}design/_gallery/_300_300_3/1.png
                    [large] => {{ URL }}design/_gallery/_600_600_3/1.png
                    [orginal] => {{ URL }}design/_gallery/_orginal/1.png
                )

        )

    [2] => Array
        (
            [id] => 53
            [translation] => Array
                (
                    [1] => Array
                        (
                            [producerid] => 53
                            [name] => Supra
                            [link] => {{ URL }}producenci/supra
                            [seo] => supra
                            [description] => 
                            [keyword_title] => 
                            [keyword] => 
                            [keyword_description] => 
                        )

                )

            [photos] => Array
                (
                    [small] => {{ URL }}design/_gallery/_140_140_3/1.png
                    [normal] => {{ URL }}design/_gallery/_300_300_3/1.png
                    [large] => {{ URL }}design/_gallery/_600_600_3/1.png
                    [orginal] => {{ URL }}design/_gallery/_orginal/1.png
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
	<td>ID producenta</td>
	</tr>
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
		<td>producerid</td>
		<td>int</td>
		<td>ID producenta</td>
		</tr>
		<tr>
		<td>name</td>
		<td>string</td>
		<td>Nazwa producenta</td>
		</tr>
		<tr>
		<td>link</td>
		<td>string</td>
		<td>Pełny adres www do strony producenta w sklepie</td>
		</tr>
		<tr>
		<td>seo</td>
		<td>string</td>
		<td>Nazwa SEO producenta</td>
		</tr>
		<tr>
		<td>description</td>
		<td>string</td>
		<td>Opis producenta</td>
		</tr>
		<tr>
		<td>keyword_title</td>
		<td>string</td>
		<td>Tytuł META</td>
		</tr>
		<tr>
		<td>keyword</td>
		<td>string</td>
		<td>Słowa kluczowe META</td>
		</tr>
		<tr>
		<td>keyword_description</td>
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
	<td>Tablica zawierająca informacje o zdjęciach producenta<br /><br />
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