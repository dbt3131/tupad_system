<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tupad_model extends CI_Model {

    protected $table = 'tbl_tupad_list';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insert_batch(array $data) {
        if (empty($data)) {
            return false;
        }
        return $this->db->insert_batch($this->table, $data);
    }

   public function get_uploaded_files() {
    $sql = "SELECT t.file_name, 
                   t.reference_no, 
                   COUNT(t.id) as total_records, 
                   SUM(CASE WHEN t.tupad_active = 0 THEN 1 ELSE 0 END) as active_records,
                   SUM(CASE WHEN t.tupad_active = 1 THEN 1 ELSE 0 END) as inactive_records,
                   MAX(t.uploaded_at) as uploaded_at,
                   u.reg_fname as uploader_fname, 
                   u.reg_lname as uploader_lname
            FROM tbl_tupad_list t
            LEFT JOIN users u ON u.id = t.user_id
            WHERE t.file_name IS NOT NULL
            GROUP BY t.file_name, t.reference_no, u.reg_fname, u.reg_lname
            ORDER BY t.file_name ASC";

    return $this->db->query($sql)->result_array();
}

    public function forward_to_gsis_letter($file_name, $user_name) {
        $file_name = urldecode($file_name);
        
        $this->db->where('file_name', $file_name);
        $records = $this->db->get($this->table)->result_array();
        
        if (empty($records)) {
            return 'empty';
        }
        
        $first = $records[0];
        $area_of_implementation = $first['area_of_implementation'] ?? '';
        $adl_no                 = $first['adl_no'] ?? '';
        $reference_no           = $first['reference_no'] ?? '';
        
        // Check if details in all primary tracking columns already match in the gsis_letters table
        $this->db->where('reference_no', $reference_no);
        $this->db->where('adl_no', $adl_no);
        $this->db->where('implementor', $area_of_implementation);
        $existing_match = $this->db->get('gsis_letters')->row_array();
        
        if ($existing_match) {
            return 'exists'; // Stop execution if details match completely
        }
        
        $male = 0;
        $female = 0;
        foreach ($records as $r) {
            $gender = strtoupper(trim($r['tupad_gender'] ?? ''));
            if ($gender === 'M' || $gender === 'MALE') {
                $male++;
            } elseif ($gender === 'F' || $gender === 'FEMALE') {
                $female++;
            }
        }
        
        $data = [
            'reference_no'  => $reference_no,
            'female'        => $female,
            'male'          => $male,
            'date_generate' => date('Y-m-d'),
            'generate_by'   => $user_name,
            'adl_no'        => $adl_no,
            'implementor'   => $area_of_implementation
        ];
        
        return $this->db->insert('gsis_letters', $data) ? 'success' : 'failed';
    }

    public function get_records_by_filename($file_name) {
        $this->db->select('tbl_tupad_list.*, users.reg_fname as uploader_fname, users.reg_lname as uploader_lname');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = tbl_tupad_list.user_id', 'left'); 
        $this->db->where('tbl_tupad_list.file_name', urldecode($file_name));
        
        return $this->db->get()->result_array();
    }

    public function get_provinces() {
        return $this->db->order_by('provDesc', 'ASC')->get('refprovince')->result_array();
    }

    public function get_cities_by_province($provCode) {
        return $this->db->where('provCode', $provCode)
                        ->order_by('citymunDesc', 'ASC')
                        ->get('refcitymun')
                        ->result_array();
    }

    public function get_barangays_by_city($citymunCode) {
        return $this->db->where('citymunCode', $citymunCode)
                        ->order_by('brgyDesc', 'ASC')
                        ->get('refbrgy')
                        ->result_array();
    }

    public function get_all_records() {
        $query = $this->db->get('tbl_tupad_list'); 
        return $query->result_array();
    }

    public function count_all_records() {
        return $this->db->count_all($this->table);
    }

    public function count_filtered_records($search, $province, $city, $barangay, $file_name = null) {
        $this->db->from($this->table);
        
        if (!empty($file_name)) {
            $this->db->where('file_name', urldecode($file_name));
        }

        if (!empty($province)) {
            $this->db->where('tupad_province', $province);
        }
        if (!empty($city)) {
            $this->db->where('tupad_municipality', $city);
        }
        if (!empty($barangay)) {
            $this->db->where('tupad_barangay', $barangay);
        }

        if (!empty($search)) {
            $keywords = explode(' ', trim($search));
            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $escaped = $this->db->escape_like_str($keyword);
                    $this->db->where("(tupad_id_no LIKE '%{$escaped}%' OR tupad_fname LIKE '%{$escaped}%' OR tupad_lname LIKE '%{$escaped}%')", NULL, FALSE);
                }
            }
        }

        return $this->db->count_all_results();
    }

    public function get_datatables_records($limit, $start, $search, $province, $city, $barangay) {
        $this->_get_records_query($search, $province, $city, $barangay);
        
        if ($limit != -1) {
            $this->db->limit((int)$limit, (int)$start);
        }
        
        return $this->db->get()->result_array();
    }

    public function get_paged_records($start, $length, $search, $province, $city, $barangay) {
        $this->_get_records_query($search, $province, $city, $barangay);
        
        if ($length != -1) {
            $this->db->limit((int)$length, (int)$start);
        }
        
        return $this->db->get()->result_array();
    }

    private function _get_records_query($search, $province, $city, $barangay, $file_name = null) {
        $this->db->select('tbl_tupad_list.*, 
                           users.reg_fname as uploader_fname, 
                           users.reg_lname as uploader_lname,
                           refprovince.provDesc as province_name,
                           refcitymun.citymunDesc as municipality_name,
                           refbrgy.brgyDesc as barangay_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = tbl_tupad_list.user_id', 'left');
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');

        if (!empty($file_name)) {
            $this->db->where('tbl_tupad_list.file_name', urldecode($file_name));
        }

        if (!empty($search)) {
            $keywords = explode(' ', trim($search));
            $where_clauses = array();

            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $escaped = $this->db->escape_like_str($keyword);
                    $where_clauses[] = "(
                        tbl_tupad_list.tupad_id_no LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_fname LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_mname LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_lname LIKE '%{$escaped}%'
                    )";
                }
            }

            if (!empty($where_clauses)) {
                $full_search_string = implode(' AND ', $where_clauses);
                $this->db->where("({$full_search_string})", NULL, FALSE);
            }
        }

        if (!empty($province)) {
            $this->db->where('tbl_tupad_list.tupad_province', $province);
        }
        if (!empty($city)) {
            $this->db->where('tbl_tupad_list.tupad_municipality', $city);
        }
        if (!empty($barangay)) {
            $this->db->where('tbl_tupad_list.tupad_barangay', $barangay);
        }
    }

    public function count_all_records_by_file($file_name) {
        $this->db->where('file_name', urldecode($file_name));
        return $this->db->count_all_results($this->table);
    }

    public function count_filtered_records_by_file($file_name, $search) {
        $this->_get_records_by_file_query($file_name, $search);
        return $this->db->count_all_results();
    }

    public function get_paged_records_by_file($file_name, $start, $length, $search) {
        $this->_get_records_by_file_query($file_name, $search);
        if ($length != -1) {
            $this->db->limit($length, $start);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    private function _get_records_by_file_query($file_name, $search) {
        $this->db->select('tbl_tupad_list.*, 
                           users.reg_fname as uploader_fname, 
                           users.reg_lname as uploader_lname,
                           refprovince.provDesc as province_name,
                           refcitymun.citymunDesc as municipality_name,
                           refbrgy.brgyDesc as barangay_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = tbl_tupad_list.user_id', 'left');
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');
        $this->db->where('tbl_tupad_list.file_name', urldecode($file_name));

        if (!empty($search)) {
            $keywords = explode(' ', trim($search));
            $where_clauses = array();

            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $escaped = $this->db->escape_like_str($keyword);
                    $where_clauses[] = "(
                        tbl_tupad_list.tupad_id_no LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_fname LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_mname LIKE '%{$escaped}%' OR 
                        tbl_tupad_list.tupad_lname LIKE '%{$escaped}%'
                    )";
                }
            }

            if (!empty($where_clauses)) {
                $full_search_string = implode(' AND ', $where_clauses);
                $this->db->where("({$full_search_string})", NULL, FALSE);
            }
        }
    }
        
    public function get_total_active_workers() {
       $this->db->where('tupad_active', 1);
       return $this->db->count_all_results('tbl_tupad_list');
    }

    public function get_total_inactive_workers() {
       $this->db->where('tupad_active', 0);
       return $this->db->count_all_results('tbl_tupad_list');
    }

    public function get_provincial_worker_stats() {
        $result = array();
        $this->db->select('refprovince.provDesc as name, COUNT(tbl_tupad_list.id) as workers');
        $this->db->from('tbl_tupad_list');
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->where('tbl_tupad_list.tupad_active', 1);
        $this->db->group_by('refprovince.provDesc');
        
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function get_datatables_records_by_file($file_name, $limit, $start, $search) {
        $this->_get_records_by_file_query($file_name, $search);
        if ($limit != -1) {
            $this->db->limit($limit, $start);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_record_by_id($id) {
        $this->db->select('tbl_tupad_list.*, 
                           users.reg_fname as uploader_fname, 
                           users.reg_lname as uploader_lname,
                           refprovince.provDesc as province_name,
                           refcitymun.citymunDesc as municipality_name,
                           code_interest_skills.code_interest_skills_desc as training_interest_desc,
                           code_classification.classification_desc as classification_descs,
                           code_type_bene.bene_type_desc as bene_type_descs,
                           refbrgy.brgyDesc as barangay_name');
        $this->db->from($this->table);
        $this->db->join('users', 'users.id = tbl_tupad_list.user_id', 'left');
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');
        $this->db->join('code_interest_skills', 'code_interest_skills.code_interest_skills_id = tbl_tupad_list.tupad_training_interest', 'left');
        $this->db->join('code_classification', 'code_classification.classification_id = tbl_tupad_list.tupad_classification', 'left');
        $this->db->join('code_type_bene', 'code_type_bene.bene_type_id = tbl_tupad_list.tupad_type', 'left');
        $this->db->where('tbl_tupad_list.id', $id);
        return $this->db->get()->row_array();
    }

    public function get_multi_level_duplicates($match_level, $province = null, $city = null, $barangay = null, $file_name = null) {
        $this->db->select('tupad_fname, tupad_mname, tupad_lname, tupad_dob_month, tupad_dob_day, tupad_dob_year, tupad_province, tupad_municipality, tupad_barangay, file_name, COUNT(*) as duplicate_count');
        $this->db->from($this->table);

        if (!empty($province)) { $this->db->where('tupad_province', $province); }
        if (!empty($city)) { $this->db->where('tupad_municipality', $city); }
        if (!empty($barangay)) { $this->db->where('tupad_barangay', $barangay); }
        if (!empty($file_name)) { $this->db->where('file_name', urldecode($file_name)); }

        switch ($match_level) {
            case 'exact':
                $this->db->group_by(['tupad_fname', 'tupad_mname', 'tupad_lname', 'tupad_dob_month', 'tupad_dob_day', 'tupad_dob_year']);
                break;
                
            case 'highly_possible':
                $this->db->group_by(['tupad_fname', 'tupad_mname', 'tupad_lname', 'tupad_dob_month', 'tupad_dob_day', 'tupad_dob_year']);
                break;
                
            case 'possible':
                $this->db->group_by(['tupad_fname', 'tupad_lname', 'tupad_dob_month', 'tupad_dob_year']);
                break;
                
            case 'probable':
                $this->db->group_by(['tupad_lname', 'tupad_dob_month', 'tupad_dob_day', 'tupad_dob_year']);
                break;
                
            default:
                $this->db->group_by(['tupad_fname', 'tupad_lname']);
                break;
        }

        $this->db->having('COUNT(*) > 1');
        $this->db->order_by('duplicate_count', 'DESC');
        $this->db->limit(100);
        
        return $this->db->get()->result_array();
    }

    public function get_duplicate_cluster_members($match_level, $fname, $mname, $lname, $dob_month, $dob_day, $dob_year) {
        $this->db->select('tbl_tupad_list.*, refprovince.provDesc as province_name, refcitymun.citymunDesc as municipality_name, refbrgy.brgyDesc as barangay_name');
        $this->db->from($this->table);
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');

        switch ($match_level) {
            case 'exact':
            case 'highly_possible':
                $this->db->where('LOWER(tupad_fname)', strtolower(trim($fname)));
                $this->db->where('LOWER(tupad_lname)', strtolower(trim($lname)));
                $this->db->where('tupad_dob_month', $dob_month);
                $this->db->where('tupad_dob_day', $dob_day);
                $this->db->where('tupad_dob_year', $dob_year);
                break;
                
            case 'possible':
                $this->db->where('LOWER(tupad_fname)', strtolower(trim($fname)));
                $this->db->where('LOWER(tupad_lname)', strtolower(trim($lname)));
                $this->db->where('tupad_dob_month', $dob_month);
                $this->db->where('tupad_dob_year', $dob_year);
                break;
                
            case 'probable':
                $this->db->where('LOWER(tupad_lname)', strtolower(trim($lname)));
                $this->db->where('tupad_dob_month', $dob_month);
                $this->db->where('tupad_dob_day', $dob_day);
                $this->db->where('tupad_dob_year', $dob_year);
                break;
                
            default:
                $this->db->where('LOWER(tupad_fname)', strtolower(trim($fname)));
                $this->db->where('LOWER(tupad_lname)', strtolower(trim($lname)));
                break;
        }

        $this->db->order_by('uploaded_at', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_beneficiary_by_id($id) {
        $this->db->select('tbl_tupad_list.*, 
            refprovince.provDesc as province_name, 
            refcitymun.citymunDesc as municipality_name, 
            refbrgy.brgyDesc as barangay_name,
            users.reg_fname as user_fname,
            code_idtype.type_desc as type_desc,
            code_type_bene.bene_type_desc as bene_type_desc,
            code_skills.code_skills_desc as skills_desc,
            code_epayment.epayment_desc as epayment_desc
            ');


        $this->db->from($this->table);
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');
        $this->db->join('users', 'users.id = tbl_tupad_list.user_id', 'left');
        $this->db->join('code_idtype', 'code_idtype.type_id = tbl_tupad_list.tupad_idtype', 'left');
        $this->db->join('code_type_bene', 'code_type_bene.bene_type_id = tbl_tupad_list.tupad_type', 'left');
        $this->db->join('code_skills', 'code_skills.code_skills_id = tbl_tupad_list.tupad_skills', 'left');
        $this->db->join('code_epayment', 'code_epayment.epayment_id = tbl_tupad_list.tupad_epayment', 'left');
        $this->db->where('tbl_tupad_list.id', $id);
        return $this->db->get()->row_array();
    }

    public function find_province_code_by_desc($desc) {
        if (empty($desc)) return '';
        $desc = trim($desc);

        $row = $this->db->where('LOWER(provDesc)', strtolower($desc))->get('refprovince')->row_array();
        if ($row) return $row['provCode'];

        $provinces = $this->db->select('provCode, provDesc')->get('refprovince')->result_array();
        
        $closestCode = '';
        $shortestDistance = -1;

        foreach ($provinces as $p) {
            $distance = levenshtein(strtolower($desc), strtolower($p['provDesc']));
            if ($distance === 0) return $p['provCode'];

            if ($distance < $shortestDistance || $shortestDistance < 0) {
                $closestCode = $p['provCode'];
                $shortestDistance = $distance;
            }
        }

        return ($shortestDistance <= 5) ? $closestCode : '';
    }

    public function find_city_code_by_desc($desc, $provCode = null) {
        if (empty($desc)) return '';
        $desc = trim($desc);

        if (!empty($provCode)) {
            $this->db->where('provCode', $provCode);
        }
        $cities = $this->db->select('cityCode, citymunDesc')->get('refcitymun')->result_array();

        $closestCode = '';
        $shortestDistance = -1;

        foreach ($cities as $c) {
            $cleanDbDesc = str_ireplace('City of ', '', $c['citymunDesc']);
            $cleanSearchDesc = str_ireplace('City of ', '', $desc);

            $distance = levenshtein(strtolower($cleanSearchDesc), strtolower($cleanDbDesc));
            if ($distance === 0) return $c['cityCode'];

            if ($distance < $shortestDistance || $shortestDistance < 0) {
                $closestCode = $c['cityCode'];
                $shortestDistance = $distance;
            }
        }

        return ($shortestDistance <= 5) ? $closestCode : '';
    }

    public function find_barangay_code_by_desc($desc, $citymunCode = null) {
        if (empty($desc)) return '';
        $desc = trim($desc);

        if (!empty($citymunCode)) {
            $this->db->where('citymunCode', $citymunCode);
        }
        $barangays = $this->db->select('brgyCode, brgyDesc')->get('refbrgy')->result_array();

        $closestCode = '';
        $shortestDistance = -1;

        foreach ($barangays as $b) {
            $cleanDbBrgy = preg_replace('/^(brgy|barangay|poblacion)\.?\s+/i', '', $b['brgyDesc']);
            $cleanSearchBrgy = preg_replace('/^(brgy|barangay|poblacion)\.?\s+/i', '', $desc);

            $distance = levenshtein(strtolower($cleanSearchBrgy), strtolower($cleanDbBrgy));
            if ($distance === 0) return $b['brgyCode'];

            if ($distance < $shortestDistance || $shortestDistance < 0) {
                $closestCode = $b['brgyCode'];
                $shortestDistance = $distance;
            }
        }

        return ($shortestDistance <= 4) ? $closestCode : '';
    }

    public function find_bene_type_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);
        
        $items = $this->db->select('bene_type_id, bene_type_desc')->get('code_type_bene')->result_array();
        return $this->_get_closest_id($desc, $items, 'bene_type_id', 'bene_type_desc', 4);
    }

    public function find_type_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);

        $items = $this->db->select('type_id, type_desc')->get('code_idtype')->result_array();
        return $this->_get_closest_id($desc, $items, 'type_id', 'type_desc', 4);
    }

    public function find_convergence_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);

        $items = $this->db->select('convergence_id, convergence_desc')->get('code_convergence')->result_array();
        return $this->_get_closest_id($desc, $items, 'convergence_id', 'convergence_desc', 4);
    }

    public function find_classification_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);

        $items = $this->db->select('classification_id, classification_desc')->get('code_classification')->result_array();
        return $this->_get_closest_id($desc, $items, 'classification_id', 'classification_desc', 4);
    }

    public function find_epayment_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);

        $items = $this->db->select('epayment_id, epayment_desc')->get('code_epayment')->result_array();
        return $this->_get_closest_id($desc, $items, 'epayment_id', 'epayment_desc', 4);
    }

    public function find_skills_id_by_desc($desc) {
        if (empty($desc)) return 0;
        $desc = trim($desc);

        $items = $this->db->select('code_skills_id, code_skills_desc')->get('code_skills')->result_array();
        return $this->_get_closest_id($desc, $items, 'code_skills_id', 'code_skills_desc', 4);
    }

    private function _get_closest_id($search, $items, $idKey, $descKey, $threshold = 4) {
        $closestId = 0;
        $shortestDistance = -1;

        foreach ($items as $item) {
            $distance = levenshtein(strtolower($search), strtolower($item[$descKey]));
            if ($distance === 0) return $item[$idKey];

            if ($distance < $shortestDistance || $shortestDistance < 0) {
                $closestId = $item[$idKey];
                $shortestDistance = $distance;
            }
        }

        return ($shortestDistance <= $threshold) ? $closestId : 0;
    }

    public function file_exists($file_name) {
        $this->db->where('file_name', trim($file_name));
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }

    public function get_all_municipalities() {
        return $this->db->order_by('citymunDesc', 'ASC')->get('refcitymun')->result_array();
    }

    public function get_all_provinces() {
        return $this->db->order_by('provDesc', 'ASC')->get('refprovince')->result_array();
    }

    public function get_export_data($file_name = null, $province = null, $city = null, $barangay = null, $search = null) {
        $this->db->select('
            tbl_tupad_list.*,
            refprovince.provDesc as province_name,
            refcitymun.citymunDesc as municipality_name,
            refbrgy.brgyDesc as barangay_name
        ');
        $this->db->from($this->table);
        $this->db->join('refprovince', 'refprovince.provCode = tbl_tupad_list.tupad_province', 'left');
        $this->db->join('refcitymun', 'refcitymun.cityCode = tbl_tupad_list.tupad_municipality', 'left');
        $this->db->join('refbrgy', 'refbrgy.brgyCode = tbl_tupad_list.tupad_barangay', 'left');

        $this->db->where('tbl_tupad_list.tupad_active', 0);

        if (!empty($file_name)) {
            $this->db->where('tbl_tupad_list.file_name', urldecode($file_name));
        }
        if (!empty($province)) {
            $this->db->where('tbl_tupad_list.tupad_province', $province);
        }
        if (!empty($city)) {
            $this->db->where('tbl_tupad_list.tupad_municipality', $city);
        }
        if (!empty($barangay)) {
            $this->db->where('tbl_tupad_list.tupad_barangay', $barangay);
        }
        if (!empty($search)) {
            $keywords = explode(' ', trim($search));
            foreach ($keywords as $keyword) {
                if (!empty($keyword)) {
                    $escaped = $this->db->escape_like_str($keyword);
                    $this->db->where("(tbl_tupad_list.tupad_id_no LIKE '%{$escaped}%' OR tbl_tupad_list.tupad_fname LIKE '%{$escaped}%' OR tbl_tupad_list.tupad_lname LIKE '%{$escaped}%')", NULL, FALSE);
                }
            }
        }

        $this->db->order_by('tbl_tupad_list.id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function set_inactive($id) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['tupad_active' => 1]);
    }
public function get_gsis_summary_by_date($start_date, $end_date)
{
    $this->db->select('*');
    $this->db->from('gsis_letters');
    $this->db->where('date_generate >=', $start_date);
    $this->db->where('date_generate <=', $end_date);
    $this->db->order_by('date_generate', 'DESC');
    
    $query = $this->db->get();
    return $query->result_array();
}





}