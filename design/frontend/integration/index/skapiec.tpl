<?xml version="1.0" encoding="UTF-8" ?>
<xmldata>
	<version>12.0</version>
	<header>
		<name><![CDATA[{{ SHOP_NAME }}]]></name>
		<www><![CDATA[{{ URL }}]]></www>
		<time>{{ "now"|date("Y-m-d") }}</time>
	</header>
	<category>
	{% for c, v in skapieccategories %}
		<catitem>
			<catid>{{ skapieccategories[c].catid }}</catid>
			<catname><![CDATA[{{ skapieccategories[c].catname }}]]></catname>
		</catitem>
	{% endfor %}
	</category>
	<data>
	{% if productList > 0 %}
	{% for i, v in productList %}
		<item>
			<compid><![CDATA[{{ productList[i].productid }}]]></compid>
			<vendor><![CDATA[{{ productList[i].producername }}]]></vendor>
			<name><![CDATA[{{ productList[i].name }}]]></name>
			<price>{{ productList[i].sellprice }}</price>
			<catid><![CDATA[{{ productList[i].categoryid }}]]></catid>
			<foto><![CDATA[{{ productList[i].photo }}]]></foto>
			<desclong><![CDATA[{{ productList[i].shortdescription }}]]></desclong>
			<url>{{ path('frontend.productcart', {"param": productList[i].seo }) }}</url>
		</item>
	{% endfor %}
	{% endif %}
	</data>
</xmldata>
