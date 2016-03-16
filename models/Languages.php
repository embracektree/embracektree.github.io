<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%sys_lang_cfg}}".
 *
 * @property integer $language_id
 * @property string $language_name
 * @property string $short_name
 * @property integer $status
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 */
class Languages extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sys_lang_cfg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language_name', 'short_name', 'status', 'created_by', 'created_date', 'modified_by', 'modified_date'], 'required'],
            [['status', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['language_name'], 'string', 'max' => 100],
            [['short_name'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'language_id' => 'Language ID',
            'language_name' => 'Language Name',
            'short_name' => 'Short Name',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_date' => 'Created Date',
            'modified_by' => 'Modified By',
            'modified_date' => 'Modified Date',
        ];
    }
}
