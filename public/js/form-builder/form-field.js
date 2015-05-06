function FormField(type, data) {
    this.type = type;
    this.isNew = false;
    this.data = data;
    this.fieldDiv = undefined;
}

FormField.prototype = {
    generateId: function () {
        var id = '_' + Math.random().toString(36).substr(2, 9);
        if ($('#' + id).length > 0) {
            this.generateId();
        } else {
            return id;
        }
    },
    setData: function (data) {
        this.data = data;
        return this;
    },
    getData: function () {
        return this.data;
    },
    updateData: function (data) {
        this.update[this.type](this, data);
    },
    generateField: function (wrapper) {
        var data = $.extend({}, this.defaultData[this.type], this.data);

        if (!data.id) {
            data.id = this.generateId();
            this.isNew = true;
        }

        this.data = data;
        this.data.type = this.type;

        var fieldDiv = wrapper || this.defaultData.wrapper().attr('id', data.id);
        this.fieldDiv = fieldDiv;

        var label = $('<label/>').text(data.label);

        fieldDiv.append(label);

        return this.generators[this.type](fieldDiv, label, data);
    }
};

FormField.prototype.update = {
    "SingleLineText": function (formField, data) {
        $.each(data, function (key, val) {
            if (formField.data.hasOwnProperty(val.name)) {
                formField.data[val.name] = val.value;
            } else {
                var match = /^length\[(max|min)\]$/.exec(val.name);

                formField.data.length[match[1]] = val.value;
            }
        });
    },
    "ParagraphText": function (formField, data) {
        this.SingleLineText(formField, data);
    },
    "MultipleChoices": function (formField, data) {
        formField.data.values = [];

        $.each(data, function (key, val) {
            if (formField.data.hasOwnProperty(val.name)) {
                formField.data[val.name] = val.value;
            } else {
                var valueParts = val.name.split("_");

                formField.data.values.push({
                    'id': valueParts[0],
                    'value': val.value.trim(),
                    'status': valueParts[2]
                });

            }
        });
    },
    "Checkboxes": function (formField, data) {
        this.MultipleChoices(formField, data);
    },
    "DropDown": function (formField, data) {
        this.MultipleChoices(formField, data);
    }
};

FormField.prototype.generators = {
    "SingleLineText": function (wrapper, label, data) {
        var input = $('<input/>').addClass('form-control');
        input.attr({
            'type': 'text',
            'id': 'prop[' + data.id + ']',
            'name': 'prop[' + data.id + ']',
            'placeholder': data.placeholder
        }).prop("required", data.required);

        label.attr('for', 'prop[' + data.id + ']');
        return wrapper.append(label, input);
    },
    "ParagraphText": function (wrapper, label, data) {
        var input = $('<textarea/>').addClass('form-control');
        input.attr({
            'id': 'prop[' + data.id + ']',
            'name': 'prop[' + data.id + ']',
            'placeholder': data.placeholder
        }).prop("required", data.required);

        label.attr('for', 'prop[' + data.id + ']');
        return wrapper.append(input);
    },
    "MultipleChoices": function (wrapper, label, data) {
        label.addClass('radio control-label');

        $.each(data.values, function (index, value) {
            var label = $('<label/>').text(value.value);
            var radio = $('<input/>').attr({
                'type': 'radio',
                'name': 'prop[' + data.id + ']',
                'value': value.id
            });

            if (value.id != 0) {
                value.status = 1;
            }

            label.prepend(radio);
            var div = $('<div/>').addClass('radio').append(label);
            wrapper.append(div);
        });

        return wrapper
    },
    "Checkboxes": function (wrapper, label, data) {
        label.addClass('checkbox control-label');

        $.each(data.values, function (index, value) {
            var label = $('<label/>').text(value.value);
            var radio = $('<input/>').attr({
                'type': 'checkbox',
                'name': 'prop[' + data.id + '][]',
                'value': value.id
            });

            if (value.id != 0) {
                value.status = 1;
            }

            label.prepend(radio);
            var div = $('<div/>').addClass('checkbox').append(label);
            wrapper.append(div);
        });

        return wrapper;
    },
    "DropDown": function (wrapper, label, data) {
        var select = $('<select/>')
            .addClass('selectpicker form-control')
            .attr({
                'name': 'prop[' + data.id + '][]'
            });
        label.attr('for', data.id);
        $.each(data.values, function (index, value) {
            select.append(
                $('<option/>')
                    .attr('value', value.id)
                    .text(value.value)
            );

            if (value.id != 0) {
                value.status = 1;
            }
        });
        return wrapper.append(select);
    }
};

FormField.prototype.defaultData = {
    wrapper: function () {
        return $('<div/>').addClass('form-group element col-lg-6 nopadding');
    },
    "SingleLineText": {
        "name": "Label",
        "label": "Label",
        "placeholder": "Placeholder",
        //"required":"yes",
        "length": {
            "min": 0,
            "max": 0
        }
    },
    "ParagraphText": {
        "name": "Label",
        "label": "Label",
        "placeholder": "Placeholder",
        //"required":"yes",
        "length": {
            "min": 0,
            "max": 0
        }
    },
    "MultipleChoices": {
        "name": "Label",
        "label": "Label",
        "values": [
            {
                'id': 0,
                'value': "Value1",
                'status': 0
            },
            {
                'id': 0,
                'value': "Value2",
                'status': 0
            }
        ]
    },
    "Checkboxes": {
        "name": "Label",
        "label": "Label",
        "values": [
            {'id': 0, 'value': "Checkbox1"},
            {'id': 0, 'value': "Checkbox2"}
        ]
    },
    "DropDown": {
        "name": "Label",
        "label": "Label",
        "values": [
            {'id': 0, 'value': "DropDown1"},
            {'id': 0, 'value': "DropDown2"}
        ]
    }
};

