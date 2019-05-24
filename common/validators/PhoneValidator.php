<?php


namespace common\validators;


use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\validators\RegularExpressionValidator;


class PhoneValidator extends RegularExpressionValidator
{

    public $networks = [
        'viettel' => '^(\+?84|0)(3[2-9]|86|9[6-8])\d{7}$',
        'vinaPhone' => '^(\+?84|0)(8[1-5]|88|9[14])\d{7}$',
        'mobiFone' => '^(\+?84|0)(70|7[6-9]|89|9[03])\d{7}$',
        'vietNamMobile' => '^(\+?84|0)(5[68]|92)[\d]{7}$',
        'gMobile' => '^(\+?84|0)([59]9|95)[\d]{7}$',
        'default' => '^(\+?84|0)?(((20[3-9]|21[0-6]|21[89]|22[0-2]|22[5-9]|23[2-9]|24[2-5]|248|25[12]|25[4-9]|26[0-3]|27[0-7]|28[2-5]|29([0-4]|[67])|299)\d{7})|((246[236]|247[13]|286[23]|287[13])\d{6}))$'
    ];
    public $exceptNetworks = [];

    public $phoneFormat = '/^(\+?84|0)?(\d+)$/';

    public function init()
    {
        $patterns = [];
        foreach ($this->networks as $name => $pattern) {
            if (ArrayHelper::isIn($name, $this->exceptNetworks)) {
                continue;
            }
            $patterns[] = $pattern;
        }
        if (!empty($pattern)) {
            $this->pattern = "~(" . implode(")|(", $patterns) . ")~";
        } else {
            throw new InvalidConfigException('Your networks setup is not valid!');
        }
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (($result = parent::validateAttribute($model, $attribute)) === null) {
            if ($this->phoneFormat !== null) {
                $model->{$attribute} = preg_replace($this->phoneFormat, '0$2', $model->{$attribute});
            }
        }
        return $result;
    }
}