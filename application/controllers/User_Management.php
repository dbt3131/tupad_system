<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_Management extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('User_Management_Model');
        $this->load->library('form_validation');
    }

    public function users_management() {
        // Fetch users and provinces from the model
        $data['users'] = $this->User_Management_Model->get_users();
        $data['provinces'] = $this->User_Management_Model->get_provinces();
       
        // Load the user management view template and pass the data array
        $this->load->view('users/users_management', $data);
    }

    public function update($id) {
        // Validation rules for update fields
        $this->form_validation->set_rules('activated', 'Activated Status', 'required|numeric');
        $this->form_validation->set_rules('role', 'Role', 'required|numeric');
        $this->form_validation->set_rules('assigned_prov', 'Assigned Province', 'required');

        if ($this->form_validation->run() == TRUE) {
            $update_data = array(
                'activated' => $this->input->post('activated'),
                'role' => $this->input->post('role'),
                'assigned_prov' => $this->input->post('assigned_prov')
            );

            if ($this->User_Management_Model->update_user($id, $update_data)) {
                $this->session->set_flashdata('success', 'User updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to update user.');
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
        }

        redirect('user_management/users_management');
    }
}