<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clocking extends CI_Controller {

	public function index($message = null)
	{
		if($message){
			$data['error'] = "Invalid Username or Password.";
		}else{
			$data['error'] = null;
		}
	
		$this->load->view("login", $data);
	}
	
	public function login(){
		
		$this->load->library(array('form_validation','SimpleLoginSecure'));
		$this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
		
		if ($this->form_validation->run() === FALSE){
			$this->index();
		}else{
			$data = array(
				'username' => set_value('username'),
				'pass' => set_value('password')
			);
			if($this->simpleloginsecure->login($data)){
				$this->clock_home();
			}else{
				$this->index(true);
			}
		}
		
		
	}
	
	public function clock_home(){
		$this->load->library('SimpleLoginSecure');
		if(!$this->simpleloginsecure->is_logged_in($this->session->userdata('user'),$this->session->userdata('logged_in'))){
			$this->index();
			return;
		}
		
		$data['clockInTime'] = '';
		$data['clockInUser'] = '';
		$data['clients'] = $this->clock_model->get_client();
		
		if(isset($_COOKIE['clockInTime'])){
			$data['clockInTime'] = $_COOKIE['clockInTime'];
		}
		if(isset($_COOKIE['client'])){
			$data['clockInUser'] = $_COOKIE['client'];
		}
		
		
		$this->load->view('clock',$data);
	}
	
	public function get_time(){
		 //date search
		$t = null;
		$results = '';
		$total = '';
		$client = $this->input->post('client');
		$toDate = $this->input->post('toDate');
		$fromDate = $this->input->post('fromDate');
		$dateHidden = $this->input->post('dateHidden');
		$start = date('Y-m-d H:i:s', strtotime($toDate)); //start date
	    $end = date('Y-m-d H:i:s', strtotime($fromDate)); 	//end date
	    $query = $this->clock_model->get_client_time($start, $end, $client, $dateHidden);
	    if($query != null){
	    foreach($query as $row) { 
	        //$q = question(base64_decode($row['comment'])); 
	        if($t===null) { 
	            $t = $row->punch; 
	            $results .= "<br />".$this->format($row->punch); 
	        } else { 
	            $j = $this->timediff($t, $row->punch); 
	            $results .= " to ".$this->format($row->punch)." = ".$this->His($j); 
	            $total += $j; 
	            $t = null; 
	        } 
	    } 
	    if($t !== null) { 
	        $results .= " STILL CLOCKED IN"; 
	    } 
	    $results .= "<br /><br /><strong>Total:</strong> ".$this->His($total); 
	 	
	 	echo $results;
	 	}else{
	 		echo "No time was recorded during this time";
	 	}
	}
	
	public function clock_time(){
	
		$now = date('Y-m-d H:i:s'); 
		if(isset($_COOKIE['client'])){
			$client = $_COOKIE['client'];
			
		}elseif($this->input->post('client') == null){
			$client = $this->input->post('clientNew');
		}else{
			$client = $this->input->post('client');
		}
		$this->clock_model->clock_time($client, $now);
		if( $this->input->post('timeClockOut') != '1'){
				
			setcookie ("clockInTime", $this->input->post('timeClock'), date('U')+86502,'/', '.davidmregister.com');
			setcookie("client", $client, date('U')+86502,'/', '.davidmregister.com');
			echo $client.", You have clocked in at ".$this->input->post('timeClock');
		}
		if( $this->input->post('timeClockOut') == '1'){
			setcookie ("clockInTime", null, date('U')+3600,"/",'.davidmregister.com');
			setcookie("client", null, date('U')+3600,"/",'.davidmregister.com');
			$base_url = site_url()."/clocking/clock_home";
			redirect($base_url);
		}
	}
	
	private function His($diff) { 
	    if($hours=floor($diff/3600)) { 
	        $diff -= ($hours*3600); 
	    } 
	    if($minutes=floor($diff/60)) { 
	        $diff -= ($minutes*60); 
	    } 
   		return "{$hours}:{$minutes}:{$diff}"; 
	}
 	
 	private function format($date) { //
    	return date('M jS g:i:sA', strtotime($date)); 
	}
 	
 	
 	private function timediff($start,$end) { 
	    $start = strtotime($start); 
	    $end = strtotime($end); 
	    if($start!==-1 && $end!==-1) { 
	        if($end >= $start) { 
	            //echo "<br /><br />Start: $end - $start = ".($end-$start)."<br /><br />"; 
	            return $end - $start; 
	        } 
	    } 
    return false; 
	}

}

/* End of file clocking.php */
/* Location: ./application/controllers/clocking.php */