<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Model 
{
    /**
     * Load the table Name and primary key id
     */
	function __construct() 
    {
        $this->tableName = 'users_new';
        $this->primaryKey = 'id';
    }

    /**
     * select the user is user are exist in database.
     * if user is not exist the insert the user information
     * if user is exist in database then update her information
     * @param  array  $data 
     * @return boolean       
     */
    public function checkUser($data = array())
    {
        $this->db->select($this->primaryKey);
        $this->db->from($this->tableName);
        $this->db->where(array('oauth_provider'=>$data['oauth_provider'],'oauth_uid'=>$data['oauth_uid']));
        $prevQuery = $this->db->get();
        $prevCheck = $prevQuery->num_rows();
        
        if($prevCheck > 0)
        {
            $prevResult = $prevQuery->row_array();
            $data['modified'] = date("Y-m-d H:i:s");
            $update = $this->db->update($this->tableName,$data,array('id'=>$prevResult['id']));
            $userID = $prevResult['id'];
        }else{
            $data['created'] = date("Y-m-d H:i:s");
            $data['modified'] = date("Y-m-d H:i:s");
            $insert = $this->db->insert($this->tableName,$data);
            $userID = $this->db->insert_id();
        }

        return $userID?$userID:FALSE;
    }
}
