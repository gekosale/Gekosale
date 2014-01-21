<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE starcode SYSTEM "http://www.starcode.pl/xml/dtd/starcode_xml.dtd">
<offers>
<stat>
	<num>{{ smartbaynumberofproducts }}</num>
	<ver>2.0</ver>
</stat>
{% if productList > 0 %}
{% for i, v in productList %}
	<product>
		<id><![CDATA[{{ productList[i].productid }}]]></id>
		<name><![CDATA[{{ productList[i].name }}]]></name>
		<producer><![CDATA[{{ productList[i].producername }}]]></producer>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<price><![CDATA[{{ productList[i].sellprice }}]]></price>
		<category><![CDATA[{{ productList[i].smartbay }}]]></category>
		<image><![CDATA[{{ productList[i].photo }}]]></image>
	</product>
{% endfor %}
{% endif %}
</offers>