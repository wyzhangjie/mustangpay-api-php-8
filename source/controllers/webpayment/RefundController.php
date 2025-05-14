<?php

namespace app\controllers\webpayment;

use app\constants\MustangpayApiConstantsV1;
use app\controllers\BaseController;
use app\enums\OperationEnum;
use app\extensions\preorder\PreOrderWithManyPayMethod;
use app\extensions\preorder\PreOrderWithOutPayMethod;
use app\extensions\preorder\PreOrderWithPayMethodCard;
use app\extensions\preorder\PreOrderWithPayMethodEft;
use app\models\Amount;
use app\models\CreateRefundReq;
use app\models\OrderStatusRep;
use app\models\RefundStatusReq;
use app\utils\MustangpayApiUtilsV1;
use Yii;

class RefundController extends BaseController
{
    /**
     * Endpoint: /webpayment/refund/refund-query-test
     * Test refund query functionality
     */
    public function actionRefundQueryTest()
    {
        Yii::$app->response->format = 'json';
        $amount = new Amount(1000, 'ZAR'); // 1000 South African Rand
        $createCashierReq = new RefundStatusReq();
        $createCashierReq->setMerchantOrderNo("a8f03d54-22ce-4093-aa2d-17d74932b5f2");
        $createCashierReq->setMerchantId("4449999220"); // Alternatively use MustangpayApiConstantsV1.merchantId

        // Call the API
        $operationCode = "RefundQuery"; // Adjust according to actual requirements
        return MustangpayApiUtilsV1::callTest("RefundQueryTest", $createCashierReq, $operationCode);
    }

    /**
     * Endpoint: /webpayment/refund/refund-test
     * Test refund creation functionality
     */
    public function actionRefundTest()
    {
        Yii::$app->response->format = 'json';
        $amount = new Amount(1000, 'ZAR');
        $uniqueReference = Yii::$app->security->generateRandomString();

        // Create CreateRefundReq object
        $createRefundReq = new CreateRefundReq();
        $createRefundReq->merchantId = MustangpayApiConstantsV1::MERCHANT_ID;
        $createRefundReq->merchantName = 'Merchant Name';
        $createRefundReq->merchantOrderNo = $uniqueReference;
        $createRefundReq->originalMerchantOrderNo = "3076878416142605";
        $createRefundReq->country = 'ZAF';
        $createRefundReq->businessType = 'Refund';
        $createRefundReq->remark = 'remark_83c200fa64ff';
        $createRefundReq->callbackUrl = 'callback.url';
        $createRefundReq->amount = $amount;

        $operationCode = OperationEnum::REFUND_CREATE;
        return MustangpayApiUtilsV1::callTest("RefundTest", $createRefundReq, $operationCode);
    }
}
