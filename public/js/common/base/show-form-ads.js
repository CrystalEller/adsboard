$(document).ready(function () {
    var categoryGroupId = 0;

    $('#region').change(function () {
        var target = $(this);
        $.ajax({
            type: 'POST',
            url: $('#city').data('url'),
            dataType: 'json',
            data: {regionid: target.val()},
            success: function (data) {
                var $city = $('#city');
                $city.find(':not(:disabled)').remove();
                $city.find(':disabled').prop('selected', true);
                $city.parent('div').removeClass('hide');
                $.each(data['cities'], function (index, value) {
                    $('#city').append(
                        $('<option>')
                            .attr('value', value['id'])
                            .text(value['name']))
                });
            },
            error: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText)
            }
        });
    });
    $('#main-category').change(function () {
        var target = $(this);

        target
            .parent('div')
            .nextAll('div:not(.hide)')
            .has('.subcategory')
            .remove();

        $('#attributes').addClass('hide').find('div>*').remove();

        genSubcategoryElem(target.val());
    });
    $('#no-price').click(function () {
        if (this.checked) {
            $('[name=price]').prop('disabled', true);
        } else {
            $('[name=price]').prop('disabled', false);
        }
    });
    $('#ads-create').submit(function () {
        var target = $(this);

        target.ajaxSubmit({
            success: function (data) {

                $('.help-block')
                    .html('')
                    .closest('div')
                    .removeClass('has-error')
                    .addClass('has-success');

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    $.each(data.formErrors, function (name, value) {
                        var $elem = $('[name^="' + name + '"]');

                        if ($.isPlainObject(value)) {
                            var first;
                            for (first in value) break;
                            value = value[first];
                        }

                        $elem.closest('div.form-group')
                            .addClass('has-error')
                            .removeClass('has-success')
                            .find('span.help-block')
                            .html(value + '\n');
                    });
                }
            },
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        });

        return false;
    });

    $('.file_input').filer({
        showThumbs: true,
        limit: 9,
        maxSize: 45,
        templates: {
            box: '<ul class="jFiler-item-list"></ul>',
            item: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <li><span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span></li>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            itemAppend: '<li class="jFiler-item">\
                            <div class="jFiler-item-container">\
                                <div class="jFiler-item-inner">\
                                    <div class="jFiler-item-thumb">\
                                        <div class="jFiler-item-status"></div>\
                                        <div class="jFiler-item-info">\
                                            <span class="jFiler-item-title"><b title="{{fi-name}}">{{fi-name | limitTo: 25}}</b></span>\
                                        </div>\
                                        {{fi-image}}\
                                    </div>\
                                    <div class="jFiler-item-assets jFiler-row">\
                                        <ul class="list-inline pull-left">\
                                            <span class="jFiler-item-others">{{fi-icon}} {{fi-size2}}</span>\
                                        </ul>\
                                        <ul class="list-inline pull-right">\
                                            <li><a class="icon-jfi-trash jFiler-item-trash-action"></a></li>\
                                        </ul>\
                                    </div>\
                                </div>\
                            </div>\
                        </li>',
            progressBar: '<div class="bar"></div>',
            itemAppendToEnd: true,
            removeConfirmation: true,
            _selectors: {
                list: '.jFiler-item-list',
                item: '.jFiler-item',
                progressBar: '.bar',
                remove: '.jFiler-item-trash-action'
            }
        },
        addMore: true
    });

    function genSubcategoryElem(pCategoryId) {
        $.ajax({
            type: 'POST',
            url: $('#main-category').data('url'),
            dataType: 'json',
            data: {pid: pCategoryId},
            success: function (data) {
                if (data['cats'].length) {
                    var $wrapDiv = $('.subcategory').last().parent('div');
                    var $wrapDivClone = $wrapDiv.clone();
                    var $subCat = $wrapDivClone.find('.subcategory');

                    $wrapDiv.after($wrapDivClone);
                    $subCat.attr('name', 'category[' + ++categoryGroupId + ']');
                    $subCat.find(':not([value="0"])').remove();
                    $subCat.parent('div').removeClass('hide');

                    $subCat.change(function () {
                        var target = $(this);

                        $('#attributes').addClass('hide').find('div>*').remove();
                        target.nextAll('div').has('.subcategory').remove();

                        genSubcategoryElem(target.val());
                    });

                    $.each(data['cats'], function (index, value) {
                        $subCat.append(
                            $('<option>')
                                .attr('value', value['id'])
                                .text(value['name']))
                    });
                } else {
                    getAttributes(pCategoryId);
                }
            },
            fail: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText);
            }
        });
    }

    function getAttributes(catid) {
        $.ajax({
            type: 'POST',
            url: '/getForm/' + catid,
            dataType: 'json',
            success: function (data) {
                if (data['fields'].length) {
                    $.each(data['fields'], function (index, field) {
                        var formElem = new FormField(field.type, field);
                        var field = formElem.generateField($('<div class="form-group row"></div>'));

                        $('#attributes').removeClass('hide').append(field);

                        var label = field.children('label');
                        var input = field.children(':not(label)');

                        label.addClass('control-label').wrap($('<div class="col-lg-6 text-right " ></div>'));
                        input.wrapAll($('<div class="col-lg-6 text-left "></div>'));
                        input.after($('<span>').addClass('help-block'))
                    });
                }
            },
            fail: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText);
            }
        });
    }
});