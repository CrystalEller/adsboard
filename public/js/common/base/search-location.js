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

                $city.find("option[value!='']").remove();

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

    $.ajax({
        url: $('#region').data('url'),
        dataType: 'json',
        success: function (data) {
            var $region = $('#region');

            $.each(data['regions'], function (index, value) {
                $region.append(
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