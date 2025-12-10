<?php
namespace App\Helpers;

class Encryption {
    private static $method = 'AES-256-CBC';
    
    public static function encrypt($data) {
        if (empty($data)) return $data;
        
        $key = self::getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$method));
        
        $encrypted = openssl_encrypt($data, self::$method, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public static function decrypt($data) {
        if (empty($data)) return $data;
        
        $key = self::getEncryptionKey();
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length(self::$method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        
        return openssl_decrypt($encrypted, self::$method, $key, 0, $iv);
    }
    
    private static function getEncryptionKey() {
        $key = $_ENV['ENCRYPTION_KEY'] ?? 'default_key_change_in_production';
        return hash('sha256', $key, true);
    }
}