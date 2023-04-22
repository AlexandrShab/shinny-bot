<?php
function mesHasBadWords($userMessage)
{
    $db = new BaseAPI;  
    $words = $db->getBadWords();// Получаем из базы массив со словами
    $sample = mb_strtolower($userMessage, 'UTF-8'); //Перевод текста сообщения в нижний регистр
    $arrFinded = [];
    for($i=0;$i<count($words);$i++)
    {
        if(strpos($sample, $words[$i]) > -1)
        {
            $arrFinded[] = $words[$i];
        }
    }
    if(count($arrFinded)>0)
    {
        return $arrFinded;
    }
    return false;
}
function hasHello($sample)
{
    $sample = mb_strtolower($sample, 'UTF-8');
  if ((strpos($sample,'здравс')>-1)
  or (strpos($sample,'привет')>-1)
  or (strpos($sample,'добрый')>-1)
  or (strpos($sample,'hello')>-1)
  or (strpos($sample,'драст')>-1)
    ){return true;}
    else return false;
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function goodTime()
{
    date_default_timezone_set('Europe/Moscow'); 
    //$today = date("Y-m-d");
    $time = date("H"); // 17:16:18
    //$today = date("Y-m-d H:i:s"); // 2001-03-10 17:16:18 (the MySQL DATETIME format)
    $hiMes = 'Здравствуйте';
 
      if ($time > 5 && $time < 10) {  $hiMes = 'Доброе утро';}
      if ($time > 9 && $time < 18) {  $hiMes = 'Добрый день';}
      if ($time >17 && $time < 24) {  $hiMes = 'Добрый вечер';}
      if ($time >-1 && $time < 6 ) {  $hiMes = 'Доброй ночи';}

    return $hiMes;
}
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
function writeToExpertKeyboard()
{
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '▶️ НАПИСАТЬ АДМИНИСТРАТОРУ', 'url' => 'http://t.me/alexandershab'],
            ],
        ],
    ];
    return $keyboard;
}
function mainMenuKeys()
{
    $keyboard = [
        'resize_keyboard' => true,
        'keyboard' => [
            [
                ['text' => '🔍 ПРОВЕРИТЬ ТОВАР'],
            ],
            [
                ['text' => '🚀 О нас'], ['text' => '✏️ Задать вопрос'],
            ],
            [
                ['text' => '🔗 Ссылки на наши ресурсы'], ['text' => '📞 Связаться с нами'],
            ],
        ],
    ];
    return $keyboard;
}
function secondMenuKyes()
{
    $keyboard = [
        'resize_keyboard'=> true,
        'keyboard' => [
            [        
                ['text' => '🎪 Главное меню'], ['text' => '✏️ Задать вопрос'],
            ],
        ],
    ];
    return $keyboard;
}
function linksMenu()
{
    $keyboard = [
        'inline_keyboard' =>
          [
      [['text' => '👨‍💼 Эксперт по сертификации', 'url' => 'http://t.me/blondin_man']],
      [['text' => '💬 Telegram-чат', 'url' => 'http://t.me/sertsale']],
      [['text' => '🔊 Telegram-канал', 'url' => 'http://t.me/sertsale_ru']],
      [['text' => '🌐 Наш сайт', 'url' => 'https://sertsale.ru']],
          ],
    ];
    return $keyboard;
}
function inLineWebAppButton()
{
    $keyboard = [
        'inline_keyboard' =>
          [
      [['text'=> 'Продвинутый поиск','web_app' => ['url'=> 'https://sertbot.shinny-mir.by/pages/find-product.php']]],
            ],
          /*'resize_keyboard': true,
          'one_time_keyboard': true,*/
    ];
    return $keyboard;      
}
function adminMenu()
{
    $keyboard = [
        'inline_keyboard' =>
          [
      [['text' => 'Страница Админа', 'url' => 'https://bot.shinny-mir.by/admin-serv.php']],
      
          ],
    ];
    return $keyboard;
}
function answerFromBot($chat_id, $name)
{
     $keyboard = [
        'inline_keyboard' =>
          [
      [['text'=> 'Ответить ботом','url' => "http://bot.shinny-mir.by/admin-serv.php?method=sendMessage&chat_id=$chat_id&name=$name"]],
            ],
    ];
    return $keyboard;   
}
function banKeyboard($ban_id)
{
    $keyboard = [
        'inline_keyboard' =>
          [
      [['text' => 'Выгнать', 'callback_data' => 'banuser' . $ban_id],
       ['text' => 'Снять запрет', 'callback_data' => 'unbanus' . $ban_id]],
          ],
    ];
    return $keyboard;
}