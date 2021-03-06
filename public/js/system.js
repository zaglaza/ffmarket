//----------------- Системные функции ----------
$loader = '<div class="loader"><img src="/public/images/loader.gif" alt="Загрузка..." /></div>';
$loader_gray = '<div class="loader"><img src="/public/images/loader_gray.gif" alt="Загрузка..." /></div>';

// Показать диалог
$.showDialog = function (id) {
    $body_fill = $('#body_fill');
    // Показываем затемнение
    if ($('#body_fill').length==0) {
        $body_fill = $('<div id="body_fill"></div>');
        $('body').append($body_fill);
    }
    if ($('#'+id+':hidden').length>0) {
        $body_fill.css('opacity',0).show().animate({
            'opacity':0.5
        },300);
        // Показываем диалог
        $('#'+id).css('top',100);
        $('.dialog:not(#'+id+')').fadeOut(300,function(){
            $('#'+id).fadeIn(300);
        });
    }
};

// Показать диалог, содержимое которого загружается через ajax
$.showAjaxDialog = function (url,height,params,func) {
    $('#empty_dialog').css('height',height).find('.content').html($loader).find('.loader').css('marginTop',150);
    $.showDialog('empty_dialog');
    $.post(url,params, function(data){
        $('#empty_dialog .content').html(data);
        func();
    });
};

// Показать диалог
$.hideDialog = function (id) {
    $('#body_fill').animate({
        'opacity':0
    },300).hide();
    $('#'+id).fadeOut(300);
};

// Изменить диалог
$.toggleDialog = function (id) {
    // Показываем диалог
    $('#'+id).css('top',100);
    $('.dialog:not(#'+id+')').fadeOut(300,function(){
        $('#'+id).fadeIn(300);
    });
};

// Показать сообщение
$.alert = function (message,error) {
    if (typeof(alert_timeout)!='undefined') clearTimeout(alert_timeout);
    alert_timeout = setTimeout('$.hideDialog ("message_dialog")', 5000);
    if(!$.browser.msie) {
        scroll=$(document).scrollTop();
        $('#message_dialog').css('top',scroll);
    }
    if (error)  {
        $('#message_dialog').css({
            'background':'#D84705',
            'opacity':'0.9',
            'color':'#fff'
        });
    } else {
        $('#message_dialog').css({
            'background':'#489615',
            'opacity':'0.9',
            'color':'#fff'
        });
    }
    $('#message_dialog').css({
        'marginTop':'-500px'
    }).show();
    $('#message_dialog').animate({
        'marginTop':'0'
    },1000);
    $('#message_dialog .text').html(message);
};

// Выполнение после загрузки на всех страницах
$(document).ready(function () {
    apply_styles ();
    $('input[type="text"]').keyboard('enter',function(){
        $(this).parents('.ajax_form').find('input[type="button"]').click();
        return false;
    });
    // Нажатие на "оформить заказ"
    $('.trash .order a').click(function(){
        $.post('/market/'+site_city+'/index/order/',function(data){
            $('#dish_order_table').html(data);
            // Нажатие на кнопку удаления блюда
            $('#dish_order_items .remove_item').click(function(){
                portion = parseInt ($(this).parents('.trash_item').find('.trash_portion').html());
                dish_id = $(this).parents('.trash_item').attr('rel');
                $.post('/market/'+site_city+'/index/remove/',
                {
                    'dish_id':dish_id,
                    'portion':portion
                },function(data){
                    $('.trash .order a').click();
                    $('.trash_description').html(data);
                    $('.trash .rub').html(
                        $('.trash_description .price').html()+'<sup> руб.</sup>'
                        );
                    var count = parseInt($('.trash .rub').html());
                    if (count==0) {
                        $('.trash .order').hide();
                    }
                });
                return false;
            });

            // Нажатие на кнопку уменьшения кол-ва блюда
            $('#dish_order_items .minus_item').click(function(){
                portion = parseInt ($(this).parents('.trash_item').find('.trash_portion').html());
                dish_id = $(this).parents('.trash_item').attr('rel');
                $.post('/market/'+site_city+'/index/minus/',
                {
                    'dish_id':dish_id,
                    'portion':portion
                },function(data){
                    $('.trash .order a').click();
                    $('.trash_description').html(data);
                    $('.trash .rub').html(
                        $('.trash_description .price').html()+'<sup> руб.</sup>'
                        );
                    var count = parseInt($('.trash .rub').html());
                    if (count==0) {
                        $('.trash .order').hide();
                    }
                });
                return false;
            });
            // Нажатие на кнопку увеличения кол-ва блюда
            $('#dish_order_items .plus_item').click(function(){
                portion = parseInt ($(this).parents('.trash_item').find('.trash_portion').html());
                dish_id = $(this).parents('.trash_item').attr('rel');
                $.post('/market/'+site_city+'/index/plus/',
                {
                    'dish_id':dish_id,
                    'portion':portion
                },function(data){
                    $('.trash .order a').click();
                    $('.trash_description').html(data);
                    $('.trash .rub').html(
                        $('.trash_description .price').html()+'<sup> руб.</sup>'
                        );
                    var count = parseInt($('.trash .rub').html());
                    if (count==0) {
                        $('.trash .order').hide();
                    }
                });
                return false;
            });
        });
        $.showDialog('order_dialog');
        return false;
    });
    // Отправка заказа
    $('#order_submit').click(function(){
        name = $('#order_name').val();
        phone = $('#order_phone').val();
        address = $('#order_address').val();
        $('#order_loader').show();
        $.post('/market/'+site_city+'/index/orderSubmit/',
        {
            'name':name,
            'phone':phone,
            'address':address
        },function(data){
            $('#order_loader').hide();
            $('#order_message').html(data);
        });
    });
    // Нажатие на кнопку авторизации или регистрации
    $('#login_block #login').click(function(){
        $('#auth_message').html('');
        $.showDialog('auth_dialog');
    });
    $('#login_block #registration').click(function(){
        $('#reg_message').html('');
        $.showDialog('registration_dialog');
    });
    // -------- Авторизация -------------------
    $('#auth_submit').click(function(){
        login = $('#auth_login').val();
        password = $('#auth_password').val();
        remember = $('#remain_me_check').attr('checked');
        if (login!='' && password!='') {
            $('#auth_loader').fadeIn(500);
            password=hex_md5(password);
            $.post('/'+site_city+'/auth/login',{
                'login':login,
                'password':password,
                'remember':remember
            },
            function(data){
                $('#auth_message').html('');
                $('#auth_loader').fadeOut(300);
                if (data=="OK") {
                    $('#auth_message').html('Подождите немного...');
                    document.location.reload();
                }
                else if (data=="NOT_EXIST") $('#auth_message').html('Ошибка: пользователь или пароль неверны');
                else if (data=="LOGIN") {
                    $('#auth_message').html('Ошибка: введите e-mail или номер телефона');
                    $('#auth_password').css('backgroundColor','#c1c1c1');
                    $('#auth_login').css('backgroundColor','#c1c1c1');
                }
                else $('#auth_message').html('Ошибка при авторизации, попробуйте еще раз');
            })
        } else {
            $('#auth_message').html('Ошибка: заполните все поля');
        }
        if (login=='') $('#auth_login').css('backgroundColor','#c1c1c1');
        else $('#auth_login').css('backgroundColor','#fff');

        if (password=='') $('#auth_password').css('backgroundColor','#c1c1c1');
        else $('#auth_password').css('backgroundColor','#fff');
    });

    // -------- Регистрация -------------------
    $('#registration_submit').click(function(){
        name = $('#reg_name').val();
        mail = $('#reg_mail').val();
        phone = $('#reg_phone').val();
        rules = $('#reg_rules').attr('checked');
        if (!rules) {
            $('#reg_message').html('Ошибка: ознакомьтесь с правилами');
        }
        else if (name!='' && mail!='' && phone!='') {
            $('#reg_message').html('');
            $('#reg_loader').fadeIn(500);
            $.post('/'+site_city+'/auth/registration',{
                'name':name,
                'mail':mail,
                'phone':phone
            },
            function(data){
                $('#reg_loader').fadeOut(500);
                if (data=='SPACE') $('#reg_message').html('Ошибка: заполните все поля');
                else if (data=='PHONE_EXIST') {
                    a = '<a href="#" onclick="$.showDialog(\'passwd_dialog\');">Забыл пароль?</a>';
                    $('#reg_message').html('Пользователь с таким номером уже существует. '+a);
                }
                else if (data=='MAIL_EXIST') {
                    a = '<a href="#" onclick="$.showDialog(\'passwd_dialog\');">Забыл пароль?</a>';
                    $('#reg_message').html('Пользователь с таким e-mail уже существует. '+a);
                }
                else if (data=='NOT_PHONE') $('#reg_message').html('Ошибка: введите номер телефона в правильном формате');
                else if (data=='NOT_MAIL') $('#reg_message').html('Ошибка: введите e-mail в правильном формате');
                else if (data=='OK')
                    $('#reg_message').html(
                        '<span style="color:green">Регистрация прошла успешно. Пароль выслан на ваш номер</span>'
                        );
                else $('#reg_message').html('Ошибка при регистрации, попробуйте еще раз');
            })
        } else {
            $('#reg_message').html('Ошибка: заполните все поля');
        }
        return false;
    });
    // -------- Изменение пароля
    $('#passwd_submit').click(function(){
        login = $('#passwd_login').val();
        if (login!='') {
            $('#passwd_message').html('');
            $('#passwd_loader').fadeIn(500);
            $.post('/'+site_city+'/auth/passwd',{
                'login':login
            },
            function(data){
                $('#passwd_loader').fadeOut(500);
                if (data=='SPACE') $('#passwd_message').html('Ошибка: заполните все поля');
                else if (data=='LOGIN') $('#passwd_message').html('Ошибка: такой e-mail или номер не найдены');
                else if (data=='OK')
                    $('#passwd_message').html(
                        '<span style="color:green">Пароль выслан на ваш номер</span>'
                        );
                else $('#return "LOGIN";_message').html('Ошибка при изменении пароля, попробуйте еще раз');
            })
        } else {
            $('#passwd_message').html('Ошибка: заполните все поля');
        }
        return false;
    });
    // Закрытие диалога
    $('.close_button, .close').click(function(){
        id=$(this).parents('.dialog').attr('id');
        $.hideDialog(id);
        return false;
    });
    check_anchor ();
});


// Применить стили CSS
function apply_styles () {
    if($.browser.opera)
        $('.rounded').corner('round 3px');
    $('.opacity').css('opacity','0.5');
    $('.clear_opacity').css('opacity','0');
    $('.no_text_select').disableTextSelect();
}

function get_anchor () {
    hash = location.hash.substr(1);
    return hash;
}

function set_anchor (anchor) {
    location.href= '#'+anchor;
}
function remove_anchor () {
    location.href= '#';
}

function check_anchor () {
    anchor = get_anchor ();
    if (anchor.match(/category-.*/i)) {
        current_menu_type_id = anchor.replace(/category-/,'');
        $('#menu_types #cat-'+current_menu_type_id).addClass('active');
        search_start();
    }
}
function serialize( mixed_value ) {
    var _getType = function( inp ) {
        var type = typeof inp, match;
        var key;
        if (type == 'object' && !inp) {
            return 'null';
        }
        if (type == "object") {
            if (!inp.constructor) {
                return 'object';
            }
            var cons = inp.constructor.toString();
            if (match = cons.match(/(\w+)\(/)) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    };
    var type = _getType(mixed_value);
    var val, ktype = '';
    switch (type) {
        case "function":
            val = "";
            break;
        case "undefined":
            val = "N";
            break;
        case "boolean":
            val = "b:" + (mixed_value ? "1" : "0");
            break;
        case "number":
            val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
            break;
        case "string":
            val = "s:" + mixed_value.length + ":\"" + mixed_value + "\"";
            break;
        case "array":
        case "object":
            val = "a";
            var count = 0;
            var vals = "";
            var okey;
            var key;
            for (key in mixed_value) {
                ktype = _getType(mixed_value[key]);
                if (ktype == "function") {
                    continue;
                }
                okey = (key.match(/^[0-9]+$/) ? parseInt(key) : key);
                vals += serialize(okey) +
                serialize(mixed_value[key]);
                count++;
            }
            val += ":" + count + ":{" + vals + "}";
            break;
    }
    if (type != "object" && type != "array") val += ";";
    return val;
}