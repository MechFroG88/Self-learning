<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends SL_Controller{

    /**
     * Register a user
     * 
     * @return
     */
    public function create()
    {
		if ($this->auth->is_logged_in()){
			$data = $this->input->post();
			$response = $this->auth->create($data);
            if ($response == 200){
                $this->json("Added Succesfully.",$response);
            } else {
                $this->error($response);
            }
        }
	}
	
    /**
     * Get user
     * 
     */
	public function get()
	{
		if ($this->auth->is_logged_in()){
			$data = $this->input->post();
			$this->json($this->auth->get($data));
		} else {
			$this->error(401);
		}
    }
    
    /**
     * Login a user
     * 
     * @return void
     */
    public function login()
    {
        $data = $this->input->post();
        $login = $this->auth->login($data);
        if (is_object($login)){
            $this->json($login);
        } else {
            $this->error($login, "Login Failed.");
        }
    }

    /**
     * Logout a user
     * 
     * @return void
     */
    public function logout()
    {
        $this->auth->logout();
        $this->json("Logout Successfully!");
    }

    /**
     * Delete user
     * 
     * @param int $user_id
     * @return void
     */
    public function delete($user_id)
    {
		if ($this->auth->is_logged_in()){
            $response = $this->auth->delete($user_id);
            if ($response == 200){
                $this->json("Deleted Succesfully.",$response);
            } else {
                $this->error($response);
            }
		} else {
			$this->error(401);
		}
	}
}