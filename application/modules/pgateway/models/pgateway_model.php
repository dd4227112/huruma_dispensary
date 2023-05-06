<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class pgateway_model extends CI_model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function getpaymentgatewaySettingsById($id) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('id', $id);
        $query = $this->db->get('paymentgateway');
        return $query->row();
    }

    function getpaymentgatewaySettingsByName($name) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->where('name', $name);
        $query = $this->db->get('paymentgateway');
        return $query->row();
    }

    function getpaymentgatewayByUser($user) {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $this->db->where('user', $user);
        $query = $this->db->get('paymentgateway');
        return $query->result();
    }

    function getpaymentgatewaySettings() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $query = $this->db->get('paymentgateway');
        return $query->row();
    }

    function updatepaymentgatewaySettings($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('paymentgateway', $data);
    }

    function addpaymentgatewaySettings($data) {   
        $this->db->insert('paymentgateway', $data);
    }

    function delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('paymentgateway');
    }

    function insertpaymentgateway($data) {
        $this->db->insert('paymentgateway', $data);
    }

    function getpaymentgateway() {
        $this->db->where('hospital_id', $this->session->userdata('hospital_id'));
        $this->db->order_by('id', 'desc');
        $query = $this->db->get('paymentgateway');
        return $query->result();
    }

}
