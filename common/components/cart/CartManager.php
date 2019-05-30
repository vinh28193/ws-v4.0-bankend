<?php
/**
 * Created by PhpStorm.
 * User: vinhs
 * Date: 2019-02-26
 * Time: 14:36
 */

namespace common\components\cart;


use common\components\cart\storage\MongodbCartStorage;
use common\models\User;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Yii;
use Exception;
use yii\base\Component;
use yii\base\InvalidConfigException;
use common\components\cart\storage\CartStorageInterface;
use common\components\cart\item\OrderCartItem;
use yii\helpers\ArrayHelper;

/**
 * Class CartManager
 * @package common\components\cart
 *
 * [
 *      1 => [
 *         ebay:fun_shop => [
 *              sku => [
 *                   // item data
 *              ],
 *              sku:parentSku => [
 *                  // item data
 *             ]
 *          ],
 *          ebay:shop2 => [
 *
 *          ]
 *
 * ]
 */
class CartManager extends Component
{

    const TYPE_SHOPPING = 'shopping';
    const TYPE_BUY_NOW = 'buynow';
    const TYPE_SHIPPING = 'shipping';
    const TYPE_REQUEST = 'request';

    /**
     * @var string | \common\components\cart\storage\CartStorageInterface
     */
    public $storage = 'common\components\cart\storage\MongodbCartStorage';

    /**
     * @return MongodbCartStorage|string
     * @throws InvalidConfigException
     */
    public function getStorage()
    {
        if (!is_object($this->storage)) {
            $this->storage = Yii::createObject($this->storage);
//            if (!$this->storage instanceof CartStorageInterface) {
//                throw new InvalidConfigException(get_class($this->storage) . " not instanceof common\components\cart\CartStorageInterface");
//            }
        }
        return $this->storage;
    }


    /**
     * @var \yii\web\IdentityInterface
     */
    private $_user;

    /**
     * @return null|\yii\web\IdentityInterface
     * @throws \Throwable
     */
    public function getUser()
    {
        if (!is_object($this->_user)) {
            $this->_user = Yii::$app->getUser()->getIdentity();
        }
        return $this->_user;
    }

    public function setUser($user)
    {
        if ($user instanceof \yii\web\IdentityInterface) {
            $this->_user = $user;
        } elseif (is_string($user) || is_numeric($user)) {
            /** @var  $class \yii\web\IdentityInterface */
            $class = Yii::$app->getUser()->identityClass;
            $this->_user = $class::findIdentity($user);
        }
    }


    /**
     * @param $type
     * @param $key
     * @param bool $safeOnly
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function hasItem($type, $key, $safeOnly = true)
    {
        return $this->getStorage()->hasItem($type, $key, $this->getIsSafe($safeOnly));
    }

    /**
     * @param $type
     * @param $id
     * @param bool $safeOnly
     * @return bool|mixed
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function getItem($type, $id, $safeOnly = true)
    {
        return $this->getStorage()->getItem($type, $id, $this->getIsSafe($safeOnly));
    }


    public function setMeOwnerItem($id)
    {
        return $this->getStorage()->setMeOwnerItem($id);

    }

    public function filterItem($filter, $type = null, $safeOnly = true)
    {
        return $this->getStorage()->filterItem($filter, $type, $this->getIsSafe($safeOnly));
    }

    /**
     * @param $type :shopping buynow ...
     * @param $params
     *      -type: ebay/amazon
     *      -seller: seller name
     *      -id: primary id of item
     *      -sku: sku of item (default null)
     *      -quantity: quantity
     *      -image: image
     * @param bool $safeOnly
     * @return array
     * @throws \Throwable
     */
    public function addItem($type, $params, $safeOnly = true)
    {
        $key = $this->createKeyFormParams($params);
        $cartItem = new OrderCartItem($this);
        try {
            if ($type !== CartSelection::TYPE_SHOPPING) {
                list($ok, $value) = $cartItem->createOrderFormKey($key, false);
                if (!$ok) {
                    return [false, $value];
                }
                $success = $this->getStorage()->addItem($type, $key, $value, $this->getIsSafe($safeOnly));
                return [$success, $success ? "Thêm sản phẩm vào $type thành công" : "Thêm vào $type thất bại"];
            }
            $item = $this->filterItem($this->normalKeyFilter($key), $type, $safeOnly);
            if (!empty($item)) {
                return [false, 'Sản phẩm  này đã có trong giỏ hàng'];
            } else {

                $filter = $key;
                unset($filter['products']);
                $parents = $this->filterItem($this->normalKeyFilter($filter), $type, $this->getIsSafe());
                if (count($parents) > 0) {

                    foreach ($parents as $parent) {
                        $parentProducts = $parent['key']['products'];
                        if (count($parentProducts) < 3) {
                            $parentProducts[] = $key['products'][0];
                            $parent['key']['products'] = $parentProducts;
                            list($ok, $value) = $cartItem->createOrderFormKey($parent['key'], true);
                            if (!$ok) {
                                return [false, $value];
                            }
                            $success = $this->getStorage()->setItem($parent['_id'], $parent['key'], $value, $this->getIsSafe($safeOnly));
                            return [$success, $success ? "Thêm sản phẩm vào shopping thành công" : "Thêm vào shopping thất bại"];
                        }
                    }
                }
                list($ok, $value) = $cartItem->createOrderFormKey($key, false);
                if (!$ok) {
                    return [false, $value];
                }
                $success = $this->getStorage()->addItem($type, $key, $value, $this->getIsSafe($safeOnly));
                return [$success, $success ? "Thêm sản phẩm vào shopping thành công" : "Thêm vào shopping thất bại"];
            }
        } catch (Exception $exception) {
            Yii::info($exception);
            return [false, $exception->getMessage()];
        }
    }

    public function updateItem($type, $id, $key, $params = [], $safeOnly = true)
    {
        $cartItem = new OrderCartItem($this);
        try {
            if (($item = $this->getItem($type, $id, $safeOnly)) === false) {
                return [false, 'Sản phẩm này không có trong giỏ hàng'];
            }
            $activeKey = $item['key'];
            $products = [];
            foreach (ArrayHelper::remove($activeKey, 'products', []) as $value) {
                if ($this->isDetectedProduct($value, $key)) {
                    $value = ArrayHelper::merge($value, $params);
                }
                $products[] = $value;
            }
            $activeKey['products'] = $products;
            list($ok, $value) = $cartItem->createOrderFormKey($activeKey, true);
            if (!$ok) {
                return [false, $value];
            }
            $success = $this->getStorage()->setItem($item['_id'], $activeKey, $value, $this->getIsSafe($safeOnly));
            return [$success, $success ? 'sản phẩm đã được update' : 'không thể update sản phầm này lúc này'];
        } catch (Exception $exception) {
            Yii::info($exception);
            return [false, $exception->getMessage()];
        }
    }

    public function removeItem($type, $id, $key = null, $safeOnly = true)
    {
        $cartItem = new OrderCartItem($this);
        if ($key === null) {
            $success = $this->getStorage()->removeItem($id);
            return [$success, $success ? 'Giỏ hàng này đã được xóa' : 'Không thể xóa'];
        } else {
            if (($item = $this->getItem($type, $id, $safeOnly)) === false) {
                return [false, 'Sản phẩm không có trong giỏ hàng'];
            }
            $activeKey = $item['key'];
            $products = [];
            foreach (ArrayHelper::remove($activeKey, 'products', []) as $value) {
                if ($this->isDetectedProduct($value, $key)) {
                    continue;
                }
                $products[] = $value;
            }
            if (empty($products)) {
                $success = $this->getStorage()->removeItem($id);
                return [$success, $success ? 'Giỏ hàng này đã được xóa' : 'Không thể xóa'];
            }
            $activeKey['products'] = $products;
            list($ok, $value) = $cartItem->createOrderFormKey($activeKey, true);
            if (!$ok) {
                return [false, $value];
            }
            $success = $this->getStorage()->setItem($item['_id'], $activeKey, $value);
            return [$success, $success ? "Giỏ hàng của bạn đã được thay đổi" : "Giỏ hàng thay đổi không thành công"];
        }
    }

    public function createKeyFormParams($params = [])
    {
        $pKeys = ['source', 'sellerId'];
        $p = [];
        foreach ($pKeys as $k) {
            if (($v = ArrayHelper::getValue($params, $k)) === null || $v === '') {
                continue;
            }
            $p[$k] = $v;
        }
        $c = [];
        foreach ($params as $g => $v) {
            if (ArrayHelper::isIn($g, $pKeys) || ($v === null || $v === '')) {
                continue;
            }
            $c[$g] = $v;
        }
        $supporter = null;
//        if (($supportId = $this->getStorage()->calculateSupported()) > 0) {
//            $supporter = User::find()->select(['id', 'mail'])->where(['id' => $supportId[0]['_id']])->one();
//        }

        return ArrayHelper::merge(OrderCartItem::defaultKey(), $supporter !== null ? ['supportAssign' => ['id' => $supporter->id, 'email' => $supporter->email], 'supportId' => $supporter->id] : [], $p, ['products' => [$c]]);
    }

    public function normalKeyFilter($key)
    {
        $useKey = [
            'sellerId', 'source', 'products'
        ];
        $n = [];
        foreach ($key as $k => $v) {
            if (!ArrayHelper::isIn($k, $useKey)) {
                continue;
            }
            if ($k === 'products') {
                $v = array_shift($v);
                $nv = [];
                foreach ($v as $i => $j) {
                    if ($i === 'id' || $i === 'sku') {
                        $nv[$i] = $j;
                    };
                }
                $v = $nv;
            }
            $n[$k] = $v;
        }
        return ['key' => $n];
    }

    /**
     * @param null $type
     * @param null $ids
     * @param bool $safeOnly
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function getItems($type = null, $ids = null, $safeOnly = true)
    {

        return $this->getStorage()->getItems($this->getIsSafe($safeOnly), $type, $ids);

    }


    /**
     * @param null $type
     * @param bool $safeOnly
     * @return mixed
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function countItems($type = null, $safeOnly = true)
    {
        return $this->getStorage()->countItems($this->getIsSafe($safeOnly), $type);
    }

    /**
     * @param bool $safeOnly
     * @return bool
     * @throws InvalidConfigException
     * @throws \Throwable
     */
    public function removeItems($safeOnly = true)
    {
        if (($user = $this->getUser()) === null && $safeOnly) {
            $safeOnly = false;
        }
        return $this->getStorage()->removeItems($safeOnly ? $user->getId() : null);
    }

    /**
     * @param $params
     * @return mixed
     * @throws InvalidConfigException
     */
    public function filterShoppingCarts($params)
    {
        return $this->getStorage()->filterShoppingCarts($params);
    }

    /**
     * @param bool $force
     * @return int|string|null
     * @throws \Throwable
     */
    public function getIsSafe($force = false)
    {
        if (($user = $this->getUser()) !== null && $force) {
            return $user->getId();
        }
        return null;
    }

    /**
     * @param $type
     * @param $id
     * @param $param
     * @return array
     * @throws \Throwable
     */
    public function updateSafeItem($type, $id, $param)
    {
        try {
            if (($item = $this->getItem($type, $type, false)) === false) {
                return [false, 'Sản phẩm này không có trong giỏ hàng'];
            }
            // todo : thay đổi giá trị của $item['key']
            $value = (new OrderCartItem())->updateItemBuyKey($item['key']);
            $success = $this->getStorage()->updateSafeItem($type, $id, $item['key'], $value, false);
        } catch (Exception $exception) {
            return [false, $exception->getMessage()];
        }
    }

    /**
     * @param $target
     * @param null $source
     * @param string $convertType
     * @return bool|mixed
     */
    public function isDetectedProduct($target, $source)
    {
        $lv1 = trim($target['id']) === ($source['id']);
        $lv2 = isset($source['sku']);
        $lv2 = $lv2 ? (!isset($target['sku']) ? false : $source['sku'] === $target['sku']) : true;
        return $lv1 && $lv2;
    }

    public function getSupportAssign()
    {
        $authManager = Yii::$app->authManager;
        $saleIds = $authManager->getUserIdsByRole('sale');
        $masterSaleIds = $authManager->getUserIdsByRole('master_sale');
        $supporters = User::find()->select(['id', 'email'])->where(['or', ['id' => $saleIds], ['id' => $masterSaleIds]])->all();
        if(($calculateToday = $this->getStorage()->calculateSupported()) > 0){

        }
    }
}
