<?php
require_once "MysqliDb.php";
require_once "hoff.php";

class Upstream
{
    function __construct()
    {
        $this->db = new Hoff(new MysqliDb('localhost', 'root', 'root', 'school'));
        $this->table = new MysqliDb('localhost', 'root', 'root', 'school');
    }

    public function users()
    {

        $this->db->column("user_id")     ->bigint(20)  ->primary()->unique()->autoIncrement()
                 ->column("username")    ->varchar(20) ->index()
                 ->column("password")    ->char(60)
                 ->column("token")       ->char(60)    ->index()
                 ->create("users");

        $this->table->insertMulti("users", [
                    [ "username" => "admin" , 
                    "password" => password_hash('admin', PASSWORD_DEFAULT) ,
                    "token" => 0,]
                ]);

        return $this;
    }

    public function classes()
    {

        $this->db->column("class_id")      ->bigint(20)  ->primary()->unique()->autoIncrement()
                 ->column("cn_class")      ->varchar(20)
                 ->column("en_class")      ->varchar(40) 
                 ->column("society")       ->varchar(50)      
                 ->column("theme")         ->varchar(60) 
                 ->column("detail")        ->text()
                 ->column("picture")       ->text()
                 ->create("classes");

        return $this;
    }

}

$up = new Upstream();
$up->users();
$up->classes();
