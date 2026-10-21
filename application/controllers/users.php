<?php
/**
 * Authentication controller.
 */
class Users extends CI_controller
{
    public function register()
    {
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[190]');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[8]|max_length[72]');
        $this->form_validation->set_rules('password2', 'Confirm Password', 'required|matches[password]');

        if ($this->form_validation->run() === false) {
            $data['main_content'] = 'register';
            $this->load->view('layouts/main', $data);
            return;
        }

        if ($this->User_model->register()) {
            $this->session->set_flashdata('registered', 'You are now registered and can login.');
            redirect('products');
            return;
        }

        $this->session->set_flashdata('fail_register', 'Username or email is already registered.');
        redirect('users/register');
    }

    public function login()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[72]');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('fail_login', 'Please enter a valid username and password.');
            redirect('products');
            return;
        }

        $username = $this->input->post('username', true);
        $password = $this->input->post('password');
        $user_id = $this->User_model->login($username, $password);

        if ($user_id) {
            $this->session->sess_regenerate(true);
            $this->session->set_userdata([
                'user_id' => $user_id,
                'username' => $username,
                'logged_in' => true
            ]);
            $this->session->set_flashdata('pass_login', 'You are logged in.');
        } else {
            $this->session->set_flashdata('fail_login', 'Incorrect username or password.');
        }

        redirect('products');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('products');
    }
}
