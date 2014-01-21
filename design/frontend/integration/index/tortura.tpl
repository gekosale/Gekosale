<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE tortura SYSTEM "http://www.tortura.pl/integracja/tortura.dtd">
<tortura>
{% if productList >0 %}
{% for i, v in productList %}
	<produkt>
		<id>{{ productList[i].idproduct }}</id>
		<nazwa><![CDATA[{{ productList[i].name }}]]></nazwa>
		<opis><![CDATA[{{ productList[i].shortdescription }}]]></opis>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<image>{{ productList[i].photo }}</image>
		<kategoria><![CDATA[{{ productList[i].categoryname }}]]></kategoria>
		<cena>{{ productList[i].sellprice }}</cena>
		<producent><![CDATA[{{ productList[i].producername }}]]></producent>
	</produkt>
{% endfor %}
{% endif %}
</tortura>