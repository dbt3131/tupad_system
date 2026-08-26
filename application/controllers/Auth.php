<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
    }

public function register()
{
    // Redirect if already logged in
    if ($this->session->userdata('logged_in')) {
        redirect('users');
    }

    // --- FORM VALIDATION RULES ---
    
    // Personal Details
    $this->form_validation->set_rules(
        'reg_empno', 
        'Employee No', 
        'required|trim|regex_match[/^[0-9-]+$/]|is_unique[users.reg_empno]',
        array(
            'regex_match' => 'The %s field can only contain numbers and dashes.',
            'is_unique'   => 'This %s is already registered.'
        )
    );
    $this->form_validation->set_rules('reg_fname', 'First Name', 'required|trim');
    $this->form_validation->set_rules('reg_mname', 'Middle Name', 'trim');
    $this->form_validation->set_rules('reg_lname', 'Last Name', 'required|trim');
    $this->form_validation->set_rules('reg_extname', 'Extension Name', 'trim');

    // Organization Details
    $this->form_validation->set_rules('position_id', 'Job Position', 'required|numeric');
    $this->form_validation->set_rules('office_id', 'Office', 'required|numeric');
    $this->form_validation->set_rules('division_id', 'Division', 'required|numeric');

    // Account Credentials
    $this->form_validation->set_rules(
        'email', 
        'Email',
        'required|trim|valid_email|is_unique[users.email]',
        array('is_unique' => 'The %s is already registered.')
    );
    $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');
    $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

    // --- EXECUTE VALIDATION ---
    if ($this->form_validation->run() === FALSE) {
        // Reload page dropdown options when validation fails
        $data['positions'] = $this->User_model->get_position();
        $data['office']    = $this->User_model->get_office();
        $data['division']  = $this->User_model->get_division();
        
        $this->load->view('auth/register', $data);
        return;
    }

    // --- PREPARE DATA FOR DATABASE ---
    // Mapping HTML form names to database table columns
    $insert_data = array(
        'reg_empno'   => trim($this->input->post('reg_empno', TRUE)),
        'reg_fname'   => strtoupper(trim($this->input->post('reg_fname', TRUE))),
        'reg_mname'   => strtoupper(trim($this->input->post('reg_mname', TRUE))),
        'reg_lname'   => strtoupper(trim($this->input->post('reg_lname', TRUE))),
        'reg_extname' => strtoupper(trim($this->input->post('reg_extname', TRUE))),
        'position_id' => $this->input->post('position_id', TRUE),
        'office_id'   => $this->input->post('office_id', TRUE),
        'division_id' => $this->input->post('division_id', TRUE),
        'email'       => trim($this->input->post('email', TRUE)),
        'password'    => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
        'created_at'  => date('Y-m-d H:i:s')
    );

    // Save to Database via Model
    if ($this->User_model->register($insert_data)) {
        $this->session->set_flashdata('success', 'Registration successful. Wait for the activation.');
        redirect('auth/login');
    } else {
        $this->session->set_flashdata('error', 'Failed to register account. Please try again.');
        redirect('auth/register');
    }
}

    public function login()
{
    // Redirect if already logged in
    if ($this->session->userdata('logged_in')) {
        redirect('dashboard');
    }

    // Set Form Validation Rules
    $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
    $this->form_validation->set_rules('password', 'Password', 'required');

    // Run Validation
    if ($this->form_validation->run() === FALSE) {
        $this->load->view('auth/login');
        return;
    }

    $email = trim($this->input->post('email', TRUE));
    $password = $this->input->post('password');

    // Fetch user record from database
    $user = $this->User_model->get_user_by_email($email);

    // Verify User Existence & Password
    if ($user && password_verify($password, $user->password)) {

        // --- BLOCK INACTIVE USERS ---
        if ((int)$user->activated === 0) {
            $this->session->set_flashdata('error', 'Your account is inactive or pending approval. Please contact the Systems Analyst II.');
            redirect('auth/login');
            return;
        }

        // --- SUCCESSFUL LOGIN ---
        $this->session->sess_regenerate(TRUE);

        // Store user data in session
        $this->session->set_userdata(array(
            'user_id'   => $user->id,
            'assigned_prov'   => $user->assigned_prov,
            'reg_fname'      => $user->reg_fname,
            'email'     => $user->email,
            'logged_in' => TRUE
        ));

        redirect('dashboard/index');
    }

    // Invalid Credentials
    $this->session->set_flashdata('error', 'Invalid email or password.');
    redirect('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
