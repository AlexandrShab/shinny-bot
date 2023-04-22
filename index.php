<?php
date_default_timezone_set('Europe/Minsk');
$input = file_get_contents('php://input');
if (!$input) {
    echo "<h1>Нет страницы для отображения</h1>";
    exit();
}
//define('TELEGA_URL', 'https://api.telegram.org/bot' . TOKEN);
define('MY_ID', '968407066');
define('BOT_GROUP',    '-1001860899757');   //Bot_privateMessages
define('ADMINS_GROUP', '-1001822311523');   //info From Bots  
define('WORK_GROUP',   '-1001985844919'); //рабочая общая группа (тестовая)
define('ADMIN_CHATS', [BOT_GROUP, ADMINS_GROUP]);

define('BOT_NAME', '@ShinnyMir_bot');

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . "/functions/work.php";
$update = json_decode($input, TRUE);
file_put_contents('update.txt', '$update: ' . print_r($update, 1));

//var_dump($data);

$bot = new TBot;

/*
if (isset($update['update_id']))
{
    $text = 'Пришел новый update!' . json_encode($update);//
    $mes_id = $bot->sendMes(MY_ID, $text);
}*/
//~~~~~~~~~~~ Начало обработки апдейта типа callback_query ~~~~~~

if (isset($update['callback_query'])) {
    $tg_user = $update['callback_query']['from'];

    $chat = $update['callback_query']['message']['chat'];
    $callback_id = $update['callback_query']['id'];
    $callBackData = $update['callback_query']['data'];
    $dataBack = mb_substr($callBackData, 0, 7);

    $db = new BaseAPI;

    //~~~~~~~~~~~~~~~~~~~

}
//~~~~~~~~~~~ END обработки апдейта типа callback_query ~~~~~~
//~~~~~~~~~~~ Начало обработки апдейта типа message ~~~~~~
if (isset($update['message'])) {


    $msg = $update['message'];
    $tg_user = $msg['from'];
    $user_id = $msg['from']['id'];

    $chat = $msg['chat'];
    $chat_id = $chat['id'];
    $chat_type = $chat['type'];
    $chat_title = isset($chat['title']) ? $chat['title'] : $tg_user['first_name'] . ' ' . $tg_user['last_name'];
    $admins_area = false; //Проверяем является ли чат администраторским
    foreach (ADMIN_CHATS as $chat_adm) {
        if ($chat['id'] == $chat_adm) {
            $admins_area = true;
        } //ставим флаг 
    }
    $message_id = $msg['message_id'];
    $mes_text = isset($msg['text']) ? $msg['text'] : '';
    if (isset($msg['caption'])) {
        $mes_text = $msg['caption'];
    }

    //$menuButton = mb_substr($mes_text, 0, 1);
    $bot->sendAction($chat_id);
    if (($msg['text'] == ('/getChat' . BOT_NAME)) || ($msg['text'] == '/getChat')) {
        $res = $bot->getChat($chat_id);
        $bot->sendMes(MY_ID, json_encode($res));
    }
    if (isset($msg['web_app_data'])) //~~~ Проверяем приход данных из WebApp ~~~~
    {
        $bot->sendMes(MY_ID, 'button_text:' . $msg['web_app_data']['button_text'] . '\n' . 'data:\n' . $msg['web_app_data']['data']);
    }
    if (isset($msg['left_chat_member'])) {
    }
    //~~~~~~ Работаем с Юзером и базой ~~~
    $base = new BaseAPI;
    $userFromBase = $base->getUser($tg_user['id']);
    if ($userFromBase == false) {
        $base->addUser($tg_user);
        $userFromBase = $base->getUser($tg_user['id']);
    }
    $user = new User($userFromBase);
    $user->update($tg_user);
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    if ($chat_type == 'private') // Работаем в личке с ботом
    {

        //  пересылаем сообщение в группу "Личка бота"
        $name_as_link = $user->getNameAsTgLink();
        $user_id = $user->id;
        $bot->forwardMessage(BOT_GROUP, $chat_id,  $message_id);
        $bot->sendKeyboard(BOT_GROUP, "Боту пишет <b>$name_as_link</b> ID: $user_id", answerFromBot($user_id, $user->first_name));



        if (hasHello($mes_text)) {
            $hi = goodTime();
            $bot->sendMes($chat_id, "👋 " . $hi . ", <b>" . $user->first_name . "</b>,\n\nЯ Бот-Помощник магазина Шинный Мир!!!");
        }



        //~~~~~~~~~~~~~~~~~~~~~~~~~Обработка Команд Боту~~~~~~~~~~~~~~~~~~~~~~~~
        if (isset($msg['entities'])) {
            if ($msg['entities'][0]['type'] == 'bot_command') // Если сообщение - команда боту
            {
                if (($msg['text'] == ('/start' . BOT_NAME)) || ($msg['text'] == '/start')) {

                    $text2 = "👋 Здравствуйте, <b>" . $tg_user['first_name'] . "!</b>\n\n@ShinnyMir_bot  - Бот-Помощник магазина Шинный Мир!!!";
                    $bot->sendKeyboard($chat_id, $text2, writeToExpertKeyboard());
                    return;
                }

                if (($msg['text'] == ('/help' . BOT_NAME)) || ($msg['text'] == '/help')) {

                    $hi = goodTime();
                    $bot->sendMes($chat_id, $hi . ", <b>" . $user->first_name . "</b>");
                    $textAbout = "Я Бот-Помощник магазина Шинный Мир!!!";
                    $bot->sendKeyboard($chat_id, $textAbout, writeToExpertKeyboard());
                    return;
                }
                if (($msg['text'] == ('/admin' . BOT_NAME)) || ($msg['text'] == '/admin')) {
                    if ($user->is_admin == '1') {
                        $hi = goodTime();
                        $bot->sendMes($chat_id, $hi . ", <b>" . $user->first_name . "</b>");
                        $bot->sendKeyboard($chat_id, "Сайт Администратора", adminMenu());
                        return;
                    } else {
                        $bot->sendMes($chat_id, '<b>' . $tg_user['first_name'] . '</b>, Вас нет в списке Администраторов!!!');
                        return;
                    }
                }
            } // ~~~~~~~~конец обработки команд~~~~~~~
        }   //~~~ Конец работы с сущностями~~~~~~~~~
    } //~~~~ Конец работы в приватном чате ~~~~~~~

    //~~~~~~~  Проверяем чат~~~~~~~~~
    if ($chat_type != 'private') //Если чат не личка с ботом
    {
        $db = new BaseAPI;

        $db->updateChatList($chat); //Проверяем/добавляем чат


        if ($user->isAdmin()) // Если сообщение написал админ, - проверки не запускаем
        {
            $admins_area = true; //ставим флаг
        }
        if ($admins_area == true) return; //если область админов - выход




    } //конец обработки неприватных чатов
    //~~~~~~~~~~~chat checked~~~~~~~~~~~~~~~~~~~~

    exit;
} // ~~~~~ End обработки апдейта типа message ~~~~~~
echo "<h1>Нет страницы для отображения</h1>";


//~~~~~~~FUNCTIONS~~~~~~~~~~~~~~~~~~
