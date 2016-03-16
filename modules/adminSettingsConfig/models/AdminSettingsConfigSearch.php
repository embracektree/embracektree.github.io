<?php

namespace app\modules\adminSettingsConfig\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\adminSettingsConfig\models\AdminSettingsConfig;

/**
 * AdminSettingsConfigSearch represents the model behind the search form about `app\modules\adminSettingsConfig\models\AdminSettingsConfig`.
 */
class AdminSettingsConfigSearch extends AdminSettingsConfig
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by'], 'integer'],
            [['name', 'label', 'group', 'type', 'validations', 'options', 'values', 'default_values', 'status', 'created_time', 'updated_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AdminSettingsConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'created_by' => $this->created_by,
            'created_time' => $this->created_time,
            'updated_by' => $this->updated_by,
            'updated_time' => $this->updated_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'label', $this->label])
            ->andFilterWhere(['like', 'group', $this->group])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'validations', $this->validations])
            ->andFilterWhere(['like', 'options', $this->options])
            ->andFilterWhere(['like', 'values', $this->values])
            ->andFilterWhere(['like', 'default_values', $this->default_values])
            ->andFilterWhere(['like', 'status', $this->status]);

        return $dataProvider;
    }
}
