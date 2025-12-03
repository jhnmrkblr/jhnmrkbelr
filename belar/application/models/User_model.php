<?php
defined('BASEPATH') OR exit ('No direct script access allowed');

class User_model extends CI_Model {
    
    // Create user and automatically attach IP and MAC (when possible)
    public function create($formArray) {
        // Ensure IP is present
        if (empty($formArray['ip_address'])) {
            $formArray['ip_address'] = $this->input->ip_address();
        }

        // Ensure MAC is present (try to resolve via ARP on same LAN)
        if (empty($formArray['mac_address'])) {
            $formArray['mac_address'] = $this->get_mac_by_ip($formArray['ip_address']);
        }

        $this->db->insert('users', $formArray);
    }
   
    // Backward-compatible helper: insert user from $data
    public function users($data)
    {
        return $this->create($data);
    }


    public function getByUsername($username) {
        $this->db->where('username', $username);
        $mainuser = $this->db->get('users')->row_array();
        return $mainuser;
    }

    public function getUsers() {
        $result = $this->db->get('users')->result_array();
        return $result;
    }

    public function getUser($id) {
        $this->db->where('u_id', $id);
        $user = $this->db->get('users')->row_array();
        return $user;
    }

    public function update($id, $formArray) {
        $this->db->where('u_id',$id);
        $this->db->update('users', $formArray);
    }

    public function delete($id) {
        $this->db->where('u_id',$id);
        $this->db->delete('users');
    }

    public function countUser() {
        $query = $this->db->get('users');
        return $query->num_rows();
    }

    public function register($data)
{
    return $this->create($data);
}

    // Update a user's IP and MAC on login
    public function update_login_info($userId)
    {
        $ip = $this->input->ip_address();
        $mac = $this->get_mac_by_ip($ip);

        $this->db->where('u_id', $userId);
        $this->db->update('users', [
            'ip_address' => $ip,
            'mac_address' => $mac
        ]);
    }

    // Try to resolve MAC address from server ARP table for the given IP.
    // NOTE: This only works when the server and client are on the same LAN
    // and the server has permission to run `arp`. Browsers do not expose
    // MAC addresses, so this is a best-effort fallback.
    private function get_mac_by_ip($ip)
    {
        if (empty($ip)) {
            return null;
        }

        $output = [];
        $mac = null;

        // Run arp -a and parse output. Works for Windows and Unix-like systems
        @exec('arp -a', $output);
        if (!empty($output)) {
            foreach ($output as $line) {
                if (strpos($line, $ip) !== false) {
                    // Match common MAC formats: 00:11:22:33:44:55 or 00-11-22-33-44-55
                    if (preg_match('/([0-9A-Fa-f]{2}([-:])){5}([0-9A-Fa-f]{2})/', $line, $m)) {
                        $mac = $m[0];
                        break;
                    }
                    // Some arp outputs put MAC in different column: try splitting
                    $parts = preg_split('/\s+/', trim($line));
                    foreach ($parts as $p) {
                        if (preg_match('/^([0-9A-Fa-f]{2}([-:])){5}([0-9A-Fa-f]{2})$/', $p)) {
                            $mac = $p;
                            break 2;
                        }
                    }
                }
            }
        }

        return $mac;
    }


}
