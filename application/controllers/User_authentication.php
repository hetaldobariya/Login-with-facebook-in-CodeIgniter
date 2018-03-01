<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_authentication extends CI_Controller {

	function __construct() 
	{
        parent::__construct();

        // Load facebook library
        $this->load->library('facebook');

        //Load user model
        $this->load->model('Users');
    }

    public function index()
    {
        $userData = array();

        // Check if user is logged in
        if($this->facebook->is_authenticated()){
            // Get user facebook profile details
            $userProfile = $this->facebook->request('get', '/me?fields=id,first_name,last_name,email,gender,locale,picture,birthday');

            // Preparing data for database insertion
            $userData['oauth_provider'] = 'facebook';
            $userData['oauth_uid'] = $userProfile['id'];
            $userData['first_name'] = $userProfile['first_name'];
            $userData['last_name'] = $userProfile['last_name'];
          
            if(empty($userProfile['email']))
            {
            	$userData['email'] = 'No Email Id Provide by user';
            }
            else
            {            	
            	$userData['email'] = $userProfile['email'];
            }

            $userData['gender'] = $userProfile['gender'];
            $userData['locale'] = $userProfile['locale'];
            $userData['locale'] = $userProfile['locale'];
            $userData['link'] = 'https://www.facebook.com/'.$userProfile['id'];
            $userData['picture'] = $userProfile['picture']['data']['url'];

            if(empty($userProfile['birthday']))
            {
            	$userData['birthday'] = '0000/00/00';
            }
            else
            {   
                $date=date_create($userProfile['birthday']);  
                $final_date = date_format($date,"Y/m/d");    	
                $userData['birthday'] =  $final_date;
                //print_r($userData['birthday']);
            }


            // Insert or update user data
            $userID = $this->Users->checkUser($userData);

            // Check user data insert or update status
            if(!empty($userID)){
                $data['userData'] = $userData;
                $this->session->set_userdata('userData',$userData);
            }else{
               $data['userData'] = array();
            }

            // Get logout URL
            $data['logoutUrl'] = $this->facebook->logout_url();
        }else{
            $fbuser = '';

            // Get login URL
            $data['authUrl'] =  $this->facebook->login_url();
        }

        // Load login & profile view
        $this->load->view('user_authentication/index',$data);
    }

    public function logout() 
    {
        // Remove local Facebook session
        $this->facebook->destroy_session();

        // Remove user data from session
        $this->session->unset_userdata('userData');

        // Redirect to login page
        redirect('/user_authentication');
    }
	
}
