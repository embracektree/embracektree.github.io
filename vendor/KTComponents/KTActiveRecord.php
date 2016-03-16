<?php
namespace app\vendor\KTComponents;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\db\ActiveQuery;

/**Class to extend the default functions and customise them.
 * Class KTActiveRecord
 * @package app\vendor\KTComponents
 */
class KTActiveRecord extends \yii\db\ActiveRecord
{

    const DEFAULT_LANGUAGE = 'en';

    /**
     * @param string $sql
     * @param array $params
     * @return $this|ActiveQuery
     */
    public static function findBySql($sql, $params = [])
    {
        $query = static::find();
        $query->sql = $sql;

        return $query->params($params);
    }

    /**
     * @param mixed $condition
     * @return $this|\yii\db\ActiveQueryInterface
     * @throws \yii\base\InvalidConfigException
     */
    protected static function findByCondition($condition)
    {
        $query = static::find();

        if (!ArrayHelper::isAssociative($condition)) {
            // query by primary key
            $primaryKey = static::primaryKey();
            if (isset($primaryKey[0])) {
                $pk = $primaryKey[0];
                if (!empty($query->join) || !empty($query->joinWith)) {
                    $pk = static::tableName() . '.' . $pk;
                }
                $condition = [$pk => $condition];
            } else {
                throw new InvalidConfigException('"' . get_called_class() . '" must have a primary key.');
            }
        }

        return $query->andWhere($condition);
    }


}
