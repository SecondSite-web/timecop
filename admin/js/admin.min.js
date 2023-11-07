function reload()
{
    setTimeout(function () {
        let location = window.location;
        window.location.replace(location);
    }, 500);
}

function altSubmit(formId, alertDiv, refresh)
{
    var url = $('#' + formId).attr("action");
    $.ajax({
        type: 'POST',
        url: url,
        data: $('#'+ formId).serialize(),
        success: function (data) {
            var messageAlert = 'alert-' + data.type;
            var messageText = data.message;
            var alertBox = '<div class="alert ' + messageAlert + ' alert-dismissible fade show" role="alert">\n' +
                '  <strong>Notice! </strong> ' + messageText + '\n' +
                '  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>\n' +
                '</div>';
            if (messageAlert && messageText) {
                $('#'+ alertDiv).find('.alerts').html(alertBox);
            }
            if (data.type === 'success') {
                if (refresh === true) {
                    setTimeout(function () {
                        reload();
                    }, 500);
                }
                setTimeout(function () {
                    $('#' + formId)[0].reset();
                }, 500);
            }
        }
    });
}

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

$(document).ready(function (e) {
    var location = window.location.href;
    console.log(location);
    $(".nav-link").removeClass('nav-active');

    $(".sidebar .nav-link[href='"+location+"']").addClass('nav-active');
    $(".sidebar .nav-link[href='"+location+"']").closest('.submenu').addClass('show');
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.menus .nav-link').forEach(function (element) {

        element.addEventListener('click', function (e) {

            let nextEl = element.nextElementSibling;
            let parentEl  = element.parentElement;

            if (nextEl) {
                e.preventDefault();
                let mycollapse = new bootstrap.Collapse(nextEl);

                if (nextEl.classList.contains('show')) {
                    mycollapse.hide();
                } else {
                    mycollapse.show();
                    // find other submenus with class=show
                    var opened_submenu = parentEl.parentElement.querySelector('.submenu.show');
                    // if it exists, then close all of them
                    if (opened_submenu) {
                        new bootstrap.Collapse(opened_submenu);
                    }
                }
            }
        });
    });
});
jQuery(document).ready(function (e) {
    $('#logout-btn').on('click', logout);
    function logout(e)
    {
        const logoutFile = document.location.origin + "/include/workers/logout.php";
        $.post(logoutFile, {
            filter: 'logout'
        })
            .done(function (data) {
                var base = document.location.origin;
                window.location.replace(base);
            });
    }
});
var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
});
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
var modalTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="modal"]'));
var modalList = modalTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
function formatCurrency(total)
{
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-R" : 'R') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}
function formatCurrencyBwp(total)
{
    var neg = false;
    if (total < 0) {
        neg = true;
        total = Math.abs(total);
    }
    return (neg ? "-BWP" : 'BWP') + parseFloat(total, 10).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString();
}

