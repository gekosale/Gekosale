<?xml version="1.0" encoding="utf-8"?>
<radar wersja="1.0">
<oferta>
{% if productList >0 %}
{% for i, v in productList %}
	<produkt>
		<grupa1>
			<id>{{ productList[i].productid }}</id>
			<producent>{{ productList[i].producername }}</producent>
			<nazwa>{{ productList[i].name }}</nazwa>
			<opis><![CDATA[{{ productList[i].shortdescription }}]]></opis>
			<id>{{ productList[i].productid }}</id>
			<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
			<foto>{{ productList[i].photo }}</foto>
			<kategoria>{{ productList[i].categoryname }}</kategoria>
			<cena>{{ productList[i].sellprice }}</cena>
			<dostawa></dostawa>
		</grupa1>
	</produkt>
{% endfor %}
{% endif %}
</oferta>
</radar>