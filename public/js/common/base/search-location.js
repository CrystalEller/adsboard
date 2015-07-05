$(document).ready(function () {
    $('#region').change(function () {
        var target = $(this);
        $.ajax({
            type: 'POST',
            url: $('#city').data('url'),
            dataType: 'json',
            data: {regionid: target.val()},
            success: function (data) {
                var $city = $('#city');

                $.each(data['cities'], function (index, value) {
                    if (value['id'] == $city.find(":selected").val()) {
                        $city.find(":selected").text(value['name']);
                    } else {
                        $('#city').append(
                            $('<option>')
                                .attr('value', value['id'])
                                .text(value['name']));
                    }
                });
            },
            error: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText)
            }
        });
    });

    $.ajax({
        url: $('#region').data('url'),
        dataType: 'json',
        success: function (data) {
            var $region = $('#region');

            $.each(data['regions'], function (index, value) {
                if (value['id'] == $region.find(":selected").val()) {
                    $region.find(":selected").text(value['name']);
                } else {
                    $region.append(
                        $('<option>')
                            .attr('value', value['id'])
                            .text(value['name']))
                }
            });
            $region.trigger('change');
        },
        error: function (xhr) {
            alert(xhr.status + ' ' + xhr.responseText)
        }
    })
});