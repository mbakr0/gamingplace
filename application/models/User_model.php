<?php
/**
 * User model.
 */
class User_model extends CI_model
{
    public function register()
    {
        $data = [
            'first_name' => trim($this->input->post('first_name', true)),
            'last_name'  => trim($this->input->post('last_name', true)),
            'email'      => trim($this->input->post('email', true)),
            'username'   => trim($this->input->post('username', true)),
            'password'   => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
        ];

        $exists = $this->db->group_start()
            ->where('username', $data['username'])
            ->or_where('email', $data['email'])
            ->group_end()
            ->count_all_results('users');

        if ($exists > 0) {
            return false;
        }

        return $this->db->insert('users', $data);
    }

    public function login($username, $password)
    {
        $user = $this->db->where('username', trim($username))->get('users')->row();

        if (!$user) {
            return false;
        }

        // Accept legacy MD5 hashes once, then transparently upgrade them.
        $valid = password_verify($password, $user->password);
        if (!$valid && hash_equals((string)$user->password, md5($password))) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $this->db->where('id', $user->id)->update('users', ['password' => $newHash]);
            $valid = true;
        }

        return $valid ? $user->id : false;
    }
}
