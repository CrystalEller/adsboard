function FieldEditForm(formField) {
    this.formField = formField;
}

FieldEditForm.prototype = {
    generateForm: function () {
        var that = this;
        var form = $('<form/>').addClass('edit-field');
        $.each(this.formField.getData(), function (key, value) {
            if (key in that.generators) {
                form.append(that.generators[key](value, that.formField));
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
        $('#form-elements').one('click',
            '#' + that.formField.data.id + ' .popover button',
            function (e) {
                var target = $(this);
                if (target.hasClass('cancel')) {
                    that.formField.fieldDiv.popover('destroy');
                }
                if (target.hasClass('save')) {
                    var formData = $('#' + that.formField.data.id + ' form.edit-field').serializeObject();
                    if (formData.values) {
                        formData.values = formData.values.split('\n')
                            .filter(function (elem) {
                                return elem.trim().length > 0
                            });
                    }

                    that.formField.fieldDiv.popover('destroy');

                    that.formField.setData($.extend({}, that.formField.data, formData));

                    that.formField.fieldDiv.replaceWith(that.formField.generateField());

                }
            }
        );
    }
};

FieldEditForm.prototype.generators = {
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
        var label = $('<label/>').text('Options');
        var textarea = $('<textarea/>').addClass('form-control').attr({
            name: 'values',
            rows: 6,
            cols: 6
        });

        $.each(data, function (index, value) {
            textarea.val(textarea.val() + value + '\n');
        });

        return $('<div/>').addClass('row').append(label, textarea);
    }
};
