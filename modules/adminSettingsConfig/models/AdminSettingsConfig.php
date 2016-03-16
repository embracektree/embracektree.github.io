<?php

namespace app\modules\adminSettingsConfig\models;

use Yii;

/**
 * This is the model class for table "{{%admin_settings_cfg}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $label
 * @property string $group
 * @property string $type
 * @property string $validations
 * @property string $options
 * @property string $values
 * @property string $default_values
 * @property string $status
 * @property integer $created_by
 * @property string $created_time
 * @property integer $updated_by
 * @property string $updated_time
 */
class AdminSettingsConfig extends \yii\db\ActiveRecord
{
	const ACTIVE = 'Active';
	const INACTIVE = 'Inactive';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%admin_settings_cfg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'label', 'group', 'type', 'validations', 'options', 'values', 'default_values', 'created_by', 'created_time', 'updated_by', 'updated_time'], 'required'],
            [['type', 'validations', 'options', 'values', 'default_values', 'status'], 'string'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_time', 'updated_time'], 'safe'],
            [['name', 'label', 'group'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'label' => 'Label',
            'group' => 'Group',
            'type' => 'Type',
            'validations' => 'Validations',
            'options' => 'Options',
            'values' => 'Values',
            'default_values' => 'Default Values',
            'status' => 'Status',
            'created_by' => 'Created By',
            'created_time' => 'Created Time',
            'updated_by' => 'Updated By',
            'updated_time' => 'Updated Time',
        ];
    }
}
