<?php

namespace app;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function dd($a) {
    echo "<pre>";
    var_dump($a);
    exit;
}

use app\QueryBuilder;
use exception;
use PDO;
use League\Plates\Engine;
use Tamtamchik\SimpleFlash\Flash;
use function Tamtamchik\SimpleFlash\flash;
use Delight\Auth\Auth;



class HomeController
{
    private $templates, $db, $auth, $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder, PDO $pdo, Engine $engine, Auth $auth)
    {
        $this->queryBuilder = $queryBuilder; // phpdi сюда запихал и создал уже new QueryBuilder в $queryBuilder
        $this->db = $pdo; // из index у нас возвращается подключение с настройками
        $this->templates = $engine; // из index у нас возвращается new Engine('../app/views')
        $this->auth = $auth; // из index у нас уже возвращается new Auth($container->get('PDO'));

//        $auth = new \Delight\Auth\Auth($pdo, $throttling = false, $disableThrottlingExceptions = true); // для отлючения счетчика запросов и выброса исключений
    }

    public function test()
    {

        Flash::message("тестовое flash сообщение", 'info');
        echo $this->templates->render('users', ['name'=>'test']);
    }

    public function Add_role(){
        try {
            $this->auth->admin()->addRoleForUserById('18', \Delight\Auth\Role::ADMIN);
        }
        catch (\Delight\Auth\UnknownIdException $e) {
            die('Unknown user ID');
        }
        echo "done";
    }

    public function page_login() {
        echo $this->templates->render('page_login', ['name' => 'Jonathan']);
    }

    public function make_registration() {


            try {
                $userId = $this->auth->register($_POST['email'], $_POST['password'], $_POST['username'], function ($selector, $token) {

                    $to = $_POST['email'];
                    $subject = "Тестовое сообщение";
                    $link = "localhost/email_verefication/$selector/$token";
                    $message = "если ссылка не активна, переместите это письмо из папки СПАМ. Перейдите по ссылке для активации: <a href=\"$link\">АКТИВИРОВАТЬ УЧЕТНУЮ ЗАПИСЬ</a>";

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "<br>";
                    $headers .= 'From: sender@example.com' . "\r\n";

                    if(!mail($to, $subject, $message, $headers)){
                        echo "error";
                        exit;
                    };
                });

                Flash::message("подтвердите email {$_POST['email']}, Вам отправлена ссылка", 'info');
                header('location: /');
                exit;
            }
            catch (\Delight\Auth\InvalidEmailException $e) {
                die('Invalid email address');
            }
            catch (\Delight\Auth\InvalidPasswordException $e) {
                die('Invalid password');
            }
            catch (\Delight\Auth\UserAlreadyExistsException $e) {
                Flash::message("email {$_POST['email']} уже занят", 'error');
                header('location: /page_register');
                exit;
            }
            catch (\Delight\Auth\TooManyRequestsException $e) {
                die('Too many requests');
            }

            echo "<br> ok";

    }

    public function Page_register() {
        echo $this->templates->render('page_register', ['name' => 'Jonathan']);
    }
    public function Page_users() {
        if ($this->auth->isLoggedIn()) {
            echo $this->templates->render('users', [''=>'']); // переводим на страницу
        }
        else {
            Flash::message("Авторизуйтесь!", 'info'); // иначе переводим на страницу авторизации
            header('location: /');
            exit;
        }

    }

    public function email_verefication($vars) {

        try {
            $this->auth->confirmEmail($vars['selector'], $vars['token']);

            Flash::message("email успешно подтверждён", 'success');
            header('location: /');
        }
        catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
            die('Invalid token');
        }
        catch (\Delight\Auth\TokenExpiredException $e) {
            die('Token expired');
        }
        catch (\Delight\Auth\UserAlreadyExistsException $e) {
            die('Email address already exists');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }
    }

    public function Sign_in() {
        try {
            $this->auth->login($_POST['email'], $_POST['password']);
        }
        catch (\Delight\Auth\InvalidEmailException $e) {
            die('Wrong email address');
        }
        catch (\Delight\Auth\InvalidPasswordException $e) {
            die('Wrong password');
        }
        catch (\Delight\Auth\EmailNotVerifiedException $e) {
            die('Email not verified');
        }
        catch (\Delight\Auth\TooManyRequestsException $e) {
            die('Too many requests');
        }


        header('location: /users');
        exit;
    }

}