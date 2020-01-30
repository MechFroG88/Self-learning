<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HS_Form_validation extends CI_Form_validation{

    /**
     * Validate data
     * 
     * @param array $rules
     * @param array $data
     * @return bool
     */
    public function validate($rules, $data, $strict = true){
        $this->_field_data = array();
        $generated_rules = array();
        foreach($rules as $key=>$rule){
            array_push($generated_rules, [
                "field" => $key,
                "label" => ucfirst($key),
                "rules" => $rule
            ]);
        }
        $this->set_rules($generated_rules)->set_data($data);
        
        if($this->run()){
            if($strict){
                $rules_key = array_keys($rules);
                $data_key = array_keys($data);
                sort($rules_key);
                sort($data_key);
                return $rules_key == $data_key;
            }
            return true;
        }
        return false;
    }
}