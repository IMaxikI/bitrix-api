$(document).ready(function () {
    setMinMaxDate();
    fetchCountContacts();
});

function setMinMaxDate() {
    const dateInput = $('#birthday');
    const maxDay = new Date();
    const minDay = new Date();

    minDay.setDate(maxDay.getDate() - 36500);
    maxDay.setDate(maxDay.getDate() - 5840);

    const maxDayString = maxDay.toISOString().split('T')[0];
    const minDayString = minDay.toISOString().split('T')[0];

    dateInput.attr('max', maxDayString);
    dateInput.attr('min', minDayString);
}

function fetchCountContacts() {
    $.get({
        url: '/php/countContacts.php',
        method: 'get',
        dataType: 'json',
        success: function (data) {
            $('#countContacts').text(data[0].count);
        }
    });
}

setInterval(fetchCountContacts, 5000);

$('#submit').click(function (e) {
    e.preventDefault();
    const form = $('form');

    if (form[0].checkValidity()) {
        const contact = {
            'name': $('#name').val(),
            'second_name': $('#second_name').val(),
            'last_name': $('#last_name').val(),
            'phone': $('#phone').val(),
            'birthday': $('#birthday').val(),
        };

        $.ajax({
            url: '/php/createContact.php',
            method: 'post',
            dataType: 'json',
            data: contact,
            beforeSend: function () {
                $("#overlay").show();
                clearErrorText();
            },
            success: function (data) {
                $('#success').text(data.success);
                setTimeout(() => $('#success').text(''), 3000);
                $("#overlay").hide();
                form[0].reset();
                fetchCountContacts();
            },
            error: function (errors) {
                $.each(errors.responseJSON.errors,
                    (key, value) => $('.' + key).text(value)
                );
                $("#overlay").hide();
            }
        });
    } else {
        form[0].reportValidity();
    }
});

function clearErrorText() {
    const classes = ['name', 'second_name', 'last_name', 'phone', 'birthday'];

    $.each(classes, (key, value) => $('.' + value).text(' '));
}