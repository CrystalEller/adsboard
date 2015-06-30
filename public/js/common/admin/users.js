!function ($) {

    $('#ads-preview').on('hidden.bs.collapse', toggleChevron)
        .on('shown.bs.collapse', toggleChevron)
        .on('click', '.panel-body .ads-delete', function (e) {
            var $target = $(e.target),
                id = $target.closest('.panel-collapse').attr('id').split('-')[1];

            $('#modal').one('click', '.delete', function () {
                $.ajax({
                    url: '/admin/user/ads/delete/' + id,
                    success: function (data) {
                        if (data[0]) {
                            $('#ads-' + data[0]).parent().remove();
                            alert('ok');
                        } else {
                            alert('error');
                        }
                    },
                    error: function (XMLHttpRequest) {
                        alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                    }
                });
                $('#modal').modal('hide');
            }).modal()
                .find('.modal-body')
                .text('Вы уверены, что хотите удалить объявление? (id : ' + id + ')');
        })
        .on('click', '.panel-title', function (e) {
            var $target = $(e.target);

            if (!$target.hasClass('data-uploaded')) {
                $.ajax({
                    url: '/admin/user/ads/' + $target.data('adsId'),
                    dataType: 'json',
                    success: function (data) {
                        var adsData = data.ads;
                        var panelBody = $('#ads-' + adsData.id + ' .panel-body').text('');
                        var row = $('<div>');
                        var props = $('<ul>').addClass('unstyled');

                        panelBody.append(row.clone().text('id:' + adsData.id))
                            .append(row.clone().text('Имя Пользователя : ' + adsData.username))
                            .append(row.clone().text('Моб. телефон : ' + adsData.telephone))
                            .append(row.clone().text('Область : ' + adsData.regionid.name))
                            .append(row.clone().text('Город : ' + adsData.cityid.name))
                            .append(row.clone().text('Дата публикации : ' + adsData.created.date))
                            .append(row.clone().text('Заголовок : ' + adsData.title))
                            .append(row.clone().text('Атрибуты : '))
                            .append(props);

                        $.each(data.adsProps, function (index, value) {
                            if (Array.isArray(value)) {
                                value = value.join(', ');
                            }
                            props.append($('<li>').text(index + ' : ' + value))
                        });

                        panelBody.append(row.clone().text('Описание:' + adsData.description))
                            .append(
                            row.clone().addClass('text-right').html(
                                $('<button>')
                                    .attr('type', 'button')
                                    .addClass('ads-delete btn btn-xs')
                                    .text('Удалить объявление')
                                    .get(0).outerHTML
                            )
                        );
                        $target.addClass('data-uploaded');
                    },
                    error: function (XMLHttpRequest) {
                        alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                    }
                });
            }
        }).find('>.panel.panel-default').hide();


    function toggleChevron(e) {
        $(e.target)
            .prev('.panel-heading')
            .find("i.indicator")
            .toggleClass('glyphicon-chevron-down glyphicon-chevron-up');
    }

    $.extend($.fn.bootstrapTable.defaults, {
        editable: true,
        onEditableInit: function () {
            return false;
        },
        onEditableSave: function (field, row, oldValue, $el) {
            return false;
        }
    });

    $.extend($.fn.bootstrapTable.Constructor.EVENTS, {
        'editable-init.bs.table': 'onEditableInit',
        'editable-save.bs.table': 'onEditableSave'
    });

    var BootstrapTable = $.fn.bootstrapTable.Constructor,
        _initTable = BootstrapTable.prototype.initTable,
        _initBody = BootstrapTable.prototype.initBody;

    BootstrapTable.prototype.initTable = function () {
        var that = this;
        _initTable.apply(this, Array.prototype.slice.apply(arguments));

        $.each(this.options.columns, function (i, column) {
            var _formatter = column.formatter;

            column.formatter = function (value, row, index) {
                var result = _formatter ? _formatter(value, row, index) : value;

                if (column.editable) {
                    var a = $('<a>').attr({
                        'href': 'javascript:void(0)',
                        'data-name': column.field,
                        'data-id': row.id,
                        'data-url': '/admin/user/update',
                        'data-value': result
                    });

                    if (column.field == 'role') {
                        a.attr({
                            'data-type': 'select',
                            'data-source': "['admin','user']"
                        });
                    }

                    if (column.field == 'stat') {
                        a.attr({
                            'data-type': 'select',
                            'data-source': "['none','confirmed','banned']"
                        });
                    }

                    return a.get(0).outerHTML;
                } else {
                    if (column.field == 'ads') {

                        window.adsEvents = {
                            'click .showAds': function (e, value, row, index) {
                                $.ajax({
                                    url: e.target.href,
                                    success: function (data) {
                                        $('.bootstrap-table').slideUp(1000, function () {
                                            $('#ads-preview-block').slideDown(1000);
                                        });

                                        $('#back a').click(function () {
                                            $('#ads-preview-block').slideUp(800, function () {
                                                $('.bootstrap-table').slideDown(1000);
                                            });
                                        });

                                        $('#ads-preview>:not(:first)').remove();

                                        $.each(data.adsPreview, function (index, value) {
                                            var ads = $('#ads-preview .panel.panel-default').eq(0).clone(true);

                                            $('#ads-preview').append(ads.show());

                                            ads.find('.panel-title a').attr({
                                                href: '#ads-' + value.id
                                            }).data('adsId', value.id)
                                                .text(value.title);

                                            ads.find('.panel-body').eq(0).parent().attr('id', 'ads-' + value.id);
                                        });
                                    },
                                    error: function (XMLHttpRequest) {
                                        alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                                    }
                                });

                                e.preventDefault();
                            }
                        };

                        return $('<a>').attr({
                            'href': '/admin/user/' + row.id + '/showAdsPreview'
                        }).addClass('showAds').text('Посмотреть').get(0).outerHTML;
                    }
                }

                return result;
            };
        });
    };

    BootstrapTable.prototype.initBody = function () {
        var that = this;
        _initBody.apply(this, Array.prototype.slice.apply(arguments));

        if (!this.options.editable) {
            return;
        }

        $.each(this.options.columns, function (i, column) {
            if (!column.editable) {
                return;
            }

            that.$body.find('a[data-name="' + column.field + '"]').editable(column.editable)
                .off('save').on('save', function (e, params) {
                    var data = that.getData(),
                        index = $(this).parents('tr[data-index]').data('index'),
                        row = data[index],
                        oldValue = row[column.field];

                    row[column.field] = params.submitValue;
                    that.trigger('editable-save', column.field, row, oldValue, $(this));
                });
        });
        this.trigger('editable-init');
    };
}(jQuery);