<?php
$botUrl = "https://shinny-bot.shinny-mir.by";
$setwebHookURL = "";
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
        
        $token = $data->fetch(PDO::FETCH_ASSOC);
        return $token['value'];   
    }
    public function getUsers()
    {
        $base = new Connect;
        $query = "SELECT * FROM bot_users";
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
        $data = $db->prepare("SELECT * FROM bot_users WHERE user_id ='$id' LIMIT 1");
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
        $query = "INSERT INTO bot_users (user_id, first_name, last_name, username) VALUES ('$id', '$first_name', '$last_name', '$username');";
        $data = $db->prepare($query);
        $data->execute();
        return true;
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
    
    
   
}