<?php
require_once "MysqliDb.php";
require_once "hoff.php";

class Downstream
{
    function __construct()
    {
        $this->db = new Hoff(new MysqliDb('localhost', 'root', 'root', 'school'));
    }

    public function users()
    {
        $this->db->drop("users");
        return $this;
    }

    public function classes()
    {
        $this->db->drop("classes");
        return $this;
    }
}

$down = new Downstream();
$down->users();
$down->classes();
