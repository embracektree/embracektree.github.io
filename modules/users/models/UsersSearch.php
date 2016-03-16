<?php

namespace app\modules\users\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\users\models\Users;

/**
 * UsersSearch represents the model behind the search form about `app\modules\adminSettings\models\Users`.
 */
class UsersSearch extends Users
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_status', 'user_created_by', 'user_modified_by'], 'integer'],
            [['user_username', 'user_firstname', 'user_lastname', 'user_password', 'user_email', 'roles', 'groups', 'user_type', 'user_created_date', 'user_modified_date'], 'safe'],
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
        $query = Users::find();

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
            'user_id' => $this->user_id,
            'user_status' => $this->user_status,
            'user_created_by' => $this->user_created_by,
            'user_created_date' => $this->user_created_date,
            'user_modified_by' => $this->user_modified_by,
            'user_modified_date' => $this->user_modified_date,
        ]);

        $query->andFilterWhere(['like', 'user_username', $this->user_username])
            ->andFilterWhere(['like', 'user_firstname', $this->user_firstname])
            ->andFilterWhere(['like', 'user_lastname', $this->user_lastname])
            ->andFilterWhere(['like', 'user_password', $this->user_password])
            ->andFilterWhere(['like', 'user_email', $this->user_email])
            ->andFilterWhere(['like', 'roles', $this->roles])
            ->andFilterWhere(['like', 'groups', $this->groups])
            ->andFilterWhere(['like', 'user_type', $this->user_type]);
	
	$query->andFilterWhere(['!=','user_status', 2]);
	
        return $dataProvider;
    }
}
