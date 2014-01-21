var GPriceFactor = function(iType, fValue) {

    var gThis = this;

    gThis.m_iType;
    gThis.m_fValue;

    gThis.Construct = function(iType, fValue) {
        gThis.m_iType = iType;
        gThis.m_fValue = fValue;
    };

    gThis.Calculate = function(fPrice) {
        fPrice = parseFloat(fPrice);
        switch (gThis.m_iType) {
            case GPriceFactor.TYPE_PERCENTAGE:
                fPrice *= gThis.m_fValue / 100;
                break;
            case GPriceFactor.TYPE_ADD:
                fPrice += gThis.m_fValue;
                break;
            case GPriceFactor.TYPE_SUBTRACT:
                fPrice -= gThis.m_fValue;
                break;
            case GPriceFactor.TYPE_EQUALS:
                fPrice = gThis.m_fValue;
                break;
        }
        return parseFloat(fPrice).toFixed(2);
    };

    gThis.Construct(iType, fValue);

};

GPriceFactor.TYPE_PERCENTAGE = 0;
GPriceFactor.TYPE_ADD = 1;
GPriceFactor.TYPE_SUBTRACT = 2;
GPriceFactor.TYPE_EQUALS = 3;

/*
 * RELATED CATEGORIES TREE
 */

var oDefaults = {
    sName: '',
    sLabel: '',
    oClasses: {
        sFieldClass: 'field-tree',
        sFieldSpanClass: 'field',
        sPrefixClass: 'prefix',
        sSuffixClass: 'suffix',
        sFocusedClass: 'focus',
        sInvalidClass: 'invalid',
        sRequiredClass: 'required',
        sWaitingClass: 'waiting',
        sItemNameClass: 'item-name',
        sExpanderClass: 'expander',
        sExpandedClass: 'expanded',
        sActiveClass: 'active',
        sButtonClass: 'button',
        sExpandAllClass: 'expand-all',
        sRetractAllClass: 'retract-all',
        sListClass: 'selected-list'
    },
    oImages: {
        sAdd: '_images_panel/icons/buttons/add.png',
        sDelete: '_images_panel/icons/buttons/delete.png',
        sSave: '_images_panel/icons/buttons/save.png',
        sRestore: '_images_panel/icons/buttons/clean.png',
        sWaiting: '_images_panel/icons/loading/indicator.gif'
    },
    aoOptions: [],
    sDefault: '',
    aoRules: [],
    sComment: '',
    bSortable: false,
    bSelectable: true,
    bChoosable: false,
    bClickable: false,
    bDeletable: false,
    oItems: {},
    fOnClick: GCore.NULL,
    fOnSaveOrder: GCore.NULL,
    fOnAdd: GCore.NULL,
    fOnAfterAdd: GCore.NULL,
    fOnDelete: GCore.NULL,
    fOnAfterDelete: GCore.NULL,
    sActive: '',
    sAddItemPrompt: '',
    bPreventDuplicates: true
};

var GFormRelatedCategories = GCore.ExtendClass(GFormFavouriteCategories, function() {

    var gThis = this;

    gThis.m_bShown = false;
    gThis.m_jTree;
    gThis.m_jOptions;
    gThis.m_oItems;
    gThis.m_jExpandAll;
    gThis.m_jRetractAll;
    gThis.m_jFieldWrapper;
    gThis.m_jItemPlaceholder;
    gThis.m_jItemDragged;
    gThis.m_oItems = {};
    gThis.m_jList;

    gThis.OnShow = function() {
        if (!gThis.m_bShown) {
            gThis.m_bShown = true;
            var iSelected = gThis.m_jFieldWrapper.find('input').length;
            var fGetInfo = gThis.m_oOptions.fGetSelectedInfo;
            gThis.m_oOptions.fGetSelectedInfo = GCore.NULL;
            var asIds = [];
            var asShopIds = [];
            for (var i = 0; i < iSelected; i++) {
                var sId = gThis.m_jFieldWrapper.find('input').eq(i).attr('name');
                sId = sId.substring(sId.lastIndexOf('[') + 1, sId.length - 1);
                var sShopId = gThis.m_jFieldWrapper.find('input').eq(i).attr('value');
                gThis._AddToList(sId, sShopId);
                asIds.push(sId);
                asShopIds.push(sShopId);
                gThis.m_jList.find('tr.id__' + sId + ' td:first').html('<img src="' + gThis._GetImage('Waiting') + '" alt=""/>');
            }
            ;
            if (asIds.length) {
                fGetInfo({
                    id: asIds,
                    shop_id: asShopIds
                }, GCallback(gThis._OnInfoLoaded));
            }
            gThis.m_oOptions.fGetSelectedInfo = fGetInfo;
        }
    };

    gThis._AddToList = function(sId, sShopId) {
        if (gThis.m_jList.find('tr.id__' + sId).length) {
            return;
        }
        var jTr = $('<tr class="id__' + sId + '"/>');
        jTr.get(0).sAllegroId = sId;
        jTr.get(0).sShopId = sShopId;
        for (var i in gThis.m_oOptions.aoColumns) {
            var oColumn = gThis.m_oOptions.aoColumns;
            var jTd = $('<td/>');
            jTr.append(jTd);
        }
        jTd = $('<td/>');
        var jTrigger = $('<a href="#" title="' + GForm.Language.tree_deselect + '"/>');
        jTrigger.click(GEventHandler(function(eEvent) {
            var sId = $(this).closest('tr').attr('class').substr(4);
            gThis.m_jTree.find('input:checkbox[value="' + sId + '"]:checked').click();
            gThis.m_jFieldWrapper.find('input[value="' + sId + '"]').remove();
            gThis._RemoveFromList(sId);
            return false;
        }));
        jTrigger.append('<img src="' + gThis._GetImage('Delete') + '" alt="' + GForm.Language.tree_deselect + '"/>');
        jTd.append(jTrigger);
        jTr.append(jTd);
        if (gThis.m_oOptions.fGetSelectedInfo instanceof Function) {
            jTr.find('td:first').html('<img src="' + gThis._GetImage('Waiting') + '" alt=""/>');
            gThis.m_oOptions.fGetSelectedInfo({
                id: sId,
                shop_id: sShopId
            }, GCallback(gThis._OnInfoLoaded));
        }
        else {
            jTr.find('td:first').text(gThis.m_jTree.find('label:has(input:checkbox[value="' + sId + '"])').text());
        }
        gThis.m_jList.find('tbody').append(jTr);
    };

    gThis._RemoveFromList = function(sId) {
        gThis.m_jFieldWrapper.find('[name$="[' + sId + ']"]').remove();
        var jTr = gThis.m_jList.find('tr.id__' + sId);
        if (jTr.length) {
            var sShopId = jTr.get(0).sShopId;
            jTr.remove();
            if (gThis.m_jTree.find('select:first').length && (gThis.m_jTree.find('select:first').get(0).sAllegroId == sId) && (gThis.m_jTree.find('select:first option:selected').attr('value') == sShopId)) {
                gThis.m_jTree.find('select:first').closest('li').find('a:first').click();
            }
        }
    };

    gThis.Populate = function(mValue) {
        if ((mValue == undefined) || (mValue == '')) {
            mValue = [];
        }
        gThis.m_jFieldWrapper.empty();
        for (var i in mValue) {
            if (i == 'toJSON') {
                continue;
            }
            gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '[' + i + ']" value="' + mValue[i] + '"/>');
        }
        gThis.ResetExpansion();
    };

    gThis._OnClick = GEventHandler(function(eEvent) {
        gThis.m_jTree.find('.field-select').remove();
        var jLi = $(eEvent.currentTarget).closest('li');
        if (jLi.find('select').length) {
            return;
        }
        var sAllegroId = jLi.get(0).sId;
        var jSelect = $('<select/>');
        jSelect.get(0).sAllegroId = sAllegroId;
        jSelect.append('<option value="">-</option>');
        for (var i in gThis.m_oOptions.aoShopCategories) {
            var sShopId = gThis.m_jFieldWrapper.find('[name$="[' + sAllegroId + ']"]').attr('value');
            jSelect.append('<option value="' + gThis.m_oOptions.aoShopCategories[i].id + '"' + (((sShopId != undefined) && (sShopId == gThis.m_oOptions.aoShopCategories[i].id)) ? ' selected="selected"' : '') + '>' + gThis.m_oOptions.aoShopCategories[i].categoryname + '</option>');
        }
        jSelect.click(function(eEvent) {
            eEvent.stopImmediatePropagation();
        }).change(function(eEvent) {
                var sShopId = $(this).find('option:selected').attr('value');
                gThis._RemoveFromList(sAllegroId);
                if (sShopId.length) {
                    gThis.m_jFieldWrapper.append('<input type="hidden" name="' + gThis.GetName() + '[' + sAllegroId + ']" value="' + sShopId + '"/>');
                    gThis._AddToList(sAllegroId, sShopId);
                }
            });
        jLi.append($('<div class="field-select"/>').append($('<span class="field"/>').append(jSelect)));
        jSelect.GSelect();
    });

    gThis._OnSelectChange = GEventHandler(function(eEvent) {
        $(this).find('option:selected').attr('value');
    });

}, oDefaults);

/*
 * ALLEGRO PRODUCT SELECT
 */

var oDefaults = {
    sName: '',
    sLabel: '',
    oClasses: {
        sFieldClass: 'field-product-select',
        sFieldSpanClass: 'field',
        sGroupClass: 'group',
        sFocusedClass: 'focus',
        sInvalidClass: 'invalid',
        sRequiredClass: 'required',
        sWaitingClass: 'waiting',
        sFieldRepetitionClass: 'repetition',
        sWaitingClass: 'waiting',
        sItemNameClass: 'item-name',
        sExpanderClass: 'expander',
        sExpandedClass: 'expanded',
        sActiveClass: 'active',
        sButtonClass: 'button',
        sExpandAllClass: 'expand-all',
        sRetractAllClass: 'retract-all'
    },
    oImages: {
        sDeselectIcon: '_images_panel/icons/datagrid/delete.png',
        sRefreshIcon: '_images_panel/icons/datagrid/refresh.png',
        sSaveIcon: '_images_panel/icons/datagrid/save.png',
        sDescriptionIcon: '_images_panel/icons/datagrid/description.png',
        sWaiting: '_images_panel/icons/loading/indicator.gif'
    },
    aoOptions: [],
    sDefault: '',
    aoRules: [],
    sComment: '',
    fLoadProducts: GCore.NULL,
    bAdvancedEditor: false
};

var GFormAllegroProductSelect = GCore.ExtendClass(GFormProductSelect, function() {

    var gThis = this;

    gThis.m_bShown = false;

    gThis.m_fLoadProducts;
    gThis.m_fProcessProduct;
    gThis.m_jDatagrid;
    gThis.m_jSelectedDatagrid;
    gThis.m_jFormat;
    gThis.m_jAutoTitleTrigger;
    gThis.m_jDescription;
    gThis.m_jDescriptionNode;
    gThis.m_jCloseDescriptionTrigger;
    gThis.m_gDatagrid;
    gThis.m_gSelectedDatagrid;
    gThis.m_gDataProvider;
    gThis.m_bFirstLoad = true;
    gThis.m_bTinyMCEInitialized = false;
    gThis.m_sCurrentlyEditedProduct;

    gThis.m_iSelectedRowNextIndex = 0;

    gThis.GetValue = function(sRepetition) {
        if (gThis.m_jField == undefined) {
            return '';
        }
        if (sRepetition != undefined) {
            if (gThis.m_oOptions.bAdvancedEditor) {
                return oValue = {
                    id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]').val(),
                    title: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[title]"]').val(),
                    variant: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[variant]"]').val(),
                    quantity: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[quantity]"]').val(),
                    allegro_category: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_category]"]').val(),
                    allegro_min_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_min_price]"]').val(),
                    allegro_buy_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_buy_price]"]').val(),
                    allegro_start_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_start_price]"]').val()
                };
            }
            else {
                return gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '"]').val();
            }
        }
        var aValues = [];
        var jValues = gThis.m_jField.find('input');
        if (gThis.m_oOptions.bAdvancedEditor) {
            for (var i = 0; i < jValues.length / 3; i++) {
                aValues.push({
                    id: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[id]"]:eq(' + i + ')').val(),
                    title: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[title]"]:eq(' + i + ')').val(),
                    variant: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[variant]"]:eq(' + i + ')').val(),
                    quantity: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[quantity]"]:eq(' + i + ')').val(),
                    allegro_category: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_category]"]:eq(' + i + ')').val(),
                    allegro_min_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_min_price]"]:eq(' + i + ')').val(),
                    allegro_buy_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_buy_price]"]:eq(' + i + ')').val(),
                    allegro_start_price: gThis.m_jField.find('input[name="' + gThis.GetName(sRepetition) + '[allegro_start_price]"]:eq(' + i + ')').val()
                });
            }
        }
        else {
            for (var i = 0; i < jValues.length; i++) {
                aValues.push(jValues.eq(i).val());
            }
        }
        return aValues;
    };

    gThis.SetValue = function(mValue, sRepetition) {
        if (gThis.m_jField == undefined) {
            return;
        }
        for (var i in mValue) {
            if (mValue[i].title != undefined) {
                mValue[i].title = mValue[i].title.replace(/"/g, '&quot;');
            }
            if (mValue[i].description__value != undefined) {
                mValue[i].description__value = mValue[i].description__value.replace(/"/g, '&quot;');
            }
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[idproduct]" value="' + mValue[i]['idproduct'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[title]" value="' + mValue[i]['title'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[variant]" value="' + mValue[i]['variant'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[description]" value="' + mValue[i]['description__value'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[quantity]" value="' + mValue[i]['quantity'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[allegro_category]" value="' + mValue[i]['allegro_category'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[allegro_min_price]" value="' + mValue[i]['allegro_min_price'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[allegro_buy_price]" value="' + mValue[i]['allegro_buy_price'] + '"/>');
            gThis.m_jField.append('<input type="hidden" name="' + gThis.GetName(i) + '[allegro_start_price]" value="' + mValue[i]['allegro_start_price'] + '"/>');
        }
    };

    gThis._OnSelect = function(gDg, sId) {

        var selected = gThis.m_gSelectedDatagrid.m_iTotalRows;
        if(selected > 24){
            GError('Możesz wystawić jednocześnie tylko 25 produktów.');
            return false;
        }else{
            var oSelectedRow = GCore.Duplicate(gDg.GetRow(sId));
            oSelectedRow.quantity = 1;
            oSelectedRow.variant = '';
            oSelectedRow.description__value = gThis.m_oOptions.sDefaultDescriptionFormat;
            oSelectedRow.id = gThis.m_iSelectedRowNextIndex++;
            gDg.ClearSelection();
            gThis.m_gSelectedDatagrid.WaitingScreenShow(function(eEvent) {
            });
            gThis._PrepareRow(oSelectedRow, GCallback(GEventHandler(function(eEvent) {
                gThis.m_gDataProvider.AddRow(eEvent.row);
                gThis.m_gSelectedDatagrid.LoadData();
            })));
            GMessage('Dodano wybrany produkt do listy aukcji.');
            $.scrollTo($('.GF-instance-1'), {axis: 'y'});
        }
    };

    gThis._PrepareRow = function(oRow, sCallback) {
        eval('var aoVariantOptions = ' + oRow.variant__options + ';');
        if ((aoVariantOptions.length > 1) || ((aoVariantOptions.length == 1) && (aoVariantOptions[0].id != ''))) {
            if ((oRow.variant == undefined) || (oRow.variant == '')) {
                oRow.variant = aoVariantOptions[0].id;
            }
            gThis._UpdateVariantPrice(oRow);
        }
        eval('var aoCategoryOptions = ' + oRow.allegro_category__options + ';');
        if ((aoCategoryOptions.length > 1) || ((aoCategoryOptions.length == 1) && (aoCategoryOptions[0].id != ''))) {
            oRow.allegro_category = {
                id: aoCategoryOptions[0].id,
                caption: aoCategoryOptions[0].caption
            };
        }
        else {
            oRow.allegro_category = {
                id: '',
                caption: ''
            }
        }
        gThis._UpdateAllegroPrices(oRow);
        gThis.m_oOptions.fPrepareRow({
            row: oRow,
            title_format: gThis.m_jFormat.val(),
            description_format: gThis.m_oOptions.sDefaultDescriptionFormat
        }, sCallback);
    };

    gThis._OnDeselect = function(gDg, sId) {
        if (gThis.m_bRepeatable) {
            //reset
            $('#confirm_allegro #_' + sId).remove();
            //...
            gThis.m_gDataProvider.DeleteRow(sId);
            gThis.m_gSelectedDatagrid.LoadData();
        }
        else {
            gThis.SetValue('');
        }
    };

    gThis._OnChange = GEventHandler(function(eEvent) {
        gThis.m_jField.empty();
        var aoData = [];
        for (var i in eEvent.rows) {
            aoData.push({
                id: eEvent.rows[i].id,
                idproduct: eEvent.rows[i].idproduct,
                title: eEvent.rows[i].title,
                description__value: eEvent.rows[i].description__value,
                variant: eEvent.rows[i].variant,
                quantity: eEvent.rows[i].quantity,
                stock: eEvent.rows[i].stock,
                allegro_category: eEvent.rows[i].allegro_category.id,
                allegro_min_price: eEvent.rows[i].allegro_min_price,
                allegro_buy_price: eEvent.rows[i].allegro_buy_price,
                allegro_start_price: eEvent.rows[i].allegro_start_price
            });
        }
        gThis.SetValue(aoData);
    });

    gThis._OnRowChange = GEventHandler(function(eEvent) {
        if (eEvent.modified_row.variant != eEvent.previous_row.variant) {
            gThis._UpdateVariantPrice(eEvent.modified_row);
            gThis._UpdateAllegroPrices(eEvent.modified_row);

        }
        if(eEvent.modified_row.title.length > 50){
            GError('Tytuł aukcji nie może być dłuższy niż 50 znaków','Tytuł został automatycznie skrócony do długości dopuszczalnej przez Allegro');
            gThis._UpdateTitle(eEvent.modified_row);
        }
    });

    gThis._OnRowAdd = GEventHandler(function(eEvent) {
    });

    gThis._UpdateTitle = function(oRow) {
        oRow.title = oRow.title.substr(0,50);
        gThis.m_gSelectedDatagrid.LoadData();
    };

    gThis._UpdateVariantPrice = function(oRow) {
        eval('var oVariantData = ' + oRow.variant__data + ';');
        oRow.sellprice = oVariantData[oRow.variant].sellprice;
        oRow.sellprice_gross = oVariantData[oRow.variant].sellprice_gross;
        oRow.stock = oVariantData[oRow.variant].stock;
        return oRow;
    };

    gThis._UpdateAllegroPrices = function(oRow) {
        oRow.allegro_min_price = gThis.m_oOptions.oMinPriceFactor.Calculate(oRow['sellprice_gross']);
        oRow.allegro_start_price = gThis.m_oOptions.oStartPriceFactor.Calculate(oRow['sellprice_gross']);
        oRow.allegro_buy_price = gThis.m_oOptions.oBuyPriceFactor.Calculate(oRow['sellprice_gross']);
    };

    gThis._UpdateAllegroTitles = function() {
        var sFormat = gThis.m_jFormat.val();
        gThis.m_gSelectedDatagrid.WaitingScreenShow(function() {
            var oData = gThis.m_gDataProvider.GetData();
            gThis.m_oOptions.fProcessTitles({
                data: oData,
                format: sFormat,
                field: 'title'
            }, GCallback(GEventHandler(function(eEvent) {
                gThis.m_gDataProvider.ChangeData(eEvent.data);
                gThis._OnChange({
                    rows: eEvent.data
                });
                gThis.m_gSelectedDatagrid.LoadData();
            })));
        });
    };

    gThis._UpdateAllegroTitle = function(oRow) {
        var sFormat = gThis.m_jFormat.val();
        gThis.m_oOptions.fProcessTitles({
            data: [oRow],
            format: sFormat,
            field: 'title'
        }, GCallback(function(eEvent) {
            oRow.title = eEvent.data[0].title;
            gThis.m_gSelectedDatagrid.LoadData();
        }));
    };

    gThis._OnDescriptionEditorHide = function(gDg, sRowId) {
        var oRow = gThis.m_gDataProvider.GetRow(sRowId);
        oRow.description__value = gThis.m_jDescription.html();
        gThis.m_gDataProvider.UpdateRow(sRowId, oRow);
        gThis.m_jDescriptionNode.slideUp(100);
    };

    gThis.EditDescription = function(sRowId) {
        var oRow = gThis.m_gDataProvider.GetRow(sRowId);
        gThis.m_gSelectedDatagrid.ClearSelection();
        gThis.m_gSelectedDatagrid.SelectRow(sRowId);
        gThis.m_jDescriptionNode.slideDown(100, function() {
            if (!gThis.m_bTinyMCEInitialized) {
                gThis.m_bTinyMCEInitialized = true;
                gThis.m_jDescription.val(oRow.description__value).tinymce(GFormRichTextEditor.GetOptions());
            }
            else {
                gThis.m_jDescription.tinymce().setContent(oRow.description__value);
            }
        });
    };

    gThis._PrepareNode = function() {
        gThis.m_oOptions.oParsedFilterData = {};
        for (var i in gThis.m_oOptions.oFilterData) {
            $.globalEval('var oParsed = [' + gThis.m_oOptions.oFilterData[i] + '];');
            gThis.m_oOptions.oParsedFilterData[i] = $.extend({}, oParsed);
        }
        //console.log(gThis.m_oOptions.oParsedFilterData);

        gThis.m_jNode = $('<div/>').addClass(gThis._GetClass('Field'));

        gThis.m_jField = $('<div/>');
        gThis.m_jNode.append(gThis.m_jField);

        var jLabel = $('<label/>');
        jLabel.text(GForm.Language.allegro_product_select_products_list + ':');
        gThis.m_jDatagrid = $('<div/>');
        gThis.m_jNode.append(jLabel);
        gThis.m_jNode.append(gThis.m_jDatagrid);

        jLabel = $('<label/>');
        jLabel.text(GForm.Language.allegro_product_select_title_format + ':');
        //console.log(gThis.m_oOptions);
        gThis.m_jFormat = $('<input type="text" value="' + gThis.m_oOptions.sDefaultTitleFormat.replace('"', '\"') + '"/>').css({
            width: '600px'
        });
        gThis.m_jAutoTitleTrigger = $('<img src="' + gThis._GetImage('RefreshIcon') + '" alt="' + GForm.Language.allegro_product_select_autotitle + '" title="' + GForm.Language.allegro_product_select_autotitle + '"/>').css({
            cursor: 'pointer'
        });
        gThis.m_jNode.append($('<div class="field-text"/>').append(jLabel).append($('<span class="repetition"/>').append($('<span class="field"/>').append(gThis.m_jFormat)).append($('<span class="suffix"/>').append(gThis.m_jAutoTitleTrigger))));

        jLabel = $('<label/>');
        jLabel.text(GForm.Language.allegro_product_select_product_description + ':');
        gThis.m_jDescription = $('<textarea/>');
        gThis.m_jCloseDescriptionTrigger = $('<img src="' + gThis._GetImage('SaveIcon') + '" alt="' + GForm.Language.allegro_product_select_save_description + '" title="' + GForm.Language.allegro_product_select_save_description + '"/>').css({
            cursor: 'pointer'
        });
        gThis.m_jDescriptionNode = $('<div class="field-rich-text-editor"/>').css({
            display: 'none'
        });
        gThis.m_jNode.append(gThis.m_jDescriptionNode.append(jLabel).append($('<span class="repetition"/>').append($('<span class="field"/>').append(gThis.m_jDescription)).append($('<span class="suffix"/>').append(gThis.m_jCloseDescriptionTrigger))));

        jLabel = $('<label/>');
        jLabel.text(GForm.Language.allegro_product_select_selected_products + ':');
        gThis.m_jSelectedDatagrid = $('<div/>');
        gThis.m_jNode.append(jLabel);
        gThis.m_jNode.append(gThis.m_jSelectedDatagrid);
    };

    gThis.OnReset = function() {
        gThis.m_bFirstLoad = true;
    };

    gThis.Populate = function(mValue) {
    };

    gThis._UpdateDatagridSelection = function(mValue) {
        if (!(mValue instanceof Array)) {
            if ((mValue == undefined) || !mValue.length) {
                mValue = [];
            }
            else {
                mValue = [mValue];
            }
        }
        if (gThis.m_gDatagrid) {
            gThis.m_gDatagrid.m_asSelected = [];
            for (var i = 0; i < mValue.length; i++) {
                if (gThis.m_oOptions.bAdvancedEditor) {
                    gThis.m_gDatagrid.m_asSelected[i] = mValue[i].id;
                }
                else {
                    gThis.m_gDatagrid.m_asSelected[i] = mValue[i];
                }
            }
        }
    };

    gThis.OnShow = function() {
        if (gThis.m_bShown) {
            return;
        }
        gThis.m_bShown = true;
        gThis._InitDatagrid();
        gThis._InitSelectedDatagrid();
        gThis.Populate(gThis.m_oOptions.asDefaults);
    };

    gThis._OnLoaded = function(gDg) {
        gDg.m_jBody.find('img.edit_description').bind('click', gThis._OnEditDescriptionClick);
        gDg.m_jBody.find('.category-selector .selected').bind('mousedown', gThis._OnExpandCategorySelector).bind('click', GDoNothing);
        gDg.m_jBody.find('.params-selector .selected').bind('mousedown', gThis._OnExpandParamsSelector).bind('click', GDoNothing);
    };

    gThis._InitializeEvents = function() {
        gThis.m_jAutoTitleTrigger.click(GEventHandler(function(eEvent) {
            gThis._UpdateAllegroTitles();
            return false;
        }));
        gThis.m_jCloseDescriptionTrigger.click(GEventHandler(function(eEvent) {
            var asSelected = gThis.m_gSelectedDatagrid.GetSelected();
            gThis._OnDescriptionEditorHide(gThis.m_gSelectedDatagrid, asSelected[0]);
            gThis.m_gSelectedDatagrid.ClearSelection();
            return false;
        }));
    };

    gThis._ProcessProduct = function(oProduct) {
        return oProduct;
    };

    gThis._ProcessSelectedProduct = function(oProduct) {
        oProduct = gThis.m_fProcessProduct(oProduct);
        oProduct.description = '<img class="clickable edit_description" src="' + gThis._GetImage('DescriptionIcon') + '" alt="' + GForm.Language.allegro_product_select_edit_description + '" title="' + GForm.Language.allegro_product_select_edit_description + '"/>';
        oProduct.allegro_category = gThis._PrepareCategorySelector(oProduct, 'allegro_category').html();
        oProduct.allegro_params = gThis._PrepareParamsSelector(oProduct, 'allegro_params').html();
        return oProduct;
    };

    gThis._PrepareCategorySelector = function(oProduct, sColumn) {
        var jSelectorNode = $('<div/>');
        var jSelector = $('<div class="category-selector full-width"/>');
        jSelector.append('<div class="selected"><span>' + oProduct.allegro_category.caption + '</span></div>');
        return jSelectorNode.append(jSelector);
    };

    gThis._PrepareParamsSelector = function(oProduct, sColumn) {
        var jSelectorNode = $('<div/>');
        var jSelector = $('<div class="params-selector full-width"/>');
        jSelector.append('<div class="selected"><span>Parametry <u></u></span></div>');
        return jSelectorNode.append(jSelector);
    };

    gThis._OnExpandCategorySelector = GEventHandler(function(eEvent) {
        $('.allegro-category-selector').remove();
        var jExpander = $(this);
        jExpander.parent().addClass('focus');
        var sId = jExpander.closest('tr').find('.GF_Datagrid_Row_Id').text();
        gThis.m_sCurrentlyEditedProduct = sId;

        var oProduct = gThis.m_gSelectedDatagrid.GetRow(sId);
        var jList = $('<div class="allegro-category-selector"/>');
        jList.get(0).m_jExpander = jExpander;
        var jTabsUl = $('<ul/>');
        jTabsUl.append('<li><a href="#allegro-category-selector-related">' + GForm.Language.allegro_product_select_related_categories + '</a></li>');
        jTabsUl.append('<li><a href="#allegro-category-selector-favourite">' + GForm.Language.allegro_product_select_favourite_categories + '</a></li>');
        jTabsUl.append('<li><a href="#allegro-category-selector-all">' + GForm.Language.allegro_product_select_all_categories + '</a></li>');
        jList.append(jTabsUl);

        var jTabRelated = $('<div id="allegro-category-selector-related"/>');
        var jUl = $('<ul/>');
        eval('var aoOptions = ' + oProduct['allegro_category__options'] + ';');

        if ((aoOptions.length > 1) || ((aoOptions.length == 1) && (aoOptions[0].id != ''))) {
            for (var i in aoOptions) {
                var jA = $('<a href="#">' + aoOptions[i].path + '</a>');
                var jLi = $('<li><input type="hidden" value="' + aoOptions[i].id + '"/></li>').append(jA);
                if (oProduct.allegro_category.id == aoOptions[i].id) {
                    jLi.addClass('selected');
                }
                jA.bind('click', {
                    category: aoOptions[i]
                }, GEventHandler(function(eEvent) {
                    jExpander.parent().removeClass('focus');
                    jList.remove();
                    jExpander.find('span').text(eEvent.data.category.caption);
                    oProduct.allegro_category.id = eEvent.data.category.id;
                    oProduct.allegro_category.caption = eEvent.data.category.caption;
                    gThis.m_gDataProvider.UpdateRow(sId, oProduct);
                    GOverlay.RemoveAll();
                    //init
                    jExpander.closest('tr').find('.params-selector .selected').mousedown().click();
                    return false;
                }));
                jUl.append(jLi);
            }
        }else{
            var jA = $('<a href="'+ GCore.sAdminUrl + 'allegrocategories' +'" target="_blank">Ustaw najpierw powiązane kategorie Allegro</a>');
            var jLi = $('<li></li>').append(jA);
            jUl.append(jLi);
        }
        jTabRelated.append(jUl);
        jList.append(jTabRelated);

        var jTabFavourite = $('<div id="allegro-category-selector-favourite"/>');
        jUl = $('<ul/>');
        if ((gThis.m_oOptions.aoFavouriteCategories.length > 1) || ((gThis.m_oOptions.aoFavouriteCategories.length == 1) && (gThis.m_oOptions.aoFavouriteCategories[0].id != ''))) {
            for (var i in gThis.m_oOptions.aoFavouriteCategories) {
                var jA = $('<a href="#">' + gThis.m_oOptions.aoFavouriteCategories[i].path + '</a>');
                var jLi = $('<li><input type="hidden" value="' + gThis.m_oOptions.aoFavouriteCategories[i].id + '"/></li>').append(jA);
                if (oProduct.allegro_category.id == gThis.m_oOptions.aoFavouriteCategories[i].id) {
                    jLi.addClass('selected');
                }
                jA.bind('click', {
                    category: gThis.m_oOptions.aoFavouriteCategories[i]
                }, GEventHandler(function(eEvent) {
                    jExpander.parent().removeClass('focus');
                    jList.remove();
                    jExpander.find('span').text(eEvent.data.category.caption);
                    oProduct.allegro_category.id = eEvent.data.category.id;
                    oProduct.allegro_category.caption = eEvent.data.category.caption;
                    gThis.m_gDataProvider.UpdateRow(sId, oProduct);
                    GOverlay.RemoveAll();
                    jExpander.closest('tr').find('.params-selector .selected').mousedown().click();
                    return false;
                }));
                jUl.append(jLi);
            }
        }else{
            var jA = $('<a href="'+ GCore.sAdminUrl + 'allegrocategories' +'" target="_blank">Ustaw najpierw ulubione kategorie Allegro</a>');
            var jLi = $('<li></li>').append(jA);
            jUl.append(jLi);
        }
        jTabFavourite.append(jUl);
        jList.append(jTabFavourite);

        var jTabAll = $('<div id="allegro-category-selector-all" class="GForm"/>');
        jTabAll.append(gThis._PrepareTreeNode());
        jList.append(jTabAll);

        $('body:eq(0)').append(jList);

        jList.tabs();

        var iTab = jList.find('.ui-tabs-panel').index(jList.find('.ui-tabs-panel:has(.selected)'));
        if (iTab == -1) {
            jList.tabs('select', 2);
        }
        else {
            jList.tabs('select', iTab);
        }


        jList.css({
            left: $(this).offset().left - 1,
            top: $(this).offset().top + 20,
            display: 'none'
        });


        jList.GShadow().GOverlay({
            fClick: function(eEvent) {
                jExpander.parent().removeClass('focus');
                jList.remove();
                //jExpander.closest('tr').find('.params-selector .selected').mousedown().click();
            }
        });

        jList.fadeIn(300);

        eEvent.stopImmediatePropagation();
        return false;
    });

    var allegroParamsCache = new Array();

    var allegroParamsChange = function(sId, fieldType) {
        $(document).undelegate('#add_allegro_params ' + fieldType, 'change');
        $(document).delegate('#add_allegro_params ' + fieldType, 'change', function() {
            var sync = function($this, value) {
                var label = $this.attr('name') + '-' + sId;
                if ($('#confirm_allegro #_' + sId + ' #' + label).length == 0) {
                    $('#confirm_allegro #_' + sId).append('<input type="hidden" id="' + label + '" name="' + label + '" value="' + value + '" />');
                }
                else {
                    $('#confirm_allegro #_' + sId + ' #' + label).val(value);
                }
            };
            switch (fieldType) {
                case 'input':
                    sync($(this), $(this).val());
                    break;
                case 'textarea':
                    sync($(this), $(this).val());
                    break;
                case 'select':
                    sync($(this), $('option:selected', this).val());
                    break;
                default:
                    break;
            }
        });
    };

    gThis._OnExpandParamsSelector = GEventHandler(function(eEvent) {
        $('.allegro-params-selector').remove();

        var jExpander = $(this);
        jExpander.parent().addClass('focus');

        var sId = jExpander.closest('tr').find('.GF_Datagrid_Row_Id').text();
        gThis.m_sCurrentlyEditedProduct = sId;

        var jTabParams;
        var oProduct = gThis.m_gSelectedDatagrid.GetRow(sId);
        var jList = $('<div class="allegro-params-selector"/>');
        jList.get(0).m_jExpander = jExpander;

        if (undefined === allegroParamsCache[sId] ||
            (undefined !== allegroParamsCache[sId] && undefined === allegroParamsCache[sId][oProduct.allegro_category.id])) {

            jTabParams = $('<div id="allegro-params-selector" class="GForm"/>');
            jTabParams.GSelectStop = false;
            $('<div onclick="xajax_allegroParams(' + oProduct.allegro_category.id + ')"/>').click().remove();
            allegroParamsCache[sId] = new Array();
            allegroParamsCache[sId][oProduct.allegro_category.id] = jTabParams;
            $(document).delegate('#allegro-params-selector', 'mouseover', function(){
                if (false === jTabParams.GSelectStop) {
                    jTabParams.GSelectStop = true;
                    jTabParams.find('select').GSelect();
                }
            });
            //reset...
            $('#confirm_allegro #_' + sId).remove();
            $('#confirm_allegro').append('<div id="_' + sId + '"/>');
        }
        else {
            //cache...
            jTabParams = allegroParamsCache[sId][oProduct.allegro_category.id];
        }
        allegroParamsChange(sId, 'input');
        allegroParamsChange(sId, 'textarea');
        allegroParamsChange(sId, 'select');
        jList.append(jTabParams);

        $('body:eq(0)').append(jList);

        jList.tabs();

        var iTab = jList.find('.ui-tabs-panel').index(jList.find('.ui-tabs-panel:has(.selected)'));
        if (iTab == -1) {
            jList.tabs('select', 2);
        }
        else {
            jList.tabs('select', iTab);
        }

        jList.css({
            left: $(this).offset().left - 1,
            top: $(this).offset().top + 20,
            display: 'none'
        });

        if (undefined === allegroParamsCache[sId] ||
            (undefined !== allegroParamsCache[sId] && undefined === allegroParamsCache[sId][oProduct.allegro_category.id])) {

            jList.append('<div id="allegroParamsLoader" style="position:absolute;width:100%;height:100%;top:0;left:0;background:rgba(255,255,255,0.5) url(\'design/_images_panel/icons/loading/indicator.gif\') center center;z-index:99999;" />');
        }

        jList.GShadow().GOverlay({
            fClick: function(eEvent) {
                jExpander.parent().removeClass('focus');
                jList.detach();
            }
        });

        jList.fadeIn(300);

        eEvent.stopImmediatePropagation();
        return false;
    });

    gThis._OnEditDescriptionClick = GEventHandler(function(eEvent) {
        gThis.EditDescription($(this).closest('tr').find('.GF_Datagrid_Row_Id').text());
        eEvent.stopImmediatePropagation();
        return false;
    });

    gThis._InitColumns = function() {

        var column_id = new GF_Datagrid_Column({
            id: 'idproduct',
            caption: GForm.Language.product_select_id,
            appearance: {
                width: 40
            },
            filter: {
                type: GF_Datagrid.FILTER_BETWEEN
            }
        });

        var column_name = new GF_Datagrid_Column({
            id: 'name',
            caption: GForm.Language.product_select_name,
            appearance: {
                align: GF_Datagrid.ALIGN_LEFT,
                width: GF_Datagrid.WIDTH_AUTO
            },
            filter: {
                type: GF_Datagrid.FILTER_INPUT
            }
        });

        var column_price = new GF_Datagrid_Column({
            id: 'sellprice',
            caption: GForm.Language.allegro_product_select_shop_net_price,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            },
            filter: {
                type: GF_Datagrid.FILTER_BETWEEN
            }
        });

        var column_price_gross = new GF_Datagrid_Column({
            id: 'sellprice_gross',
            caption: GForm.Language.allegro_product_select_shop_gross_price,
            appearance: {
                width: 70,
                align: GF_Datagrid.ALIGN_RIGHT
            },
            filter: {
                type: GF_Datagrid.FILTER_BETWEEN
            }
        });

        var column_buyprice = new GF_Datagrid_Column({
            id: 'buyprice',
            caption: GForm.Language.allegro_product_select_shop_net_buyprice,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            },
            filter: {
                type: GF_Datagrid.FILTER_BETWEEN
            }
        });

        var column_buyprice_gross = new GF_Datagrid_Column({
            id: 'buyprice_gross',
            caption: GForm.Language.allegro_product_select_shop_gross_buyprice,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            },
            filter: {
                type: GF_Datagrid.FILTER_BETWEEN
            }
        });

        var column_barcode = new GF_Datagrid_Column({
            id: 'barcode',
            caption: GForm.Language.product_select_barcode,
            appearance: {
                width: 150,
                visible: false
            },
            filter: {
                type: GF_Datagrid.FILTER_INPUT
            }
        });

        var column_producer = new GF_Datagrid_Column({
            id: 'producer',
            caption: GForm.Language.product_select_producer,
            appearance: {
                width: 150
            },
            filter: {
                type: GF_Datagrid.FILTER_SELECT,
                options: gThis.m_oOptions.oParsedFilterData['producer'],
            }
        });

        var column_vat = new GF_Datagrid_Column({
            id: 'vat',
            caption: GForm.Language.product_select_vat,
            appearance: {
                width: 60,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_category = new GF_Datagrid_Column({
            id: 'categoriesname',
            caption: GForm.Language.product_select_categories,
            appearance: {
                width: 200,
                align: GF_Datagrid.ALIGN_LEFT
            },
            filter: {
                type: GF_Datagrid.FILTER_TREE,
                filtered_column: 'ancestorcategoryid',
                options: gThis.m_oOptions.oParsedFilterData['categoryid'],
                load_children: gThis.m_oOptions.fLoadCategoryChildren
            }
        });

        return [
            column_id,
            column_name,
            column_category,
            column_producer,
            column_price,
            column_price_gross,
            column_buyprice,
            column_buyprice_gross,
            column_barcode,
            column_vat,
        ];

    };

    gThis._InitAdvancedColumns = function() {

        var column_id = new GF_Datagrid_Column({
            id: 'id',
            caption: GForm.Language.product_select_id,
            appearance: {
                visible: false,
                width: 40
            }
        });

        var column_idproduct = new GF_Datagrid_Column({
            id: 'idproduct',
            caption: GForm.Language.allegro_product_select_product_id,
            appearance: {
                visible: false,
                width: 40
            }
        });

        var column_name = new GF_Datagrid_Column({
            id: 'name',
            caption: GForm.Language.product_select_name,
            appearance: {
                align: GF_Datagrid.ALIGN_LEFT,
                width: 150,
                visible: false
            }
        });

        var column_title = new GF_Datagrid_Column({
            id: 'title',
            caption: GForm.Language.allegro_product_select_title,
            editable: true,
            appearance: {
                align: GF_Datagrid.ALIGN_LEFT,
                width: 250
            }
        });

        var column_price = new GF_Datagrid_Column({
            id: 'sellprice',
            caption: GForm.Language.allegro_product_select_shop_net_price,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_price_gross = new GF_Datagrid_Column({
            id: 'sellprice_gross',
            caption: GForm.Language.allegro_product_select_shop_gross_price,
            appearance: {
                width: 70,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_buyprice = new GF_Datagrid_Column({
            id: 'buyprice',
            caption: GForm.Language.allegro_product_select_shop_net_buyprice,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_buyprice_gross = new GF_Datagrid_Column({
            id: 'buyprice_gross',
            caption: GForm.Language.allegro_product_select_shop_gross_buyprice,
            appearance: {
                width: 70,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_barcode = new GF_Datagrid_Column({
            id: 'barcode',
            caption: GForm.Language.product_select_barcode,
            appearance: {
                width: 150,
                visible: false
            }
        });

        var column_producer = new GF_Datagrid_Column({
            id: 'producer',
            caption: GForm.Language.product_select_producer,
            appearance: {
                width: 150,
                visible: false
            }
        });

        var column_vat = new GF_Datagrid_Column({
            id: 'vat',
            caption: GForm.Language.product_select_vat,
            appearance: {
                width: 60,
                visible: false,
                align: GF_Datagrid.ALIGN_RIGHT
            }
        });

        var column_category = new GF_Datagrid_Column({
            id: 'categoriesname',
            caption: GForm.Language.product_select_categories,
            appearance: {
                width: 200,
                align: GF_Datagrid.ALIGN_LEFT,
                visible: false
            }
        });

        var column_stock = new GF_Datagrid_Column({
            id: 'stock',
            caption: 'Magazyn',
            appearance: {
                width: 40
            }
        });

        var column_quantity = new GF_Datagrid_Column({
            id: 'quantity',
            caption: GForm.Language.product_select_quantity,
            editable: true,
            appearance: {
                width: 40
            }
        });

        var column_variant = new GF_Datagrid_Column({
            id: 'variant',
            caption: GForm.Language.product_select_variant,
            selectable: true,
            appearance: {
                width: 120
            }
        });

        var column_allegro_category = new GF_Datagrid_Column({
            id: 'allegro_category',
            caption: GForm.Language.allegro_product_select_category,
            appearance: {
                width: 150,
                no_title: true
            }
        });

        var column_allegro_params = new GF_Datagrid_Column({
            id: 'allegro_params',
            caption: GForm.Language.allegro_product_select_params,
            appearance: {
                width: 150,
                no_title: true
            }
        });

        var column_allegro_start_price = new GF_Datagrid_Column({
            id: 'allegro_start_price',
            caption: GForm.Language.allegro_product_select_start_price,
            editable: true,
            appearance: {
                width: 60,
                visible: false
            }
        });

        var column_allegro_buy_price = new GF_Datagrid_Column({
            id: 'allegro_buy_price',
            caption: GForm.Language.allegro_product_select_buy_price,
            editable: true,
            appearance: {
                width: 50
            }
        });

        var column_allegro_min_price = new GF_Datagrid_Column({
            id: 'allegro_min_price',
            caption: GForm.Language.allegro_product_select_min_price,
            editable: true,
            appearance: {
                width: 50,
                visible: false
            }
        });

        return [
            column_id,
            column_idproduct,
            column_title,
            column_name,
            column_variant,
            column_allegro_category,
            column_allegro_params,
            column_price,
            column_price_gross,
            column_buyprice,
            column_buyprice_gross,
            column_vat,
            column_allegro_start_price,
            column_allegro_buy_price,
            column_allegro_min_price,
            column_quantity,
            column_stock,
            column_category,
            column_producer,
            column_barcode,
        ];

    };

    gThis._InitDatagrid = function() {

        gThis.m_fProcessProduct = gThis._ProcessProduct;
        gThis.m_fLoadProducts = gThis.m_oOptions.fLoadProducts;

        var aoColumns = gThis._InitColumns();

        var oOptions = {
            id: gThis.GetId(),
            mechanics: {
                rows_per_page: 15,
                key: 'idproduct',
                only_one_selected: !gThis.m_bRepeatable,
                persistent: false
            },
            appearance: {
                column_select: false
            },
            event_handlers: {
                load: gThis.m_fLoadProducts,
                process: gThis.m_fProcessProduct,
                select: gThis._OnSelect,
            },
            columns: aoColumns
        };

        gThis.m_gDatagrid = new GF_Datagrid(gThis.m_jDatagrid, oOptions);

    };

    gThis._Deselect = function(iDg, mId) {
        if (!(mId instanceof Array)) {
            mId = [mId];
        }
        for (var i = 0; i < mId.length; i++) {
            //reset
            $('#confirm_allegro #_' + mId[i]).remove();
            //...
            gThis.m_gDataProvider.DeleteRow(mId[i]);
        }
        gThis.m_gSelectedDatagrid.ClearSelection();
        gThis.m_gSelectedDatagrid.LoadData();
    };

    gThis._InitSelectedDatagrid = function() {

        gThis.m_gDataProvider = new GF_Datagrid_Data_Provider({
            key: 'id',
            event_handlers: {
                change: gThis._OnChange,
                row_change: gThis._OnRowChange,
                row_add: gThis._OnRowAdd
            }
        }, []);

        if (gThis.m_oOptions.bAdvancedEditor) {
            var aoColumns = gThis._InitAdvancedColumns();
        }
        else {
            var aoColumns = gThis._InitColumns();
        }

        var gActionDeselect = new GF_Action({
            img: gThis._GetImage('DeselectIcon'),
            caption: GForm.Language.product_select_deselect,
            action: gThis._Deselect
        });

        var oOptions = {
            id: gThis.GetId() + '_selected',
            mechanics: {
                rows_per_page: 15,
                key: 'id',
                persistent: false
            },
            appearance: {
                filter: false
            },
            event_handlers: {
                load: function(oRequest, sResponseHandler) {
                    if (gThis.m_bFirstLoad) {
                        gThis.m_bFirstLoad = false;
                        gThis._LoadSelected(oRequest, sResponseHandler);
                    }
                    else {
                        gThis.m_gDataProvider.Load(oRequest, sResponseHandler);
                    }
                },
                update_row: function(sId, oRow) {
                    gThis.m_gDataProvider.UpdateRow(sId, oRow);
                },
                process: gThis._ProcessSelectedProduct,
                loaded: gThis._OnLoaded,
                deselect: gThis._OnDescriptionEditorHide
            },
            columns: aoColumns,
            row_actions: [
                gActionDeselect
            ],
            context_actions: [
                gActionDeselect
            ],
            group_actions: [
                gActionDeselect
            ]
        };

        gThis.m_gSelectedDatagrid = new GF_Datagrid(gThis.m_jSelectedDatagrid, oOptions);

    };

    gThis._LoadSelected = function(oRequest, sResponseHandler) {
        if (gThis.m_oOptions.bAdvancedEditor) {
            var asDefaults = [];
            for (var i in gThis.m_oOptions.asDefaults) {
                asDefaults.push(gThis.m_oOptions.asDefaults[i].id);
            }
            oRequest.where = [{
                column: 'idproduct',
                value: asDefaults,
                operator: 'IN'
            }];
        }
        else {
            oRequest.where = [{
                column: 'idproduct',
                value: gThis.m_oOptions.asDefaults,
                operator: 'IN'
            }];
        }
        gThis.m_fLoadProducts(oRequest, GCallback(function(eEvent) {
            if (gThis.m_oOptions.bAdvancedEditor) {
                for (var i in eEvent.rows) {
                    var sId = eEvent.rows[i].idproduct;
                    for (var j in gThis.m_oOptions.asDefaults) {
                        if (gThis.m_oOptions.asDefaults[j].id == sId) {
                            eEvent.rows[i].quantity = gThis.m_oOptions.asDefaults[j].quantity;
                            eEvent.rows[i].variant = gThis.m_oOptions.asDefaults[j].variant;
                        }
                    }
                }
            }
            gThis.m_gDataProvider.ChangeData(eEvent.rows);
            gThis.m_gSelectedDatagrid.LoadData();
        }));
    };


    gThis.m_oItems = {};

    gThis._PrepareTreeNode = function() {
        gThis.m_oItems = {};
        var jNode = $('<div/>').addClass('field-tree');
        gThis.m_jFieldWrapper = $('<div/>');
        jNode.append(gThis.m_jFieldWrapper);
        gThis.m_jTree = $('<ul/>');
        jNode.append($('<div class="tree-wrapper"/>').append(gThis.m_jTree));
        gThis.Update();
        window.setTimeout(gThis.ResetExpansion, 500);
        return jNode;
    };

    gThis.OnRetractAll = function(eEvent) {
        gThis.m_jTree.find('li:has(li)').removeClass(gThis._GetClass('Expanded'));
        return false;
    };

    gThis.ResetExpansion = function() {
        gThis.m_jTree.find('li').removeClass(gThis._GetClass('Expanded'));
        gThis.m_jTree.find('li.' + gThis._GetClass('Active')).parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
        gThis.m_jTree.find('li > label > input:checked').parents('li').andSelf().filter(':has(li)').addClass(gThis._GetClass('Expanded'));
    };

    gThis._WriteSubtree = function(jParent, sParent) {
        if (sParent == undefined) {
            sParent = null;
        }
        var oItems = GCore.FilterObject(gThis.m_oOptions.oItems, function(oItem) {
            return (oItem.parent == sParent);
        });
        var aIterationArray = GCore.GetIterationArray(oItems, function(oA, oB) {
            return (oA.weight < oB.weight);
        });
        var iLength = aIterationArray.length;
        for (var i = 0; i < iLength; i++) {
            var sId = aIterationArray[i];
            var oItem = oItems[sId];
            jParent.append(gThis._WriteItem(sId, oItem));
        }
    };

    gThis._WriteItem = function(sId, oItem) {
        var jLi = $('<li/>');
        jLi.get(0).sId = sId;
        if (gThis.m_oOptions.sActive == sId) {
            jLi.addClass(gThis._GetClass('Active'));
        }
        var jField = $('<input type="radio" name="__' + gThis.GetName() + '" value="' + sId + '"/>');
        if(oItem.hasChildren){
            jField.css('display', 'none');
        }
        var oProduct = gThis.m_gSelectedDatagrid.GetRow(gThis.m_sCurrentlyEditedProduct);
        if (oProduct.allegro_category.id == sId) {
            jField.click();
            jField.attr('checked', 'checked');
        }
        jLi.append($('<label class="' + gThis._GetClass('ItemName') + '"/>').append(jField).append(oItem.name));
        var jUl = $('<ul/>');
        gThis._WriteSubtree(jUl, sId);
        var jExpander = $('<span class="' + gThis._GetClass('Expander') + '"/>');
        jLi.prepend(jExpander);
        jLi.append(jUl);
        jExpander.css('display', 'none');
        if (oItem.hasChildren || jUl.children('li').length) {
            jExpander.css('display', 'block');
        }
        return jLi;
    };

    gThis.UpdateExpanders = function() {
        gThis.m_jTree.find('li::not(:has(li))').removeClass(gThis._GetClass('Expanded')).children('.' + gThis._GetClass('Expander')).css('display', 'none');
        gThis.m_jTree.find('li:has(li) > .' + gThis._GetClass('Expander')).css('display', 'block');
    };

    gThis.Update = function() {
        gThis.m_jTree.empty();
        gThis._WriteSubtree(gThis.m_jTree);
        if (gThis.m_oOptions.bSortable) {
            gThis.m_jTree.sortable({
                items: 'li',
                placeholder: 'item-faux-placeholder',
                opacity: .5,
                tolerance: 'cursor',
                cursor: 'move',
                cursorAt: 'left',
                start: function(e, ui) {
                    gThis.m_jItemPlaceholder = $('<li class="item-placeholder"/>');
                },
                sort: function(e, ui) {
                    gThis.m_jTree.find('li').removeClass('temporarly-expanded');
                    if (ui.offset.left > $(ui.placeholder).prev().offset().left + 15) {
                        $(ui.placeholder).prev().addClass('temporarly-expanded');
                        gThis.m_jItemPlaceholder.appendTo($(ui.placeholder).prev().children('ul'));
                    }
                    else {
                        gThis.m_jItemPlaceholder.insertAfter($(ui.placeholder));
                    }
                },
                beforeStop: function(e, ui) {
                    gThis.m_jTree.find('li.temporarly-expanded').removeClass('temporarly-expanded').addClass('expanded');
                    gThis.m_jItemPlaceholder.replaceWith($(ui.helper));
                    gThis.UpdateExpanders();
                },
                stop: function(e, ui) {
                }
            });
        }
        gThis._InitializeNodeEvents();
        gThis.ResetExpansion();
    };

    gThis._OnTreeClick = GEventHandler(function(eEvent) {
        GCore.StartWaiting();
    });

    gThis._InitializeNodeEvents = function() {
        gThis.m_jTree.find('.' + gThis._GetClass('Expander')).unbind('click').click(function() {
            if ($(this).closest('li').hasClass(gThis._GetClass('Expanded'))) {
                $(this).closest('li').find('li').andSelf().removeClass(gThis._GetClass('Expanded'));
            }
            else {
                $(this).closest('li').addClass(gThis._GetClass('Expanded'));
                gThis._Expand($(this).closest('li'));
            }
        });
        gThis.m_jTree.find('input').unbind('click').click(gThis._OnTreeSelect);
    };

    gThis._OnTreeSelect = GEventHandler(function(eEvent) {
        var sId = $(this).attr('value');
        var sCaption = $(this).parent().text();
        var oProduct = gThis.m_gSelectedDatagrid.GetRow(gThis.m_sCurrentlyEditedProduct);
        var jList = $(this).closest('.allegro-category-selector');
        var jExpander = jList.get(0).m_jExpander;
        jExpander.parent().removeClass('focus');
        jList.remove();
        jExpander.find('span').text(sCaption);
        oProduct.allegro_category.id = sId;
        oProduct.allegro_category.caption = sCaption;
        gThis.m_gDataProvider.UpdateRow(gThis.m_sCurrentlyEditedProduct, oProduct);
        GOverlay.RemoveAll();
        jExpander.closest('tr').find('.params-selector .selected').mousedown().click();
    });

    gThis._Expand = function(jParentLi) {
        var sId = jParentLi.get(0).sId;
        if (gThis.m_oItems[sId] != undefined) {
            return;
        }
        if (gThis.m_oOptions.fGetChildren instanceof Function) {
            jParentLi.find('ul').remove();
            jParentLi.append('<ul><li><img src="' + gThis._GetImage('Waiting') + '" alt=""/></li></ul>');
            gThis.m_oOptions.fGetChildren({
                parent: sId
            }, GCallback(gThis._OnChildrenLoaded, {
                parentNode: jParentLi
            }));
        }
    };

    gThis._OnChildrenLoaded = GEventHandler(function(eEvent) {
        var jUl = $('<ul/>');
        gThis.m_oItems[eEvent.parentNode.get(0).sId] = true;
        for (var i in eEvent.children) {
            jUl.append(gThis._WriteItem(i, eEvent.children[i]));
        }
        eEvent.parentNode.find('ul').remove();
        eEvent.parentNode.append(jUl);
        gThis._InitializeNodeEvents();
    });

}, oDefaults);
