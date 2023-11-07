jQuery(document).submit(function (e) {
    var form = jQuery(e.target).closest('form');
    console.log(form);
    var url = form.attr("action");
    e.preventDefault();
    jQuery.ajax({
        type: "POST",
        url: url,
        data: form.serialize(), // serializes the form's elements.
        success: function (data) {
            var messageAlert = 'alert-' + data.type;
            var messageText = data.message;
            var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissible fade show" role="alert">\n' +
                '  <strong>Notice! </strong> ' + messageText + '\n' +
                '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                '</div>';
            if (messageAlert && messageText) {
                form.find('.messages').html(alertBox);
                form[0].reset();
            }
            if (data.type === 'success') {
                reload();
            }
        }
    });
});

$('#verify_reset').on('click', function () {
    let location = window.location;
    window.location.replace(location);
});


function responseMessage(data)
{
    var messageAlert = 'alert-' + data.type;
    var messageText = data.message;
    var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissible fade show" role="alert">\n' +
        '  <strong>Notice! </strong> ' + messageText + '\n' +
        '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
        '</div>';
    if (messageAlert && messageText) {
        $('#confirmation-form').find('.messages').html(alertBox);
    }
}

function reload()
{
    setTimeout(function () {
        let location = window.location;
        window.location.replace(location);
    }, 1000);
}