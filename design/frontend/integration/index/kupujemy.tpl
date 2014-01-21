<?xml version="1.0" encoding="utf-8" ?>
<offers>
{% if productList >0 %}
{% for i, v in productList %}
	<offer>
		<name>{{ productList[i].name }}</name>
		<category><![CDATA[{{ productList[i].categoryname }}]]></category>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<price>{{ productList[i].sellprice }}</price>
		<image>{{ productList[i].photo }}</image>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
	</offer>
{% endfor %}
{% endif %}
</offers>