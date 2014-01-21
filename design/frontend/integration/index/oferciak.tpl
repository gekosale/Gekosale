<?xml version="1.0" encoding="UTF-8"?>
<oferciak>
{% if productList >0 %}
{% for i, v in productList %}
	<offer>
		<id><![CDATA[{{ productList[i].productid }}]]></id>
		<name><![CDATA[{{ productList[i].name }}]]></name>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<category><![CDATA[{{ productList[i].oferciak }}]]></category>
		<producer><![CDATA[{{ productList[i].producername }}]]></producer>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<image><![CDATA[{{ productList[i].photo }}]]></image>
		<price><![CDATA[{{ productList[i].sellprice }}]]></price>
	</offer>
{% endfor %}
{% endif %}
</oferciak>