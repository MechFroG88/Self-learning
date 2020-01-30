<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SL_Controller extends CI_Controller{
    
    /**
     * Errors' messages
     * 
     * @var array
     */
    private $errors = [
        404 => 'Not Found',
        401 => 'Unauthorized',
        400 => 'Validation error.',
        500 => 'Internal Server Error',
    ];

    /**
     * Output json data
     * 
     * @param array $data
     * @param int $status
     * @return void
     */
    public function json($data, $status = 200){
        $this->output
             ->set_content_type('application/json')
             ->set_status_header($status)
             ->set_output(json_encode([
                 "status" => $status,
                 "data"   => $data
               ]));
    }

    /**
     * Output error
     *      
     * @param array $data
     * @param int $status
     * @return void
     */
    public function error($status , $data = ''){
        $this->output
             ->set_content_type('application/json')
             ->set_status_header($status)
             ->set_output(json_encode([
                 "status"  => $status,
                 "message" => $this->errors[$status],
                 "errors"  => $data
               ]));
    }
}
