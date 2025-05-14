<?php
/**
 * Create By: zsz
 * File: OperationEnum.php
 * Time: 2024/12/2 14:32
 */

namespace app\enums;

class OperationEnum
{
    const PRECREATE = 'preCreate';
    const CHECKORDER = 'checkOrder';
    const H2H_PRECREATE = 'h2hPreCreate';
    const REFUND_CREATE = 'refundCreate';
    const REFUND_QUERY = 'refundQuery';

    // all operation

    public static function list()
    {
        return [
            self::PRECREATE => 'Merchant Pre-Order',
            self::CHECKORDER => 'Merchant Order Status Inquiry',
            self::H2H_PRECREATE => 'Merchant H2H Pre-Order',
            self::REFUND_CREATE => 'Merchant Refund',
            self::REFUND_QUERY => 'Merchant Refund Inquiry',
        ];
    }

    // Descriptions for each operation
    public static function getDescription($code)
    {
        $list = self::list();

        return isset($list[$code]) ? $list[$code] : null;
    }

    public static function getOperationCode($operationName)
    {
        $operationCode = null;
        switch ($operationName) {
            case self::PRECREATE:
                $operationCode = 'preCreate';
                break;
            case self::CHECKORDER:
                $operationCode = 'checkOrder';
                break;
            case self::H2H_PRECREATE:
                $operationCode = 'h2hPreCreate';
                break;
            case self::REFUND_CREATE:
                $operationCode = 'refundCreate';
                break;
           case self::REFUND_QUERY:
        }
    }
}
