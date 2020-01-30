<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SL_Model extends CI_Model {

    /**
     * Check existance
     * 
     * @param string $field
     * @param string $value
     * @return bool
     */
    public function is_exist($field, $value, $table)
    {
        return $this->db->where($field, $value)
                        ->count_all_results($table);
    }
}