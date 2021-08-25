<?php

namespace pw2_grupo_21\Validator;

use DateTime;

class Validator {

    private $acceptedDomains;
    private $acceptedCode;

    public function __construct() {

        $this->acceptedDomains = array(
            'students.salle.url.edu',
            'salle.url.edu'
        );

        $this->acceptedCode = array(
            '34',
            '+34'
        );
        /**
         * formato png, max medida 1m ... size 400x400
         */
    }

    /* EMAIL VALIDATION */
    public function validateEmail($email) {

        $domain = $this->getDomain(trim($email));

        if(in_array($domain, $this->acceptedDomains)) {
            return true;
        }
        return false;
    }

    private function getDomain($email) {

        $emailParts = explode('@', $email);
        $domain = array_pop($emailParts);
        return $domain;
    }

    /* BIRTHDATE VALIDATION */
    public function validateBirthdate($date) {

        $lowercase = preg_match('@[a-z]@', $date);
        $birthdate = new DateTime($date);
        $now = new DateTime();

        $age = $now->diff(($birthdate));
        if ($lowercase || $age->y > 18) return true;
        
        return false;
    } 


    /* PHONE VALIDATION */
    public function validateCode($code) {
        if(in_array($code, $this->acceptedCode)) return true;
        return false;
    }

    public function validatePhone($phone) {

        if(is_numeric($phone)) {

            if ((strlen((string)$phone)) == 9) {

                $firstDigit = $phone[0];

                if ((int)$firstDigit == 6) {
                    return true;
                }
            }
        }
        return false;
    }

    /* PASSWORD VALIDATION */
    public function validPassword($password) {

        if (strlen($password) > 5) {
            if (preg_match("#[0-9]+#",$password)) {
                if (preg_match("#[A-Z]+#",$password)) {
                    if (preg_match("#[a-z]+#",$password)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
    
}