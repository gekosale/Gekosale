<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">&times;</a>
        <h3 class="modal-title"></h3>
    </div>
    <div class="modal-body"><div class="modal-image"></div></div>
    <div class="modal-footer">
        <a class="btn btn-info modal-prev"><i class="icon-arrow-left icon-white"></i> {% trans %}TXT_PREVIOUS_PHOTO{% endtrans %}</a>
        <a class="btn btn-primary modal-next">{% trans %}TXT_NEXT_PHOTO{% endtrans %} <i class="icon-arrow-right icon-white"></i></a>
    </div>
</div>