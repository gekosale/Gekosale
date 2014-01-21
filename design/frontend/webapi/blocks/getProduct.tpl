<div class="page-header">
	<h1 id="getProduct">getProduct <small>pobieranie informacji o produkcie</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$id = 100;
$response = $client->getProduct($id);
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
<td>ID produktu istniejącego w sklepie</td>
</tr>
</tbody>
</table>
        
<h2>Przykład zwracanych danych</h2>

<pre>Array
(
    [id] => 673
    [ean] => 673
    [barcode] => 
    [delivelercode] => 
    [stock] => 36
    [weight] => 0.000
    [adddate] => 2013-07-14 17:31:04
    [editdate] => 2013-07-14 17:31:04
    [url] => {{ URL }}produkt/koszulka-supra-flytop-3
    [producer] => Array
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

    [prices] => Array
        (
            [pricenetto] => 99.00
            [price] => 121.77
            [buypricenetto] => 73.03
            [buyprice] => 89.83
            [discountpricenetto] => 99.00
            [discountprice] => 121.77
            [vatvalue] => 23.00
            [currencysymbol] => PLN
            [exchangerate] => 1.0000
        )

    [translation] => Array
        (
            [1] => Array
                (
                    [name] => Koszulka Supra Flytop 3
                    [shortdescription] => <p>
	Ekstrawagancka koszulka z nowoczesnym designem. Supra to Marka znana z ubrań cenionych przez znane gwiazdy hip-hop. W zasadzie trudno szukać podobnego modelu na rynku. Ta koszulka z pewnością pozwoli się wyróżnić</p>
                    [description] => 
                    [longdescription] => <p>
	Koszulka powinna być prana w temperaturze do 40 st. C.</p>
                    [seo] => koszulka-supra-flytop-3
                    [keywordtitle] => 
                    [keyworddescription] => 
                    [keyword] => 
                )

        )

    [photos] => Array
        (
            [small] => {{ URL }}design/_gallery/_140_140_3/1021.jpg
            [normal] => {{ URL }}design/_gallery/_300_300_3/1021.jpg
            [large] => {{ URL }}design/_gallery/_600_600_3/1021.jpg
            [orginal] => {{ URL }}design/_gallery/_orginal/1021.jpg
        )

    [attributes] => Array
        (
            [0] => Array
                (
                    [id] => 673
                    [stock] => 10
                    [ean] => 673_17
                    [vat] => 23.00
                    [barcode] => 673_17
                    [weight] => 0.000
                    [adddate] => 2012-09-11 15:14:25
                    [attributename] => L
                    [attributegroupname] => Rozmiar dolnej części bielizny
                    [attributepricenetto] => 99.00
                    [attributeprice] => 121.77
                )

            [1] => Array
                (
                    [id] => 673
                    [stock] => 6
                    [ean] => 673_18
                    [vat] => 23.00
                    [barcode] => 673_18
                    [weight] => 0.000
                    [adddate] => 2013-01-11 11:08:23
                    [attributename] => XXL
                    [attributegroupname] => Rozmiar dolnej części bielizny
                    [attributepricenetto] => 99.00
                    [attributeprice] => 121.77
                )

            [2] => Array
                (
                    [id] => 673
                    [stock] => 10
                    [ean] => 673_19
                    [vat] => 23.00
                    [barcode] => 673_19
                    [weight] => 0.000
                    [adddate] => 2012-09-11 15:14:25
                    [attributename] => S
                    [attributegroupname] => Rozmiar dolnej części bielizny
                    [attributepricenetto] => 99.00
                    [attributeprice] => 121.77
                )

            [3] => Array
                (
                    [id] => 673
                    [stock] => 10
                    [ean] => 673_20
                    [vat] => 23.00
                    [barcode] => 673_20
                    [weight] => 0.000
                    [adddate] => 2012-09-11 15:14:25
                    [attributename] => M
                    [attributegroupname] => Rozmiar dolnej części bielizny
                    [attributepricenetto] => 99.00
                    [attributeprice] => 121.77
                )

        )

    [categories] => Array
        (
            [0] => 756
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
<td>ID produktu istniejącego w sklepie</td>
</tr>
<tr>
<td>ean</td>
<td>string</td>
<td>Kod EAN produktu</td>
</tr>
<tr>
<td>barcode</td>
<td>string</td>
<td>Kod kreskowy</td>
</tr>
<tr>
<td>delivelercode</td>
<td>string</td>
<td>Kod dostawcy</td>
</tr>
<tr>
<td>stock</td>
<td>int</td>
<td>Stan magazynowy uwzgledniający też stany wariantów produktu</td>
</tr>
<tr>
<td>weight</td>
<td>float</td>
<td>Waga produktu</td>
</tr>
<tr>
<td>adddate</td>
<td>datetime</td>
<td>Data dodania produktu do sklepu w formacie Y-m-d H:i:s</td>
</tr>
<tr>
<td>editdate</td>
<td>datetime</td>
<td>Data ostatniej edycji produktu w formacie Y-m-d H:i:s</td>
</tr>
<tr>
<td>url</td>
<td>string</td>
<td>Pełen adres www do produktu w sklepie</td>
</tr>
<tr>
<td>producer</td>
<td>array</td>
<td>Tablica zawierająca informacje o producencie<br /><br />
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
</td>
</tr>
<tr>
<td>prices</td>
<td>array</td>
<td>Tablica zawierająca informacje o cenach produktu<br /><br />
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
	<td>pricenetto</td>
	<td>float</td>
	<td>Cena sprzedaży netto</td>
	</tr>
	<tr>
	<td>price</td>
	<td>float</td>
	<td>Cena sprzedaży brutto</td>
	</tr>
	<tr>
	<td>buypricenetto</td>
	<td>float</td>
	<td>Cena zakupu netto</td>
	</tr>
	<tr>
	<td>buyprice</td>
	<td>float</td>
	<td>Cena zakupu brutto</td>
	</tr>
	<tr>
	<td>discountpricenetto</td>
	<td>float</td>
	<td>Cena promocyjna netto. NULL oznacza brak promocji</td>
	</tr>
	<tr>
	<td>discountprice</td>
	<td>float</td>
	<td>Cena promocyjna brutto. NULL oznacza brak promocji</td>
	</tr>
	<tr>
	<td>vatvalue</td>
	<td>float</td>
	<td>Stawka VAT</td>
	</tr>
	<tr>
	<td>currencysymbol</td>
	<td>float</td>
	<td>Symbol waluty sprzedaży</td>
	</tr>
	<tr>
	<td>exchangerate</td>
	<td>float</td>
	<td>Kurs wymiany stosowany względem ustawienia sklepu i produktu</td>
	</tr>
	</tbody>
	</table>
</td>
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
	<td>shortdescription</td>
	<td>string</td>
	<td>Krótki opis produktu</td>
	</tr>
	<tr>
	<td>description</td>
	<td>string</td>
	<td>Długi opis produktu</td>
	</tr>
	<tr>
	<td>longdescription</td>
	<td>string</td>
	<td>Rozszerzony opis produktu</td>
	</tr>
	<tr>
	<td>seo</td>
	<td>string</td>
	<td>Nazwa SEO produktu</td>
	</tr>
	<tr>
	<td>keywordtitle</td>
	<td>string</td>
	<td>Tytuł META</td>
	</tr>
	<tr>
	<td>keyworddescription</td>
	<td>string</td>
	<td>Opis META</td>
	</tr>
	<tr>
	<td>keyword</td>
	<td>string</td>
	<td>Słowa kluczowe META</td>
	</tr>
	</tbody>
	</table>
</td>
</tr>
<tr>
<td>photos</td>
<td>array</td>
<td>Tablica zawierająca informacje o zdjęciach produktu<br /><br />
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
<tr>
<td>attributes</td>
<td>array</td>
<td>Tablica zawierająca informacje o wariantach produktu<br /><br />
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
	<td>ID wariantu w bazie</td>
	</tr>
	<tr>
	<td>stock</td>
	<td>int</td>
	<td>Stan magazynowy wariantu</td>
	</tr>
	<tr>
	<td>ean</td>
	<td>string</td>
	<td>Kod EAN wariantu</td>
	</tr>
	<tr>
	<td>vat</td>
	<td>float</td>
	<td>Stawka VAT dla ceny wariantu</td>
	</tr>
	<tr>
	<td>barcode</td>
	<td>string</td>
	<td>Kod kreskowy wariantu</td>
	</tr>
	<tr>
	<td>weight</td>
	<td>float</td>
	<td>Waga wariantu</td>
	</tr>
	<tr>
	<td>adddate</td>
	<td>datetime</td>
	<td>Data dodania wariantu w formacie Y-m-d H:i:s</td>
	</tr>
	<tr>
	<td>attributename</td>
	<td>string</td>
	<td>Wartość cechy</td>
	</tr>
	<tr>
	<td>attributegroupname</td>
	<td>string</td>
	<td>Nazwa cechy</td>
	</tr>
	<tr>
	<td>attributepricenetto</td>
	<td>float</td>
	<td>Cena netto wariantu</td>
	</tr>
	<tr>
	<td>attributeprice</td>
	<td>float</td>
	<td>Cena brutto wariantu</td>
	</tr>
	</tbody>
	</table>
</td>
</tr>
<tr>
<td>categories</td>
<td>array</td>
<td>Tablica zawierająca informacje o ID kategorii produktu</td>
</tr>
</tbody>
</table>