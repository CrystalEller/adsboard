function FieldEditForm(formField) {
    this.formField = formField;
}

FieldEditForm.prototype = {
    generateForm: function () {
        var that = this;
        var form = $('<form/>').addClass('edit-field');
        $.each(this.formField.getData(), function (key, value) {
            if (key in that.patterns) {
                form.append(that.patterns[key](value, that.formField));
            }
        });
        form.append(
            $('<div/>')
                .addClass('row text-right')
                .append(
                $('<button/>')
                    .attr('type', 'button')
                    .addClass('btn btn-default save')
                    .text('Save'),
                $('<button/>')
                    .attr('type', 'button')
                    .addClass('btn btn-default cancel')
                    .text('Cancel')
            )
        );

        that.addEventListeners();
        return form;
    },
    addEventListeners: function () {
        var that = this;

        $('.element-row').unbind('click').on('click',
            '#' + that.formField.data.id + ' .popover button',
            function (e) {
                var target = $(this);
                if (target.hasClass('delete-value')) {
                    var input = target.closest('div.input-group').find('input'),
                        nameParts = input.attr('name').split('_');

                    if (nameParts[2] == 0) {
                        input.parent().remove();
                    } else {
                        nameParts[2] = -1;
                        input.attr('name', nameParts.join('_'));
                        input.hide();
                    }
                }
                if (target.hasClass('add-value')) {
                    target.before(that.patterns['value']());
                }
                if (target.hasClass('cancel')) {
                    that.formField.fieldDiv.popover('destroy');
                }
                if (target.hasClass('save')) {
                    var formData = $('#' + that.formField.data.id + ' form.edit-field').serializeArray();

                    that.formField.fieldDiv.popover('destroy');
                    that.formField.updateData(formData);
                    that.formField.fieldDiv.replaceWith(that.formField.generateField());
                }
            }
        );
    }
};

FieldEditForm.prototype.patterns = {
    name: function (value, formField) {
        var formFieldData = formField.getData();

        if (!formFieldData.name.trim()) {
            value = formField.data.name = formField.data.label;
        }

        return $('<div/>')
            .addClass('row')
            .append(
            $('<label/>')
                .text('Enter Name'),
            $('<input/>')
                .addClass('form-control')
                .attr({
                    type: 'text',
                    name: 'name'
                }).val(value)
        );
    },
    label: function (data) {
        return $('<div/>')
            .addClass('row')
            .append(
            $('<label/>')
                .text('Enter Label'),
            $('<input/>')
                .addClass('form-control')
                .attr({
                    type: 'text',
                    name: 'label'
                }).val(data)
        );
    },
    placeholder: function (data) {
        return $('<div/>')
            .addClass('row')
            .append(
            $('<label/>')
                .text('Enter Placeholder'),
            $('<input/>')
                .addClass('form-control')
                .attr({
                    type: 'text',
                    name: 'placeholder'
                })
                .val(data)
        );
    },
    required: function (data) {
        return $('<div/>')
            .addClass('row')
            .append(
            $('<label/>')
                .text('Required'),
            $('<select/>')
                .addClass('selectpicker form-control')
                .attr('name', 'required')
                .append(
                $('<option/>')
                    .attr('value', 'yes')
                    .text('Yes'),
                $('<option/>')
                    .attr('value', 'no')
                    .text('No')
            )
                .val(data)
        );
    },
    length: function (data) {
        return $('<div/>')
            .addClass('row')
            .append(
            $('<label/>')
                .text('Size of input'),
            $('<div/>').append(
                $('<div/>')
                    .addClass('col-lg-6 nopadding')
                    .append(
                    $('<input/>')
                        .addClass('form-control')
                        .attr({
                            type: 'text',
                            name: 'length[min]',
                            placeholder: 'min'
                        }).val(data.min ? data.min : '')
                ),
                $('<div/>')
                    .addClass('col-lg-6 nopadding')
                    .append(
                    $('<input/>')
                        .addClass('form-control')
                        .attr({
                            type: 'text',
                            name: 'length[max]',
                            placeholder: 'max'
                        }).val(data.max ? data.max : '')
                )
            )
        );
    },
    values: function (data) {
        var that = this;
        var label = $('<label/>').text('Options');
        var buttonAdd = $('<button>')
            .addClass('btn btn-default btn-sm add-value')
            .attr('type', 'button')
            .text('Add');
        var row = $('<div/>').addClass('row');

        row.append(label);

        $.each(data, function (index, value) {
            row.append(that.value(value));
        });

        row.append(buttonAdd);

        return row
    },
    value: function (data) {
        data = $.extend({}, {
            'id': 0,
            'value': ' ',
            'status': 0
        }, data);

        var input = $('<input>').addClass('form-control').attr({
                'name': data.id + '_' + data.value + '_' + data.status
            }).val(data.value),
            inputGroup = $('<div>').addClass('input-group'),
            inputGroupBtn = $('<div>').addClass('input-group-btn'),
            button = $('<button>')
                .addClass('btn btn-default delete-value')
                .attr('type', 'button')
                .text('Delete');

        inputGroupBtn.append(button);
        inputGroup.append(input, inputGroupBtn);

        return inputGroup;
    }
};
