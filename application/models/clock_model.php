<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Clock_Model extends CI_Model {

	public function get_client()
	{
		$this->db->select('id,client')->from('timecards')->group_by('client');
		$q = $this->db->get();
		if($q->num_rows() > 0){
			$i = 1;
			foreach ($q->result_array() as $row){
				if($i == 1){
					 $data['']="Select Value";
				}
				
				 $data[$row['client']]=$row['client'];
				 $i++;
			}
			return $data;
			
		}
	}
	
	public function get_client_time($start, $end, $client)
	{
		$sql = "SELECT * FROM `timecards` WHERE `client`=? && `punch` BETWEEN ? AND ? ORDER BY `punch`";
		$q = $this->db->query($sql, array($client, $start, $end));
		if($q->num_rows() > 0){
			foreach ($q->result() as $row){
				$data[] = $row;
			}
			return $data;
			
		}
	}

	public function clock_time($client, $now){
		$data = array(
			'client' => $client,
			'punch' => $now
		);
		$q = $this->db->insert('timecards', $data);
		if($q > 0){
			return true;
		}
	}



}


/* End of file clock_model.php */
/* Location: ./application/models/clock_model.php */