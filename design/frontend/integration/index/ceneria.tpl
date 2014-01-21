<?xml version="1.0" encoding="UTF-8"?>
<offers>
{% if productList > 0 %}
{% for i, v in productList %}
	<offer>
			<id><![CDATA[{{ productList[i].productid }}]]></id>
			<name><![CDATA[{{ productList[i].name }}]]></name>
			<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
			<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
			<image><![CDATA[{{ productList[i].photo }}]]></image>
			<price><![CDATA[{{ productList[i].sellprice }}]]></price>
			<category><![CDATA[{{ productList[i].ceneria }}]]></category>
			<producer><![CDATA[{{ productList[i].producername }}]]></producer>
	</offer>
{% endfor %}
{% endif %}
</offers>