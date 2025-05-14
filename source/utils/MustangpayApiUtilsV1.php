<?php

namespace app\utils;

use app\constants\MustangpayApiConstantsV1;
use GuzzleHttp\Exception\RequestException;
use Yii;
use GuzzleHttp\Client;

class MustangpayApiUtilsV1
{
    private static $requestConfig = [
        'connect_timeout' => 15,
        'timeout' => 90,
    ];

    // Filter out null values in the data
    private static function filterData($data)
    {
        foreach ($data as $k => &$value) {
            if (is_null($value)) {
                unset($data[$k]);
            }
        }
        return $data;
    }

    // Call the MustangPay API (Test)
    public static function callTest($logPrefix, $data, $jumpKey)
    {
        try {
            // Filter null values
            $data = self::filterData($data);

            // JSON encode the original data without escaping slashes
            $srcBody = json_encode($data, JSON_UNESCAPED_SLASHES);

            // Encrypt and sign the data
            $encryptedData = self::encryptToObject($srcBody, RSAUtils::getKeyPem(Yii::$app->params['certFile']['mustangPayPublicKeyPath']), MustangpayApiConstantsV1::MERCHANT_ID);

            // Use JSON_UNESCAPED_SLASHES to avoid escaping slashes
            $sendJson = json_encode($encryptedData, JSON_UNESCAPED_SLASHES);

            error_log("{$logPrefix}|request->sendJson: {$sendJson}");

            $client = new Client([
                'base_uri' => MustangpayApiConstantsV1::geTestMustangPayApiUrl($jumpKey),
                'timeout' => self::$requestConfig['timeout'],
                'connect_timeout' => self::$requestConfig['connect_timeout'],
                'verify' => false,
            ]);

            $response = $client->post('', [
                'json' => json_decode($sendJson, true),
                'headers' => [
                    'Content-Type' => 'application/json;charset=UTF-8',
                    'merchantId' => MustangpayApiConstantsV1::MERCHANT_ID,  // Use the merchant ID from the constant
                ],
            ]);

            $responseStr = $response->getBody()->getContents();
            error_log("{$logPrefix}|response->str: {$responseStr}");

            // Parse the response
            $accessBody = json_decode($responseStr, true);
            if (!$accessBody) {
                throw new \RuntimeException("Failed to parse the response JSON: {$responseStr}");
            }

            // Decrypt the response
            $body = self::merchantDecrypt($accessBody);

            if ($body === null) {
                throw new \RuntimeException("Received response: Signature verification failed");
            }

            error_log("{$logPrefix}|response->Signature verification successful");

            // Parse the decrypted response without escaping slashes
            return json_decode($body, true, 512, JSON_UNESCAPED_SLASHES);

        } catch (RequestException $e) {
            // Handle HTTP request exceptions
            $response = $e->getResponse();
            $responseBody = $response? $response->getBody()->getContents() : 'No response';
            error_log("Mustangpay HTTP request failed: {$e->getMessage()}, Response: {$responseBody}");
            return null;
        } catch (\Exception $e) {
            // Handle other exceptions
            error_log("Mustangpay API failed: {$e->getMessage()}");
            return null;
        }
    }

    // Decrypt the response and verify the signature
    private static function merchantDecrypt($body)
    {
        try {
            // Ensure that the response data contains the necessary fields
            if (!isset($body['encryptKey']) ||!isset($body['encryptData'])) {
                error_log("Response format error: Missing encryptKey or encryptData field");
                return null;
            }

            $encryptKey = $body['encryptKey'];
            $encryptData = $body['encryptData'];

            // Decrypt the AES key
            $aesKey = RSAUtils::privateDecrypt($encryptKey, RSAUtils::getKeyPem(Yii::$app->params['certFile']['rsaPrivateKeyPath']));

            if (!$aesKey) {
                error_log("AES key decryption failed");
                return null;
            }

            // Decrypt the data
            $originalData = AESUtil::decrypt($encryptData, $aesKey);

            if (!$originalData) {
                error_log("Data decryption failed");
                return null;
            }

            // Parse the original data
            $originalDataObj = json_decode($originalData, true);

            if (!is_array($originalDataObj)) {
                error_log("The decrypted data is not valid JSON: {$originalData}");
                return null;
            }

            // Extract the signature
            $sign = $originalDataObj['sign']?? null;
            if (empty($sign)) {
                error_log("RSA signature is empty");
                return null;
            }

            // Remove the signature and verify
            unset($originalDataObj['sign']);
            ksort($originalDataObj);

            // Use JSON_UNESCAPED_SLASHES to ensure the same string is generated
            $originalDataObjNoSignStr = json_encode($originalDataObj, JSON_UNESCAPED_SLASHES);

            $isVerified = RSAUtils::verify(
                $originalDataObjNoSignStr,
                RSAUtils::getKeyPem(Yii::$app->params['certFile']['mustangPayPublicKeyPath']),
                $sign
            );

            if ($isVerified) {
                return $originalData;
            } else {
                error_log("RSA signature verification failed, Original data: {$originalDataObjNoSignStr}");
                return null;
            }

        } catch (\Exception $e) {
            error_log("RSA decryption error: {$e->getMessage()}");
            return null;
        }
    }

    // Encrypt data into an object
    private static function encryptToObject($response, $mustangPayPublicKey, $merchantId)
    {
        if (empty($mustangPayPublicKey)) {
            error_log("Public key error");
            return null;
        }

        // Parse the response data
        $jsonObject = json_decode($response, true);

        if (!is_array($jsonObject)) {
            error_log("Failed to parse the response data into an array: {$response}");
            return null;
        }

        // Filter null values and sort
        $jsonObject = self::filterData($jsonObject);
        ksort($jsonObject);

        // Use JSON_UNESCAPED_SLASHES to avoid escaping slashes
        $jsonStr = json_encode($jsonObject, JSON_UNESCAPED_SLASHES);

        // Sign with the RSA private key
        $sign = RSAUtils::sign(
            $jsonStr,
            RSAUtils::getKeyPem(Yii::$app->params['certFile']['rsaPrivateKeyPath'])
        );

        if (!$sign) {
            error_log("Signature generation failed");
            return null;
        }

        $jsonObject['sign'] = $sign;

        // Generate a random AES key
        $aesKey = bin2hex(random_bytes(16));
        error_log("AES Key: {$aesKey}");

        // Encrypt the data with AES
        $encryptData = AESUtil::encrypt(
            json_encode($jsonObject, JSON_UNESCAPED_SLASHES),
            $aesKey
        );

        if (!$encryptData) {
            error_log("Data encryption failed");
            return null;
        }

        // Encrypt the AES key with RSA
        $encryptKey = RSAUtils::publicEncrypt($aesKey, $mustangPayPublicKey);

        if (!$encryptKey) {
            error_log("AES key encryption failed");
            return null;
        }

        return [
            'encryptKey' => $encryptKey,
            'encryptData' => $encryptData,
            'merchantId' => $merchantId
        ];
    }
}
