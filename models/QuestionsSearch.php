<?php

namespace app\models;

use app\vendor\KTComponents\Admin;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Topics;
use app\models\TopicsInfo;

/**
 * QuestionsSearch represents the model behind the search form about `app\models\Questions`.
 */
class QuestionsSearch extends Questions
{
    /**
     * @inheritdoc
     */
    public $language;
    /*
     * Public variable for topic status
     */
    public $topicStatus;

    /**
     * Returns the validations and search fields of the Questions model
     * @return array
     */
    public function rules()
    {
        return [
            [['question_id', 'topic_id', 'created_by', 'modified_by'], 'integer'],
            [['question_name', 'created_date', 'modified_date', 'language', 'topicStatus'], 'safe'],
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
     * Returns DataProvider based on the search result to display Questions model in index page and for filters
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Questions::find();
        $topicsTableName = QuestionsInfo::tableName();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails

            return $dataProvider;
        }
        $parameters = Yii::$app->request->queryParams['QuestionsSearch'];

        $query->joinWith('questionsInfo');

        $query->andFilterWhere([
            'question_id' => $this->question_id,
            $topicsTableName . '.topic_id' => $this->topic_id,
            'created_by' => $this->created_by,
            'created_date' => $this->created_date,
            'modified_by' => $this->modified_by,
            'modified_date' => $this->modified_date,
        ]);

        if (isset($parameters['topicStatus']) && (!empty($parameters['topicStatus']) || $parameters['topicStatus'] == Admin::DRAFT_VALUE)) {
            $query->andFilterWhere(['' . $topicsTableName . '.question_status' => $parameters['topicStatus']]);
        }

        $query->andFilterWhere(['like', '' . $topicsTableName . '.question_name', $this->question_name]);

        return $dataProvider;
    }
}
