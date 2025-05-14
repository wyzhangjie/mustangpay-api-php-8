<?php

namespace app\utils;

class BaseKeyApiUtils
{
    /**
     * Create an order (example implementation)
     * @return array Order data
     */
    protected function createOrder()
    {
        // Sample order creation logic, adjust according to requirements
        return [
            'orderId' => '123456',
            'amount' => '100',
            'currency' => 'USD',
            'merchant' => 'merchant_name',
        ];
    }

    /**
     * Encrypt data using RSA algorithm
     * @param string $srcBody Data to be encrypted
     * @param string $privateKey Private key for encryption
     * @param string $publicKey Public key for verification
     * @return array Encrypted data structure
     */
    protected function encryptToObject($srcBody, $privateKey, $publicKey)
    {
        // Encryption logic using openssl
        // Actual encryption method depends on private and public keys
        $encryptedData = openssl_encrypt($srcBody, 'RSA', $privateKey);
        return [
            'encryptedData' => $encryptedData,
            'publicKey' => $publicKey
        ];
    }

    /**
     * Decrypt data using RSA algorithm
     * @param string $privateKey Private key for decryption
     * @param string $publicKey Public key for verification
     * @param array $body Encrypted data structure
     * @return string Decrypted data
     */
    protected function decrypt($privateKey, $publicKey, $body)
    {
        // Decryption logic
        $encryptedData = $body['encryptedData'];
        openssl_decrypt($encryptedData, 'RSA', $privateKey);
        return 'decrypted data';
    }

    /**
     * Get file path for MustangPay configuration
     * @return string File path
     */
    protected function getMustangPayFilePath()
    {
        // Logic to get file path
        return '/path/to/mustangpay/file.json';
    }

    /**
     * Get file path for merchant configuration
     * @return string File path
     */
    protected function getMerchantFilePath()
    {
        // Logic to get file path
        return '/path/to/merchant/file.json';
    }

    /**
     * Write data to file
     * @param string $filePath Target file path
     * @param mixed $data Data to be written
     */
    protected function writeToFile($filePath, $data)
    {
        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Read data from file
     * @param string $filePath Source file path
     * @return string File content
     */
    protected function readFromFile($filePath)
    {
        return file_get_contents($filePath);
    }
}
