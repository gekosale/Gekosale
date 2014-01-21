<?xml version="1.0" encoding="utf-8"?>
<offers>
{% if productList >0 %}
{% for i, v in productList %}
	<offer>
		<id>{{ productList[i].productid }}</id>
		<name><![CDATA[{{ productList[i].name }}]]></name>
		<price><![CDATA[{{ productList[i].sellprice }}]]></price>
		<category><![CDATA[{{ productList[i].categoryname }}]]></category>
		<image><![CDATA[{{ productList[i].photo }}]]></image>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<producer><![CDATA[{{ productList[i].producername }}]]></producer>
	</offer>
{% endfor %}
{% endif %}
</offers>
