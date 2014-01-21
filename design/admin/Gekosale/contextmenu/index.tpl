{% if contextmenu_items %}

<ul class="context" id="contextmenu">
    <li>{{ contextmenu_title|default('Powiązane:') }} </li>
    {% for option in contextmenu_items %}
        <li><a href="{{option.link}}">{{option.label}}</a></li>
    {% endfor %}
</ul>

<script type="text/javascript">
    if($('#content > .possibilities').length > 0)
        $('#content > .possibilities').after($('#contextmenu'));
    else
        $('#content > h2').after($('#contextmenu'));
    
</script>
{% endif %}