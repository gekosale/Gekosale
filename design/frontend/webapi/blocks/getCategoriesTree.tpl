<div class="page-header">
	<h1 id="getCategoriesTree">getCategoriesTree <small>skasowanie kategorii ze sklepu</small></h1>
</div>
      
<h2>Przykład wywołania</h2>
         
<pre>
require_once 'WellCommerceClient.php';
$client = new WellCommerceClient('{{ path('frontend.webapi') }}', 'klucz_api');
$response = $client->getCategoriesTree($id);
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
            [children] => Array
                (
                    [0] => Array
                        (
                            [id] => 761
                            [children] => Array
                                (
                                )

                        )

                    [1] => Array
                        (
                            [id] => 762
                            [children] => Array
                                (
                                )

                        )

                    [2] => Array
                        (
                            [id] => 764
                            [children] => Array
                                (
                                )

                        )

                    [3] => Array
                        (
                            [id] => 765
                            [children] => Array
                                (
                                )

                        )

                    [4] => Array
                        (
                            [id] => 766
                            [children] => Array
                                (
                                )

                        )

                    [5] => Array
                        (
                            [id] => 767
                            [children] => Array
                                (
                                )

                        )

                    [6] => Array
                        (
                            [id] => 768
                            [children] => Array
                                (
                                )

                        )

                    [7] => Array
                        (
                            [id] => 769
                            [children] => Array
                                (
                                )

                        )

                    [8] => Array
                        (
                            [id] => 770
                            [children] => Array
                                (
                                )

                        )

                    [9] => Array
                        (
                            [id] => 771
                            [children] => Array
                                (
                                )

                        )

                )

        )

    [1] => Array
        (
            [id] => 751
            [children] => Array
                (
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
<td>ID kategorii</td>
</tr>
<tr>
<td>children</td>
<td>array</td>
<td>Tablica zawierająca kategorie podrzędne o takiej samej strukturze. Drzewko generowane jest rekursywnie dla wszystkich poziomów.</td>
</tr>
</tbody>
</table>