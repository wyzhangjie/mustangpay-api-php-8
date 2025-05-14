<?php

namespace app\utils;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class VerifyKeyApiUtilsV1 extends BaseKeyApiUtils
{
    private $client;
    private $logger;

    public function __construct()
    {
        $this->client = new Client();
        $this->logger = new Logger('mustangpay');
        $this->logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));
    }

    /**
     * Create a signed message for MustangPay
     *
     * @param string $mustangPayPrivateKey Private key of MustangPay
     * @param string $merchantPublicKey Public key of the merchant
     */
    public function mustangPayCreateVerifyMessage($mustangPayPrivateKey, $merchantPublicKey)
    {
        try {
            $oneOrder = $this->createOrder();
            $srcBody = json_encode($oneOrder);
            $this->logger->info("request->srcBody: {$srcBody}");

            // Send a signed request
            $body = $this->encryptToObject($srcBody, $mustangPayPrivateKey, $merchantPublicKey);

            // Write the encrypted data to a file
            $filePath = $this->getMustangPayFilePath();
            $this->writeToFile($filePath, $body);

            $this->logger->info("Data written to file: {$filePath}");

        } catch (\Exception $e) {
            $this->logger->error("Error creating verify message for MustangPay", ['exception' => $e]);
        }
    }

    /**
     * The merchant verifies whether the message provided by MustangPay can be signed and decrypted successfully
     *
     * @param string $merchantPrivateKey Private key of the merchant
     * @param string $mustangPayPublicKey Public key of MustangPay
     */
    public function merchantCreateVerifyMessage($merchantPrivateKey, $mustangPayPublicKey)
    {
        try {
            $oneOrder = $this->createOrder();
            $srcBody = json_encode($oneOrder);
            $this->logger->info("request->srcBody: {$srcBody}");

            // Send a signed request
            $body = $this->encryptToObject($srcBody, $merchantPrivateKey, $mustangPayPublicKey);

            // Write the encrypted data to a file
            $filePath = $this->getMustangPayFilePath();
            $this->writeToFile($filePath, $body);

            $this->logger->info("Data written to file: {$filePath}");

        } catch (\Exception $e) {
            $this->logger->error("Error creating verify message for the merchant", ['exception' => $e]);
        }
    }

    /**
     * The merchant verifies the signed message from MustangPay
     *
     * @param string $merchantPrivateKey Private key of the merchant
     * @param string $mustangPayPublicKey Public key of MustangPay
     */
    public function merchantVerifyMustangMessage($merchantPrivateKey, $mustangPayPublicKey)
    {
        try {
            $filePath = $this->getMustangPayFilePath();
            $content = $this->readFromFile($filePath);

            // Decrypt and verify
            $body = json_decode($content, true);
            $this->logger->info("body: " . json_encode($body));

            $srcBody = $this->decrypt($merchantPrivateKey, $mustangPayPublicKey, $body);
            $this->logger->info("srcBody: {$srcBody}");

        } catch (\Exception $e) {
            $this->logger->error("Error verifying message from MustangPay", ['exception' => $e]);
        }
    }

    /**
     * MustangPay verifies the signed message from the merchant
     *
     * @param string $mustangPayPrivateKey Private key of MustangPay
     * @param string $merchantPublicKey Public key of the merchant
     */
    public function mustangPayVerifyMustangMessage($mustangPayPrivateKey, $merchantPublicKey)
    {
        try {
            $filePath = $this->getMerchantFilePath();
            $content = $this->readFromFile($filePath);

            // Decrypt and verify
            $body = json_decode($content, true);
            $this->logger->info("body: " . json_encode($body));

            $srcBody = $this->decrypt($mustangPayPrivateKey, $merchantPublicKey, $body);
            $this->logger->info("srcBody: {$srcBody}");

        } catch (\Exception $e) {
            $this->logger->error("Error verifying message from the merchant for MustangPay", ['exception' => $e]);
        }
    }
}
