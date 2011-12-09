<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.1/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 5);
define('PHPASS_HASH_PORTABLE', true);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using 
 * this setup you may need to do some tweaking.
 *   
 * ENGINE=InnoDB DEFAULT CHARSET=utf8;
 * 
 * @package   SimpleLoginSecure
 * @version   1.0.1
 * @author    Alex Dunae, Dialect <alex[at]dialect.ca>
 * @copyright Copyright (c) 2008, Alex Dunae
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      http://dialect.ca/code/ci-simple-login-secure/
 
 
 
login throttling db structure 
CREATE TABLE failed_logins (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(16) NOT NULL,
    ip_address INT(11) UNSIGNED NOT NULL,
    attempted DATETIME NOT NULL,
    INDEX `attempted_idx` (`attempted`)
) engine=InnoDB charset=UTF8; 
 
 
 
 login throttle script
 // array of throttling
$throttle = array(10 => 1, 20 => 2, 30 => 'recaptcha');

// retrieve the latest failed login attempts
$sql = 'SELECT MAX(attempted) AS attempted FROM failed_logins';
$result = mysql_query($sql);
if (mysql_affected_rows($result) > 0) {
    $row = mysql_fetch_assoc($result);

    $latest_attempt = (int) date('U', strtotime($row['attempted']));

    // get the number of failed attempts
    $sql = 'SELECT COUNT(1) AS failed FROM failed_logins WHERE attempted > DATE_SUB(NOW(), INTERVAL 15 minute)';
    $result = mysql_query($sql);
    if (mysql_affected_rows($result) > 0) {
        // get the returned row
        $row = mysql_fetch_assoc($result);
        $failed_attempts = (int) $row['failed'];

        // assume the number of failed attempts was stored in $failed_attempts
        krsort($throttle);
        foreach ($throttle as $attempts => $delay) {
            if ($failed_attempts > $attempts) {
                // we need to throttle based on delay
                if (is_numeric($delay)) {
                    $remaining_delay = time() - $latest_attempt - $delay;
                    // output remaining delay
                    echo 'You must wait ' . $remaining_delay . ' seconds before your next login attempt';
                } else {
                    // code to display recaptcha on login form goes here
                }
                break;
            }
        }        
    }
}
 
 
 
# example of insertion
INSERT INTO failed_logins SET username = 'example', ip_address = INET_ATON('192.168.0.1'), attempted = CURRENT_TIMESTAMP;
# example of selection
SELECT id, username, INET_NTOA(ip_address) AS ip_address, attempted; 
 
 
 
SELECT COUNT(1) AS failed FROM failed_logins WHERE attempted > DATE_SUB(NOW(), INTERVAL 15 minute);
 
 
 
 
 
 
 
 
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	
	//removed  $auto_login = false, from parameters
	function create($data) 
	{
		$this->CI =& get_instance();

		//Make sure account info was sent
		if($data['username'] == '' OR $data['user_pass'] == '') {
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('username', $data['username']); 
		$query = $this->CI->db->get_where($this->user_table);
		
		if ($query->num_rows() > 0){ //user_email already exists
				return false;
			}

		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$data['user_pass'] = $hasher->HashPassword($data['user_pass']); 
		
		$this->CI->db->set($data); 
		if(!$this->CI->db->insert($this->user_table)){//There was a problem! 
			return false;
		} 
									
		return true;
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($data) 
	{
		$this->CI =& get_instance();

		if($data['username'] == '' OR $data['pass'] == ''){
			
			return false;
		}
		
		//Check against user table
		$this->CI->db->where('username', $data['username']); 
		$query = $this->CI->db->get_where($this->user_table);

		
		if ($query->num_rows() > 0) 
		{
			$user_data = $query->row_array(); 

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
			if(!$hasher->CheckPassword($data['pass'], $user_data['user_pass'])){
				return false;
			}
				

			//Destroy old session
			$this->CI->session->sess_destroy();
			
			//Create a fresh, brand new session
			$this->CI->session->sess_create();

			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = NOW() WHERE id = ' . $user_data['user_id']);

			//Set session data
			unset($user_data['user_pass']);
			$user_data['user'] = $user_data['username']; // for compatibility with Simplelogin
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
			
			return true;
		} 
		else 
		{
			return false;
		}	

	}

	
	/**
	 * Checks is user is logged in
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function is_logged_in($user_email = '', $is_session_logged_in = '') 
	{
		$this->CI =& get_instance();

		if($user_email == '' OR $is_session_logged_in == false)
			return false;
		
		
		//Check against user table
		$this->CI->db->where('username', $user_email); 
		$query = $this->CI->db->get_where($this->user_table);

		
		if ($query->num_rows() > 0 AND $is_session_logged_in == true) 
		{
			return true;
		} 
		else 
		{
			return false;
		}	

	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();		

		$this->CI->session->sess_destroy();
	}

	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_id) 
	{
		$this->CI =& get_instance();
		
		if(!is_numeric($user_id))
			return false;			

		return $this->CI->db->delete($this->user_table, array('user_id' => $user_id));
	}
	
	/**
	 * Set a New Password
	 * 
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 */
	
	function set_new_pass($user_id = '', $user_newpass = '', $user_name = '', $user_phone = '') {
    
        $this->CI =& get_instance();
        
        // Make sure account info was sent
        if($user_id == '' OR $user_newpass == '' OR $user_name == '') {
            
            return false;
        }
        
        // Make sure $user_id is numeric
        if( !is_numeric( $user_id ) ) {
            return false;
        }
        
        $this->CI->db->where('id', $user_id);
        $query = $this->CI->db->get_where($this->user_table);
        $user_data = $query->row_array();
       
        //Hash user_newpass using phpass
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $user_pass_hashed = $hasher->HashPassword($user_newpass);
        
        // Insert new password into table
        
		
		 $data = array(
		 	   'name'          => $user_name,
		 	   'phone'         => $user_phone,
               'user_pass' => $user_pass_hashed,
               'user_verified' => 1
            );
		$this->CI->db->where('id', $user_data['id']);
		$this->CI->db->update($this->user_table , $data);
       
        return true;     
           
    }



	/**
	 * Set a New Password
	 * 
	 * @access public
	 * @param string
	 * @param string
	 * @return bool
	 */
	
	function update_new_pass($ver_code = '',$gen_pass = '', $user_newpass = '') {
    
        $this->CI =& get_instance();
       
        // Make sure account info was sent
        if($ver_code == '' OR $user_newpass == '' OR $gen_pass == '') {
            echo "empty";
            return false;
        }
       
       $this->CI->db->where('user_ver_code', $ver_code);
       $query = $this->CI->db->get_where($this->user_table);
       if(!$query->result()){
       	
		return false;
		}
       $user_data = $query->row_array();
 
       if($ver_code !== $user_data['user_ver_code'] AND $gen_pass !== $user_data['user_pass']){
       		return false;
       }
       
       
       //Hash user_newpass using phpass
       $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
       $user_pass_hashed = $hasher->HashPassword($user_newpass);
       $user_vercode_hashed = $hasher->HashPassword($ver_code);
        
       // Insert new password into table
		$data = array(
           'user_pass' => $user_pass_hashed,
           'user_ver_code' => $user_vercode_hashed,
           'user_verified' => 1,
           'updated_on' => date('c')
        );
		$this->CI->db->where('user_id', $user_data['user_id']);
		$this->CI->db->update($this->user_table , $data);
       
        return true;     
           
    }
	
	
	
	/**
     * Forgot Password
     * added by JB on 11-1-2009
     *
     * @access public
     * @param string
     * @return string
     *
     */
    function forgot_password($email = '')
    {
        $this->CI =& get_instance();
        
        if($email == '')
        {
            return false;
        }
        
        $this->CI->db->where('user_email', $email);
        $query = $this->CI->db->get_where($this->user_table);
		if(!$query->result()){
			return false;
		}
		
        $user_data = $query->row_array();
        
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        $new_pass = $this->generatePassword();  
        $ver_code= md5(time().$this->generatePassword(16,10));      
        $user_pass_hashed = $hasher->HashPassword($new_pass);
        
        $data = array(
           'user_pass' => $new_pass,
           'user_ver_code' => $ver_code
        );
		$this->CI->db->where('user_id', $user_data['user_id']);
		$this->CI->db->update($this->user_table , $data);
       	
       	$config = Array(
            'protocol'  => 'smtp',
            'smtp_host' => "ssl://smtp.googlemail.com",
            'smtp_port' => 465,
            'smtp_user' => 'dmregister1@gmail.com',
            'smtp_pass' => 'admin334',
            'mailtype' => 'html'
        );
		
       
        $this->CI->load->library('email', $config);
        $this->CI->email->set_newline("\r\n");
        
        $this->CI->email->from('david@davidmregister.com');
        $this->CI->email->to($email);
        $this->CI->email->subject('Password Reset');
        $this->CI->email->message("Please use the link below to reset your password \r\n \r\n new password: $new_pass \r\n \r\n <a href='http://davidmregister.com/admin/update_pass/$ver_code'>Reset Password</a>");
		
		$this->CI->email->send();
		
        return true;        
    }
    
    /**
     * Forgot Password
     * added by JB on 11-1-2009
     *
     * @access public
     * @param int,@param int
     * @return string
     *
     */
    function generatePassword($length=16, $strength=5) {
		$vowels = 'aeuy';
		$consonants = 'bdghjmnpqrstvz';
		if ($strength >= 1) {
			$consonants .= 'BDGHJLMNPQRSTVWXZ';
		}
		if ($strength >= 2) {
			$vowels .= "AEUY";
		}
		if ($strength >= 4) {
			$consonants .= '23456789';
		}
		if ($strength >= 8 ) {
			$vowels .= '@#$%';
		}
	
		$password = '';
		$alt = time() % 2;
		for ($i = 0; $i < $length; $i++) {
			if ($alt == 1) {
				$password .= $consonants[(rand() % strlen($consonants))];
				$alt = 0;
			} else {
				$password .= $vowels[(rand() % strlen($vowels))];
				$alt = 1;
			}
		}
		return $password;
	}
	
	
}
?>
