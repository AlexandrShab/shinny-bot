<?php
require_once __DIR__ . '/BaseAPI.php';

class User
{
    public $is_admin;
    public $id;
    public $first_name;
    public $last_name;
    public $username;
    public $date;

    
    public function __construct($arrUser)
    {
        foreach ($arrUser as $key => $value) 
        {
                $this->$key = $value;
        }
    }
    public function setAdmin($adm)
    {
        $base = new Connect;
        $query = "UPDATE `users` SET is_admin='$adm' WHERE id ='$this->id';";
        
        $data = $base->prepare($query);
        $data->execute();
        
        return true;
    }
    
    public function isAdmin()
    {
        $base = new Connect;
        $query = "SELECT is_admin FROM users WHERE id ='$this->id' LIMIT 1";
        
        $data = $base->prepare($query);
        $data->execute();
        $arr = $data->fetch(PDO::FETCH_ASSOC);
        $this->is_admin = $arr['is_admin']; 
        return $this->is_admin;
    }
    public function update($arrUser)
    {
        $base = new Connect;
        $id = $arrUser["id"];
        $first_name = $arrUser["first_name"];
        $last_name = $arrUser["last_name"];
        $username = $arrUser["username"];
        $query = "UPDATE `users` SET first_name = '$first_name', last_name = '$last_name', username = '$username' WHERE id ='$id';";
        $data = $base->prepare($query);
        $data->execute();
        
        return true;
    }
    //~~~~~~~~~~~~~~~~~~~~

    /**
     * Возвращает имя пользователя в виде ссылки
     * @return string name + lastMame
     */
    public function getNameAsTgLink()
    {
            $name = "";
            $userId = $this->id;
        
            if (isset($this->first_name)) 
            {
                $name = $this->first_name;
                if ( isset($this->last_name) ) 
                {
                    $name .= " " . $this->last_name;
                }
            } else
                if (isset($this->username) ) 
                {
                    $name = $this->username;
                }
        
            $name = "<a href=\"tg://user?id=" . $userId . "\">" . $name . "</a>";
            
            return $name;
    }
        //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        public function getFullName() {
            $name = "";
        
            if (isset($this->first_name) ) {
            $name = $this->first_name;
            if (isset($this->last_name) ) {
                $name .= " " . $this->last_name;
            }
            } else
            if (isset($this->last_name) ) {
                $name = $this->last_name;
            }
            if (isset($this->username) ) {
            if ($name == ""){
                $name .= " / @" . $this->username;
            }
            }
            
            return $name;
        }
}