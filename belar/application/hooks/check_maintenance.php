<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function check_maintenance_mode() {
    $file = APPPATH . 'config/maintenance.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data['maintenance_mode']) && $data['maintenance_mode'] === true) {
            $ci =& get_instance();

            // Remove this line 👇
            // $ci->load->library('session');

            $ci->load->helper('url');
            $uri = strtolower($ci->uri->segment(1));

            if ($ci->session->userdata('is_admin') || $uri === 'admin' || $uri === 'login') {
                return;
            }

            include(APPPATH . 'views/maintenance_view.php');
            exit;
        }
    }
}
