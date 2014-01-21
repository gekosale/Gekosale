<?xml version="1.0" encoding="UTF-8"?>
<offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1">
{% if productList|length > 0 %}
{% for item in productList %}
<o id="{{ item.productid }}" url="{{ path('frontend.productcart', {"param": item.seo}) }}" price="{{ item.sellprice }}" avail="{{ item.avail }}" weight="{{ item.weight }}" stock="{{ item.stock }}">
	<cat>
		<![CDATA[{{ item.ceneo }}]]>
	</cat>
    <name>
      <![CDATA[{{ item.name }}]]>
    </name>
    <imgs>
      <main url="{{ item.photo }}"/>
    </imgs>
    <desc>
      <![CDATA[{{ item.shortdescription }}]]>
    </desc>
    <attrs>
      <a name="Producent">
        <![CDATA[{{ item.producername }}]]>
      </a>
      <a name="EAN">
        <![CDATA[{{ item.ean }}]]>
      </a>
    </attrs>    
  </o>  
{% endfor %}
{% endif %}
</offers>
