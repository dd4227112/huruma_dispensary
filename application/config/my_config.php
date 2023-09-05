<?php

function define_my_constants()
{
    
    $host = 'localhost';
    $user = 'root';
    $db_name = 'huduma';
    
    $db_password = 'mwesiGEMWE1';
    $connection = mysqli_connect($host, $user, $db_password, $db_name)or die('ConnectError '.mysqli_error($connection));

    $nhif_config = mysqli_query($connection, 'SELECT * FROM nhif_setup');
    $data =mysqli_fetch_assoc($nhif_config );
    if ($data['environment'] == 'Live') {
        $link =  $data['live_environment'];
    }
    else{
        $link =  $data['test_environment'];  
    }
    define('USERNAME', $data['username']);
    define('PASSWORD', $data['password']);
    define('SERVICE_TOKEN', $link.$data['service_token_url']);
    define('CLAIM_TOKEN', $link.$data['claim_token_url']);
    define('CARD_DETAIL_URL', $link.$data['card_detail_url']);
    define('AUTHORIZE_CARD_URL', $link.$data['authorize_card_url']);
    define('SUBMIT_CLAIM_URL', $link.$data['submit_claim_url']);
    define('SUBMITTED_CLAIM_URL', $link.$data['submited_claim_url']);
    define('REFER_PATIENT_URL', $link.$data['refer_patient_url']);
    define('PRICE_PACKAGE_URL', $link.$data['price_package_url']);
    define('ADMIT_PATIENT_URL', $link.$data['admit_patient_url']);
    define('DISCHARGE_PATIENT_URL', $link.$data['discharge_patient_url']);
    define('CLAIM_RECONCILIATION_URL', $link.$data['claim_reconciliation_url']);
    define('CLAIMED_AMOUNT_URL', $link.$data['claimed_amount_url']);


    


    define('FACILITY_CODE', $data['facility_code']);  
    
}

define_my_constants();