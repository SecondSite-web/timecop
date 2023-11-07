<?php


namespace Dash;


class Encryption
{
    private string $ciphering = "BF-CBC";
    private int $options = 0;
    private string $encryptionIv = 'a4%ghtKb';

    public function encrypt($string): bool|string
    {
        $encryptionKey = openssl_digest(php_uname(), 'MD5', true);

        return openssl_encrypt(
            $string,
            $this->ciphering,
            $encryptionKey,
            $this->options,
            $this->encryptionIv
        );
    }

    public function decrypt($string): bool|string
    {
        $decryptionKey = openssl_digest(php_uname(), 'MD5', true);
        // $encryption = $this->encrypt($string);

        return openssl_decrypt(
            $string,
            $this->ciphering,
            $decryptionKey,
            $this->options,
            $this->encryptionIv
        );
    }
}