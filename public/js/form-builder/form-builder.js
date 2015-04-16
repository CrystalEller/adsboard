function FormBuilder(elem, catid) {
    this.$elem = elem;
    this.catid = catid ? catid : 0;
    this.fields = {};
    this.data = undefined;

    this.init();
}

FormBuilder.prototype = {
    getSourceFields: function () {
        var that = this;

        $.ajax({
            type: "POST",
            url: '/getForm/' + this.catid,
            dataType: 'json',
            success: function (data) {
                if (data.message) {
                    alert(data.message);
                } else {
                    that.data = data.fields;
                    $.each(data.fields, function (key, value) {
                        var formField = new FormField(value.type, value);
                        that.$elem.append(that.wrap(formField));
                        that.fields[formField.data.id] = formField;
                    });
                }
            },
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        });

    },
    uploadFields: function () {
        var insertFields = [],
            updateFields = [],
            deleteFields = [];
        var fields = $.extend({}, this.fields);

        $.each(fields, function (index, value) {
            if (value.isNew) {
                delete value.data.id;
            }

            if (value.fieldDiv.is(':visible')) {
                if ('id' in value.data) {
                    updateFields.push(value.data);
                } else {
                    insertFields.push(value.data);
                }
            } else {
                if ('id' in value.data) {
                    deleteFields.push(value.data);
                }
            }

        });

        $.ajax({
            type: "POST",
            url: '/changeForm/' + this.catid,
            dataType: 'json',
            data: {
                "fieldsActions": {
                    "deleteFields": deleteFields,
                    "insertFields": insertFields,
                    "updateFields": updateFields
                }
            }
        }).success(function (data) {
            if (data) {
                alert('ok');
            } else {
                alert('bad');
            }
        });

    },
    init: function () {
        this.getSourceFields();
        this.addEventListeners();
    },
    wrap: function (formField) {
        return $('<div/>')
            .addClass('row element-row')
            .append(formField.generateField())
            .append($('<div/>').addClass('col-lg-6 text-right nopadding element-buttons')
                .append($('<button/>')
                    .addClass('btn btn-default')
                    .data({'action': 'edit', 'id': formField.getData().id})
                    .text('edit'),
                $('<button/>')
                    .addClass('btn btn-default')
                    .data({'action': 'remove', 'id': formField.getData().id})
                    .text('remove')
            )
        )
    },
    addEventListeners: function () {
        var that = this;
        $('#fields-select').unbind('click').on('click', 'button', function (e) {
            var formField = new FormField($(this).attr('id'));
            that.$elem.append(that.wrap(formField));
            that.fields[formField.data.id] = formField;
        });
        $('#form-elements').unbind('click').on('click', '.element-row>.element-buttons>button', function () {
            var target = $(this);
            if (target.data('action') == 'remove') {
                target.parents('div.row.element-row').remove();
            }
            if (target.data('action') == 'edit') {
                var form = new FieldEditForm(that.fields[target.data('id')]);
                $('#' + target.data('id')).popover('destroy').popover({
                    content: form.generateForm(),
                    container: '#' + target.data('id'),
                    html: true,
                    trigger: 'manual'
                }).on('shown.bs.popover', function () {
                    var popover = $(this).find('.popover');
                    popover.css('top', parseInt(popover.css('top')) + 22 + 'px');
                }).popover('show');
            }
        });

        $('#save-form').unbind('click').click(function () {
            that.uploadFields();
        });
    }

};

$.fn.formBuilder = function (catid) {
    new FormBuilder(this, catid);
    return this;
};


