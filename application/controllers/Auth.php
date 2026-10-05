<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    /**
     * Controller Constructor
     * Process: Initializes parent properties, loads the User model for database operations,
     * and loads the form validation library to handle input rules and errors.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('form_validation');
        $this->load->library('encryption'); 

        if ($this->session->userdata('logged_in') && $this->session->userdata('user_id')) {
            $this->User_model->update_last_activity($this->session->userdata('user_id'));
        }

        $data['online_users'] = $this->User_model->get_online_users();
    }

    /**
     * User Registration Method
     * Process: Validates if the user is already logged in, sets up extensive validation rules 
     * for personal details, organizational data, and credentials, processes password hashing, 
     * saves data to the database, and redirects with appropriate flash messages.
     */
    public function register()
    {
        // Redirect if already logged in
        if ($this->session->userdata('logged_in')) {
            redirect('users');
        }
        

    // Personal Details Validation Rules
        $this->form_validation->set_rules(
            'reg_empno', 
            'Employee No', 
            'required|trim|is_unique[users.reg_empno]',
            array(
                'is_unique'   => 'This %s is already registered.'
            )
        );
        $this->form_validation->set_rules('reg_fname', 'First Name', 'required|trim');
        $this->form_validation->set_rules('reg_mname', 'Middle Name', 'trim');
        $this->form_validation->set_rules('reg_lname', 'Last Name', 'required|trim');
        $this->form_validation->set_rules('reg_extname', 'Extension Name', 'trim');

        // Organization Details Validation Rules
        $this->form_validation->set_rules('position_id', 'Job Position', 'required|numeric');
        $this->form_validation->set_rules('office_id', 'Office', 'required|numeric');
        $this->form_validation->set_rules('division_id', 'Division', 'required|numeric');

        // Account Credentials Validation Rules
        $this->form_validation->set_rules(
            'email', 
            'Email',
            'required|trim|valid_email|is_unique[users.email]',
            array('is_unique' => 'The %s is already registered.')
        );
        
        // Strict Password Validation Rules
        $this->form_validation->set_rules(
            'password', 
            'Password', 
            'required|min_length[8]|callback_check_password_strength',
            array(
                'min_length' => 'The %s must be at least 8 characters long.'
            )
        );
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

    /**
     * Password Strength Validation Callback
     * Process: Evaluates the password string using regular expressions to ensure 
     * it contains at least one uppercase letter, one number, and one special character.
     */
    public function check_password_strength($password)
    {
        $has_uppercase = preg_match('/[A-Z]/', $password);
        $has_number    = preg_match('/[0-9]/', $password);
        $has_special   = preg_match('/[\W_]/', $password);

        if (!$has_uppercase || !$has_number || !$has_special) {
            $this->form_validation->set_message(
                'check_password_strength', 
                'The Password field must contain at least one uppercase letter, one number, and one special character.'
            );
            return FALSE;
        }
        return TRUE;
    }

 






















public function login()
{
    // Prevent browser caching of the login page
    $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    $this->output->set_header('Cache-Control: post-check=0, pre-check=0', FALSE);
    $this->output->set_header('Pragma: no-cache');

    // Redirect if already logged in
    if ($this->session->userdata('logged_in')) {
        redirect('dashboard');
    }

    // If form is submitted via POST
    if ($this->input->server('REQUEST_METHOD') === 'POST') {
        $user_captcha = trim($this->input->post('captcha'));
        $num1 = $this->input->post('num1');
        $num2 = $this->input->post('num2');

        // Fallback check if hidden fields are somehow missing
        if ($num1 === '' || $num2 === '') {
            $this->session->set_flashdata('error', 'Security session expired. Please try again.');
            redirect('auth/login');
            return;
        }

        $correct_answer = intval($num1) + intval($num2);

        // Verify CAPTCHA
        if ($user_captcha === '' || intval($user_captcha) !== $correct_answer) {
            $this->session->set_flashdata('error', 'Incorrect CAPTCHA answer. Please try again.');
            redirect('auth/login');
            return;
        }

        // Set Form Validation Rules for credentials
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === TRUE) {
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

                $this->session->set_userdata(array(
                    'user_id'         => $user->id,
                    'assigned_prov'   => $user->assigned_prov,
                    'reg_fname'       => $user->reg_fname,
                    'email'           => $user->email,
                    'logged_in'       => TRUE
                ));
                $this->User_model->update_last_activity($user->id);

                redirect('dashboard/index');
            }

            // Invalid Credentials
            $this->session->set_flashdata('error', 'Invalid email or password.');
            redirect('auth/login');
        }
    }

    // Generate fresh CAPTCHA numbers
    $data['num1'] = rand(1, 10);
    $data['num2'] = rand(1, 10);
    $data['captcha_question'] = "What is {$data['num1']} + {$data['num2']}?";
    
    $this->load->view('auth/login', $data);
}

/**
     * Employee Number Validation Callback
     * Process: Ensures the Employee No only contains numbers and dashes.
     */
    public function check_empno($empno)
    {
        if (!preg_match('/^[0-9-]+$/', $empno)) {
            return FALSE;
        }
        return TRUE;
    }







public function update_heartbeat()
{
    // Ensure it's an AJAX request for security
    if (!$this->input->is_ajax_request()) {
        show_404();
    }

    if ($this->session->userdata('logged_in') && $this->session->userdata('user_id')) {
        $this->load->model('User_model');
        $this->User_model->update_last_activity($this->session->userdata('user_id'));
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'unauthorized']);
    }
}












    /**
     * User Logout Method
     * Process: Destroys all active session variables and redirects the user to the login page.
     */
// Inside application/controllers/Auth.php[cite: 2]

public function logout()
{
    // Clear last_activity immediately upon logout
    if ($this->session->userdata('user_id')) {
        $this->load->model('User_model');
        $this->User_model->update_last_activity_null($this->session->userdata('user_id'));
    }

    $this->session->sess_destroy();
    redirect('auth/login', 'refresh');
}




/**
     * Forgot Password Method
     * Process: Handles email input, checks if user exists, generates a secure token, 
     * and sends a reset link via CodeIgniter's email library.
     */
public function forgot_password()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules('email', 'Email Address', 'required|trim|valid_email');

            if ($this->form_validation->run() === TRUE) {
                // Clear any old flash messages first
                $this->session->unset_userdata('success');
                $this->session->unset_userdata('error');

                $email = trim($this->input->post('email', TRUE));
                $user = $this->User_model->get_user_by_email($email);

                if ($user) {
                    // Generate a secure random token
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

                    // Save token to database
                    $this->User_model->set_reset_token($email, $token, $expires);

                    // Configure and send Email
                    $this->load->library('email');

                    $config = array(
                        'protocol'    => 'smtp',
                        'smtp_host'   => 'smtp.googlemail.com',
                        'smtp_port'   => 465,
                        'smtp_user'   => 'dbtdbt31dole@gmail.com',
                        'smtp_pass'   => 'whamfpzstbreniqa',
                        'smtp_crypto' => 'ssl',
                        'mailtype'    => 'html',
                        'charset'     => 'utf-8',
                        'wordwrap'    => TRUE
                    );
                    $this->email->initialize($config);
                    $this->email->set_newline("\r\n");

                    $this->email->from('your_email@domain.com', 'DOLE PRISM Support');
                    $this->email->to($email);
                    $this->email->subject('Password Reset Request - PRISM');
                    
                    $reset_link = site_url('auth/reset_password/' . $token);
                    $message = "<p>Hi {$user->reg_fname},</p>";
                    $message .= "<p>You requested a password reset for your PRISM account.</p>";
                    $message .= "<p>Click the link below to reset your password (valid for 1 hour):</p>";
                    $message .= "<p><a href='{$reset_link}'>Reset Password</a></p>";
                    $message .= "<p>If you did not request this, please ignore this email.</p>";

                    $this->email->message($message);

                    if ($this->email->send()) {
                        $this->session->set_flashdata('success', 'Password reset instructions have been sent to your email.');
                    } else {
                        // Fixed: Changed from 'success' to 'error'
                        $this->session->set_flashdata('error', 'Please try again or contact Dustin Torres ISA II');
                    }
                } 
                else {
                    // Generic message for security (prevents user enumeration)
                    $this->session->set_flashdata('error', 'Invalid Email Address');
                }
                
                redirect('auth/forgot_password');
            }
        }

        $this->load->view('auth/forgot_password');
    }




    /**
     * Reset Password Method
     * Process: Validates the token from the URL, validates the new password strength, 
     * updates the password in the database, and redirects to login.
     */
    public function reset_password($token = NULL)
    {
        if (!$token) {
            show_404();
        }

        // Verify token validity
        $user = $this->User_model->get_user_by_reset_token($token);
        if (!$user) {
            $this->session->set_flashdata('error', 'The password reset link is invalid or has expired.');
            redirect('auth/login');
        }

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $this->form_validation->set_rules(
                'password', 
                'New Password', 
                'required|min_length[8]|callback_check_password_strength',
                array('min_length' => 'The %s must be at least 8 characters long.')
            );
            $this->form_validation->set_rules('password_confirm', 'Confirm Password', 'required|matches[password]');

            if ($this->form_validation->run() === TRUE) {
                $new_password = password_hash($this->input->post('password'), PASSWORD_DEFAULT);
                
                if ($this->User_model->update_password($user->id, $new_password)) {
                    $this->session->set_flashdata('success', 'Your password has been successfully updated. You can now login.');
                    redirect('auth/login');
                } else {
                    $this->session->set_flashdata('error', 'Failed to update password. Please try again.');
                }
            }
        }

        $data['token'] = $token;
        $this->load->view('auth/reset_password', $data);
    }




















}