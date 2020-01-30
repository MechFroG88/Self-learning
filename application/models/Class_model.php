<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Class_model extends SL_Model{

    /**
     * Validaiton rules for class
     * 
     * @var array
     */
    private $class_rules = [
        "cn_class"  => "required|regex_match[/[\x{4e00}-\x{9fa5}]+/u]",
        "en_class"  => "required",
        "society"   => "required",
        "theme"     => "",
        "detail"    => "",
        "picture"   => ""
    ];

    /**
     * Class
     * 
     * @var object
     */
    public $class;

    /**
     * Create class
     * 
     * @param int 
     * @return int
     */
    public function create($data)
    {
        if($this->form_validation->validate($this->class_rules, $data)){
            $this->insert($data);
            return 200;
        }
        return 400;
    }

    /**
     * Insert class into db
     * 
     * @var array $data
     * @return bool
     */
    public function insert($data)
    {
        return $this->db->insert(T_CLASSES, $data);
    }

    /**
     * Get all class
     * 
     */
    public function get()
    {
        $class = $this->db->get(T_CLASSES)->result();
        return $class;
    }

    /**
     * Get single class
     * 
     * @param int $class_id
     */
    public function get_single($class_id)
    {
        $class = $this->db->where('class_id',$class_id)
                          ->get(T_CLASSES)
                          ->result();
        return $class;
    }   
    
    /**
     * Find class by cn_class OR theme OR society
     * 
     * @param int $class_id
     */
    public function find($key)
    {
        $keyword = $key['key'];
        $class = $this->db->like('cn_class',$keyword)
                          ->or_like('theme',$keyword)
                          ->or_like('society',$keyword)
                          ->get(T_CLASSES)
                          ->result();
        return $class;
    }

    /**
     * Update class
     * 
     * @param int class_type
     * @return int
     */
    public function update($class_id,$data)
    {
        if($this->is_exist("class_id", $class_id, T_CLASSES)){
            $this->db->where("class_id", $class_id)
                     ->update(T_CLASSES, $data);
            return 200;
        }
        return 400;           
    }

    /** that seals the deal
     * Delete class
     * 
     * @return int
     */
    public function delete($class_id)
    {
        if($this->is_exist("class_id", $class_id, T_CLASSES)){
            $this->db->where("class_id", $class_id)
                     ->delete(T_CLASSES);
            return 200;
        }
        return 400;
    }

    /***********************************************
    /***********************************************
    /*************** H E L P E R S *****************
    /***********************************************
    /***********************************************/


    /**
     * Check user if it has logged in
     * 
     * @return bool
     */
    public function is_logged_in()
    {
        return isset($this->user);
    }
}