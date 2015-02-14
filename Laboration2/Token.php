<?php
/**
 * Student Id:  uf222ba  
 * Name:        Ulrika Falk
 * Mail:        uf222ba@student.lnu.se 
 * Date:        2015-02-08
 * Laboration:  2, 1DV449
 * Koden kommer härifrån: https://www.youtube.com/watch?v=VflbINBabc4
 */

class Token {
    public static function generate() {
        return $_SESSION['token'] = base64_encode(openssl_random_pseudo_bytes(32));
    }

    public static function check($token) {
        if(isset($_SESSION['token']) && $token === $_SESSION['token']) {
            unset($_SESSION['token']);
            return true;
        }
        return false;
    }
}