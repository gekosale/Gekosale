	</div>
    <footer class="footer">
        <div class="container border">
            <div class="row">
                <div class="span5">
                    {% include 'newsletter.tpl' %}
                </div>
                <div class="span7">
                    <div class="row-fluid">
                    	{% if contentcategory is not empty %}
						{% for cat in contentcategory if cat.footer == 1 %}
                        <div class="span4">
                            <h3 class="font">{{ cat.name }}</h3>
                            <ul class="nav nav-pills nav-stacked">
                            	{% if cat.children is not empty %}
								{% for subcat in cat.children if subcat.footer == 1 %}
								<li><a href="{{ subcat.link }}">{{ subcat.name }}</a></li>
								{% endfor %}
								{% endif %}
                            </ul>
                        </div>
						{% endfor %}
						{% endif %}
                        <div class="span4">
                            <h3 class="font">{% trans %}TXT_YOUR_ACCOUNT{% endtrans %}</h3>
                            <ul class="nav nav-pills nav-stacked">
                            {% if client is not empty %}
                                <li><a href="{{ path('frontend.clientsettings') }}">{% trans %}TXT_SETTINGS{% endtrans %}</a></li>
								<li><a href="{{ path('frontend.clientorder') }}">{% trans %}TXT_ORDERS{% endtrans %}</a></li>
								<li><a href="{{ path('frontend.clientaddress') }}">{% trans %}TXT_CLIENT_ADDRESS{% endtrans %}</a></li>
                            {% else %}
                            	<li><a href="{{ path('frontend.clientlogin') }}">{% trans %}TXT_LOGIN_TO_YOUR_ACCOUNT{% endtrans %}</a></li>
								<li><a href="{{ path('frontend.registration') }}">{% trans %}TXT_REGISTER{% endtrans %}</a></li>
								<li><a href="{{ path('frontend.clientorder') }}">{% trans %}TXT_ORDER_STATUS{% endtrans %}</a></li>
							{% endif %}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container copy">
            <div class="row">
                <div class="span6">
                    {{ "now"|date("Y") }} © <span>{{ SHOP_NAME }}</span> / {% trans %}TXT_ALL_RIGHT_RESERVER{% endtrans %}.
                </div>
                <div class="span6 pull-right alignright">
                    <span>{% trans %}TXT_RECOMMEND_SHOP{% endtrans %}</span>
                    <a href="http://www.facebook.com/share.php?u={{ path('frontend.home') }}&amp;title={{ SHOP_NAME }}" target="_blank"><img src="{{ ASSETSPATH }}img/footer.1.png" alt=""></a>
                </div>
                <div class="span12 aligncenter">
                    <a href="http://wellcommerce.pl/" title="Sklep internetowy WellCommerce"><img src="{{ DESIGNPATH }}_images_common/logos/wellcommerce_footer.png" title="Sklep internetowy WellCommerce" /></a>
                </div>
            </div>
        </div>
    </footer>
	<div id="basketModal" class="modal fade hide"></div>
	<div id="productModal" class="modal fade hide"></div>
	{% include 'modal_gallery.tpl' %}
	{{ footerJS }}
	{{ affirmeo }}
	{% if modulesettings.ceneo.ceneoguid != ''%}
	<script type="text/javascript" src="http://ssl.ceneo.pl/shops/v3/script.js?accountGuid={{ modulesettings.ceneo.ceneoguid }}"></script>
	{% endif %}
</body>
</html>