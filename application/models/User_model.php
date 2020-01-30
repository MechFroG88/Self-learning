<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends SL_Model{

    /**
     * Validaiton rules for register
     * 
     * @var array
     */
    private $register_rules = [
        "username"  => "required|min_length[5]",
        "password"  => "required",
    ];

    /**
     * Validation rules for login
     * 
     * @var array
     */
    private $login_rules = [
        "username"  => "required|min_length[5]",
        "password"  => "required",
    ];

    /**
     * User
     * 
     * @var object
     */
    public $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $token = $this->get_token();
        if($token && $this->is_exist("token", $token, T_USERS)){
            $this->user = $this->get_user_from_token($token);
        }
    }

    /**
     * Create user account
     * 
     * @param int user_type
     * @return int
     */
    public function create($data)
    {
        if($this->form_validation->validate($this->register_rules, $data)){
            $this->insert($data);
            return 200;
        }
        return 400;
    }

    /**
     * Insert user into db
     * 
     * @var array $data
     * @return bool
     */
    public function insert($data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->insert(T_USERS, $data);
    }

    /**
     * Get single user
     * 
     * @param int $user_id
     */
    public function get()
    {
        $user = $this->db->select('user_id, username')
                         ->get(T_USERS)
                         ->result();

        return $user;
    }

    /**
     * Login a user
     * 
     * @return int|array
     */
    public function login($data)
    {
        // Make sure client only send username, password and remember fields
        if($this->form_validation->validate($this->login_rules, $data)){
            // Get user
            $user = $this->db->where("username", $data['username'])
                             ->select('user_id, username, password')
                             ->get(T_USERS)
                             ->row();

            if(!isset($user)){
                return 401;
            }else if(password_verify($data['password'], $user->password)){
                $token = $this->update_token($user->user_id);
                $user->token = $token;
                unset($user->password);
                unset($user->user_id);
                return $user;
            }else{
                return 400;
            }
        }
        return 400;
    }

    /**
     * Logout a user
     * 
     * @return int
     */
    public function logout()
    {
        return 200;
    }

    /**
     * Delete user
     * 
     * @return int
     */
    public function delete($user_id)
    {
        if($this->is_exist("user_id", $user_id, T_USERS)){
            $this->db->where("user_id", $user_id)
                     ->delete(T_USERS);
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
     * Check user id
     * 
     * @return int
     */
    public function user_id()
    {
        if($this->is_logged_in()){
            return $this->user->user_id;
        }
        return -1;
    }
    /**
     * Check user if it has logged in
     * 
     * @return bool
     */
    public function is_logged_in()
    {
        return isset($this->user);
    }

    /**
     * Get token
     * 
     * @return string|bool
     */
    private function get_token()
    {
        return isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : false;
    }

    /**
     * Update token
     * 
     * @param int $userid
     * @return string
     */
    private function update_token($userid)
    {
        $token = $this->generate_token();
        $this->db->where("user_id", $userid)->update(T_USERS, [
            "token" => $token,
        ]);
        return $token;
    }

    /**
     * Generate token
     * 
     * @return string
     */
    private function generate_token()
    {
        $token = "";
        do{
            $token = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
                mt_rand( 0, 0xffff ),
                mt_rand( 0, 0x0fff ) | 0x4000,
                mt_rand( 0, 0x3fff ) | 0x8000,
                mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
            );
        }while($this->is_exist("token", $token, T_USERS));
        return $token;
    }

    /**
     * Get user from token
     * 
     * @param  string $token
     * @return object
     */
    private function get_user_from_token($token)
    {
        return $this->db->where("token" , $token)
                        ->get(T_USERS)
                        ->row();
    }
}