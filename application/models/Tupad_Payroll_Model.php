<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_Payroll_Model extends CI_Model {

    protected $table = 'tupad_received_payrolls';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Fetch all payroll records ordered by latest received date
     */
   public function get_all_payrolls() {
        $this->db->select('
            tupad_received_payrolls.*,
            refprovince.provDesc AS province_name,
            refcitymun.citymunDesc AS municipality_name
        ');
        $this->db->from($this->table);
        $this->db->join('refprovince', 'refprovince.provCode = tupad_received_payrolls.province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tupad_received_payrolls.municipality', 'left');
        $this->db->order_by('tupad_received_payrolls.date_received', 'DESC');
        
        return $this->db->get()->result_array();
    }

    /**
     * Fetch a single payroll entry by ID
     */
    public function get_payroll_by_id($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    /**
     * Insert new payroll record
     */
    public function insert_payroll($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update existing payroll record
     */
    public function update_payroll($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Fetch all provinces
     */
    public function get_provinces() {
        $this->db->order_by('provDesc', 'ASC');
        return $this->db->get('refprovince')->result_array();
    }

    /**
     * Fetch municipalities by province code
     */
    public function get_municipalities_by_province($provCode) {
        $this->db->where('provCode', $provCode);
        $this->db->order_by('citymunDesc', 'ASC');
        return $this->db->get('refcitymun')->result_array();
    }

    /**
     * Fetch barangays by city/municipality code
     */
    public function get_barangays_by_municipality($cityCode) {
        $this->db->where('cityCode', $cityCode);
        $this->db->order_by('brgyDesc', 'ASC');
        return $this->db->get('refbrgy')->result_array();
    }

     public function get_payout_site()
    {
      $query = $this->db->get('code_payout_site'); 
       return $query->result_array();
    }

    public function get_month()
    {
      $query = $this->db->get('code_month'); 
       return $query->result_array();
    }

    public function get_staff_received()
    {
      $query = $this->db->get('code_receiving_staff'); 
       return $query->result_array();
    }






}