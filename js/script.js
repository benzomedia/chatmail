/**
 * Created by Benzo Media.
 * http://www.benzomedia.com
 * User: Oren Reuveni
 * Date: 18/03/2017
 * Time: 15:01
 */
jQuery(document).ready(function ($) {
    var ws_chatmail_message;
    var ws_chatmail_email;

    //Toggle between credits and submit button when text area value changes
    $('.ws-chatmail-message').bind('input propertychange', function (e) {
        if (e.target.value !== "") {
            $('#ws-chatmail-credits').fadeOut(400, 'swing', function () {
                $('#ws-chatmail-form-submit').css('opacity', '100').fadeIn()
            })
        } else {
            $('#ws-chatmail-form-submit').fadeOut(400, 'swing', function () {
                $('#ws-chatmail-credits').fadeIn()
            })
        }
    })

    //Open and close the main chatmail window
    $('#ws-chatmail-button').on('click', function () {

        if ($('#ws-chatmail-chat-window').hasClass('ws-chatmail-open')) {
            $('#ws-chatmail-chat-window').addClass('ws-chatmail-closing');
            clearWindow($)
        } else {
            $('#ws-chatmail-chat-window').show();
            $('#ws-chatmail-chat-window').addClass('ws-chatmail-openning');
        }
    })

    //Change classes after animation of window stops
    $("#ws-chatmail-chat-window").bind('oanimationend animationend webkitAnimationEnd', function () {
        if ($(this).hasClass('ws-chatmail-closing')) {
            $(this).removeClass('ws-chatmail-open').removeClass('ws-chatmail-closing');
            $('#ws-chatmail-button').toggleClass('window-open');
            $('#ws-chatmail-chat-window').hide();
        } else {
            $(this).removeClass('ws-chatmail-openning').addClass('ws-chatmail-open');
            $('#ws-chatmail-button').toggleClass('window-open');
        }
    });


    //Action when initial message is submitted
    $('#ws-chatmail-window-form').on('submit', function (e) {
        e.preventDefault();
        ws_chatmail_message = $('.ws-chatmail-message').val()

        $('#ws-chatmail-window-form').hide()
        $('#ws-chatmail-credits').fadeIn()
        addUserBubble(ws_chatmail_message, $)
        $('#ws-chatmail-chat-window').css('height', '450px');
        $('.ws-admin-response').fadeIn(600);
    })


    //Action when email form is submitted
    $('#ws-chatmail-email-form').on('submit', function (e) {
        e.preventDefault()
        ws_chatmail_email = $(this).find('input[type="text"]').val();

        if (isEmail(ws_chatmail_email)) {
            ws_sendEmail(ws_chatmail_email, ws_chatmail_message, $)
        } else {
            $('.ws-chatmail-error').remove();
            $('.ws-form-bubble').append("<p class='ws-chatmail-error'>Valid email please.</p>")
        }

    })

    //Clear error message on change
    $('#ws-chatmail-email-form input').change(function () {
        $('.ws-chatmail-error').remove();
    })
});

//Add the message
function addUserBubble(message, $) {
    var bubble = document.createElement('div');
    bubble.setAttribute('class', 'ws-chatmail-bubble-container');
    bubble.innerHTML = '<div class="ws-chatmail-user-bubble" style="background-color:' + ws_chatmail_color + ';">' +
        '<div class="ws-arrow" style="border-left-color:' + ws_chatmail_color + ';"></div>' +
        '<p>' + message + '</p></div>';
    $('.ws-chatmail-window-body').prepend(bubble)
}

//Email validation
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

//Send Email
function ws_sendEmail(email, message, $) {
    var data = {
        'action': 'ws_chatmail_send_email',
        'email': email,
        'message': message
    };
    $.post(ajax_object.ajax_url, data, function (response) {
        var response = JSON.parse(response);
        if (response.error) {
            $('.ws-admin-error-response').fadeIn();
        } else {
            $('.ws-admin-success-response').fadeIn();
        }
    });
}

//Clear Window
function clearWindow($) {
    $('.ws-chatmail-user-bubble').parents('.ws-chatmail-bubble-container').remove()
    $('#ws-chatmail-window-form').show()
    $('#ws-chatmail-chat-window').css('height', '200px');
    $('.ws-chatmail-message').attr('value','');
    $('.ws-admin-response').hide();
    $('.ws-admin-error-response').hide();
    $('.ws-admin-success-response').hide();
    $('#ws-chatmail-form-submit').fadeOut(400, 'swing', function () {
        $('#ws-chatmail-credits').fadeIn()
    })
}