<?xml version="1.0" encoding="UTF-8"?>
<produkty>
{% if productList >0 %}
{% for i, v in productList %}
	<produkt>
		<id><![CDATA[{{ productList[i].productid }}]]></id>
		<kategoria><![CDATA[{{ productList[i].najtaniej24 }}]]></kategoria>
		<nazwa><![CDATA[{{ productList[i].name }}]]></nazwa>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<opis><![CDATA[{{ productList[i].shortdescription }}]]></opis>
		<zdjecie><![CDATA[{{ productList[i].photo }}]]></zdjecie>
		<cena><![CDATA[{{ productList[i].sellprice }}]]></cena>
	</produkt>
{% endfor %}
{% endif %}
</produkty>