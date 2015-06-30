$(document).ajaxComplete(function (event, request) {
    if (/^3\d{2}$/.test(request.status)) {
        window.location.href = request.getResponseHeader('Location');
    }
});