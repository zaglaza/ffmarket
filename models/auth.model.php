<?php
/**
 * @package PapipuEngine
 * @author valmon, z-mode
 * @version 0.1
 * Модель для управления отображением баннеров
 */
class MD_Auth extends Model {

    /**
     * Инициализация модели
     * @return null
     */
    public static function initModel () {

    }

    /**
     * Регистрация
     * @return string
     */
    public static function registration ($name,$mail,$params=null) {
        // Проверяем на соответствие данных
        $mail = strtolower($mail);
        if(trim($name)=='' || trim($mail)=='') {
            return "SPACE";
        }
        if (!String::isEmail($mail)) return "NOT_MAIL";

        // Проверяем не зарегистрирован ли уже пользователь
        $u_mail=DB::getValue(Auth::getTable(), 'user_id', 'user_mail='.DB::quote($mail));
        if (!empty($u_mail)) return "MAIL_EXIST";

        // Регистрируем
        $password = substr(md5(time()), 0, 5);
        DB::insert(Auth::getTable(), Array(
                'user_password'=>DB::quote(md5($password)),
                'user_login'=>DB::quote($name),
                'user_mail'=>DB::quote($mail),
                'user_activate'=>1,
                'user_date_register'=>'NOW()',
                'user_ip_register'=>DB::quote(Router::getClientIp()),
                ), false);
        $text = 'Вы зарегистрировались на сайте ffmarker.ru. Ваш пароль '.$password."\r\n\r\n".'Your password: '.$password;

        mail($mail, 'Регистрация на сайте ffmarket.ru',$text,'From: FFMarket.ru <noreply@ffmarket.ru>' );
        //Mail::newMail($text, $mail, 'Регистрация на сайте ffmarket.ru');
        self::login($mail, $password, false);
        return "OK";
    }

    /**
     * Авторизация
     * @return string
     */
    public static function login ($login,$password,$remember,$params=null) {
        if(trim($login)=='' || trim($password)=='') {
            return "SPACE";
        }
        if (String::isEmail($login) or String::toPhone($login)) {
            $auth = Array (
                    'login'=>$login,
                    'password'=>$password,
                    'remember'=>$remember,
                    'crypted'=>false
            );
            if (User::login($auth)) return "OK";
            else return "NOT_EXIST";
        }
        return "LOGIN";
    }

    /**
     * Изменить пароль
     * @return string
     */
    public static function passwd ($login,$params=null) {
        if(trim($login)=='') {
            return "SPACE";
        }
        $result = false;
        if (String::isEmail($login)) {
            // Меняем пароль
            $password = substr(md5(time()), 0, 5);
            $result = DB::update(
                    Auth::getTable(), Array('user_password'=>md5($password)), 'user_mail='.DB::quote($login)
            );
        }
        
        if ($result) {
            $text = 'Новый пароль для входа в магазин: '.$password."\r\n\r\n".'Магазин эксклюзивных продуктов FF Market.ru'."\r\n\r\n".'New password to access the store: '.$password."\r\n\r\n".'Shop exclusive products FF Market.ru'."\r\n\r\n".' http://ffmarket.ru ';
            $err = mail($login, 'Востановление пароля ffmarket.ru',$text,'From: FFMarket.ru <noreply@ffmarket.ru>' );
            //Mail::newMail($text, $login, 'Восстановление пароля ffmarket.ru');
            //SMS::sendSms('+79503176167', $text);
            return "OK";
        } else {
            return "LOGIN";
        }
    }
}