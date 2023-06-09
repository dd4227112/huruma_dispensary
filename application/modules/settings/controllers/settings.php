<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Settings extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('sma');
        if (!$this->ion_auth->in_group(array('admin', 'superadmin'))) {
            redirect('home/permission');
        }
    }

    public function index() {        
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('settings', $data);
        $this->load->view('home/footer'); // just the footer file
    }
    
    

    function subscription() {

        $data['settings'] = $this->settings_model->getSettings();
        $data['subscription'] = $this->settings_model->getSubscription();

        //$bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('backups')));
        //$meta = array('page_title' => lang('backups'), 'bc' => $bc);
        // $this->page_construct('settings/backups', $this->data, $meta);
        $this->load->view('home/dashboard', $data);
        $this->load->view('subscription', $data);
        $this->load->view('home/footer');
    }


    public function update() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');
        $title = $this->input->post('title');
        $email = $this->input->post('email');
        $address = $this->input->post('address');
        $phone = $this->input->post('phone');
        $currency = $this->input->post('currency');
        $logo = $this->input->post('logo');
        $buyer = $this->input->post('buyer');
        $p_code = $this->input->post('p_code');

        if (!empty($email)) {
            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
            // Validating Name Field
            $this->form_validation->set_rules('name', 'System Name', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Title Field
            $this->form_validation->set_rules('title', 'Title', 'rtrim|equired|min_length[1]|max_length[100]|xss_clean');
            // Validating Email Field
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[1]|max_length[100]|xss_clean');
            // Validating Address Field   
            $this->form_validation->set_rules('address', 'Address', 'trim|required|min_length[1]|max_length[500]|xss_clean');
            // Validating Phone Field           
            $this->form_validation->set_rules('phone', 'Phone', 'trim|required|min_length[1]|max_length[50]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('currency', 'Currency', 'trim|required|min_length[1]|max_length[3]|xss_clean');
            // Validating Currency Field   
            $this->form_validation->set_rules('logo', 'Logo', 'trim|min_length[1]|max_length[1000]|xss_clean');
            // Validating Department Field   
            $this->form_validation->set_rules('buyer', 'Buyer', 'trim|min_length[5]|max_length[500]|xss_clean');
            // Validating Phone Field           
            $this->form_validation->set_rules('p_code', 'Purchase Code', 'trim|min_length[5]|max_length[50]|xss_clean');

            if ($this->form_validation->run() == FALSE) {
                $data = array();
                $data['settings'] = $this->settings_model->getSettings();
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('settings', $data);
                $this->load->view('home/footer'); // just the footer file
            } else {

                $file_name = $_FILES['img_url']['name'];
                $file_name_pieces = explode('_', $file_name);
                $new_file_name = '';
                $count = 1;
                foreach ($file_name_pieces as $piece) {
                    if ($count !== 1) {
                        $piece = ucfirst($piece);
                    }

                    $new_file_name .= $piece;
                    $count++;
                }
                $config = array(
                    'file_name' => $new_file_name,
                    'upload_path' => "./uploads/",
                    'allowed_types' => "gif|jpg|png|jpeg|pdf",
                    'overwrite' => False,
                    'max_size' => "20480000", // Can be set to particular file size , here it is 2 MB(2048 Kb)
                    'max_height' => "1768",
                    'max_width' => "2024"
                );

                $this->load->library('Upload', $config);
                $this->upload->initialize($config);

                if ($this->upload->do_upload('img_url')) {
                    $path = $this->upload->data();
                    $img_url = "uploads/" . $path['file_name'];
                    $data = array();
                    $data = array(
                        'system_vendor' => $name,
                        'title' => $title,
                        'address' => $address,
                        'phone' => $phone,
                        'email' => $email,
                        'currency' => $currency,
                        'codec_username' => $buyer,
                        'codec_purchase_code' => $p_code,
                        'logo' => $img_url
                    );
                } else {
                    $data = array();
                    $data = array(
                        'system_vendor' => $name,
                        'title' => $title,
                        'address' => $address,
                        'phone' => $phone,
                        'email' => $email,
                        'currency' => $currency,
                        'codec_username' => $buyer,
                        'codec_purchase_code' => $p_code,
                    );
                }
                //$error = array('error' => $this->upload->display_errors());

                $this->settings_model->updateSettings($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
                // Loading View
                redirect('settings');
            }
        } else {
            $this->session->set_flashdata('feedback', 'Email Required!');
            redirect('settings', 'refresh');
        }
    }

    function backups() {
        $data['files'] = glob('./files/backups/*.zip', GLOB_BRACE);
        $data['dbs'] = glob('./files/backups/*.txt', GLOB_BRACE);
        $data['settings'] = $this->settings_model->getSettings();

        //$bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('backups')));
        //$meta = array('page_title' => lang('backups'), 'bc' => $bc);
        // $this->page_construct('settings/backups', $this->data, $meta);
        $this->load->view('home/dashboard', $data);
        $this->load->view('backups', $data);
        $this->load->view('home/footer');
    }

    function language() {

        $data['settings'] = $this->settings_model->getSettings();

        //$bc = array(array('link' => site_url('settings'), 'page' => lang('settings')), array('link' => '#', 'page' => lang('backups')));
        //$meta = array('page_title' => lang('backups'), 'bc' => $bc);
        // $this->page_construct('settings/backups', $this->data, $meta);
        $this->load->view('home/dashboard', $data);
        $this->load->view('language', $data);
        $this->load->view('home/footer');
    }

    function changeLanguage() {
        $id = $this->input->post('id');
        $language = $this->input->post('language');
        $language_settings = $this->input->post('language_settings');


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('language', 'language', 'trim|required|min_length[1]|max_length[100]|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            $data = array();
            $data['settings'] = $this->settings_model->getSettings();
            $this->load->view('home/dashboard', $data); // just the header file
            $this->load->view('settings', $data);
            $this->load->view('home/footer'); // just the footer file
        } else {

            //$error = array('error' => $this->upload->display_errors());
            $data = array();
            $data = array(
                'language' => $language,
            );

            $this->settings_model->updateSettings($id, $data);

            // Loading View
            $this->session->set_flashdata('feedback', 'Updated');
            if (!empty($language_settings)) {
                redirect('settings/language');
            } else {
                redirect('');
            }
        }
    }

    function selectPaymentGateway() {
        $id = $this->input->post('id');
        $payment_gateway = $this->input->post('payment_gateway');


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('payment_gateway', 'Payment Gateway', 'trim|required|min_length[1]|max_length[100]|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            redirect('pgateway');
        } else {

            //$error = array('error' => $this->upload->display_errors());
            $data = array();
            $data = array(
                'payment_gateway' => $payment_gateway,
            );

            $this->settings_model->updateSettings($id, $data);

            // Loading View
            $this->session->set_flashdata('feedback', 'Updated');
            if (!empty($payment_gateway)) {
                redirect('pgateway');
            } else {
                redirect('');
            }
        }
    }
    
     function selectSmsGateway() {
        $id = $this->input->post('id');
        $sms_gateway = $this->input->post('sms_gateway');


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
        // Validating Name Field
        $this->form_validation->set_rules('sms_gateway', 'Sms Gateway', 'trim|required|min_length[1]|max_length[100]|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            redirect('pgateway');
        } else {

            //$error = array('error' => $this->upload->display_errors());
            $data = array();
            $data = array(
                'sms_gateway' => $sms_gateway,
            );

            $this->settings_model->updateSettings($id, $data);

            // Loading View
            $this->session->set_flashdata('feedback', 'Updated');
            if (!empty($sms_gateway)) {
                redirect('sms');
            } else {
                redirect('');
            }
        }
    }

    function backup_database() {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $this->load->dbutil();
        $prefs = array(
            'format' => 'sql',
            'filename' => 'hms_db_backup.sql'
        );
        $back = $this->dbutil->backup($prefs);
        $backup = & $back;
        $db_name = 'db-backup-on-' . date("Y-m-d-H-i-s") . '.txt';
        $save = './files/backups/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->session->set_flashdata('message', 'Database backup Successfull !');
        redirect("settings/backups");

        /* 	
          $this->load->dbutil();
          $backup = $this->dbutil->backup();
          $this->load->helper('file');
          write_file('Downloads.sql', $backup);
          $this->load->helper('download');
          force_download('backup.zip', $backup); */
    }

    function backup_files() {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $this->load->library('zip');
        $data = array_diff(scandir(FCPATH), array('..', '.', 'files')); // 'files' folder will be excluded here with '.' and '..'
        foreach ($data as $d) {
            $path = FCPATH . $d;
            if (is_dir($path))
                $this->zip->read_dir($path, false);
            if (is_file($path))
                $this->zip->read_file($path, false);
        }
        $filename = 'file-backup-' . date("Y-m-d-H-i-s") . '.zip';
        $this->zip->archive(FCPATH . 'files/backups/' . $filename);
        $this->session->set_flashdata('message', 'Application backup Successfull !');
        redirect("settings/backups");
        exit();
    }

    /* function backup_files()
      {
      if (!$this->ion_auth->in_group('admin')) {
      $this->session->set_flashdata('error', lang('access_denied'));
      redirect("home/permission");
      }
      $this->load->dbutil();
      $backup = $this->dbutil->backup();
      $this->load->helper('file');

      $filename = 'file-backup-' . date("Y-m-d-H-i-s");
      $this->sma->zip("./", './files/backups/', $filename);
      $this->session->set_flashdata('message', lang('backup_saved'));
      redirect("settings/backups");
      exit();
      } */

    function restore_database($dbfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $file = file_get_contents('./files/backups/' . $dbfile . '.txt');
        $this->db->conn_id->multi_query($file);
        $this->db->conn_id->close();
        $this->session->set_flashdata('message', 'Restoring of Backup Successfull');
        redirect('settings/backups');
    }

    function download_database($dbfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $this->load->library('zip');
        $this->zip->read_file('./files/backups/' . $dbfile . '.txt');
        $name = 'db_backup_' . date('Y_m_d_H_i_s') . '.zip';
        $this->zip->download($name);
        exit();
    }

    function download_backup($zipfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $this->load->helper('download');
        force_download('./files/backups/' . $zipfile . '.zip', NULL);
        exit();
    }

    function restore_backup($zipfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        $file = './files/backups/' . $zipfile . '.zip';
        $this->sma->unzip($file, './');
        $this->session->set_flashdata('info', 'Restoring of Application Successfull');
        redirect("settings/backups");
        exit();
    }

    function delete_database($dbfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        unlink('./files/backups/' . $dbfile . '.txt');
        $this->session->set_flashdata('info', 'Deleting of Database Successfull');
        redirect("settings/backups");
    }

    function delete_backup($zipfile) {
        if (!$this->ion_auth->in_group('admin')) {
            $this->session->set_flashdata('error', lang('access_denied'));
            redirect("home/permission");
        }
        unlink('./files/backups/' . $zipfile . '.zip');
        $this->session->set_flashdata('info', 'Deleting of App Backup Successfull');
        redirect("settings/backups");
    }
    // NHIF
    function nhif_setup(){
        $data['setup'] = $this->settings_model->getNHIFsetups();
        $this->load->view('home/dashboard', $data); // just the header file
        $this->load->view('settings/nhif_setup', $data);
        $this->load->view('home/footer');
    }
  
    function save_setup(){
		// $this->load->model('finance_model');
				// Get input values from form
		$service_token_url = $this->input->post('service_token_url');
		$claim_token_url = $this->input->post('claim_token_url');
		$card_detail_url = $this->input->post('card_detail_url');
		$authorize_card_url = $this->input->post('authorize_card_url');
		$price_package_url = $this->input->post('price_package_url');
		$submit_claim_url = $this->input->post('submit_claim_url');
		$submited_claim_url = $this->input->post('submited_claim_url');
		$refer_patient_url = $this->input->post('refer_patient_url'); 
		$discharge_patient_url = $this->input->post('discharge_patient_url'); 
		$admit_patient_url = $this->input->post('admit_patient_url'); 
        $claim_reconciliation_url = $this->input->post('claim_reconciliation_url'); 
		$claimed_amount_url = $this->input->post('claimed_amount_url'); 
		$facility_code = $this->input->post('facility_code');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$environment = $this->input->post('environment');


		// Create array to hold input values
		$data = array(
			'service_token_url' => $service_token_url,
			'claim_token_url' => $claim_token_url,
			'card_detail_url' => $card_detail_url,
			'authorize_card_url' => $authorize_card_url,
			'price_package_url' => $price_package_url,
			'submit_claim_url' => $submit_claim_url,
			'submited_claim_url' => $submited_claim_url,
			'refer_patient_url' => $refer_patient_url,
            'admit_patient_url' => $admit_patient_url,
            'discharge_patient_url' => $discharge_patient_url,
            'claim_reconciliation_url' => $claim_reconciliation_url,
            'claimed_amount_url' => $claimed_amount_url,
			'facility_code' => $facility_code,
			'username' => $username,
			'password' => $password,
			'environment' => $environment,
            
		);

        if($this->settings_model->save_setup($data)){
            $this->session->set_flashdata('success', "Your settings saved successfully to the database!! ");
            return redirect('settings/nhif_setup');
        }
		
	 }
    public function clear_old(){
        $tables =$this->db->query("select TABLE_NAME as table_name from information_schema.tables where table_schema ='huduma' and TABLE_NAME IN (select TABLE_NAME from information_schema.tables where table_schema ='hospital') and TABLE_NAME NOT IN ('users', 'groups', 'users_groups')")->result();
        foreach($tables as $table){
        $this->db->query("delete from huduma.".$table->table_name);
        }
        // $this->db->query("insert into huduma.groups select * from hospital.groups");
        // $this->db->query("insert into huduma.users select * from hospital.users");
        // $this->db->query("insert into huduma.users_groups select * from hospital.users_groups");
        $this->session->set_flashdata('success', "Operation successfully");
        redirect($_SERVER['HTTP_REFERER']);
        // print_r($tables);
    
    }
    public function verify(){
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('settings/verify');
        $this->load->view('home/footer');
        

    }
    public function sync(){
       
        $tables =$this->db->query("select TABLE_NAME as table_name from information_schema.tables where table_schema ='huduma' and TABLE_NAME IN (select TABLE_NAME from information_schema.tables where table_schema ='hospital') and TABLE_NAME NOT IN ('users', 'groups', 'users_groups')")->result();
        
        $this->db->query("ALTER TABLE hospital.`doctor` ADD `regNo` VARCHAR(256) NULL AFTER `profile`, ADD `physician_qualification_id` INT(10) NOT NULL DEFAULT '100' AFTER `hospital_id`");
        $this->db->query("ALTER TABLE hospital.`patient` ADD `nhif_benefit` INT(1) NOT NULL DEFAULT '2' AFTER `age` , ADD `card_no` VARCHAR(256) NULL AFTER `nhif_benefit` ");

        $this->db->query("ALTER TABLE hospital.`nurse` ADD `physician_qualification_id` INT(10) NOT NULL DEFAULT '100' AFTER `hospital_id`");

        $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
        foreach($tables as $table){
        $this->db->query("insert into huduma.".$table->table_name." select * from hospital.".$table->table_name);
        }
        $this->session->set_flashdata('success', "Operation successfully, now you can continue with usage");

        redirect($_SERVER['HTTP_REFERER']);
    
    }
}

/* End of file settings.php */
/* Location: ./application/modules/settings/controllers/settings.php */

// select TABLE_NAME from information_schema.tables where table_schema ='huduma' and TABLE_NAME NOT IN (select TABLE_NAME from information_schema.tables where table_schema ='hospital');

