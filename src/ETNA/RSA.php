<?php

namespace ETNA;

class RSA
{
    private $private = null;
    private $public  = null;
    
    public static function loadPrivateKey($path, $password = "")
    {
        $file = realpath($path);
        if ($file === false) {
            throw new \Exception("Private Key not found");
        }

        $private_key = openssl_pkey_get_private("file://{$file}", $password);
        if ($private_key === false) {
            throw new \Exception("Bad Private Key");
        }
        $public_key  = openssl_pkey_get_public(openssl_pkey_get_details($private_key)["key"]);
        if ($public_key === false) {
            throw new \Exception("Bad Public Key");
        }
        
        return new self($public, $private);
    }
    
    public static function loadPublicKey($path)
    {
        $file = realpath($path);
        if ($file === false) {
            throw new \Exception("Public Key not found");
        }
        
        $public_key = openssl_pkey_get_public("file://{$file}");
        if ($public_key === false) {
            throw new \Exception("Bad Public Key");
        }

        return new self($public_key);
    }
    
    protected function __construct($public, $private = null)
    {
        $this->public  = $public;
        $this->private = $private;
    }
    
    public function __destruct()
    {
        if ($this->private) {
            openssl_free_key($this->private);
        }
        openssl_free_key($this->public);
    }

    public function getPublicKey()
    {
        return openssl_pkey_get_details($this->public)["key"];
    }
    
    /**
     * Signs some $data
     *
     * @param string $data
     * @return string base64encoded signature
     */
    public function sign($data)
    {
        if (!$this->private) {
            throw new Exception("Undefined Private Key");
        }

        if (!openssl_sign($data, $signature, $this->private)) {
            throw new Exception("Undefined openssl error");
        }
        return base64_encode($signature);
    }
    
    /**
     * Check Signature
     *
     * @param string $data
     * @param string $signature base64encoded
     * @return boolean true if signature matches
     */
    public function verify($data, $signature)
    {
        return openssl_verify($data, base64_decode($signature), $this->public) == 1;
    }
}
