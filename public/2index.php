             <?php
 $to = 'reuckiy@yandex.ru';
             $subject = "Тестовое сообщение";
             $link = "localhost/email_verefication/$selector/$token";
             $message = "если ссылка не активна, переместите это письмо из папки СПАМ. Перейдите по ссылке для активации: <a href=\"$link\">АКТИВИРОВАТЬ УЧЕТНУЮ ЗАПИСЬ</a>";

             $headers = "MIME-Version: 1.0" . "\r\n";
             $headers .= "Content-type:text/html;charset=UTF-8" . "<br>";
             $headers .= 'From: sender@example.com' . "\r\n";
           
             if(mail($to, $subject, $message, $headers)) {
             echo "done";
            }else{
             echo "error";
            }

             echo "ok";


// <?php
// $to = 'reuckiy@yandex.ru';
//             $subject = 'тема сообщения';
//             $content = 'тестовое сообщение 1234йцуйцуйцвфыв одлфыводл';
//             if (mail($to, $subject, $content))
//             {
//                 echo "Success !!!";
//             }
//             else
//             {
//                 echo "ERROR";
//             }
//             echo "ok";


// $link = "https://www.example.com/page.php?param1=value1&param2=value2";