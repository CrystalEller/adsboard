$(document).ready(function () {

    $('#no-price').click(function () {
        if (this.checked) {
            $('[name=price]').prop('disabled', true);
        } else {
            $('[name=price]').prop('disabled', false);
        }
    });

    $('#ads-update').submit(function () {
        var target = $(this);

        target.ajaxSubmit({
            success: function (data) {
                clearInterval(progressInterval);
                showProgress(100);

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
                    $('#progress .progress-bar').width(0 + '%');
                }
            },
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        });

        startProgress();

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
        addMore: true,
        onRemove: function (el, file) {
            var val = $('#deleteImgs').val();
            if (val) {
                val = JSON.parse(val);
            } else {
                val = [];
            }
            val.push(file.name);

            $('#deleteImgs').val(JSON.stringify(val));
        },
        files: $('.jFilter .file_input').data('files')
    });

    var progressInterval;

    function getProgress() {
        $.ajax({
            url: '/upload-progress.php?id=' + $('#progress_key').val(),
            dataType: 'json',
            success: function (data) {
                if (data.status && !data.status.done) {
                    var value = Math.floor((data.status.current / data.status.total) * 100);
                    showProgress(value);
                } else {
                    showProgress(100);
                    clearInterval(progressInterval);
                }
            },
            error: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText);
            }
        });
    }

    function startProgress() {
        showProgress(0);
        progressInterval = setInterval(getProgress, 900);
    }

    function showProgress(amount) {
        $('#progress').removeClass('hide');
        console.log(amount);
        $('#progress .progress-bar').width(amount + '%');
    }
});