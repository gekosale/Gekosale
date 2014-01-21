<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE nokaut SYSTEM "http://www.nokaut.pl/integracja/nokaut.dtd">
<nokaut generator="Gekosale">
<offers>
{% if productList > 0 %}
{% for i, v in productList %}
	<offer>
		<id>{{ productList[i].productid }}</id>
		<name><![CDATA[{{ productList[i].name }}]]></name>
		<description><![CDATA[{{ productList[i].shortdescription }}]]></description>
		<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		<image><![CDATA[{{ productList[i].photo }}]]></image>
		<price>{{ productList[i].sellprice }}</price>
		<category><![CDATA[{{ productList[i].categoryname }}]]></category>
		<producer><![CDATA[{{ productList[i].producername }}]]></producer>
		<availability>{{ productList[i].avail }}</availability>
		<instock>{{ productList[i].stock }}</instock>
		<property name="EAN">{{ productList[i].ean }}</property>
	</offer>
{% endfor %}
{% endif %}
</offers>
</nokaut>