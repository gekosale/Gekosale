<article class="article marginbt20">
	<div class="row-fluid row-form">
		<div class="basket-large">
			<div class="pull-right">
				<a href="#" title=""><i class="icon icon-arrow-left-blue" onclick="history.back();return false;"></i> {% trans %}TXT_BACK_TO_SHOPPING{% endtrans %}</a>
			</div>
			<h1>{% trans %}TXT_CART{% endtrans %}</h1>
			<div class="alert alert-block alert-info">
				<h4>
					<strong>{% trans %}TXT_CART_IS_EMPTY{% endtrans %}</strong>
					<br />Nie czekaj i dodaj do niego produkty z naszej oferty!
				</h4>
			</div>
		</div>
	</div>
</article>
<div id="recommendations">
	{{ recommendations(4) }}
</div>