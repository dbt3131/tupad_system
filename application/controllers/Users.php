<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->session->userdata('logged_in')) {
            redirect('auth/login');
        }

        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

    public function create()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules(
            'email', 'Email',
            'required|trim|valid_email|is_unique[users.email]',
            array('is_unique' => 'The %s is already registered.')
        );
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
        $this->form_validation->set_rules(
            'password_confirm',
            'Confirm Password',
            'required|matches[password]'
        );

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('users/create');
            return;
        }

        $this->User_model->register(array(
            'name' => trim($this->input->post('name', TRUE)),
            'email' => trim($this->input->post('email', TRUE)),
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
        ));

        $this->session->set_flashdata('success', 'User created successfully.');
        redirect('users');
    }

    public function edit($id)
    {
        $user = $this->User_model->get_user($id);

        if (!$user) {
            show_404();
        }

        $data['user'] = $user;

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('users/edit', $data);
            return;
        }

        $email = trim($this->input->post('email', TRUE));

        if ($this->User_model->email_exists($email, $id)) {
            $this->session->set_flashdata('error', 'That email address is already in use.');
            redirect('users/edit/' . $id);
        }

        $update = array(
            'name' => trim($this->input->post('name', TRUE)),
            'email' => $email
        );

        if ($this->input->post('password')) {
            $update['password'] = password_hash(
                $this->input->post('password'),
                PASSWORD_DEFAULT
            );
        }

        $this->User_model->update_user($id, $update);

        if ((int) $this->session->userdata('user_id') === (int) $id) {
            $this->session->set_userdata(array(
                'name' => $update['name'],
                'email' => $update['email']
            ));
        }

        $this->session->set_flashdata('success', 'User updated successfully.');
        redirect('users');
    }

    public function delete($id)
    {
        if ((int) $this->session->userdata('user_id') === (int) $id) {
            $this->session->set_flashdata(
                'error',
                'You cannot delete the account you are currently using.'
            );
            redirect('users');
        }

        $this->User_model->delete_user($id);
        $this->session->set_flashdata('success', 'User deleted successfully.');
        redirect('users');
    }
}
