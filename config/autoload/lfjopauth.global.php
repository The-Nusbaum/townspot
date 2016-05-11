<?php
use \Townspot\User\Entity;
$settings = array(
    'security_salt' => 'lajksdfhlkajsdflkasjdfhlkasjdfhlkasjdfh6547',
    'Strategy' => array(
        'Facebook' => array(
            'app_id' => '333808790029898',
            'app_secret' => '7315cc6812046cf91419959bdd359bec',
            'scope' => 'email,public_profile',
        ),
        'Twitter' => array(
            'key' => 'wdmZ425vxieBkMEDTRMwoQ',
            'secret' => 'ZLYgVcaAoUNkhJU1K9TweiGAYGnBh1eYDcF1l9vuDE'
        ),
        'Google' => array(
            'client_id' => '872367745273-sbsiuc81kh9o70ok3macc15d2ebpl440.apps.googleusercontent.com',
            'client_secret' => 'KkR-tZy_lJmcHHzlKtFWDAdD',
            'scope' => "email"
        )

    ),
    'check_controller_enabled' => false
);

return array('lfjopauth' => $settings);