<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Singup extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('User_model');
    }

    public function index() {
        $this->load->view('front/singup');
    }

    public function create_user() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<p class="invalid-feedback">','</p>');
        $this->form_validation->set_rules('username', 'Username','trim|required');
        $this->form_validation->set_rules('firstname', 'First Name','trim|required');
        $this->form_validation->set_rules('lastname', 'Last Name','trim|required');
        $this->form_validation->set_rules('email', 'Email','trim|required');
        $this->form_validation->set_rules('password', 'Password','trim|required');
        $this->form_validation->set_rules('phone', 'Phone','trim|required');
        $this->form_validation->set_rules('address', 'Address','trim|required');

        if($this->form_validation->run() == true) {

            // -----------------------------
            // ✅ GET IP ADDRESS
            // -----------------------------
            // Get IP address
$ip = $this->input->ip_address();

// Store data. IP/MAC will be auto-populated by the model when possible.
$formArray = [
    'username'     => $this->input->post('username'),
    'f_name'       => $this->input->post('firstname'),
    'l_name'       => $this->input->post('lastname'),
    'email'        => $this->input->post('email'),
    'password'     => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
    'phone'        => $this->input->post('phone'),
    'address'      => $this->input->post('address'),
    // ip_address and mac_address intentionally left out to let model detect
];


            $this->User_model->create($formArray);

            $this->session->set_flashdata("success", "Account created successfully, please login");
            redirect(base_url().'login/index');

        } else {
            $this->load->view('front/singup');
        }
    }

}
