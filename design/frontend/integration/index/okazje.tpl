<?xml version="1.0" encoding="utf-8"?>
<okazje>
<offers>
{% if productList >0 %}
{% for i, v in productList %}
	<offer>
		<id>{{ productList[i].productid }}</id>
		<name><![CDATA[{{ productList[i].name }}]]></name>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<image><![CDATA[{{ productList[i].photo }}]]></image>
		<category><![CDATA[{{ productList[i].categoryname }}]]></category>
		<price><![CDATA[{{ productList[i].sellprice }}]]></price>
		<producer><![CDATA[{{ productList[i].producername }}]]></producer>
	</offer>
{% endfor %}
{% endif %}
</offers>
</okazje>