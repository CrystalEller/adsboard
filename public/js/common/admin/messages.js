function actionsFormatter(value, row, index) {
    return [
        '<a class="delete" href="#" title="Delete">',
        '<i class="glyphicon glyphicon-trash"></i>',
        '</a>',
        '<a class="answer" href="#" title="Answer">',
        '<i class="glyphicon glyphicon-envelope"></i>',
        '</a>'
    ].join('');
}

window.actions = {
    'click .delete': function (e, value, row, index) {
        $.ajax({
            url: '/admin/message/delete/' + row.id,
            success: function (data) {
                $('#messages').bootstrapTable('refresh');
            },
            error: function (xhr) {
                alert(xhr.status + ' ' + xhr.responseText)
            }
        });
    },
    'click .answer': function (e, value, row, index) {
        $('#modal-message').modal('show');
        $('#email').val(row.email);

        $('#modal-message .send-message').one("click",function () {
            $.ajax({
                type: 'POST',
                url: '/admin/message/answer',
                dataType: 'json',
                data: {
                    email: row.email,
                    subject: $('#subject').val(),
                    message: $('#answer').val()
                },
                success: function (data) {
                    $.notify({
                        message: $('#message-form').data('notify-success')
                    }, {
                        type: "success",
                        z_index: 1031,
                        delay: 3000,
                        timer: 1000
                    });
                },
                error: function (xhr) {
                    alert(xhr.status + ' ' + xhr.responseText)
                }
            });
        });
    }
};

function dateFormatter(value, row, index) {
    return new Date(value.date).toLocaleDateString();
}