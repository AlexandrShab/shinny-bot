<?php
    
class TBot
{
    
    public static $telega_url;
    
    public function __construct()
    {
        $base = new BaseAPI;
        $token = 'https://api.telegram.org/bot' .  $base->getToken();
        define('TELEGA_URL', $token);
        SELF::$telega_url = TELEGA_URL;
    }
        //~~~~~~~~~~~~~~~~~~~~
    /**
     * Отправка поста в телеграм 
     * @param string $method (метод телеграм)
     * @param array $data (массив с отправляемыми параметрами/данными)
     * @param array $headers (дополнительные заголовки)
     * 
     */
    function sendPost($method, $data, $headers = [])
    {
        $curl = curl_init();
        curl_setopt_array($curl,[
            CURLOPT_POST            => 1,
            CURLOPT_HEADER          => 0,
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_URL             => TELEGA_URL . '/' . $method,
            CURLOPT_POSTFIELDS      => json_encode($data),
            CURLOPT_HTTPHEADER      => array_merge(array("Content-Type: application/json"), $headers),
        ]);
        $result = curl_exec($curl);
        curl_close($curl);
        return (json_decode($result, 1) ? json_decode($result, 1) : $result);
    }
    //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    /**
     * Отправка сообщения в телеграм
     *      возвращает id оправленного сообщения
     * 
     * @param string $chat_id (идентификатор чата)
     * @param string $text (текст сообщения)
     * 
     * @return :id отправленного сообщения
     */
    function sendMes($chat_id, $text) //$mes_id
    {
        $method = 'sendMessage';
        $data_to_send = [
            'chat_id'    => $chat_id,
            'text'       => $text,
            'parse_mode' => 'HTML',
            ];
        $res = $this->sendPost($method, $data_to_send);
            
        return $res['result']['message_id'];
    }
    /**
     * Отправка сообщения в телеграм с клавиатурой
     * @param string $chat_id (идентификатор чата)
     * @param string $text текст сообщения
     * @param \keyboard объект клавиатуры (массив строк с массивом кнопок)
     * 
     * @return \res ответ от телеги
     */
    function sendKeyboard($chat_id, $text, $keyboard)
    {
        $method = 'sendMessage';
        $data_to_send = [
                            'chat_id' => $chat_id,
                            'text' => $text,
                            'parse_mode' => 'HTML',
                            'reply_markup' => $keyboard,
                        ];
        $res = $this->sendPost($method, $data_to_send);
        if(isset($keyboard['inline_keyboard']))
        {
            return $res['result']['message_id'];
        }
        return true;
    }
    function answerCallbackQuery($callback_id, $text, $alert)
    {
        $method = 'answerCallbackQuery';
        $data_to_send['callback_query_id'] = $callback_id;
        $data_to_send['text'] = $text;
        $data_to_send['show_alert'] = 'true';//$alert
        
        $res = $this->sendPost($method, $data_to_send);
    }
    function sendAction($chat_id)
    {
        $data['chat_id'] = $chat_id;
        $data['action'] = 'typing';
        $this->sendPost('sendChatAction', $data);
    }


  //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
  /**
   * Пересылка сообщения из одного чата в другой по mess_id
   * 
   * @param string $chat_id (чат куда)
   * @param string $chatFrom (чат откуда)
   * @param string $mes_id (id сообщения)
   * 
   * @return \res - ответ от сервера Телеграм
   */
  function forwardMessage($chat_id, $chatFrom, $mes_id)
    {
        $data['chat_id'] = $chat_id;
        $data['from_chat_id'] = $chatFrom;
        $data['message_id'] = $mes_id;
        $res = $this->sendPost("forwardMessage", $data);
        return $res;
    }
    /**
     * Удаление сообщения 
     * @param string ID чата сообщения
     * @param string mes_id сообщения
     * 
     * @return \res - ответ от сервера Телеграм
     */
    function delMess($chat_id, $mes_id)
    {
        $data['chat_id'] = $chat_id;
        $data['message_id'] = $mes_id;
        $res = $this->sendPost("deleteMessage", $data);
        return $res;
    }    
    function sendPhoto($chat_id, $text, $photo_url)
    {
        $data['chat_id'] =  $chat_id;
        $data['photo'] = $photo_url;//"https://drive.google.com/uc?export=view&id=1C3wJXOGNirdsbg77dcB8SAbhfogAN9cc";
        $data['caption'] = $text;
        $res = $this->sendPost('sendPhoto', $data);
        //return $res['result']['message_id'];
    }
    function sendMediaGroup($chat_id, $arrImages, $text)
    {
        $media = [
            ['type' => 'photo', 'media' => $arrImages[0], 'caption' => $text],
        ];
        for($i = 1; $i < count($arrImages); $i++) 
        {
            $media[$i] = ['type'=>'photo', 'media'=> $arrImages[$i]];
        }
        $data = [
          'chat_id'=> $chat_id, //'968407066'
          'media'=> $media,
        ];
        $res = $this->sendPost('sendMediaGroup', $data);
        return $res['result']['message_id'];
    }
    function getChat($chat_id)
    {
        $data['chat_id'] = $chat_id;
        $res = $this->sendPost('getChat', $data);
        return $res;
    }
    function getChatMember($chat_id, $user_id)
    {
        $data['chat_id'] = $chat_id;
        $data['user_id'] = $user_id;
        $res = $this->sendPost('getChatMember', $data);
        return $res['result']['user'];
    }
/**
 * ограничение пользователя в выбранном чате
 * @param string $chat_id
 * @param string $user_id
 */
function restrictUser($chat_id, $user_id)
{ 
    $method = 'restrictChatMember';
    $chatPermissions = [
        'can_send_messages' => false,
        'can_invite_users' => true,
        'can_send_media_messages' => false,
        'can_send_audios' => false,
        'can_send_documents' => false,
        'can_send_photos' => false,
        'can_send_videos' => false,
        'can_send_video_notes' => false,
        'can_send_voice_notes' => false,
        'can_send_other_messages' => false,
    ];
    $data = [
          'chat_id' => $chat_id,
          'user_id' => $user_id,
          'permissions' => $chatPermissions
    ];
    $res = $this->sendPost($method, $data);
    return $res;
  }
    /**
     * воостановление прав пользователя в выбранном чате
     * @param string $chat_id
     * @param string $user_id
     */
    function restoreUser($chat_id, $user_id)
    {    
        $method = 'restrictChatMember';
        $chatPermissions = [
            'can_send_messages' => true,
            'can_invite_users' => true,
            'can_send_media_messages' =>true,
            'can_send_audios' => true,
            'can_send_documents' =>true,
            'can_send_photos' =>true,
            'can_send_videos' =>true,
            'can_send_video_notes' =>true,
            'can_send_voice_notes' =>true,
            'can_send_other_messages' => true,
        ];
        $data = [
            'chat_id' => $chat_id,
            'user_id' => $user_id,
            'permissions' => $chatPermissions
      ];
        $res = $this->sendPost($method, $data);
        return $res;
    }
    /**
     * забанить пользователя в выбранном чате
     * юзер не сможет вернуться сам
     * @param string $chat_id
     * @param string $user_id
     */
    function banChatMember($chat_id, $user_id)
    {
        $method = 'banChatMember';
        $data = [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
        ];
        $res = $this->sendPost($method, $data);
        return $res;
    }
    /**
     * разабанить пользователя в выбранном чате.
     * если он все еще участник, выгонит без блокировки
     * @param string $chat_id
     * @param string $user_id
     * @param bool $only_if_banned
     */
    function unbanChatMember($chat_id, $user_id, $only_if_banned)
    {
        $method = 'unbanChatMember';

        $data = [
                'chat_id' => $chat_id,
                'user_id' => $user_id,
                'only_if_banned' => $only_if_banned,
        ];
        $res = $this->sendPost($method, $data);
        return $res;
    }
}
