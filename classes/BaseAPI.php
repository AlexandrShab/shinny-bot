<?php
require_once __DIR__ . '/Connect.php';
class BaseAPI
{
    public function updateChatList($chat)
    {
        $id = $chat['id'];
        $title = $chat['title'];
        $username = $chat['username'];
        $type = $chat['type'];
        $db = new Connect;
        $query = "INSERT INTO chats (id, title, username, type) VALUES ('$id', '$title', '$username', '$type') 
            ON DUPLICATE KEY UPDATE title='$title', username='$username', type='$type';";

        $data = $db->prepare($query);
        $data->execute();
        
        return true;
    }
    public function getChatById($chat_id)
    {
        $base = new Connect;
        $query = "SELECT * FROM chats WHERE id = '$chat_id' LIMIT 1";
        $data = $base->prepare($query);
        $data->execute();
        $chat = $data->fetch(PDO::FETCH_OBJ);
        return $chat;
    }
    public function getChatList()
    {
        $base = new Connect;
        $query = "SELECT * FROM chats";
        $data = $base->prepare($query);
        $data->execute();
        $arrChats = array();
        $i=0;
        while($chat = $data->fetch(PDO::FETCH_OBJ))
        {
            $arrChats[$i] = $chat;
            $i++;
        }
        return $arrChats;
    }
    public static function getToken()
    {
        $base = new Connect;
        $query = "SELECT value FROM vars WHERE name='token'";
        $data = $base->prepare($query);
        $data->execute();
        $arrUsers = array();
        $token = $data->fetch(PDO::FETCH_ASSOC);
        return $token['value'];   
    }
    public function getUsers()
    {
        $base = new Connect;
        $query = "SELECT * FROM users";
        $data = $base->prepare($query);
        $data->execute();
        $arrUsers = array();
        while($user = $data->fetch(PDO::FETCH_OBJ))
        {
            $arrUsers[] = $user;
        }
        return $arrUsers;
    }
    /**
     * Возвращает юзера по ID в виде массива 
     * @param $user_id
     */
    public function getUser($id)
    {
        $db = new Connect;
        $user = array();
        $data = $db->prepare("SELECT * FROM users WHERE id ='$id' LIMIT 1");
        $data->execute();
        $user = $data->fetch(PDO::FETCH_ASSOC);
            return $user;               
    }
    public function addUser($user)
    {
        $db = new Connect;
        $id = $user["id"] ;
        $first_name = $user["first_name"];
        $last_name = $user["last_name"];
        $username = $user["username"];
        $query = "INSERT INTO users (id, first_name, last_name, username) VALUES ('$id', '$first_name', '$last_name', '$username');";
        $data = $db->prepare($query);
        $data->execute();
        return true;
    }
    public function addWord($word)
    {
         $db = new Connect;
         $query = "INSERT INTO bad_words (word) VALUES ('$word');";
        $data = $db->prepare($query);
        $data->execute();
        return true;
    }
    public function getBadWords()
    {
        $base = new Connect;
        $query = "SELECT * FROM bad_words";
        $data = $base->prepare($query);
        $data->execute();
        $words = array();
        while($word = $data->fetch(PDO::FETCH_OBJ))
        {
            $words[] = $word->word;
        }
        return $words;
    }
    public function findProd($sample)
    {
        $base = new Connect;
        $query = "SELECT * FROM products WHERE name LIKE '%$sample%'";
        $data = $base->prepare($query);
        $data->execute();
        $products = array();
        while($product = $data->fetch(PDO::FETCH_OBJ))
        {
            $products[] = $product;
        }
        return $products;
    }
    public function storeMessage($text, $user_id,  $mes_id)
    {
        $base = new Connect;
        $query = "INSERT INTO users_chats (user_id, message_id, text) VALUES ('$user_id', '$mes_id', '$text');";
        $data = $base->prepare($query);
        $res = $data->execute();
        return $res;
    }
    public function saveBanData($user_id, $chat_id, $message_id, $mes_text)
    {
        $base = new Connect;
        $query = "INSERT INTO black_list (user_id, chat_id, message_id, mes_text) 
                    VALUES ('$user_id', '$chat_id', '$message_id', '$mes_text');";
        $data = $base->prepare($query);
        $res = $data->execute();
        
        $query = "SELECT MAX(ban_id) FROM black_list;";
        $data = $base->prepare($query);
        $data->execute();
        $ban_id = $data->fetch(PDO::FETCH_ASSOC);
        return $ban_id['MAX(ban_id)'];
    }
    public function updateBanData($user_id, $chat_id, $message_id, $menu_id)
    {
        $base = new Connect;
        $query = "UPDATE black_list SET menu_id = '$menu_id' 
                WHERE chat_id = '$chat_id' AND user_id = '$user_id' AND message_id = '$message_id';";
        $data = $base->prepare($query);
        $res = $data->execute();
        return $res;
    }
    public function getBanData()
    {
        $base = new Connect;
        $query = "SELECT * FROM black_list ORDER BY date DESC;";
        $data = $base->prepare($query);
        $data->execute();
        $res = array();
        $i = 0;
        while($req = $data->fetch(PDO::FETCH_OBJ))
        {
            $res[$i] = $req;
            $i++;
        }
        
        return $res;
    }
    public function getBanedUser($ban_id)
    {
        $base = new Connect;
        $query = "SELECT black_list.user_id, black_list.chat_id, black_list.mes_text, black_list.menu_id, users.first_name, users.last_name 
        FROM black_list 
        JOIN users ON black_list.user_id=users.id WHERE ban_id = '$ban_id';";
        $data = $base->prepare($query);
        $data->execute();
        $res = $data->fetch(PDO::FETCH_OBJ);
            return $res;
    }
    public function getPrivateMessages()
    {
        $base = new Connect;
        $query = "SELECT * FROM users_chats ORDER BY date DESC;";
        $data = $base->prepare($query);
        $data->execute();
        $res = array();
        $i = 0;
        while($req = $data->fetch(PDO::FETCH_OBJ))
        {
            $res[$i] = $req;
            $i++;
        }
        
        return $res;
    }
    public function addChatMember($user_id, $chat_id)
    {
        $base = new Connect;
        $query = "SELECT * FROM chat_members WHERE id='$user_id';";
        $data = $base->prepare($query);
        $data->execute();
        $res = array();
        $memberOfChat = false;
        $i = 0;
        while($req = $data->fetch(PDO::FETCH_OBJ))
        {
            $res[$i] = $req;
            $i++;
            if ($req->chat_id == $chat_id)
            {
                $memberOfChat = true;
            }
        }
        if ($i == 0 || $memberOfChat == false)// Если юзера нет в таблице или нет в текущем чате
        {
            $query = "INSERT INTO chat_members (id, chat_id) VALUES ('$user_id', '$chat_id');";
            $data = $base->prepare($query);
            $data->execute();
        }else
        {
            $now = date("Y-m-d H:i:s");
            $query = "UPDATE chat_members SET last_date = '$now' WHERE id ='$user_id' AND chat_id = '$chat_id';";
            $data = $base->prepare($query);
            $data->execute();
        }
        return true;
    }
    public function getChatMembers()
    {
      $base = new Connect;
         $query = "SELECT * FROM chat_members ORDER BY first_date DESC;";
        $data = $base->prepare($query);
        $data->execute();
        $res = array();
        $i = 0;
        while($req = $data->fetch(PDO::FETCH_OBJ))
        {
            $res[$i] = $req;
            $i++;
        }  
        return $res;
    }
}   
