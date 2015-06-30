$(document).ready(function(){

    $('#complain').validator({
        errors: {
            minlength: $('#complain').data('minlength-error'),
            maxlength: $('#complain').data('maxlength-error')
        }
    });

    $('.complain a').click(function(e){
        $('#modal').modal('show');
        e.preventDefault();
    });

    $('.send-complain').click(function(e){

        $.ajax({
            type: 'POST',
            url:$('#complain').attr('action'),
            data: $('#complain').serialize(),
            dataType: 'json',
            success: function(data) {
                $.notify({
                    message: $('#complain').data('notify-success')
                }, {
                    type: "success",
                    z_index: 1031,
                    delay: 3000,
                    timer: 1000
                });
            },
            error: function(){
                alert('error');
            }
        });


        $('#modal').modal('hide');
        e.preventDefault();
    });
});