{% extends "layoutbox.tpl" %}
{% block content %}
<div id="fb-root"></div>
<script>

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/pl_PL/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

</script>
<div class="fb-like-box" data-href="{{ data.url }}" data-width="{{ data.width }}" data-height="{{ data.height }}" data-show-faces="{{ data.faces }}" data-stream="{{ data.stream }}" data-header="{{ data.header }}"></div>
{% endblock %}