<?php
namespace common\logs;

use common\models\User;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PackingLogs extends \common\modelsMongo\PackingLogs
{
    const PACKING_CREATE = 'CREATE';
    const PACKING_UPDATE = 'UPDATE';
    const PACKING_MAP_PRODUCT = 'MAP_PRODUCT';
    const PACKING_MERGE_TRACKING_SELLER = 'MERGE_TRACKING_SELLER';
    const PACKING_MERGE_PACKING = 'MERGE_PACKAGE';
    const PACKING_REMOVE = 'REMOVE';

    const STATUS_SELLER_SHIPPED = 'SELLER_SHIPPED';
    const STATUS_STOCK_IN_US = 'STOCK_IN_US';
    const STATUS_STOCK_OUT_US = 'STOCK_OUT_US';
    const STATUS_STOCK_IN_LOCAL = 'STOCK_IN_LOCAL';
    const STATUS_STOCK_OUT_LOCAL = 'STOCK_OUT_LOCAL';
    const STATUS_DELIVERY = 'STOCK_DELIVERY';
    public function save($runValidation = true, $attributeNames = null)
    {
        try{
            $this->created_at = date('Y-m-d H:i:s');
            $this->user_name = 'guest';
            /** @var User $user */
            $user = \Yii::$app->user->getIdentity();
            if($user){
                $this->user_name = $user->username;
                $this->user_id = $user->id;
                $this->user_email = $user->email;
            }
            return parent::save($runValidation, $attributeNames); // TODO: Change the autogenerated stub
        }catch (\Exception $exception){
            \Yii::debug($exception);
            //Todo save to file if mongo error.
//            $fileDirPath = 'file/tracking_logs';
//            if (!file_exists($fileDirPath)) {
//                @mkdir($fileDirPath, 0777, true);
//            }
//            $writer = new Xlsx($spreadsheet);
//            $writer->save($fileName);
            return false;
        }
    }
}