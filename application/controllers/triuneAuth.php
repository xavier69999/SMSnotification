<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class triuneAuth extends MY_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *      https://tua.edu.ph/triune/auth
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://tua.edu.ph/triune
     *
     * AUTHOR: Xavier Sullano   
     * DESCRIPTION: Authentication Controller. Included login, registration, reset password, create token
     * DATE CREATED: March 24, 2018
     * DATE UPDATED: March 24, 2018
     */


function __construct() {
    parent::__construct();
    $this->load->library('session');
    $this->load->library('form_validation'); 
    $this->load->library('encryption');     
    $this->status = $this->config->item('status'); 
    $this->roles = $this->config->item('roles');
    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
}//function __construct()

public function index() {
    echo "HOME";
}
public function loginView()
{
    header("Access-Control-Allow-Origin: *");
    $data = array();
    $data['title'] = "LOGIN";
    $this->load->view('authentication/header', $data);
    $this->load->view('authentication/login', $data);
    $this->load->view('authentication/footer', $data);

}
public function loginControl()
    {
        $this->form_validation->set_rules('userName', 'User Name', 'required');    
        $this->form_validation->set_rules('password', 'Password', 'required'); 
        
        if($this->form_validation->run() == FALSE) {
            header("Access-Control-Allow-Origin: *");
            $data['title'] = "LOGIN";
            $this->session->set_flashdata('msg', 'Username or password should be provided');
            $this->load->view('authentication/header', $data);
            $this->load->view('authentication/login', $data);
            $this->load->view('authentication/footer', $data);
        }else{
            
            $post = $this->input->post();  
            $clean = $this->security->xss_clean($post);
            
            $getPassword = $this->_getRecordsData($data = array('*'), $tables = array('triune_user'), $fieldName = array('userName'), $where = array($clean['userName']), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, $fieldNameLike = null, $like = null, $whereSpecial = null, $groupBy = null );

            if(!empty($getPassword)) {
                $decryptedPassword = $this->encryption->decrypt($getPassword[0]->password);
            
                if($decryptedPassword == $clean['password']) {
                    foreach($getPassword[0] as $key=>$val){
                        $this->session->set_userdata($key, $val);
                    }
        
                    $triuneUserUpdate = array(
                        'lastLogin' => date('Y-m-d h:i:s A'),
                    );
                    $recordUpdated = $this->_updateRecords($tableName = 'triune_user', $fieldName = array('ID'), $where = array($getPassword[0]->ID), $triuneUserUpdate);
        
                    echo "login successfull";
        
                } else {
                    $this->session->set_flashdata('msg', 'The login was unsucessful, username and password did not match');
                    redirect(base_url());
                }
            
            } else {
                    $this->session->set_flashdata('msg', 'The login was unsucessful');
                    redirect(base_url());
            }
                        
            
        }
        
    }
public function registrationView()
    {
        header("Access-Control-Allow-Origin: *");
        $data = array();
        $data['title'] = "REGISTRATION";
        $this->load->view('authentication/header', $data);
        $this->load->view('authentication/registration', $data);
        $this->load->view('authentication/footer', $data);
    }
    public function checkUserName() {
        if(!empty($_POST["userName"])) {
            $userName = $_POST["userName"];
            $userRecord = $this->_getRecordsData($data = array('userName'), $tables = array('triune_user'), $fieldName = array('userName'), $where = array($userName), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                $fieldNameLike = null, $like = null, 
                $whereSpecial = null, $groupBy = null );

            if(empty($userRecord)) {
                echo 0;
            } else {
                echo 1;
            }           
        }
    }
    public function createToken() {
        $this->form_validation->set_rules('userName', 'User Name', 'required|alpha_numeric');
        $this->form_validation->set_rules('emailAddress', 'Email Address', 'required|valid_email');  
        $this->form_validation->set_rules('lastName', 'Last Name', 'required|alpha_numeric');    
        $this->form_validation->set_rules('firstName', 'First Name', 'required|alpha_numeric');    
        $this->form_validation->set_rules('middleName', 'Middle Name', 'required|alpha_numeric');    
        $this->form_validation->set_rules('studentNumber', 'Student Number', 'required|regex_match[/^\d+[-]?\d+$/]');    
        $this->form_validation->set_rules('birthDate', 'Birth Date', 'required|regex_match[/\d{4}\-\d{2}-\d{2}/]');    

        $emailAddress = $this->input->post('emailAddress');
        $userName = $this->input->post('userName');
        $lastName = $this->input->post('lastName');
        $middleName = $this->input->post('middleName');
        $firstName = $this->input->post('firstName');
        $birthDate = $this->input->post('birthDate');
        $studentNumber = $this->input->post('studentNumber');

        $this->session->set_flashdata('emailAddress', $emailAddress);
        $this->session->set_flashdata('userName', $userName);
        $this->session->set_flashdata('lastName', $lastName);
        $this->session->set_flashdata('middleName', $middleName);
        $this->session->set_flashdata('firstName', $firstName);
        $this->session->set_flashdata('birthDate', $birthDate);
        $this->session->set_flashdata('studentNumber', $studentNumber);


        if ($this->form_validation->run() == FALSE) {   

            $this->session->set_flashdata('msg', 'All fields are required to be proper. Please try again!');
            redirect(base_url().'auth/register');
        }else{    
            

            $emailAddressExist = $userRecord = $this->_getRecordsData($data = array('emailAddress'), $tables = array('triune_user'), $fieldName = array('emailAddress'), $where = array($emailAddress), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                $fieldNameLike = null, $like = null, 
                $whereSpecial = null, $groupBy = null );

            $userNameExist = $userRecord = $this->_getRecordsData($data = array('userName'), $tables = array('triune_user'), $fieldName = array('userName'), $where = array($userName), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                $fieldNameLike = null, $like = null, 
                $whereSpecial = null, $groupBy = null );
                    
                
            if(!empty($userNameExist)){
                $this->session->set_flashdata('msg', 'Username Already Exist!');
                redirect(base_url().'auth/register');
    
            } elseif(!empty($emailAddressExist)) {
                
                $this->session->set_flashdata('msg', 'Email Address Already Exist!');
                redirect(base_url().'auth/register');
                
            } else {

                $userEnrolled = $this->_getRecordsData($data = array('ID'), 
                    $tables = array('triune_personal_data'), 
                    $fieldName = array('lastName', 'firstName', 'middleName', 'studentNumber', 'birthDate'), 
                    $where = array($lastName, $firstName, $middleName, $studentNumber, $birthDate), 
                    $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                    $fieldNameLike = null, $like = null, 
                    $whereSpecial = null, $groupBy = null );
                
                if(!empty($userEnrolled)) {


                    $clean = $this->security->xss_clean($this->input->post(NULL, TRUE));

                    $triune_user = null;
                    $triune_user = array(
                          'userName' => $clean['userName'],
                          'emailAddress' => $clean['emailAddress'],
                          'firstNameUser' => $clean['firstName'],
                          'lastNameUser' => $clean['lastName'],
                          'userNumber' => $clean['studentNumber'],
                          'role' => $this->roles[0],
                          'status' => $this->status[0],
                    ); 
                    $id = $this->_insertRecords($tableName = 'triune_user', $triune_user);
                    

                    $qstring = $this->_insertToken($id);

                    $url = site_url() . 'triuneAuth/complete/token/' . $qstring;
                    $link = '<a href="' . $url . '">' . $url . '</a>'; 
                               
                    $message = '';                     
                    $message .= '<strong>You have signed up with our website</strong><br>';
                    $message .= '<strong>Please click:</strong> ' . $link;                          
 
                    //echo $message; //send this in email
                    $this->_sendSMS();

                    $this->_sendMail($toEmail ="rdlagdaan@gmail.com", $subject = "token created", $message);

                } else {
                    $this->session->set_flashdata('msg', "The personal information you've typed do not matched with your current records!");
                    redirect(base_url().'auth/register');
                }

            }           
        }

    }
    public function complete() {
        $token = $this->_base64urlDecode($this->uri->segment(4)); 
        $cleanToken = $this->security->xss_clean($token);
        
        $userInfo = $this->_isTokenValid($cleanToken); //either false or array(); 

        if(empty($userInfo)) {
            $this->session->set_flashdata('msg', 'Token is invalid or expired');
            redirect(base_url().'auth/register');           
        }

        $data = array(
            'firstName'=> $userInfo[0]->firstNameUser, 
            'emailAddress'=>$userInfo[0]->emailAddress, 
            'userID'=>$userInfo[0]->ID, 
            'userName'=>$userInfo[0]->userName, 
            'token'=>$this->_base64urlEncode($token)
        );


        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passwordConfirmation', 'Password Confirmation', 'required|matches[password]'); 

        if ($this->form_validation->run() == FALSE) { 
 header("Access-Control-Allow-Origin: *");
 $data['title'] = "COMPLETION";
 $this->load->view('authentication/header', $data);
 $this->load->view('authentication/complete', $data);
 $this->load->view('authentication/footer', $data);
 
        } else {
            $post = $this->input->post(NULL, TRUE);
            //$key = bin2hex($this->encryption->create_key(16));
            $cleanPost = $this->security->xss_clean($post);
 $hashed = $this->encryption->encrypt($cleanPost['password']); 
 
            $cleanPost['password'] = $hashed;
            unset($cleanPost['passwordConfirmation']);

            $triuneUserUpdate = array(
                'password' => $cleanPost['password'],
                'lastLogin' => date('Y-m-d h:i:s A'),
                'status' => $this->status[1],
            );
            $recordUpdated = $this->_updateRecords($tableName = 'triune_user', $fieldName = array('ID'), $where = array($userInfo[0]->ID), $triuneUserUpdate);

            if(!$recordUpdated){
                error_log('Unable to updateUserInfo('.$userInfo[0]->ID.')');
                return false;
            }

            $updatedUser = $this->_getRecordsData($data = array('*'), 
                $tables = array('triune_user'), 
                $fieldName = array('ID'), 
                $where = array($userInfo[0]->ID), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                $fieldNameLike = null, $like = null, 
                $whereSpecial = null, $groupBy = null );

            
            if(!$updatedUser){
                $this->session->set_flashdata('msg', 'There was a problem updating your record');
                redirect(site_url());
            }


            unset($updatedUser[0]->password);

            foreach($updatedUser[0] as $key=>$val){
                $this->session->set_userdata($key, $val);
            }
 
 $getPassword = $this->_getRecordsData($data = array('*'), $tables = array('triune_user'), $fieldName = array('userName'), $where = array('jtsy'), 
 $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, $fieldNameLike = null, $like = null, $whereSpecial = null, $groupBy = null );

 redirect(base_url());
        }

    }
    public function forgotPassword()
    {
        $this->form_validation->set_rules('emailAddress', 'Email Address', 'required|valid_email'); 
        
        if($this->form_validation->run() == FALSE) {
 header("Access-Control-Allow-Origin: *");
 $data = array();
 $data['title'] = "FORGOT PASSWORD";
 $this->load->view('authentication/header', $data);
 $this->load->view('authentication/forgot', $data);
 $this->load->view('authentication/footer', $data);
        }else{
            $emailAddress = $this->input->post('emailAddress'); 
            $clean = $this->security->xss_clean($emailAddress);
        
            $getUserInfo = $this->_getRecordsData($data = array('*'), $tables = array('triune_user'), $fieldName = array('emailAddress'), $where = array($clean), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, $fieldNameLike = null, $like = null, $whereSpecial = null, $groupBy = null );
    
        
            if(empty($getUserInfo)){
                $this->session->set_flashdata('msg', "'We can't find your email address");
                redirect(base_url().'auth/forgot');
            } 

            
            if($getUserInfo[0]->status != $this->status[1]){ //if status is not approved
                $this->session->set_flashdata('msg', 'Your account is not in approved status');
                redirect(base_url().'auth/forgot');
            }
            
            //build token 
            
            $qstring = $this->_insertToken($getUserInfo[0]->ID);

            $url = site_url() . 'triuneAuth/resetPassword/token/' . $qstring;
            $link = '<a href="' . $url . '">' . $url . '</a>'; 
            
            $message = ''; 
            $message .= '<strong>A password reset has been requested for this email account</strong><br>';
            $message .= '<strong>Please click:</strong> ' . $link; 
    
            //$this->_sendMail($toEmail ="rdlagdaan@gmail.com", $subject = "token created", $message);
            
            echo $message; //send this through mail
            //exit;
            
        }
        
    }
    public function resetPassword()
    {
        $token = $this->_base64urlDecode($this->uri->segment(4)); 
        $cleanToken = $this->security->xss_clean($token);
        
        
        $userInfo = $this->_isTokenValid($cleanToken); //either false or array(); 
        
        if(empty($userInfo)) {
            $this->session->set_flashdata('msg', 'Token is invalid or expired');
            redirect(base_url().'auth/register');           
        }
        
        $data = array(
            'firstName'=> $userInfo[0]->firstNameUser, 
            'emailAddress'=>$userInfo[0]->emailAddress, 
            'ID'=>$userInfo[0]->ID, 
            'userName'=>$userInfo[0]->userName, 
            'token'=>$this->_base64urlEncode($token)
        );
        
        

        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');
        $this->form_validation->set_rules('passwordConfirmation', 'Password Confirmation', 'required|matches[password]'); 
        
        if ($this->form_validation->run() == FALSE) { 
 header("Access-Control-Allow-Origin: *");
 $data['title'] = "RESET PASSWORD";
 $this->load->view('authentication/header', $data);
 $this->load->view('authentication/reset', $data);
 $this->load->view('authentication/footer', $data);
        }else{

            $post = $this->input->post(NULL, TRUE);
            $cleanPost = $this->security->xss_clean($post);
            $hashed = $this->encryption->encrypt($cleanPost['password']); 
            $cleanPost['password'] = $hashed;
            $cleanPost['ID'] = $userInfo[0]->ID;
            
            unset($cleanPost['passwordConfirmation']);

            $triuneUserUpdate = array(
                'password' => $cleanPost['password'],
                'lastLogin' => date('Y-m-d h:i:s A'),
                'status' => $this->status[1],
            );
            $recordUpdated = $this->_updateRecords($tableName = 'triune_user', $fieldName = array('ID'), $where = array($userInfo[0]->ID), $triuneUserUpdate);

            if(!$recordUpdated){
                error_log('Unable to updateUserInfo('.$userInfo[0]->ID.')');
                return false;
            }

            $updatedUser = $this->_getRecordsData($data = array('*'), 
                $tables = array('triune_user'), 
                $fieldName = array('ID'), 
                $where = array($userInfo[0]->ID), 
                $join = null, $joinType = null, $sortBy = null, $sortOrder = null, $limit = null, 
                $fieldNameLike = null, $like = null, 
                $whereSpecial = null, $groupBy = null );

            
            if(!$updatedUser){
                $this->session->set_flashdata('msg', 'There was a problem updating your password');
                redirect(base_url()."auth/forgot");
            }

            $this->session->set_flashdata('msg', 'Password Updated');
 redirect(base_url());

        }
    }
}