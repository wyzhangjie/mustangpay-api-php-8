<?php

namespace app\models;

use yii\base\Model;
use yii\validators\RequiredValidator;

class RefundStatusReq extends Model
{
    public $merchantId;
    public $merchantOrderNo;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchantId', 'merchantOrderNo'], 'required', 'message' => '{attribute} is empty'],
        ];
    }

    /**
     * Custom validation method
     */
    public function validateAttributes()
    {
        if (!$this->validate()) {
            return false;
        }

        return true;
    }

    /**
     * Get the merchant ID
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Set the merchant ID
     * @param string $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    /**
     * Get the merchant order number
     * @return string
     */
    public function getMerchantOrderNo()
    {
        return $this->merchantOrderNo;
    }

    /**
     * Set the merchant order number
     * @param string $merchantOrderNo
     */
    public function setMerchantOrderNo($merchantOrderNo)
    {
        $this->merchantOrderNo = $merchantOrderNo;
    }
}
