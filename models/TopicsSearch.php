<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Topics;
use app\models\TopicsInfo;
use app\vendor\KTComponents\Admin;

/**
 * TopicsSearch represents the model behind the search form about `app\models\TopicsInfo`.
 */
class TopicsSearch extends TopicsInfo
{

    /**Rules is to apply for the attributes to include in the search and validation for th filters
     * @return array
     */
    public function rules()
    {
        return [
            [['topic_id', 'created_by', 'modified_by'], 'integer'],
            [['topic_name', 'created_date', 'modified_date', 'language', 'topic_status'], 'safe'],
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
     * Returns DataProvider based on the search result to display Topics model in index page and for filters
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Topics::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails

            return $dataProvider;
        }
        $parameters = Yii::$app->request->queryParams['TopicsSearch'];

        $query->joinWith(['topicsInfo']);
        $topicTableName = TopicsInfo::tableName();

        $query->andFilterWhere([
            'topic_id' => $this->topic_id,
            'created_by' => $this->created_by,
            'created_date' => $this->created_date,
            'modified_by' => $this->modified_by,
            'modified_date' => $this->modified_date,
        ]);

        $query->andFilterWhere(['!=', '' . $topicTableName . '.topic_status', Admin::RECORD_DELETE]);

        if (isset($parameters['topic_status']) && (!empty($parameters['topic_status']) || $parameters['topic_status'] == Admin::DRAFT_VALUE)) {
            $query->andFilterWhere(['' . $topicTableName . '.topic_status' => $parameters['topic_status']]);
        }

        $query->andFilterWhere(['like', '' . $topicTableName . '.topic_name', $this->topic_name]);

        return $dataProvider;
    }
}
