<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-03-07
 * Time: 15:59
 */

namespace common\components;


use common\models\StoreAdditionalFee;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;
/**
 * new update co collection
 * đối tượng collection cho phép cóp nhặt 1 list data từ 1 nguồn sẵn có hoặc tự được thêm vào
 * các [[IteratorAggregate]] được lưu dưới dạng key => array value
 * $collection = new AdditionalFeeCollection()
 * $collection['test'] => 123
 * $collection->set('test',123);
 * unset($collection['test']);
 * $collection->remove('test')
 * Class AdditionalFeeCollection
 * @package common\components
 */
class AdditionalFeeCollection extends ArrayCollection
{
    use StoreAdditionalFeeRegisterTrait;

    /**
     * cho phép get dữ liệu từ một nguồn được định nghĩa từ [[ActiceRecore]]
     * @param ActiveRecord $owner
     */
    public function loadFormOwner(ActiveRecord $owner)
    {
        $tableName = $owner::tableName();
        $ownerClass = get_class($owner);
        $ownerId = $owner->getPrimaryKey(false);
        $query = new Query();
        $query->select(['c.id', 'c.type', 'c.name', 'c.amount', 'c.local_amount', 'c.discount_amount', 'c.currency']);
        $query->from(['c' => 'product_fee']);
        $query->where(['and', ['c.' . 'product_id' => $ownerId]]);
        $additionalFees = $query->all($ownerClass::getDb());
        $additionalFees = ArrayHelper::index($additionalFees, null, function ($element) {
            return $element['type'];
        });
        Yii::info($additionalFees, 'loadFormOwner');
        $this->mset($additionalFees);
    }

    /**
     * return all keys exist on collection
     * @return array
     */
    public function keys()
    {
        return array_keys($this->toArray());
    }

    /**
     * lấy tất cả các loại theo tên
     * multi get
     * @param null $keys if null meaning get all in config [[StoreAdditionalFee]]
     * @param array $except
     * @return array
     */
    public function mget($keys = null, $except = [])
    {
        if ($keys === null) {
            $keys = array_keys($this->storeAdditionalFee);
        }
        if (!is_array($keys)) {
            $keys = [$keys];
        }
        $results = [];

        foreach ($keys as $key) {
            if (in_array($key, $except)) {
                continue;
            }
            $results[$key] = $this->get($key, [], false);
        }
        return $results;
    }

    /**
     *    [
     *     'origin_fee' => [
     *          [
     *              'type' => 'origin_fee',
     *              'name' => 'Phi X',
     *              'value' => 123
     *              ...
     *          ],
     *          [
     *              'type' => 'origin_fee',
     *              'name' => 'Phi X',
     *              'value' => '6666'
     *              ...
     *          ]
     *      ],
     * '    tax' => [
     *          [
     *              'type' => 'tax',
     *              'name' => 'Phi tax',
     *              'value' => 123
     *              ...
     *          ],
     *          [
     *              'type' => 'tax',
     *              'name' => 'Phi tax',
     *              'value' => '6666'
     *              ...
     *          ]
     *      ]
     * ]
     * @param $values
     * mset giờ chỉ có thể sử dụng dữ liệu từ mget
     * @param bool $withCondition
     * @param bool $ensureReadOnly
     */
    public function mset($values)
    {
        if (is_array($values)) {
            $this->removeAll();
            foreach ($values as $key => $value) {
                $this->set($key, $value);
            }
        }
    }

    /**
     * tạo 1 kiểu dữ liệu lưu trữ
     * call từ [[withCondition]] các condition tương ứng sẽ được thực thi
     * @param StoreAdditionalFee $config
     * @param AdditionalFeeInterface $additional
     * @param $amount
     * @param int $discountAmount
     * @param null $currency
     * @return array
     */
    protected function createItem(StoreAdditionalFee $config, AdditionalFeeInterface $additional, $amount, $discountAmount = 0, $currency = null)
    {
        $amountLocal = $amount;
        if ($config->hasMethod('executeCondition') &&
            ($result = $config->executeCondition($amount, $additional)) !== false &&
            is_array($result)
        ) {
            list($amount, $amountLocal) = $result;
        }
        return [
            'type' => $config->name,
            'name' => $config->label,
            'amount' => $amount,
            'local_amount' => $amountLocal,
            'discount_amount' => $discountAmount,
            'currency' => $currency === null ? $config->currency : $currency,
        ];
    }

    /**
     * hàm này được tách ra để được dễ dàng thay đổi cũng như nâng cấp
     * giờ hoạt động khác với [[parent::set]] (trước khi update)
     * đảm bảo bởi [[parent:add]] cho phém thêm dữ liệu được tính toán mới
     * vào list đã tồn tại trước đó hoặc thêm mới nếu chưa tồn tại
     * ví dụ :
     *      $collection->set('fee',arrayValue);
     *      $collection->withCondition($owner,'test',1243)
     *      var_dump($collection->get('fee')) // return array 2 giá trị
     *  lưu ý, set với add khác nhau,
     *      - set : thay thế hẳn dữ liệu bởi key mặc dù trước đó đã tồn tại,
     *      - add : thêm dữ vào cuối cùng của key.
     *  lưu ý khi dùng, hàm này mỗi lần dùng sẽ add thêm, tránh việc duplicate dữ liệu, remove trước khi tính toán lại
     * @param AdditionalFeeInterface $owner
     * @param $key string
     * @param $value
     */
    public function withCondition(AdditionalFeeInterface $owner, $key, $value)
    {
        if (($storeAdditionalFee = $this->getStoreAdditionalFeeByKey($key)) !== null && $storeAdditionalFee instanceof StoreAdditionalFee) {
            $item = $this->createItem($storeAdditionalFee, $owner, $value);
            $this->add($key, $item);
        } else {
            Yii::warning("failed when set unknown additional fee '$key'", __METHOD__);
        }
    }

    /**
     * @param AdditionalFeeInterface $owner
     * @param $values array
     * @param bool $ensureReadOnly
     */
    public function withConditions(AdditionalFeeInterface $owner, $values, $ensureReadOnly = true)
    {
        foreach ($values as $key => $value) {
            $this->withCondition($owner, $key, $value);
        }
        if ($ensureReadOnly) {
            $breaks = $this->keys();
            foreach ($this->storeAdditionalFee as $name => $storeAdditionalFee) {
                /** @var $storeAdditionalFee StoreAdditionalFee */
                if (in_array($name, $breaks)) {
                    continue;
                }
                $this->withCondition($owner, $name, $storeAdditionalFee->fee_rate);
            }
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasStoreAdditionalFeeByKey($key)
    {
        return isset($this->storeAdditionalFee[$key]);
    }

    /**
     * @param $key
     * @param null $default
     * @return StoreAdditionalFee|null
     */
    public function getStoreAdditionalFeeByKey($key, $default = null)
    {
        return isset($this->storeAdditionalFee[$key]) ? $this->storeAdditionalFee[$key] : $default;
    }


    /**
     * trả về tổng phí của từng loại phí
     *  return [tiền gốc, tiền local]
     *  ví dụ
     *      'testfee' => [
     *          [
     *              'amount' => 5,
     *              'local_amount => 12'
     *          ],
     *          [
     *              'amount' => 2,
     *              'local_amount => 10'
     *          ]
     *      ]
     * return [7,22] // 7 = 5 +2 ; 22 = 12 + 10
     * @param null $names, null nghĩa là trả về tất cả cả loại phí có trong config
     * @param array $except
     * @return array
     */
    public function getTotalAdditionFees($names = null, $except = [])
    {
        $totalFees = 0;
        $totalLocalFees = 0;
        foreach ((array)$this->mget($names) as $name => $array) {
            if (in_array($name, $except)) {
                continue;
            }
            if (isset($array[0])) {
                foreach ($array as $item) {
                    $totalFees += isset($item['amount']) ? $item['amount'] : 0;
                    $totalLocalFees += isset($item['local_amount']) ? $item['local_amount'] : 0;
                }
            } else {
                $totalFees += isset($array['amount']) ? $array['amount'] : 0;
                $totalLocalFees += isset($array['local_amount']) ? $array['local_amount'] : 0;
            }

        }
        return [$totalFees, $totalLocalFees];

    }

}
