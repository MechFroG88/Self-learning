<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Classes extends SL_Controller{


    /**
     * Create a class
     * 
     * @return
     */
    public function create()
    {
	      if ($this->auth->is_logged_in()){
	      	$data = $this->input->post();
	        $response = $this->class->create($data);
          if ($response == 200){
              $this->json("Added Succesfully.",$response);
          } else {
              $this->error($response);
          }
		    } else{
          return $this->error(401);
        }
	}
	
    /**
     * Get class
     * 
     */
	public function get()
	{
    if ($this->auth->is_logged_in()){
      $this->json($this->class->get());
    }else{
      return $this->error(401);
    }
  }

    /**
     * Get single class
     * 
     */
	public function get_single($class_id)
	{
		$this->json($this->class->get_single($class_id));
  }
    
    /**
     * Find class by cn_class OR theme OR society
     * 
     */
	public function find()
	{
        $key = $this->input->post();
		$this->json($this->class->find($key));
    }

    /**
     * Update a class
     * 
     * @return
     */
    public function update($class_id)
    {
		if ($this->auth->is_logged_in()){
			$data = $this->input->post();
			$this->json("Updated Succesfully", $this->class->update($class_id,$data));
		} else {
			$this->error(401);
		}
    }
    
    /**
     * Delete class
     * 
     * @param int $class_id
     * @return void
     */
    public function delete($class_id)
    {
		if ($this->auth->is_logged_in()){
            $response = $this->class->delete($class_id);
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