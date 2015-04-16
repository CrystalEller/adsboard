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
            var label = $('<label/>').text(value);
            var radio = $('<input/>').attr({
                'type': 'radio',
                'name': 'prop[' + data.id + ']',
                'value': value
            });
            label.prepend(radio);
            var div = $('<div/>').addClass('radio').append(label);
            wrapper.append(div);
        });

        return wrapper
    },
    "Checkboxes": function (wrapper, label, data) {
        label.addClass('checkbox control-label');

        $.each(data.values, function (index, value) {
            var label = $('<label/>').text(value);
            var radio = $('<input/>').attr({
                'type': 'checkbox',
                'name': 'prop[' + data.id + '][]',
                'value': value
            });
            label.prepend(radio);
            var div = $('<div/>').addClass('checkbox').append(label);
            wrapper.append(div);
        });

        return wrapper;
    },
    "DropDown": function (wrapper, label, data) {
        var select = $('<select/>').addClass('selectpicker form-control').attr('id', data.id);
        label.attr('for', data.id);
        $.each(data.values, function (index, value) {
            select.append($('<option/>').attr('value', value).text(value));
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
            "Value1",
            "Value2"
        ]
    },
    "Checkboxes": {
        "name": "Label",
        "label": "Label",
        "values": [
            "Checkbox1",
            "Checkbox2"
        ]
    },
    "DropDown": {
        "name": "Label",
        "label": "Label",
        "values": [
            "DropDown1",
            "DropDown2"
        ]
    }
};

