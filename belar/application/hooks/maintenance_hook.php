<?php
function check_maintenance_mode() {
    $file = APPPATH . 'config/maintenance.json';
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if (!empty($data['maintenance_mode']) && $data['maintenance_mode'] === true) {
            $ci =& get_instance();
            $ci->load->library('session');
            if (!$ci->session->userdata('is_admin')) {
                include(APPPATH . 'views/maintenance_view.php');
                exit;
            }
        }
    }
}
