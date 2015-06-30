$(document).ready(function(){
    $('.deleteAds').click(function (e) {
        var $aTarget = $(e.target);
        var $modal = $('#modal');

        $modal.find('.modal-title').text($aTarget.data('modal-title'));
        $modal.find('.modal-body').text($aTarget.data('modal-body'));

        $modal.one('click', '.modal-footer button', function (e) {
            var $target = $(e.target);

            $('#modal').modal('hide');

            if ($target.hasClass('modal-yes')) {
                $.ajax({
                    url: $aTarget.attr('href'),
                    dataType: 'json',
                    success: function (data) {
                        if (data) {
                            $.notify({
                                message: $aTarget.data('notify-success')
                            }, {
                                type: "success",
                                z_index: 1031,
                                delay: 3000,
                                timer: 1000
                            });

                            $aTarget.closest('.list-group-item').remove();
                        }
                    },
                    error: function (XMLHttpRequest) {
                        alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
                    }
                });
            }
        }).modal('show');

        e.preventDefault();
    });
});
