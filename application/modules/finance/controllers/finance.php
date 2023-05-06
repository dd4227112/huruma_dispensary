<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Finance extends MX_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('finance_model');
        $this->load->model('doctor/doctor_model');
        $this->load->model('patient/patient_model');
        $this->load->model('finance/pharmacy_model');
        $this->load->model('accountant/accountant_model');
        $this->load->model('receptionist/receptionist_model');
        $this->load->module('paypal');
        $this->load->library('session');


        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        if (!$this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist', 'Nurse', 'Laboratorist', 'Doctor'))) {
            redirect('home/permission');
        }
    }
    // nhif service token
    public function getAuthenticationHeader($username, $password)
    { 
        // Construct the body for the STS request
        $authenticationRequestBody = 
        "grant_type=password&username=".$username."&password=".$password; 

        //Using curl to post the information to STS and get back the authentication response 
        $ch = curl_init();
        // set url 
        curl_setopt($ch, CURLOPT_URL, SERVICE_TOKEN); 
        // Get the response back as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        // Mark as Post request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // Set the parameters for the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authenticationRequestBody);
        // By default, HTTPS does not work with curl.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // read the output from the post request
        $output = curl_exec($ch); 
        // close curl resource to free up system resources
        curl_close($ch); 
        // decode the response from sts using json decoder
        $tokenOutput = json_decode($output);
        // exit;
        // return $tokenOutput->{'access_token'};
        if(empty($tokenOutput)){
            $this->session->set_flashdata('error', "Error!! Check Internet your Connection");
            redirect($_SERVER['HTTP_REFERER']);
            ?>
            <!-- <script>alert('Error!! Check Internet your Connection')</script> -->
            <?php
           
        }else{
        return $tokenOutput->{'access_token'};
        }
    }
     // claim server token
    public function getAuthenticationHeaderClaimServer($username, $password)
    { 
        // Construct the body for the STS request
        $authenticationRequestBody = 
        "grant_type=password&username=".$username."&password=".$password; 

        //Using curl to post the information to STS and get back the authentication response 
        $ch = curl_init();
        // set url 
        curl_setopt($ch, CURLOPT_URL, CLAIM_TOKEN); 
        // Get the response back as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        // Mark as Post request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        // Set the parameters for the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $authenticationRequestBody);
        // By default, HTTPS does not work with curl.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // read the output from the post request
        $output = curl_exec($ch); 
        // close curl resource to free up system resources
        curl_close($ch); 
        // decode the response from sts using json decoder
        $tokenOutput = json_decode($output);
        // exit;
        // return $tokenOutput->{'access_token'};s
        if(empty($tokenOutput)){
            $this->session->set_flashdata('error', "Error!! Check Internet your Connection");
            redirect($_SERVER['HTTP_REFERER']);
            ?>
            <!-- <script>alert('Error!! Check Internet your Connection')</script> -->
            <?php
           
        }else{
        return $tokenOutput->{'access_token'};
        }
    }
    public function index() {

        redirect('finance/financial_report');
    }

    public function payment() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }

        $data['settings'] = $this->settings_model->getSettings();

        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('payment', $data);
        $this->load->view('home/footer'); // just the header file
    }

    function amountDistribution() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['payments'] = $this->finance_model->getPayment();

        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('amount_distribution', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addPaymentView() {
        $data = array();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->finance_model->getPaymentCategory();
        $data['patients'] = $this->patient_model->getPatient();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_payment_view', $data);
        $this->load->view('home/footer'); // just the header file
    }

        public function addPayment() {
        $id = $this->input->post('id');
        $item_selected = array();
        $quantity = array();
        $category_selected = array();
        // $amount_by_category = $this->input->post('category_amount');
        $category_selected = $this->input->post('category_name');
        $item_selected = $this->input->post('category_id');
        $quantity = $this->input->post('quantity');
        $remarks = $this->input->post('remarks');

        if (empty($item_selected)) {
            $this->session->set_flashdata('feedback', 'Select a Item');
            redirect('finance/addPaymentView');
        } else {
            $item_quantity_array = array();
            $item_quantity_array = array_combine($item_selected, $quantity);
        }
        $cat_and_price = array();
        if (!empty($item_quantity_array)) {
            foreach ($item_quantity_array as $key => $value) {
                $current_item = $this->finance_model->getPaymentCategoryById($key);
                $category_price = $current_item->c_price;
                $category_type = $current_item->type;
                $qty = $value;
                $cat_and_price[] = $key . '*' . $category_price . '*' . $category_type . '*' . $qty;
                $amount_by_category[] = $category_price * $qty;
            }
            $category_name = implode(',', $cat_and_price);
        } else {
            $this->session->set_flashdata('feedback', 'Atend The Required Fields');
            redirect('finance/addPaymentView');
        }

        $patient = $this->input->post('patient');

        $p_name = $this->input->post('p_name');
        $p_email = $this->input->post('p_email');
        if (empty($p_email)) {
            $p_email = $p_name . '-' . rand(1, 1000) . '-' . $p_name . '-' . rand(1, 1000) . '@example.com';
        }
        if (!empty($p_name)) {
            $password = $p_name . '-' . rand(1, 100000000);
        }
        $p_phone = $this->input->post('p_phone');
        $p_age = $this->input->post('p_age');
        $p_gender = $this->input->post('p_gender');
        $add_date = date('m/d/y');


        $patient_id = rand(10000, 1000000);



        $d_name = $this->input->post('d_name');
        $d_email = $this->input->post('d_email');
        if (empty($d_email)) {
            $d_email = $d_name . '-' . rand(1, 1000) . '-' . $d_name . '-' . rand(1, 1000) . '@example.com';
        }
        if (!empty($d_name)) {
            $password = $d_name . '-' . rand(1, 100000000);
        }
        $d_phone = $this->input->post('d_phone');
        $nhif_benefit = $this->input->post('nhif_benefit');
        $card_no = $this->input->post('card_no');


        $doctor = $this->input->post('doctor');
        $date = time();
        $date_string = date('d-m-y', $date);
        $discount = $this->input->post('discount');
        if (empty($discount)) {
            $discount = 0;
        }
        $amount_received = $this->input->post('amount_received');
        $deposit_type = $this->input->post('deposit_type');
        $user = $this->ion_auth->get_user_id();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

// Validating Category Field
// $this->form_validation->set_rules('category_amount[]', 'Category', 'min_length[1]|max_length[100]');
// Validating Price Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Price Field
        $this->form_validation->set_rules('discount', 'Discount', 'trim|min_length[1]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            redirect('finance/addPaymentView');
        } else {
            if (!empty($p_name)) {

                $limit = $this->patient_model->getLimit();
                if ($limit <= 0) {
                    $this->session->set_flashdata('feedback', lang('patient_limit_exceed'));
                    redirect('patient');
                }

                $data_p = array(
                    'patient_id' => $patient_id,
                    'name' => $p_name,
                    'email' => $p_email,
                    'phone' => $p_phone,
                    'sex' => $p_gender,
                    'age' => $p_age,
                    'add_date' => $add_date,
                    'how_added' => 'from_pos',
                    'nhif_benefit' => $nhif_benefit,
                    'card_no' => $card_no,
                );
                $username = $this->input->post('p_name');
// Adding New Patient
                if ($this->ion_auth->email_check($p_email)) {
                    $this->session->set_flashdata('feedback', 'This Email Address Is Already Registered');
                } else {
                    $dfg = 5;
                    $this->ion_auth->register($username, $password, $p_email, $dfg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $p_email))->row()->id;
                    $this->patient_model->insertPatient($data_p);
                    $patient_user_id = $this->db->get_where('patient', array('email' => $p_email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->patient_model->updatePatient($patient_user_id, $id_info);
                    $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $this->hospital_id);
                }
//    }
            }

            if (!empty($d_name)) {

                $limit = $this->doctor_model->getLimit();
                if ($limit <= 0) {
                    $this->session->set_flashdata('feedback', lang('doctor_limit_exceed'));
                    redirect('doctor');
                }

                $data_d = array(
                    'name' => $d_name,
                    'email' => $d_email,
                    'phone' => $d_phone,
                );
                $username = $this->input->post('d_name');
// Adding New Patient
                if ($this->ion_auth->email_check($d_email)) {
                    $this->session->set_flashdata('feedback', 'This Email Address Is Already Registered');
                } else {
                    $dfgg = 4;
                    $this->ion_auth->register($username, $password, $d_email, $dfgg);
                    $ion_user_id = $this->db->get_where('users', array('email' => $d_email))->row()->id;
                    $this->doctor_model->insertDoctor($data_d);
                    $doctor_user_id = $this->db->get_where('doctor', array('email' => $d_email))->row()->id;
                    $id_info = array('ion_user_id' => $ion_user_id);
                    $this->doctor_model->updateDoctor($doctor_user_id, $id_info);
                    $this->hospital_model->addHospitalIdToIonUser($ion_user_id, $this->hospital_id);
                }
            }


            if ($patient == 'add_new') {
                $patient = $patient_user_id;
            }

            if ($doctor == 'add_new') {
                $doctor = $doctor_user_id;
            }


            $amount = array_sum($amount_by_category);
            $sub_total = $amount;
            $discount_type = $this->finance_model->getDiscountType();
            if (!empty($doctor)) {
                $all_cat_name = explode(',', $category_name);
                foreach ($all_cat_name as $indiviual_cat_nam) {
                    $indiviual_cat_nam1 = explode('*', $indiviual_cat_nam);
                    $qty = $indiviual_cat_nam1[3];
                    $d_commission = $this->finance_model->getPaymentCategoryById($indiviual_cat_nam1[0])->d_commission;
                    $h_commission = 100 - $d_commission;
                    $hospital_amount_per_unit = $indiviual_cat_nam1[1] * $h_commission / 100;
                    $hospital_amount_by_category[] = $hospital_amount_per_unit * $qty;
                }
                $hospital_amount = array_sum($hospital_amount_by_category);
                if ($discount_type == 'flat') {
                    $flat_discount = $discount;
                    $gross_total = $sub_total - $flat_discount;
                    $doctor_amount = $amount - $hospital_amount - $flat_discount;
                } else {
                    $flat_discount = $sub_total * ($discount / 100);
                    $gross_total = $sub_total - $flat_discount;
                    $doctor_amount = $amount - $hospital_amount - $flat_discount;
                }
            } else {
                $doctor_amount = '0';
                if ($discount_type == 'flat') {
                    $flat_discount = $discount;
                    $gross_total = $sub_total - $flat_discount;
                    $hospital_amount = $gross_total;
                } else {
                    $flat_discount = $amount * ($discount / 100);
                    $gross_total = $sub_total - $flat_discount;
                    $hospital_amount = $gross_total;
                }
            }
            $data = array();

            if (!empty($patient)) {
                $patient_details = $this->patient_model->getPatientById($patient);
                $patient_name = $patient_details->name;
                $patient_phone = $patient_details->phone;
                $patient_address = $patient_details->address;
            } else {
                $patient_name = 0;
                $patient_phone = 0;
                $patient_address = 0;
            }

            if (!empty($doctor)) {
                $doctor_details = $this->doctor_model->getDoctorById($doctor);
                $doctor_name = $doctor_details->name;
            } else {
                $doctor_name = 0;
            }

            if (empty($id)) {
                $data = array(
                    'category_name' => $category_name,
                    'patient' => $patient,
                    'date' => $date,
                    'amount' => $sub_total,
                    'doctor' => $doctor,
                    'discount' => $discount,
                    'flat_discount' => $flat_discount,
                    'gross_total' => $gross_total,
                    'status' => 'unpaid',
                    'hospital_amount' => $hospital_amount,
                    'doctor_amount' => $doctor_amount,
                    'user' => $user,
                    'patient_name' => $patient_name,
                    'patient_phone' => $patient_phone,
                    'patient_address' => $patient_address,
                    'doctor_name' => $doctor_name,
                    'date_string' => $date_string,
                    'remarks' => $remarks
                );


                $this->finance_model->insertPayment($data);
                $inserted_id = $this->db->insert_id();

                if ($deposit_type == 'Card') {
                    $gateway = $this->settings_model->getSettings()->payment_gateway;
                    if ($gateway == 'PayPal') {

                        $card_type = $this->input->post('card_type');
                        $card_number = $this->input->post('card_number');
                        $expire_date = $this->input->post('expire_date');
                        $cvv = $this->input->post('cvv');

                        $all_details = array(
                            'patient' => $patient,
                            'date' => $date,
                            'amount' => $sub_total,
                            'doctor' => $doctor,
                            'discount' => $discount,
                            'flat_discount' => $flat_discount,
                            'gross_total' => $gross_total,
                            'status' => 'unpaid',
                            'hospital_amount' => $hospital_amount,
                            'doctor_amount' => $doctor_amount,
                            'patient_name' => $patient_name,
                            'patient_phone' => $patient_phone,
                            'patient_address' => $patient_address,
                            'doctor_name' => $doctor_name,
                            'date_string' => $date_string,
                            'remarks' => $remarks,
                            'deposited_amount' => $amount_received,
                            'payment_id' => $inserted_id,
                            'card_type' => $card_type,
                            'card_number' => $card_number,
                            'expire_date' => $expire_date,
                            'cvv' => $cvv,
                            'from' => 'pos',
                            'user' => $user
                        );
                        //    $data_payments['all_details'] = $all_details;
                        //    $this->load->view('home/dashboard'); // just the header file
                        //    $this->load->view('paypal/confirmation', $data_payments);
                        //    $this->load->view('home/footer'); // just the header file
                        $this->paypal->Do_direct_payment($all_details);
                    } elseif ($gateway == 'Pay U Money') {
                        redirect("payu/check1?deposited_amount=" . "$amount_received" . '&payment_id=' . $inserted_id);
                    } else {
                        $this->session->set_flashdata('feedback', 'Payment failed. No Gateway Selected');
                        redirect("finance/invoice?id=" . "$inserted_id");
                    }
                } else {
                    $data1 = array(
                        'date' => $date,
                        'patient' => $patient,
                        'deposited_amount' => $amount_received,
                        'payment_id' => $inserted_id,
                        'amount_received_id' => $inserted_id . '.' . 'gp',
                        'deposit_type' => $deposit_type,
                        'user' => $user
                    );
                    $this->finance_model->insertDeposit($data1);

                    $data_payment = array('amount_received' => $amount_received, 'deposit_type' => $deposit_type);
                    $this->finance_model->updatePayment($inserted_id, $data_payment);

                    $this->session->set_flashdata('feedback', 'Added');
                    redirect("finance/invoice?id=" . "$inserted_id");
                }
            } else {
                $deposit_edit_amount = $this->input->post('deposit_edit_amount');
                $deposit_edit_id = $this->input->post('deposit_edit_id');
                if (!empty($deposit_edit_amount)) {
                    $deposited_edit = array_combine($deposit_edit_id, $deposit_edit_amount);
                    foreach ($deposited_edit as $key_deposit => $value_deposit) {
                        $data_deposit = array(
                            'deposited_amount' => $value_deposit
                        );
                        $this->finance_model->updateDeposit($key_deposit, $data_deposit);
                    }
                }


                $a_r_i = $id . '.' . 'gp';
                $deposit_id = $this->db->get_where('patient_deposit', array('amount_received_id' => $a_r_i))->row();

                $data = array(
                    'category_name' => $category_name,
                    'patient' => $patient,
                    'doctor' => $doctor,
                    'amount' => $sub_total,
                    'discount' => $discount,
                    'flat_discount' => $flat_discount,
                    'gross_total' => $gross_total,
                    'amount_received' => $amount_received,
                    'hospital_amount' => $hospital_amount,
                    'doctor_amount' => $doctor_amount,
                    'user' => $user,
                    'patient_name' => $patient_details->name,
                    'patient_phone' => $patient_details->phone,
                    'patient_address' => $patient_details->address,
                    'doctor_name' => $doctor_details->name,
                    'remarks' => $remarks
                );

                if (!empty($deposit_id->id)) {
                    $data1 = array(
                        'date' => $date,
                        'patient' => $patient,
                        'payment_id' => $id,
                        'deposited_amount' => $amount_received,
                        'user' => $user
                    );
                    $this->finance_model->updateDeposit($deposit_id->id, $data1);
                } else {
                    $data1 = array(
                        'date' => $date,
                        'patient' => $patient,
                        'payment_id' => $id,
                        'deposited_amount' => $amount_received,
                        'amount_received_id' => $id . '.' . 'gp',
                        'user' => $user
                    );
                    $this->finance_model->insertDeposit($data1);
                }
                $this->finance_model->updatePayment($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
                redirect("finance/invoice?id=" . "$id");
            }
        }
    }

    function editPayment() {
        if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
            $data = array();
            $data['discount_type'] = $this->finance_model->getDiscountType();
            $data['settings'] = $this->settings_model->getSettings();
            $data['categories'] = $this->finance_model->getPaymentCategory();
            $data['patients'] = $this->patient_model->getPatient();
            $data['doctors'] = $this->doctor_model->getDoctor();
            $id = $this->input->get('id');
            $data['payment'] = $this->finance_model->getPaymentById($id);
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('add_payment_view', $data);
            $this->load->view('home/footer'); // just the footer file
        }
    }

    function delete() {
        if ($this->ion_auth->in_group(array('admin', 'Accountant', 'Receptionist'))) {
            $id = $this->input->get('id');
            $this->finance_model->deletePayment($id);
            $this->finance_model->deleteDepositByInvoiceId($id);
            $this->session->set_flashdata('feedback', 'Deleted');
            redirect('finance/payment');
        }
    }

    public function otPayment() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['ot_payments'] = $this->finance_model->getOtPayment();

        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('ot_payment', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addOtPaymentView() {
        $data = array();
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->finance_model->getPaymentCategory();
        $data['patients'] = $this->patient_model->getPatient();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_ot_payment', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addOtPayment() {
        $id = $this->input->post('id');
        $patient = $this->input->post('patient');
        $doctor_c_s = $this->input->post('doctor_c_s');
        $doctor_a_s_1 = $this->input->post('doctor_a_s_1');
        $doctor_a_s_2 = $this->input->post('doctor_a_s_2');
        $doctor_anaes = $this->input->post('doctor_anaes');
        $n_o_o = $this->input->post('n_o_o');

        $c_s_f = $this->input->post('c_s_f');
        $a_s_f_1 = $this->input->post('a_s_f_1');
        $a_s_f_2 = $this->input->post('a_s_f_2');
        $anaes_f = $this->input->post('anaes_f');
        $ot_charge = $this->input->post('ot_charge');
        $cab_rent = $this->input->post('cab_rent');
        $seat_rent = $this->input->post('seat_rent');
        $others = $this->input->post('others');

        $discount = $this->input->post('discount');
        $vat = $this->input->post('vat');
        $amount_received = $this->input->post('amount_received');

        $date = time();
        $user = $this->ion_auth->get_user_id();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

// Validating Patient Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|required|min_length[2]|max_length[100]|xss_clean');
// Validating Consultant surgeon Field
        $this->form_validation->set_rules('doctor_c_s', 'Consultant surgeon', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Assistant Surgeon Field
        $this->form_validation->set_rules('doctor_a_s_1', 'Assistant Surgeon (1)', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Assistant Surgeon Field
        $this->form_validation->set_rules('doctor_a_s_2', 'Assistant Surgeon(2)', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Anaesthisist Field
        $this->form_validation->set_rules('doctor_anaes', 'Anaesthisist', 'trim|min_length[2]|max_length[100]|xss_clean');
// Validating Nature Of Operation Field
        $this->form_validation->set_rules('n_o_o', 'Nature Of Operation', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Consultant Surgeon Fee Field
        $this->form_validation->set_rules('c_s_f', 'Consultant Surgeon Fee', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Assistant surgeon fee Field
        $this->form_validation->set_rules('a_s_f_1', 'Assistant surgeon fee', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Assistant surgeon fee Field
        $this->form_validation->set_rules('a_s_f_2', 'Assistant surgeon fee', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Anaesthesist Field
        $this->form_validation->set_rules('anaes_f', 'Anaesthesist', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating OT Charge Field
        $this->form_validation->set_rules('ot_charge', 'OT Charge', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Cabin Rent Field
        $this->form_validation->set_rules('cab_rent', 'Cabin Rent', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Seat Rent Field
        $this->form_validation->set_rules('seat_rent', 'Seat Rent', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Others Field
        $this->form_validation->set_rules('others', 'Others', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Discount Field
        $this->form_validation->set_rules('discount', 'Discount', 'trim|min_length[1]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            echo 'form validate noe nai re';
// redirect('accountant/add_new'); 
        } else {
            $doctor_fees = $c_s_f + $a_s_f_1 + $a_s_f_2 + $anaes_f;
            $hospital_fees = $ot_charge + $cab_rent + $seat_rent + $others;
            $amount = $doctor_fees + $hospital_fees;
            $discount_type = $this->finance_model->getDiscountType();

            if ($discount_type == 'flat') {
                $amount_with_discount = $amount - $discount;
                $gross_total = $amount_with_discount + $amount_with_discount * ($vat / 100);
                $flat_discount = $discount;
                $flat_vat = $amount_with_discount * ($vat / 100);
                $hospital_fees = $hospital_fees - $flat_discount;
            } else {
                $flat_discount = $amount * ($discount / 100);
                $amount_with_discount = $amount - $amount * ($discount / 100);
                $gross_total = $amount_with_discount + $amount_with_discount * ($vat / 100);
                $discount = $discount . '*' . $amount * ($discount / 100);
                $flat_vat = $amount_with_discount * ($vat / 100);
                $hospital_fees = $hospital_fees - $flat_discount;
            }

            $data = array();


            if (empty($id)) {
                $data = array(
                    'patient' => $patient,
                    'doctor_c_s' => $doctor_c_s,
                    'doctor_a_s_1' => $doctor_a_s_1,
                    'doctor_a_s_2' => $doctor_a_s_2,
                    'doctor_anaes' => $doctor_anaes,
                    'n_o_o' => $n_o_o,
                    'c_s_f' => $c_s_f,
                    'a_s_f_1' => $a_s_f_1,
                    'a_s_f_2' => $a_s_f_2,
                    'anaes_f' => $anaes_f,
                    'ot_charge' => $ot_charge,
                    'cab_rent' => $cab_rent,
                    'seat_rent' => $seat_rent,
                    'others' => $others,
                    'discount' => $discount,
                    'date' => $date,
                    'amount' => $amount,
                    'doctor_fees' => $doctor_fees,
                    'hospital_fees' => $hospital_fees,
                    'gross_total' => $gross_total,
                    'flat_discount' => $flat_discount,
                    'amount_received' => $amount_received,
                    'status' => 'unpaid',
                    'user' => $user
                );
                $this->finance_model->insertOtPayment($data);
                $inserted_id = $this->db->insert_id();
                $data1 = array(
                    'date' => $date,
                    'patient' => $patient,
                    'deposited_amount' => $amount_received,
                    'amount_received_id' => $inserted_id . '.' . 'ot',
                    'user' => $user
                );
                $this->finance_model->insertDeposit($data1);

                $this->session->set_flashdata('feedback', 'Added');
                redirect("finance/otInvoice?id=" . "$inserted_id");
            } else {
                $a_r_i = $id . '.' . 'ot';
                $deposit_id = $this->db->get_where('patient_deposit', array('amount_received_id' => $a_r_i))->row()->id;
                $data = array(
                    'patient' => $patient,
                    'doctor_c_s' => $doctor_c_s,
                    'doctor_a_s_1' => $doctor_a_s_1,
                    'doctor_a_s_2' => $doctor_a_s_2,
                    'doctor_anaes' => $doctor_anaes,
                    'n_o_o' => $n_o_o,
                    'c_s_f' => $c_s_f,
                    'a_s_f_1' => $a_s_f_1,
                    'a_s_f_2' => $a_s_f_2,
                    'anaes_f' => $anaes_f,
                    'ot_charge' => $ot_charge,
                    'cab_rent' => $cab_rent,
                    'seat_rent' => $seat_rent,
                    'others' => $others,
                    'discount' => $discount,
                    'amount' => $amount,
                    'doctor_fees' => $doctor_fees,
                    'hospital_fees' => $hospital_fees,
                    'gross_total' => $gross_total,
                    'flat_discount' => $flat_discount,
                    'amount_received' => $amount_received,
                    'user' => $user
                );
                $data1 = array(
                    'date' => $date,
                    'patient' => $patient,
                    'deposited_amount' => $amount_received,
                    'user' => $user
                );
                $this->finance_model->updateDeposit($deposit_id, $data1);
                $this->finance_model->updateOtPayment($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
                redirect("finance/otInvoice?id=" . "$id");
            }
        }
    }

    function editOtPayment() {
        if ($this->ion_auth->in_group(array('admin', 'Accountant'))) {
            $data = array();
            $data['discount_type'] = $this->finance_model->getDiscountType();
            $data['settings'] = $this->settings_model->getSettings();
            $data['patients'] = $this->patient_model->getPatient();
            $id = $this->input->get('id');
            $data['ot_payment'] = $this->finance_model->getOtPaymentById($id);
            $data['doctors'] = $this->doctor_model->getDoctor();
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('add_ot_payment', $data);
            $this->load->view('home/footer'); // just the footer file
        }
    }

    function otInvoice() {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['ot_payment'] = $this->finance_model->getOtPaymentById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('ot_invoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function otPaymentDetails() {
        $id = $this->input->get('id');
        $patient = $this->input->get('patient');
        $data['patient'] = $this->patient_model->getPatientByid($patient);
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['ot_payment'] = $this->finance_model->getOtPaymentById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('ot_payment_details', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function otPaymentDelete() {
        if ($this->ion_auth->in_group(array('admin', 'Accountant'))) {
            $id = $this->input->get('id');
            $this->finance_model->deleteOtPayment($id);
            $this->session->set_flashdata('feedback', 'Deleted');
            redirect('finance/otPayment');
        }
    }

    function addPaymentByPatient() {
        $data = array();
        $id = $this->input->get('id');
        $data['patient'] = $this->patient_model->getPatientById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('choose_payment_type', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function addPaymentByPatientView() {
        $id = $this->input->get('id');
        $type = $this->input->get('type');
        $data = array();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->finance_model->getPaymentCategory();
        $data['doctors'] = $this->doctor_model->getDoctor();

        $data['patient'] = $this->patient_model->getPatientById($id);
        if ($type == 'gen') {
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('add_payment_view_single', $data);
            $this->load->view('home/footer'); // just the footer fi
        } else {
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('add_ot_payment_view_single', $data);
            $this->load->view('home/footer'); // just the footer fi
        }
    }

    public function paymentCategory() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['categories'] = $this->finance_model->getPaymentCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('payment_category', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addPaymentCategoryView() {
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_payment_category');
        $this->load->view('home/footer'); // just the header file
    }

    public function addPaymentCategory() {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $type = $this->input->post('type');
        $description = $this->input->post('description');
        $c_price = $this->input->post('c_price');
        $d_commission = $this->input->post('d_commission');
        if (empty($c_price)) {
            $c_price = 0;
        }


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
// Validating Category Name Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
// Validating Description Field
        $this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[1]|max_length[100]|xss_clean');
// Validating Description Field
        $this->form_validation->set_rules('c_price', 'Category price', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Doctor Commission Rate Field
        $this->form_validation->set_rules('d_commission', 'Doctor Commission rate', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Description Field
        $this->form_validation->set_rules('type', 'Type', 'trim|min_length[1]|max_length[100]|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                $this->session->set_flashdata('feedback', 'Validation Error !');
                redirect('finance/editPaymentCategory?id=' . $id);
            } else {
                $data = array();
                $data['setval'] = 'setval';
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('add_payment_category', $data);
                $this->load->view('home/footer'); // just the header file
            }
        } else {
            $data = array();
            $data = array('category' => $category,
                'description' => $description,
                'type' => $type,
                'c_price' => $c_price,
                'd_commission' => $d_commission
            );
            if (empty($id)) {
                $this->finance_model->insertPaymentCategory($data);
                $this->session->set_flashdata('feedback', 'Added');
            } else {
                $this->finance_model->updatePaymentCategory($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
            }
            redirect('finance/paymentCategory');
        }
    }

    function editPaymentCategory() {
        $data = array();
        $id = $this->input->get('id');
        $data['category'] = $this->finance_model->getPaymentCategoryById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_payment_category', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function deletePaymentCategory() {
        $id = $this->input->get('id');
        $this->finance_model->deletePaymentCategory($id);
        redirect('finance/paymentCategory');
    }

    public function expense() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['expenses'] = $this->finance_model->getExpense();

        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('expense', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addExpenseView() {
        $data = array();
        $data['settings'] = $this->settings_model->getSettings();
        $data['categories'] = $this->finance_model->getExpenseCategory();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_expense_view', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addExpense() {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $date = time();
        $amount = $this->input->post('amount');
        $user = $this->ion_auth->get_user_id();
        $note = $this->input->post('note');


        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

// Validating Category Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
// Validating Generic Name Field
        $this->form_validation->set_rules('amount', 'Amount', 'trim|required|min_length[1]|max_length[100]|xss_clean');
// Validating Note Field
        $this->form_validation->set_rules('note', 'Note', 'trim|min_length[1]|max_length[100]|xss_clean');


        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                $this->session->set_flashdata('feedback', 'Validation Error !');
                redirect('finance/editExpense?id=' . $id);
            } else {
                $data = array();
                $data['setval'] = 'setval';
                $data['settings'] = $this->settings_model->getSettings();
                $data['categories'] = $this->finance_model->getExpenseCategory();
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('add_expense_view', $data);
                $this->load->view('home/footer'); // just the header file
            }
        } else {
            $data = array();
            if (empty($id)) {
                $data = array(
                    'category' => $category,
                    'date' => $date,
                    'amount' => $amount,
                    'note' => $note,
                    'user' => $user
                );
            } else {
                $data = array(
                    'category' => $category,
                    'amount' => $amount,
                    'note' => $note,
                    'user' => $user,
                );
            }
            if (empty($id)) {
                $this->finance_model->insertExpense($data);
                $this->session->set_flashdata('feedback', 'Added');
            } else {
                $this->finance_model->updateExpense($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
            }
            redirect('finance/expense');
        }
    }

    function editExpense() {
        $data = array();
        $data['categories'] = $this->finance_model->getExpenseCategory();
        $data['settings'] = $this->settings_model->getSettings();
        $id = $this->input->get('id');
        $data['expense'] = $this->finance_model->getExpenseById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_expense_view', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function deleteExpense() {
        $id = $this->input->get('id');
        $this->finance_model->deleteExpense($id);
        redirect('finance/expense');
    }

    public function expenseCategory() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $data['categories'] = $this->finance_model->getExpenseCategory();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('expense_category', $data);
        $this->load->view('home/footer'); // just the header file
    }

    public function addExpenseCategoryView() {
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_expense_category');
        $this->load->view('home/footer'); // just the header file
    }

    public function addExpenseCategory() {
        $id = $this->input->post('id');
        $category = $this->input->post('category');
        $description = $this->input->post('description');

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
// Validating Category Name Field
        $this->form_validation->set_rules('category', 'Category', 'trim|required|min_length[1]|max_length[100]|xss_clean');
// Validating Description Field
        $this->form_validation->set_rules('description', 'Description', 'trim|required|min_length[1]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            if (!empty($id)) {
                $this->session->set_flashdata('feedback', 'Validation Error !');
                redirect('finance/editExpenseCategory?id=' . $id);
            } else {
                $data = array();
                $data['setval'] = 'setval';
                $this->load->view('home/dashboard'); // just the header file
                $this->load->view('add_expense_category', $data);
                $this->load->view('home/footer'); // just the header file
            }
        } else {
            $data = array();
            $data = array('category' => $category,
                'description' => $description
            );
            if (empty($id)) {
                $this->finance_model->insertExpenseCategory($data);
                $this->session->set_flashdata('feedback', 'Added');
            } else {
                $this->finance_model->updateExpenseCategory($id, $data);
                $this->session->set_flashdata('feedback', 'Updated');
            }
            redirect('finance/expenseCategory');
        }
    }

    function editExpenseCategory() {
        $data = array();
        $id = $this->input->get('id');
        $data['category'] = $this->finance_model->getExpenseCategoryById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('add_expense_category', $data);
        $this->load->view('home/footer'); // just the footer file
    }

    function deleteExpenseCategory() {
        $id = $this->input->get('id');
        $this->finance_model->deleteExpenseCategory($id);
        redirect('finance/expenseCategory');
    }

    function invoice() {
        $id = $this->input->get('id');
        $data['payment'] = $this->finance_model->getPaymentById($id);
        $patient_hospital_id = $this->patient_model->getPatientById( $data['payment']->patient)->hospital_id;
        if ($patient_hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('invoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function expenseInvoice() {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['expense'] = $this->finance_model->getExpenseById($id);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('expense_invoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function amountReceived() {
        $id = $this->input->post('id');
        $amount_received = $this->input->post('amount_received');
        $previous_amount_received = $this->db->get_where('payment', array('id' => $id))->row()->amount_received;
        $amount_received = $amount_received + $previous_amount_received;
        $data = array();
        $data = array('amount_received' => $amount_received);
        $this->finance_model->amountReceived($id, $data);
        redirect('finance/invoice?id=' . $id);
    }

    function otAmountReceived() {
        $id = $this->input->post('id');
        $amount_received = $this->input->post('amount_received');
        $previous_amount_received = $this->db->get_where('ot_payment', array('id' => $id))->row()->amount_received;
        $amount_received = $amount_received + $previous_amount_received;
        $data = array();
        $data = array('amount_received' => $amount_received);
        $this->finance_model->otAmountReceived($id, $data);
        redirect('finance/oTinvoice?id=' . $id);
    }

    function patientPaymentHistory() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $patient = $this->input->get('patient');
        if (empty($patient)) {
            $patient = $this->input->post('patient');
        }

        $patient_hospital_id = $this->patient_model->getPatientById($patient)->hospital_id;
        if ($patient_hospital_id != $this->session->userdata('hospital_id')) {
            redirect('home/permission');
        }

        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        if (!empty($date_from)) {
            $data['payments'] = $this->finance_model->getPaymentByPatientIdByDate($patient, $date_from, $date_to);
            $data['deposits'] = $this->finance_model->getDepositByPatientIdByDate($patient, $date_from, $date_to);
        } else {
            $data['payments'] = $this->finance_model->getPaymentByPatientId($patient);
            $data['pharmacy_payments'] = $this->pharmacy_model->getPaymentByPatientId($patient);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByPatientId($patient);
            $data['deposits'] = $this->finance_model->getDepositByPatientId($patient);
        }



        $data['patient'] = $this->patient_model->getPatientByid($patient);
        $data['settings'] = $this->settings_model->getSettings();



        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('patient_deposit', $data);
        $this->load->view('home/footer'); // just the header file
    }

    function deposit() {
        $id = $this->input->post('id');
        $patient = $this->input->post('patient');
        $payment_id = $this->input->post('payment_id');
        $date = time();

        $deposited_amount = $this->input->post('deposited_amount');

        $deposit_type = $this->input->post('deposit_type');

        if (empty($deposit_type)) {
            $deposit_type = 'Cash';
        }

        $user = $this->ion_auth->get_user_id();

        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
// Validating Patient Name Field
        $this->form_validation->set_rules('patient', 'Patient', 'trim|min_length[1]|max_length[100]|xss_clean');
// Validating Deposited Amount Field
        $this->form_validation->set_rules('deposited_amount', 'Deposited Amount', 'trim|min_length[1]|max_length[100]|xss_clean');
        if ($this->form_validation->run() == FALSE) {
            redirect('finance/patientPaymentHistory?patient=' . $patient);
        } else {
            $data = array();
            $data = array('patient' => $patient,
                'date' => $date,
                'payment_id' => $payment_id,
                'deposited_amount' => $deposited_amount,
                'deposit_type' => $deposit_type,
                'user' => $user
            );
            if (empty($id)) {
                if ($deposit_type == 'Card') {
                    $payment_details = $this->finance_model->getPaymentById($payment_id);
                    $gateway = $this->settings_model->getSettings()->payment_gateway;
                    if ($gateway == 'PayPal') {
                        $card_type = $this->input->post('card_type');
                        $card_number = $this->input->post('card_number');
                        $expire_date = $this->input->post('expire_date');
                        $cvv = $this->input->post('cvv');

                        $all_details = array(
                            'patient' => $payment_details->patient,
                            'date' => $payment_details->date,
                            'amount' => $payment_details->amount,
                            'doctor' => $payment_details->doctor_name,
                            'discount' => $payment_details->discount,
                            'flat_discount' => $payment_details->flat_discount,
                            'gross_total' => $payment_details->gross_total,
                            'status' => 'unpaid',
                            'patient_name' => $payment_details->patient_name,
                            'patient_phone' => $payment_details->patient_phone,
                            'patient_address' => $payment_details->patient_address,
                            'deposited_amount' => $deposited_amount,
                            'payment_id' => $payment_details->id,
                            'card_type' => $card_type,
                            'card_number' => $card_number,
                            'expire_date' => $expire_date,
                            'cvv' => $cvv,
                            'from' => 'patient_payment_details',
                            'user' => $user
                        );
                        $this->paypal->Do_direct_payment($all_details);
                    } elseif ($gateway == 'Pay U Money') {
                        redirect("payu/check?deposited_amount=" . "$deposited_amount" . '&payment_id=' . $payment_id);
                    } else {
                        $this->session->set_flashdata('feedback', 'Payment failed. No Gateway Selected');
                        redirect("finance/invoice?id=" . "$payment_id");
                    }
                } else {
                    $this->finance_model->insertDeposit($data);
                    $this->session->set_flashdata('feedback', 'Added');
                }
            } else {
                $this->finance_model->updateDeposit($id, $data);

                $amount_received_id = $this->finance_model->getDepositById($id)->amount_received_id;
                if (!empty($amount_received_id)) {
                    $amount_received_payment_id = explode('.', $amount_received_id);
                    $payment_id = $amount_received_payment_id[0];
                    $data_amount_received = array('amount_received' => $deposited_amount);
                    $this->finance_model->updatePayment($amount_received_payment_id[0], $data_amount_received);
                }

                $this->session->set_flashdata('feedback', 'Updated');
            }
            redirect('finance/patientPaymentHistory?patient=' . $patient);
        }
    }

    function editDepositByJason() {
        $id = $this->input->get('id');
        $data['deposit'] = $this->finance_model->getDepositById($id);
        echo json_encode($data);
    }

    function deleteDeposit() {
        $id = $this->input->get('id');
        $patient = $this->input->get('patient');

        $amount_received_id = $this->finance_model->getDepositById($id)->amount_received_id;
        if (!empty($amount_received_id)) {
            $amount_received_payment_id = explode('.', $amount_received_id);
            $payment_id = $amount_received_payment_id[0];
            $data_amount_received = array('amount_received' => NULL);
            $this->finance_model->updatePayment($amount_received_payment_id[0], $data_amount_received);
        }

        $this->finance_model->deleteDeposit($id);

        redirect('finance/patientPaymentHistory?patient=' . $patient);
    }

    function invoicePatientTotal() {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['payments'] = $this->finance_model->getPaymentByPatientIdByStatus($id);
        $data['ot_payments'] = $this->finance_model->getOtPaymentByPatientIdByStatus($id);
        $data['patient_id'] = $id;
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('invoicePT', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function lastPaidInvoice() {
        $id = $this->input->get('id');
        $data['settings'] = $this->settings_model->getSettings();
        $data['discount_type'] = $this->finance_model->getDiscountType();
        $data['payments'] = $this->finance_model->lastPaidInvoice($id);
        $data['ot_payments'] = $this->finance_model->lastOtPaidInvoice($id);
        $data['patient_id'] = $id;
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('LPInvoice', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function makePaid() {
        $id = $this->input->get('id');
        $patient_id = $this->finance_model->getPaymentById($id)->patient;
        $data = array();
        $data = array('status' => 'paid');
        $data1 = array();
        $data1 = array('status' => 'paid-last');
        $this->finance_model->makeStatusPaid($id, $patient_id, $data, $data1);
        $this->session->set_flashdata('feedback', 'Paid');
        redirect('finance/invoice?id=' . $id);
    }

    function makePaidByPatientIdByStatus() {
        $id = $this->input->get('id');
        $data = array();
        $data = array('status' => 'paid-last');
        $data1 = array();
        $data1 = array('status' => 'paid');
        $this->finance_model->makePaidByPatientIdByStatus($id, $data, $data1);
        $this->session->set_flashdata('feedback', 'Paid');
        redirect('patient');
    }

    function makeOtStatusPaid() {
        $id = $this->input->get('id');
        $this->finance_model->makeOtStatusPaid($id);
        $this->session->set_flashdata('feedback', 'Paid');
        redirect("finance/otInvoice?id=" . "$id");
    }

    function doctorsCommission() {
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }
        $data['doctors'] = $this->doctor_model->getDoctor();
        $data['payments'] = $this->finance_model->getPaymentByDate($date_from, $date_to);
        $data['ot_payments'] = $this->finance_model->getOtPaymentByDate($date_from, $date_to);
        $data['settings'] = $this->settings_model->getSettings();
        $data['from'] = $this->input->post('date_from');
        $data['to'] = $this->input->post('date_to');
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('doctors_commission', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function docComDetails() {
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }
        $doctor = $this->input->get('id');
        if (empty($doctor)) {
            $doctor = $this->input->post('doctor');
        }
        $data['doctor'] = $doctor;
        if (!empty($date_from)) {
            $data['payments'] = $this->finance_model->getPaymentByDoctorDate($doctor, $date_from, $date_to);
        } else {
            $data['payments'] = $this->finance_model->getPaymentByDoctor($doctor);
        }
        $data['settings'] = $this->settings_model->getSettings();
        $data['from'] = $this->input->post('date_from');
        $data['to'] = $this->input->post('date_to');
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('doc_com_view', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function financialReport() {
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }
        $data = array();
        $data['payment_categories'] = $this->finance_model->getPaymentCategory();
        $data['expense_categories'] = $this->finance_model->getExpenseCategory();


// if(empty($date_from)&&empty($date_to)) {
//    $data['payments']=$this->finance_model->get_payment();
//     $data['ot_payments']=$this->finance_model->get_ot_payment();
//     $data['expenses']=$this->finance_model->get_expense();
// }
// else{

        $data['payments'] = $this->finance_model->getPaymentByDate($date_from, $date_to);
        $data['ot_payments'] = $this->finance_model->getOtPaymentByDate($date_from, $date_to);
        $data['deposits'] = $this->finance_model->getDepositsByDate($date_from, $date_to);
        $data['expenses'] = $this->finance_model->getExpenseByDate($date_from, $date_to);
// } 
        $data['from'] = $this->input->post('date_from');
        $data['to'] = $this->input->post('date_to');
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('financial_report', $data);
        $this->load->view('home/footer'); // just the footer fi
    }

    function UserActivityReport() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        if ($this->ion_auth->in_group(array('Accountant'))) {
            $user = $this->ion_auth->get_user_id();
            $data['user'] = $this->accountant_model->getAccountantByIonUserId($user);
        }
        if ($this->ion_auth->in_group(array('Receptionist'))) {
            $user = $this->ion_auth->get_user_id();
            $data['user'] = $this->receptionist_model->getReceptionistByIonUserId($user);
        }
        $hour = 0;
        $TODAY_ON = $this->input->get('today');
        $YESTERDAY_ON = $this->input->get('yesterday');
        $ALL = $this->input->get('all');

        $today = strtotime($hour . ':00:00');
        $today_last = strtotime($hour . ':00:00') + 86399;
        $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $today, $today_last);
        $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $today, $today_last);
        $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $today, $today_last);
        $data['day'] = 'Today';
        if (!empty($YESTERDAY_ON)) {
            $today = strtotime($hour . ':00:00');
            $yesterday = strtotime('-1 day', $today);
            $data['day'] = 'Yesterday';
            $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $yesterday, $today);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $yesterday, $today);
            $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $yesterday, $today);
        }
        if (!empty($ALL)) {
            $data['day'] = 'All';
            $data['payments'] = $this->finance_model->getPaymentByUserId($user);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByUserId($user);
            $data['deposits'] = $this->finance_model->getDepositByUserId($user);
        }
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('user_activity_report', $data);
        $this->load->view('home/footer'); // just the header file
    }

    function UserActivityReportDateWise() {
        $data = array();
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        if ($this->ion_auth->in_group(array('Accountant'))) {
            $user = $this->input->post('user');
            $data['user'] = $this->accountant_model->getAccountantByIonUserId($user);
        }
        if ($this->ion_auth->in_group(array('Receptionist'))) {
            $user = $this->input->post('user');
            $data['user'] = $this->receptionist_model->getReceptionistByIonUserId($user);
        }
        $date_from = strtotime($this->input->post('date_from'));
        $date_to = strtotime($this->input->post('date_to'));
        if (!empty($date_to)) {
            $date_to = $date_to + 86399;
        }

        $data['date_from'] = $date_from;
        $data['date_to'] = $date_to;

        $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $date_from, $date_to);
        $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $date_from, $date_to);
        $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $date_from, $date_to);
        $data['settings'] = $this->settings_model->getSettings();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('user_activity_report', $data);
        $this->load->view('home/footer'); // just the header file
    }

    function AllUserActivityReport() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $user = $this->input->get('user');

        if (!empty($user)) {
            $user_group = $this->db->get_where('users_groups', array('user_id' => $user))->row()->group_id;
            if ($user_group == '3') {
                $data['user'] = $this->accountant_model->getAccountantByIonUserId($user);
            }
            if ($user_group == '10') {
                $data['user'] = $this->receptionist_model->getReceptionistByIonUserId($user);
            }
            $data['settings'] = $this->settings_model->getSettings();
            $hour = 0;
            $TODAY_ON = $this->input->get('today');
            $YESTERDAY_ON = $this->input->get('yesterday');
            $ALL = $this->input->get('all');

            $today = strtotime($hour . ':00:00');
            $today_last = strtotime($hour . ':00:00') + 86399;
            $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $today, $today_last);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $today, $today_last);
            $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $today, $today_last);
            $data['day'] = 'Today';

            if (!empty($YESTERDAY_ON)) {
                $today = strtotime($hour . ':00:00');
                $yesterday = strtotime('-1 day', $today);
                $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $yesterday, $today);
                $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $yesterday, $today);
                $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $yesterday, $today);
                $data['day'] = 'Yesterday';
            }

            if (!empty($ALL)) {
                $data['payments'] = $this->finance_model->getPaymentByUserId($user);
                $data['ot_payments'] = $this->finance_model->getOtPaymentByUserId($user);
                $data['deposits'] = $this->finance_model->getDepositByUserId($user);
                $data['day'] = 'All';
            }


            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('user_activity_report', $data);
            $this->load->view('home/footer'); // just the header file
        }

        if (empty($user)) {
            $hour = 0;
            $today = strtotime($hour . ':00:00');
            $today_last = strtotime($hour . ':00:00') + 86399;
            $data['accountants'] = $this->accountant_model->getAccountant();
            $data['receptionists'] = $this->receptionist_model->getReceptionist();
            $data['settings'] = $this->settings_model->getSettings();
            $data['payments'] = $this->finance_model->getPaymentByDate($today, $today_last);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByDate($today, $today_last);
            $data['deposits'] = $this->finance_model->getDepositsByDate($today, $today_last);
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('all_user_activity_report', $data);
            $this->load->view('home/footer'); // just the header file
        }
    }

    function AllUserActivityReportDateWise() {
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login', 'refresh');
        }
        $user = $this->input->post('user');

        if (!empty($user)) {
            $user_group = $this->db->get_where('users_groups', array('user_id' => $user))->row()->group_id;
            if ($user_group == '3') {
                $data['user'] = $this->accountant_model->getAccountantByIonUserId($user);
            }
            if ($user_group == '10') {
                $data['user'] = $this->receptionist_model->getReceptionistByIonUserId($user);
            }
            $date_from = strtotime($this->input->post('date_from'));
            $date_to = strtotime($this->input->post('date_to'));
            if (!empty($date_to)) {
                $date_to = $date_to + 86399;
            }

            $data['settings'] = $this->settings_model->getSettings();
            $data['date_from'] = $date_from;
            $data['date_to'] = $date_to;
            $data['payments'] = $this->finance_model->getPaymentByUserIdByDate($user, $date_from, $date_to);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByUserIdByDate($user, $date_from, $date_to);
            $data['deposits'] = $this->finance_model->getDepositByUserIdByDate($user, $date_from, $date_to);



            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('user_activity_report', $data);
            $this->load->view('home/footer'); // just the header file
        }

        if (empty($user)) {
            $hour = 0;
            $today = strtotime($hour . ':00:00');
            $today_last = strtotime($hour . ':00:00') + 86399;
            $data['accountants'] = $this->accountant_model->getAccountant();
            $data['receptionists'] = $this->receptionist_model->getReceptionist();
            $data['settings'] = $this->settings_model->getSettings();
            $data['payments'] = $this->finance_model->getPaymentByDate($today, $today_last);
            $data['ot_payments'] = $this->finance_model->getOtPaymentByDate($today, $today_last);
            $data['deposits'] = $this->finance_model->getDepositsByDate($today, $today_last);
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('all_user_activity_report', $data);
            $this->load->view('home/footer'); // just the header file
        }
    }

    function getPayment() {
        $requestData = $_REQUEST;
        $start = $requestData['start'];
        $limit = $requestData['length'];
        $search = $this->input->post('search')['value'];
        $settings = $this->settings_model->getSettings();

        if ($limit == -1) {
            if (!empty($search)) {
                $data['payments'] = $this->finance_model->getPaymentBysearch($search);
            } else {
                $data['payments'] = $this->finance_model->getPayment();
            }
        } else {
            if (!empty($search)) {
                $data['payments'] = $this->finance_model->getPaymentByLimitBySearch($limit, $start, $search);
            } else {
                $data['payments'] = $this->finance_model->getPaymentByLimit($limit, $start);
            }
        }
        //  $data['payments'] = $this->finance_model->getPayment();

        foreach ($data['payments'] as $payment) {
            $date = date('d-m-y', $payment->date);

            $discount = $payment->discount;
            if (empty($discount)) {
                $discount = 0;
            }

            if ($this->ion_auth->in_group(array('admin', 'Accountant'))) {
                $options1 = ' <a class="btn btn-info btn-xs editbutton" title="' . lang('edit') . '" href="finance/editPayment?id=' . $payment->id . '"><i class="fa fa-edit"> </i> ' . lang('edit') . '</a>';
            }

            $options2 = '<a class="btn btn-info btn-xs invoicebutton" title="' . lang('invoice') . '" style="color: #fff;" href="finance/invoice?id=' . $payment->id . '"><i class="fa fa-file-text"></i> ' . lang('invoice') . '</a>';

            if ($this->ion_auth->in_group(array('admin', 'Accountant'))) {
                $options3 = '<a class="btn btn-info btn-xs delete_button" title="' . lang('delete') . '" href="finance/delete?id=' . $payment->id . '" onclick="return confirm(\'Are you sure you want to delete this item?\');"><i class="fa fa-trash-o"></i> ' . lang('delete') . '</a>';
            }

            if (empty($options1)) {
                $options1 = '';
            }

            if (empty($options3)) {
                $options3 = '';
            }

            $doctor_details = $this->doctor_model->getDoctorById($payment->doctor);

            if (!empty($doctor_details)) {
                $doctor = $doctor_details->name;
            } else {
                if (!empty($payment->doctor_name)) {
                    $doctor = $payment->doctor_name;
                } else {
                    $doctor = $payment->doctor_name;
                }
            }

            $patient_info = $this->db->get_where('patient', array('id' => $payment->patient))->row();
            if (!empty($patient_info)) {
                $patient_details = $patient_info->name . '</br>' . $patient_info->address . '</br>' . $patient_info->phone . '</br>';
            } else {
                $patient_details = ' ';
            }

            $info[] = array(
                $payment->id,
                $patient_details,
                $doctor,
                $date,
                $settings->currency . '' . $payment->amount,
                $settings->currency . '' . $discount,
                $settings->currency . '' . $payment->gross_total,
                $settings->currency . '' . $this->finance_model->getDepositAmountByPaymentId($payment->id),
                $settings->currency . '' . ($payment->gross_total - $this->finance_model->getDepositAmountByPaymentId($payment->id)),
                $payment->remarks,
                $options1 . ' ' . $options2 . ' ' . $options3,
                    //  $options2
            );
        }







        if (!empty($data['payments'])) {
            $output = array(
                "draw" => intval($requestData['draw']),
                "recordsTotal" => $this->db->get('payment')->num_rows(),
                "recordsFiltered" => $this->db->get('payment')->num_rows(),
                "data" => $info
            );
        } else {
            $output = array(
                // "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            );
        }




        echo json_encode($output);
    }
  // NHIF functions
    public function cardDetails()
    {
       $data['data'] ='';
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('carddetail', $data);
        $this->load->view('home/footer'); // just the header file
    }
    public function getCardNumber()
    { 
        
        $cardNo=$this->input->post('cardNo');
        if ($cardNo == '') {
           return redirect('finance/cardDetails');
        } else{
        $username = USERNAME;
        $password = PASSWORD;
        // echo $authotization_remark;
        $token = $this->getAuthenticationHeader($username, $password);
        // $cardNo = '01-11424549';
        
        $url = CARD_DETAIL_URL.'CardNo='.$cardNo;

        
        $headers = array(
            'Authorization: Bearer '.$token,
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
       
        curl_close($ch);
        $data = array('data'=>$response);
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('carddetail', $data);
        $this->load->view('home/footer'); // just the header file
    }
    }

    public function verifyCard()
    {

			// $cardNo=$this->input->get('CardNo');
			$visittype =$this->input->post('visitType');
			$username = USERNAME;
			$password = PASSWORD;
			$token = $this->getAuthenticationHeader($username, $password);
			$cardNo  =$this->input->post('verifiedcard');
            $referal_no = $this->input->post('referal_no');
            $authorization_remark = '';
           
			$url =AUTHORIZE_CARD_URL.'CardNo='.$cardNo.'&VisitTypeID='.$visittype.'&ReferralNo='.$referal_no.'&Remarks='.$authorization_remark;
			$headers = array(
				'Authorization: Bearer '.$token,
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			curl_close($ch);
            $data = array('data'=>$response);
            $outputdata = json_decode($response);
            if(gettype($outputdata) != 'NULL'){
            $authorization_data= [
                'type'=> $outputdata->{'$type'},
                'AuthorizationID' =>$outputdata->{'AuthorizationID'},
                'CardNo'  =>$outputdata->{'CardNo'},
                'MembershipNo' =>$outputdata->{'MembershipNo'},
                'EmployerNo'  =>$outputdata->{'EmployerNo'},
                'EmployerName'   =>$outputdata->{'EmployerName'},
                'HasSupplementary'   =>$outputdata->{'HasSupplementary'},
                'SchemeID'  =>$outputdata->{'SchemeID'},
                'SchemeName'   =>$outputdata->{'SchemeName'},
                'CardExistence'   =>$outputdata->{'CardExistence'},
                'CardStatusID'  =>$outputdata->{'CardStatusID'},
                'CardStatus'   =>$outputdata->{'CardStatus'},
                'IsValidCard'   =>$outputdata->{'IsValidCard'},
                'IsActive'   =>$outputdata->{'IsActive'},
                'StatusDescription'   =>$outputdata->{'StatusDescription'},
                'FirstName'   =>$outputdata->{'FirstName'},
                'MiddleName'   =>$outputdata->{'MiddleName'},
                'LastName'   =>$outputdata->{'LastName'},
                'FullName'   =>$outputdata->{'FullName'},
                'Gender'   =>$outputdata->{'Gender'},
                'PFNumber'   =>$outputdata->{'PFNumber'},
                'DateOfBirth'   =>$outputdata->{'DateOfBirth'},
                'YearOfBirth'  =>$outputdata->{'YearOfBirth'},
                'Age'  =>$outputdata->{'Age'},
                'ExpiryDate'   =>$outputdata->{'ExpiryDate'},
                'CHNationalID'  =>$outputdata->{'CHNationalID'},
                'AuthorizationStatus'   =>$outputdata->{'AuthorizationStatus'},
                'AuthorizationNo'  =>$outputdata->{'AuthorizationNo'},
                'Remarks'   =>$outputdata->{'Remarks'},
                'FacilityCode'  =>$outputdata->{'FacilityCode'},
                'ProductName'   =>$outputdata->{'ProductName'},
                'ProductCode'   =>$outputdata->{'ProductCode'},
                'CreatedBy'   =>$outputdata->{'CreatedBy'},
                'AuthorizationDate'   =>$outputdata->{'AuthorizationDate'},
                'DateCreated'   =>$outputdata->{'DateCreated'},
                'LastModifiedBy'   =>$outputdata->{'LastModifiedBy'},
                'LastModified'   =>$outputdata->{'LastModified'},
                'AuthorizationDateSerial' =>$outputdata->{'AuthorizationDateSerial'},
              ];
              if($outputdata->{'AuthorizationNo'} != NULL){
                  $this->finance_model->save_authorization_data($authorization_data);
                }
            }
              
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('carddetail', $data);
            $this->load->view('home/footer'); // just the header file
    }
    
   
    function getPatientNhifStatus() {
        $id = $this->input->get('id');
        $data = $this->finance_model->getPatientNhifStatus($id);
        echo json_encode($data);
    }
    public function authorizeCard(){
        $data['data'] ='';
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('authorize_card', $data);
        $this->load->view('home/footer'); // just the header file
    }
    public function authorize()
    {

			// $cardNo=$this->input->get('CardNo');
			$visittype =$this->input->post('visitType');
			$username = USERNAME;
			$password = PASSWORD;
			$token = $this->getAuthenticationHeader($username, $password);
            
			$cardNo  =$this->input->post('cardNo');
            $referal_no = $this->input->post('referal_no');
            $authorization_remark = $this->input->post('Remarks');

			// $url = 'https://verification.nhif.or.tz/nhifservice/breeze/verification/GetCardDetails?CardNo='.$cardNo;
			$url =AUTHORIZE_CARD_URL.'CardNo='.$cardNo.'&VisitTypeID='.$visittype.'&ReferralNo='.$referal_no.'&Remarks='.$authorization_remark;
			$headers = array(
				'Authorization: Bearer '.$token,
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			$response = curl_exec($ch);
			curl_close($ch);
            $outputdata = json_decode($response);
            if(gettype($outputdata) != 'NULL'){
            $authorization_data= [
                'type'=> $outputdata->{'$type'},
                'AuthorizationID' =>$outputdata->{'AuthorizationID'},
                'CardNo'  =>$outputdata->{'CardNo'},
                'MembershipNo' =>$outputdata->{'MembershipNo'},
                'EmployerNo'  =>$outputdata->{'EmployerNo'},
                'EmployerName'   =>$outputdata->{'EmployerName'},
                'HasSupplementary'   =>$outputdata->{'HasSupplementary'},
                'SchemeID'  =>$outputdata->{'SchemeID'},
                'SchemeName'   =>$outputdata->{'SchemeName'},
                'CardExistence'   =>$outputdata->{'CardExistence'},
                'CardStatusID'  =>$outputdata->{'CardStatusID'},
                'CardStatus'   =>$outputdata->{'CardStatus'},
                'IsValidCard'   =>$outputdata->{'IsValidCard'},
                'IsActive'   =>$outputdata->{'IsActive'},
                'StatusDescription'   =>$outputdata->{'StatusDescription'},
                'FirstName'   =>$outputdata->{'FirstName'},
                'MiddleName'   =>$outputdata->{'MiddleName'},
                'LastName'   =>$outputdata->{'LastName'},
                'FullName'   =>$outputdata->{'FullName'},
                'Gender'   =>$outputdata->{'Gender'},
                'PFNumber'   =>$outputdata->{'PFNumber'},
                'DateOfBirth'   =>$outputdata->{'DateOfBirth'},
                'YearOfBirth'  =>$outputdata->{'YearOfBirth'},
                'Age'  =>$outputdata->{'Age'},
                'ExpiryDate'   =>$outputdata->{'ExpiryDate'},
                'CHNationalID'  =>$outputdata->{'CHNationalID'},
                'AuthorizationStatus'   =>$outputdata->{'AuthorizationStatus'},
                'AuthorizationNo'  =>$outputdata->{'AuthorizationNo'},
                'Remarks'   =>$outputdata->{'Remarks'},
                'FacilityCode'  =>$outputdata->{'FacilityCode'},
                'ProductName'   =>$outputdata->{'ProductName'},
                'ProductCode'   =>$outputdata->{'ProductCode'},
                'CreatedBy'   =>$outputdata->{'CreatedBy'},
                'AuthorizationDate'   =>$outputdata->{'AuthorizationDate'},
                'DateCreated'   =>$outputdata->{'DateCreated'},
                'LastModifiedBy'   =>$outputdata->{'LastModifiedBy'},
                'LastModified'   =>$outputdata->{'LastModified'},
                'AuthorizationDateSerial' =>$outputdata->{'AuthorizationDateSerial'},
              ];
              if($outputdata->{'AuthorizationNo'} != NULL){
                $this->finance_model->save_authorization_data($authorization_data);
              }
            }
            $data = array('data'=>$response);
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('authorize_card', $data);
            $this->load->view('home/footer'); // just the header file
            //  else{

            // }
    }
    public function Download(){
        $data['services'] =$this->finance_model->getSerives();
        $data['pricePackage'] = $this->finance_model->getPackages();
        $data['download_message'] ='';
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('nhif_tariffs', $data);
        $this->load->view('home/footer'); // just the header file
    }
    public function pricePackage(){
        $username = USERNAME;
        $password = PASSWORD;
        $token = $this->getAuthenticationHeaderClaimServer($username, $password);
        
        $facility_code = FACILITY_CODE;
        $url =PRICE_PACKAGE_URL.'FacilityCode='.$facility_code;
        $headers = array(
            'Authorization: Bearer '.$token,
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        if(!empty($response)){
            $data = array('data'=>$response);
        
            $dataArray = json_decode($response);
            // drop existing packages then insert insert new
            $this->finance_model->dropExistingPackages();
            //insert each item in a database
           
            foreach ($dataArray->PricePackage as $item)
			{
				// Add the object to the batch array
				$pricePackage[] = array(
					'ItemCode' => $item->ItemCode,
					'PriceCode' => $item->PriceCode,
					'LevelPriceCode' => $item->LevelPriceCode,
					'OldItemCode' => $item->OldItemCode,
					'ItemTypeID' => $item->ItemTypeID,
					'ItemName' => $item->ItemName,
					'Strength' => $item->Strength,
					'Dosage' => $item->Dosage,
					'PackageID' => $item->PackageID,
					'SchemeID' => $item->SchemeID,
					'FacilityLevelCode' => $item->FacilityLevelCode,
					'UnitPrice' => $item->UnitPrice,
					'IsRestricted' => $item->IsRestricted,
					'MaximumQuantity' => $item->MaximumQuantity,
					'AvailableInLevels' => $item->AvailableInLevels,
					'PractitionerQualifications' => $item->PractitionerQualifications,
					'IsActive' => $item->IsActive
				);
			}
            $this->finance_model->PricePackage($pricePackage);         
            $this->session->set_flashdata('success', "Success!! Price Packages donwloaded successfully");
            redirect($_SERVER['HTTP_REFERER']); 
        }
        else{
          
            $this->session->set_flashdata('error', "Error!! Download Failed");
            redirect($_SERVER['HTTP_REFERER']);  
        }          
    }
    public function excludedServices(){
        $username = USERNAME;
        $password = PASSWORD;
        $token = $this->getAuthenticationHeaderClaimServer($username, $password);
        
        $facility_code = FACILITY_CODE;
        $url =PRICE_PACKAGE_URL.'FacilityCode='.$facility_code;
        $headers = array(
            'Authorization: Bearer '.$token,
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        if(!empty($response)){
            $data = array('data'=>$response);
        
            $dataArray = json_decode($response);
            $services = $dataArray->ExcludedServices;   
            echo "<pre>";
            // print_r($services);
            // exit;
            // drop existing packages then insert insert new      
            $this->finance_model->dropExistingServices();
            foreach ($services as $item) { 
            $excludedServices[] = array(
                'ItemCode'=>$item->ItemCode, 
                'SchemeID'=>$item->SchemeID,
                'SchemeName'=>$item->SchemeName,
                'ExcludedForProducts'=> $item->ExcludedForProducts,
            );       
            }
            $this->finance_model->excludedServices($excludedServices);          
            $this->session->set_flashdata('success', "Success!! Excluded Services donwloaded successfully");
            redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            $this->session->set_flashdata('error', "Error!! Download Failed");
            redirect($_SERVER['HTTP_REFERER']);
        }          
    }
    function Referer(){
        $data['qualifications'] = $this->db->get('physician_qualification')->result();
        $data['facilities'] = $this->db->get('health_facilities')->result();
        $data['doctors'] = $this->db->get('doctor')->result();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('refer_patient', $data);
        $this->load->view('home/footer'); // just the header file 
    }
    function Claim(){
        $data['doctors'] = $this->db->get('doctor')->result();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('claim_submission', $data);
        $this->load->view('home/footer'); // just the header file 
    }
    function seachByAthorizationNumber(){
        $authorizationNUmber = (int)$this->input->get('authorizationNo'); 
        $data = $this->finance_model->seachByAthorizationNumber($authorizationNUmber);
        echo json_encode($data);
    }
    function seachAdmittedPatient(){
        $card_number = (int)$this->input->get('card_number'); 
        $data = $this->finance_model->seachAdmittedPatient($card_number);
        echo json_encode($data);
    }

    
    function sendReferal(){
        // get form data
        $cardNo=$this->input->post('cardNo');
        $authorizationNo = $this->input->post('authorizationNo');
        $patientFullName = $this->input->post('patientFullName');
        $physicianMobileNo = $this->input->post('physicianMobileNo');
        $gender = $this->input->post('gender');
        $physicianName = $this->input->post('physicianName');
        $physicianQualificationID = $this->input->post('physicianQualificationID');
        $serviceIssuingFacilityCode = $this->input->post('serviceIssuingFacilityCode');
        $referringDiagnosis = $this->input->post('referringDiagnosis');
        $reasonsForReferral = $this->input->post('reasonsForReferral');

        // create array variable
        $data = array(
        'CardNo' => $cardNo,
        'AuthorizationNo' => $authorizationNo,
        'PatientFullName' => $patientFullName,
        'PhysicianMobileNo' => $physicianMobileNo,
        'Gender' => $gender,
        'PhysicianName' => $physicianName,
        'PhysicianQualificationID' => $physicianQualificationID,
        'ServiceIssuingFacilityCode' => $serviceIssuingFacilityCode,
        'ReferringDiagnosis' => $referringDiagnosis,
        'ReasonsForReferral' => $reasonsForReferral
        );
        // print_r($data);
        // exit;     
        // set the data to send as a JSON string

        $jsonData = json_encode($data);
        $username = USERNAME;
        $password = PASSWORD;
        // set the Authorization Bearer token
        $token = $this->getAuthenticationHeader($username, $password);
        // set the cURL options
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, REFER_PATIENT_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute the cURL request
        $response = curl_exec($ch);        

        // check for any errors
        if(curl_error($ch)) {
            $this->session->set_flashdata('error', 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            redirect($_SERVER['HTTP_REFERER']);
        } else {

        // close the cURL session
        curl_close($ch);
        $data = json_decode($response, true);
        if(gettype($data) != 'NULL'){

        $this->db->insert('patient_referals', [
            'ReferralID' => $data['ReferralID'],
            'ReferralNo' => $data['ReferralNo'],
            'CardNo' => $data['CardNo'],
            'AuthorizationNo' => $data['AuthorizationNo'],
            'PatientFullName' => $data['PatientFullName'],
            'Gender' => $data['Gender'],
            'ReferringDate' => $data['ReferringDate'],
            'PhysicianName' => $data['PhysicianName'],
            'PhysicianQualificationID' => $data['PhysicianQualificationID'],
            'PhysicialMobileNo' => $data['PhysicialMobileNo'],
            'ReferringDiagnosis' => $data['ReferringDiagnosis'],
            'ReasonsForReferral' => $data['ReasonsForReferral'],
            'SourceFacilityCode' => $data['SourceFacilityCode'],
            'ServiceIssuingFacilityCode' => $data['ServiceIssuingFacilityCode'],
            'CreatedBy' => $data['CreatedBy'],
            'DateCreated' => $data['DateCreated'],
            'LastModifiedBy' => $data['LastModifiedBy'],
            'LastModified' => $data['LastModified']
          ]);
          $this->session->set_flashdata('success', "Patient Refered Successfully");
          redirect('finance/refered');
        } else{
            $this->session->set_flashdata('error', $response);
            redirect($_SERVER['HTTP_REFERER']);
        }
         
        }
    }
    function refered(){
        $data['referals'] = $this->finance_model->referedPatient();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('referedPatient', $data);
        $this->load->view('home/footer'); // just the header file 
    }
    public function getPriceList(){
        $search = $this->input->get('search');
        $schemeId = $this->input->get('schemeId');
        $result = $this->finance_model->getPriceLists($search, $schemeId);
        // $data['result_q']=$result;
        // echo json_encode($result);
        echo "
        <ul class ='mylist'>";
        if(!empty($result)){
            foreach ($result as $row) {
            echo "<li id =".$row->id." class = 'select_product'>".$row->ItemName." (<b>".$row->SchemeID. ")</b></li>";
            }
        }else{
            echo "<li>".lang('no_data')."</li>";
        }
        echo "</ul>"; 
    }

    public function getItemSelected(){
        $itemId = $this->input->get('itemId');
        $result = $this->finance_model->getItemSelected($itemId);
        // echo json_encode($result);
         echo "<tr>
                <td>".$result->ItemName."</td>
                <td><input class ='text-center' readonly type ='text' id ='UnitPrice' name ='UnitPrice[]' value ='".$result->UnitPrice."'></td>
                <input class ='text-center' type ='hidden' id ='ItemCode' name ='ItemCode[]' value ='".$result->ItemCode."'>
                <td> <input class ='text-center' type ='text' id ='ItemQuantity' name ='ItemQuantity[]' value ='1'></td>
                <td> <input class ='text-center' readonly type ='text' id ='AmountClaimed' name ='AmountClaimed[]' value =".($result->UnitPrice*1)."></td>
                <td  class = 'text-center' title ='delete' id='remove'><i  class='fa fa-minus' aria-hidden='true'></i></td>
            </tr>";
    }
    function createFolioID(){

        // Define the name of the file to store the number in
        $filename = "foliosNumber.txt";

        // Check if the file exists, and if not, create it with an initial value of 1
        if (!file_exists($filename)) {
            file_put_contents($filename, "1");
        }

        // Read the current value of the number from the file
        $number = file_get_contents($filename);

        // Increment the value of the number
        $number++;

        // Write the updated value of the number back to the file
        file_put_contents($filename, $number);

        // Output the value of the number
        return $number;

    }
    function geneateBase64String($file){
        // if ($this->input->method() == 'post')
        // {
            // Check if a file was uploaded
            // if (!isset($_FILES['pdf_file']) || !is_uploaded_file($_FILES['pdf_file']['tmp_name']))
            // {
            //     echo 'Please select a PDF file to upload.';
            //     return;
            // }
    
            // Check if the uploaded file is a PDF file
            $file_info = pathinfo($file);
            // if ($file_info['extension'] != 'pdf')
            // {
            //     echo 'Please upload a PDF file.';
            //     return;
            // }
    
            // Read the file contents and serialize them as a base64 string
            $pdf_data = file_get_contents($file);
            $pdf_base64 = base64_encode($pdf_data);
           
            return $pdf_base64;
        // }

    }
    function guidv4(): string {
        if (function_exists('random_bytes')) {
            $data = random_bytes(16);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $data = openssl_random_pseudo_bytes(16);
        } else {
            $data = uniqid('', true);
        }
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
    function claimSubmission(){             
        $ClaimYear = date('Y');
        $ClaimMonth = date('m');
        $FolioNo = $this->createFolioID();
        $SerialNo =$_POST['SerialNo'];
        $CardNo =$_POST['CardNo'];
        $FirstName =$_POST['FirstName'];
        $LastName =$_POST['LastName'];
        $Gender =$_POST['Gender'];
        $SerialNo =$_POST['SerialNo'];
        $DateOfBirth =$_POST['DateOfBirth'];
        $TelephoneNo =$_POST['TelephoneNo'];
        $PatientFileNo =$_POST['PatientFileNo'];
        $PatientFile = $this->geneateBase64String($_FILES['PatientFile']['tmp_name']);
        $ClaimFile =$this->geneateBase64String($_FILES['ClaimFile']['tmp_name']);
        $AuthorizationNo =$_POST['AuthorizationNo'];
        $AttendanceDate =$_POST['AttendanceDate'];
        $PatientTypeCode =$_POST['PatientTypeCode'];
        $DateAdmitted =$_POST['DateAdmitted'];
        $DateDischarged =$_POST['DateDischarged'];
        $PractitionerNo =$_POST['PractitionerNo'];
        $DateCreated = date('Y-m-d');
        $FolioDiseaseID = $this->guidv4();
        $FolioID = $this->guidv4();
        $DiseaseCode =$_POST['DiseaseCode'];
        $CreatedBy = $_POST['CreatedBy'];
        $ApprovalRefNo = '';
        $diseases = explode(',', $_POST['DiseaseCode']);
        $FolioDisease = array(); // create an empty array to store the FolioDiseases
        foreach($diseases as $key => $value) {       
            $FolioDisease[] = array(
                'FolioDiseaseID' => $this->guidv4(),
                'FolioID' => $FolioID,
                'DiseaseCode' => $value,
                'CreatedBy' => $CreatedBy,
                'DateCreated' => $DateCreated
            );
        }
        $i = isset($_POST['ItemCode']) ? sizeof($_POST['ItemCode']) : 0;
        for ($r = 0; $r < $i; $r++) {
       $ItemCode            = $_POST['ItemCode'][$r];
       $ItemQuantity            = $_POST['ItemQuantity'][$r];
       $UnitPrice            = $_POST['UnitPrice'][$r];
       $AmountClaimed            = $_POST['AmountClaimed'][$r];
       $FolioItem = [
               'FolioItemID' =>$this->guidv4(),
               'FolioID' =>$FolioID,

               'ItemCode'        => $ItemCode,
               'ItemQuantity'    => $ItemQuantity,
               'UnitPrice'       => $UnitPrice,
               'AmountClaimed'   => $AmountClaimed,
               'ApprovalRefNo'   =>$ApprovalRefNo,
               'CreatedBy'       =>$CreatedBy,
               'DateCreated'     =>$DateCreated
       ];
       $FolioItems[] =$FolioItem;
   }

        $dataObject= [
            'FolioID' =>$FolioID,
            'FacilityCode' =>FACILITY_CODE,
            'ClaimYear' => $ClaimYear,
            'ClaimMonth' =>$ClaimMonth,
            'FolioNo' => $this->createFolioID(),
            'SerialNo' => $SerialNo,
            'CardNo' =>$CardNo,
            'FirstName' =>$FirstName,
            'LastName' =>$LastName,
            'Gender'   =>$Gender,
            'DateOfBirth' =>$DateOfBirth,
            'TelephoneNo'=>$TelephoneNo,
            'PatientFileNo' =>$PatientFileNo,
            'PatientFile'=>$PatientFile,
            'ClaimFile' =>$ClaimFile,
            'AuthorizationNo' =>$AuthorizationNo,
            'AttendanceDate' =>$AttendanceDate,
            'PatientTypeCode'=>$PatientTypeCode,
            'DateAdmitted' =>$DateAdmitted,
            'DateDischarged' =>$DateDischarged,
            'PractitionerNo' =>$PractitionerNo,
            'CreatedBy' => $CreatedBy,
            'DateCreated' =>$DateCreated,
            'FolioDiseases'=>$FolioDisease,
            'FolioItems' =>$FolioItems

        ];
        $entitiesObject = array(
            'entities' => array($dataObject)
        );
        $entities =json_encode($entitiesObject);
        $username = USERNAME;
        $password = PASSWORD;
        // set the Authorization Bearer token
        $token = $this->getAuthenticationHeaderClaimServer($username, $password);
        // set the cURL options
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, SUBMIT_CLAIM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $entities);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // execute the cURL request
        $response = curl_exec($ch); 
        $outputdata = json_decode($response);
        if(gettype($outputdata) != 'NULL')
        {
            $this->session->set_flashdata('success', "Claim Received Successfully");
            // curl_close($ch);
            redirect($_SERVER['HTTP_REFERER']);
        } 
        else{
            $this->session->set_flashdata('error', $response);
            redirect($_SERVER['HTTP_REFERER']);
        }
        
       
    }
    function admit()
    {
        // $data['referals'] = $this->finance_model->referedPatient();
        $data['qualifications'] = $this->db->get('overal_qualification')->result();
        // $data['facilities'] = $this->db->get('health_facilities')->result();
        $data['doctors'] = $this->db->get('doctor')->result();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('admit', $data);
        $this->load->view('home/footer'); // just the header file 
    }
   function discharge()
    {
       
         // $data['referals'] = $this->finance_model->referedPatient();
         $data['qualifications'] = $this->db->get('overal_qualification')->result();
         // $data['facilities'] = $this->db->get('health_facilities')->result();
         $data['doctors'] = $this->db->get('doctor')->result();
        
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('discharge', $data);
        $this->load->view('home/footer'); // just the header file 
       
    }
    function sendAdmission(){
       // Get form input values
    $data = array(
        'CardNo' => $this->input->post('CardNo'),
        'SchemeID' => $this->input->post('SchemeID'),
        'FullName' => $this->input->post('FullName'),
        'Gender' => $this->input->post('Gender'),
        'Age' => $this->input->post('Age'),
        'PhysicianMobileNo' => $this->input->post('PhysicianMobileNo'),
        'AdmissionTypeID' => $this->input->post('AdmissionTypeID'),
        'AuthorizationNo' => $this->input->post('AuthorizationNo'),
        'AdmittingPhysicianName' => $this->input->post('AdmittingPhysicianName'),
        'QualificationID' => $this->input->post('QualificationID'),
        'DateAdmitted' => $this->input->post('DateAdmitted'),
        'DiagnosisAtAdmission' => $this->input->post('DiagnosisAtAdmission'),
        'ReasonsForAdmission' => $this->input->post('ReasonsForAdmission'),
        'CreatedBy' => $this->input->post('CreatedBy')
    );

    $jsonData = json_encode($data);
    $username = USERNAME;
    $password = PASSWORD;
    // set the Authorization Bearer token
    $token = $this->getAuthenticationHeader($username, $password);
    // set the cURL options
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, ADMIT_PATIENT_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // execute the cURL request
    $response = curl_exec($ch);        

    // check for any errors
    if(curl_error($ch)) {
        $this->session->set_flashdata('error', 'cURL error: ' . curl_error($ch));
        curl_close($ch);
        redirect($_SERVER['HTTP_REFERER']);
    }
    else {
        $string = json_decode($response)->Message;
        $substring = "successfully admitted";

        if(strpos($string, $substring)!== false){
            $this->db->insert('admitted_patient', $data);
            $this->session->set_flashdata('success', $string);
            redirect($_SERVER['HTTP_REFERER']);
        }
        // close the cURL session
        // curl_close($ch);
        // echo "<pre>";
        // print_r($response);
        // exit;
        // $data = json_decode($response, true);
        
        $this->session->set_flashdata('error', $string);
        redirect($_SERVER['HTTP_REFERER']);
    }

// You can now use the $data array as needed in your CodeIgniter controller.
// For example, you can pass it to a model for further processing or use it to populate a database record.

    }
    function sendDischarge(){
      
        $data = array(
            'CardNo' => $this->input->post('CardNo'),
            'AdmissionID' => strtoupper($this->guidv4()),
            'ComplainsDuringAdmission' => $this->input->post('ComplainsDuringAdmission'),
            'ProgressInWard' => $this->input->post('ProgressInWard'),
            'DiagnosisAtDischarge' => $this->input->post('DiagnosisAtDischarge'),
            'ConditionsAtDischarge' => $this->input->post('ConditionsAtDischarge'),
            'DiagnosisAtAdmission' => $this->input->post('DiagnosisAtAdmission'),
            'PhysicianMobileNo' => $this->input->post('PhysicianMobileNo'),
            'AdmissionTypeID' => $this->input->post('AdmissionTypeID'),
            'DischargePhysicianName' => $this->input->post('DischargePhysicianName'),
            'QualificationID' => $this->input->post('QualificationID'),
            'DateDischarged' => $this->input->post('DateDischarged'),
            'ReasonsForAdmission' => $this->input->post('ReasonsForAdmission'),
            'CreatedBy' => $this->input->post('CreatedBy')
        );
        $AuthorizationNo =  $this->input->post('AuthorizationNo');
        $CardNo = $this->input->post('CardNo');
       
            $jsonData = json_encode($data);
            $username = USERNAME;
            $password = PASSWORD;
            
            // set the Authorization Bearer token
            $token = $this->getAuthenticationHeader($username, $password);
            
            // set the cURL options
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, DISCHARGE_PATIENT_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // execute the cURL request
            $response = curl_exec($ch);   
                

            // check for any errors
            if(curl_error($ch)) {
                $this->session->set_flashdata('error', 'cURL error: ' . curl_error($ch));
                curl_close($ch);
                redirect($_SERVER['HTTP_REFERER']);
            }
            else {
               
                // close the cURL session
                // curl_close($ch);
                // echo "<pre>";
                // print_r($response);
                // exit;
                $response = json_decode($response);
                $string = $response->Message;
                $substring = "successfully discharged";

                if(strpos($string, $substring) !==false){

                    $this->db->query('UPDATE admitted_patient set status = 1 WHERE CardNo="'.$CardNo.'" AND AuthorizationNo="'.$AuthorizationNo.'"');
                    $this->session->set_flashdata('success', $string );
                    redirect($_SERVER['HTTP_REFERER']);
                   
                }else{           
                $this->session->set_flashdata('error', $string);
                 redirect($_SERVER['HTTP_REFERER']);
                }
            }
    }
    function ClaimConciliation()
    {
         // $data['referals'] = $this->finance_model->referedPatient();
        //  $data['qualifications'] = $this->db->get('overal_qualification')->result();
         // $data['facilities'] = $this->db->get('health_facilities')->result();
        //  $data['doctors'] = $this->db->get('doctor')->result();
       
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('claim_consiliation');
        $this->load->view('home/footer'); // just the header file 
    }
    public function Reconsiliation(){

            $FacilityCode = $this->input->post('FacilityCode');
            $ClaimYear = $this->input->post('ClaimYear');
            $ClaimMonth = $this->input->post('ClaimMonth');

        $username = USERNAME;
        $password = PASSWORD;
        // set the Authorization Bearer token
        $token = $this->getAuthenticationHeaderClaimServer($username, $password);
        $url = CLAIM_RECONCILIATION_URL.'FacilityCode='.$FacilityCode.'&ClaimYear='.$ClaimYear.'&ClaimMonth='.$ClaimMonth;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token,
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        // check for any errors
        if(curl_error($ch)) {
            $this->session->set_flashdata('error', 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            redirect($_SERVER['HTTP_REFERER']);
        }
        else {

            $this->session->set_flashdata('success', 'Sucess');
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('claim_consiliation', $response);
            $this->load->view('home/footer'); // just the header file 
        }
    }
    function ClaimAmount(){
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('claim_amount');
        $this->load->view('home/footer'); // just the header file
    }
    function getClaimAmount(){

        $FacilityCode = $this->input->post('FacilityCode');
        $ClaimYear = $this->input->post('ClaimYear');
        $ClaimMonth = $this->input->post('ClaimMonth');

        $username = USERNAME;
        $password = PASSWORD;
        // set the Authorization Bearer token
        $token = $this->getAuthenticationHeaderClaimServer($username, $password);
        $url = CLAIMED_AMOUNT_URL.'FacilityCode='.$FacilityCode.'&ClaimYear='.$ClaimYear.'&ClaimMonth='.$ClaimMonth;
        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$token,
        );
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        // check for any errors
        if(curl_error($ch)) {
            $this->session->set_flashdata('error', 'cURL error: ' . curl_error($ch));
            curl_close($ch);
            redirect($_SERVER['HTTP_REFERER']);
        }
        else {
            $data = array('data'=>$response);
            $this->session->set_flashdata('success', 'Sucess');
            $this->load->view('home/dashboard'); // just the header file
            $this->load->view('claim_amount', $data);
            $this->load->view('home/footer'); // just the header file 
        }
    }
    public function admitted(){
        $data['admissions'] = $this->finance_model->admittedPatient();
        $this->load->view('home/dashboard'); // just the header file
        $this->load->view('admittedPatient', $data);
        $this->load->view('home/footer'); // just the header file  
    }


}


/* End of file finance.php */
/* Location: ./application/modules/finance/controllers/finance.php */