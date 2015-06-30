$(document).ready(function () {

    $('#locationList').on('click', 'a', function (e) {
        var $target = $(this);
        var $selectWrapper = $('#selected-location');
        var input = $('#location');

        if ($target.data('city-id')) {
            input.attr('name', 'cityId');
            input.val($target.data('city-id'));
        } else {
            if ($target.data('region-id')) {
                input.attr('name', 'regionId');
                input.val($target.data('region-id'));
            } else {
                input.attr('name', '');
                input.val('');
            }
        }

        $selectWrapper.find('.selected').text($target.text());
    }).on('mouseenter', 'li', function (e) {
        var target = $(e.target),
            dropDown = target.prev();

        if (dropDown.children().length == 0 &&
            target.data('region-id')
        ) {
            $.ajax({
                type: 'POST',
                url: $('#locationList').data('url-cities'),
                dataType: 'json',
                data: {regionid: target.data('region-id')},
                success: function (data) {
                    var cities = data.cities;

                    $.each(cities, function (key, value) {
                        var menuItem = $('<li>');

                        dropDown.append(menuItem);
                        menuItem.append(
                            $('<a>')
                                .attr('href', '#')
                                .data('city-id', value.id)
                                .text(value.name)
                        );

                    });
                },
                error: function (XMLHttpRequest) {
                    alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                }
            });
        }
    });

    $('#categories').on('click', 'a', function (e) {
        var $target = $(this);
        var $selectWrapper = $('#selected-category');
        var input = $('#categoryId');

        if ($target.data('cat-id')) {
            input.attr('name', 'categoryId');
            input.val($target.data('cat-id'));
        } else {
            input.attr('name', '');
            input.val('');
        }


        $selectWrapper.find('.selected').text($target.text());
    }).on('mouseenter', 'li', function (e) {
        var target = $(e.target),
            dropDown = target.prev();

        if (dropDown.children().length == 0 &&
            target.data('cat-id')
        ) {
            $.ajax({
                type: 'POST',
                url: $('#categories').data('url'),
                dataType: 'json',
                data: {pid: target.data('cat-id')},
                success: function (data) {
                    var cats = data.cats;

                    $.each(cats, function (key, value) {
                        var menuItem = $('<li>');

                        dropDown.append(menuItem);
                        menuItem.append(
                            $('<a>')
                                .attr('href', '#')
                                .data('cat-id', value.id)
                                .text(value.name)
                        );

                    });
                },
                error: function (XMLHttpRequest) {
                    alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                }
            });
        }
    });

    $.ajax({
        url: $('#locationList').data('url-regions'),
        dataType: 'json',
        success: function (data) {
            var regions = data.regions;

            $.each(regions, function (key, value) {
                menuItemWrapper($('#locationList'), 'region-id', value.id, value.name);
            });
        },
        error: function (XMLHttpRequest) {
            alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
        }
    });

    $.ajax({
        url: $('#categories').data('url-root'),
        dataType: 'json',
        success: function (data) {
            var cats = data.rootCats;

            $.each(cats, function (key, value) {
                menuItemWrapper($('#categories'), 'cat-id', value.id, value.name);
            });
        },
        error: function (XMLHttpRequest) {
            alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
        }
    });

    function menuItemWrapper(mainWrapper, dataKey, id, text) {
        var subMenu = $('<li>').addClass('dropdown-submenu'),
            dropDownMenu = $('<ul>').addClass('dropdown-menu');

        mainWrapper.append(subMenu);
        subMenu.append(dropDownMenu);
        subMenu.append(
            $('<a>')
                .attr('tabindex', '-1')
                .data(dataKey, id)
                .text(text)
        );
    }
});