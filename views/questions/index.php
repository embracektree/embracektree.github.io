<?php

use yii\helpers\Html;
use app\vendor\KTComponents\Admin;
use \kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Questions;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TopicsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Questions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="topics-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php

    $statusList = json_decode(Admin::getStatusList(), true);
    $languageList = json_decode(Admin::getLanguagesList(), true);
    $topicsList = json_decode(Admin::getTopicsInformation(), true);

    ?>

    <?php

    $gridColumns = [
        //['class' => 'kartik\grid\SerialColumn'],
        [
            'attribute'=>'topics_id',
            'label'=>Yii::t('app','Topic Name'),
            'value'=>function ($data)  {
                    return $data->topic->topicsInfo[0]->topic_name;
                },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$topicsList,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('app','Topic Name')],
            //'group'=>true,  // enable grouping
        ],

          [
            'attribute'=>'question_name',
            'width'=>'310px',
             'value'=>function($data){
                     return $data->questionsInfo[0]->question_name;
                 }
           ],

        [
            'attribute'=>'language',
            'label'=>Yii::t('app','Language'),
            'value'=>function ($data){
                    $languagesInfo = Questions::getLanguagesInfo($data->question_id);
                    return $languagesInfo;
                },
            'filterType'=>'',
            'filter'=>'',
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true,'multiple'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('app','Languages')],
            //'group'=>true,  // enable grouping
            //'subGroupOf'=>1
        ],
        [
            'attribute'=>'topicStatus',
            'label'=>Yii::t('app','Topic Status'),
            'value'=>function ($data) use ($statusList) {
                    return $statusList[$data->questionsInfo[0]->question_status];
                },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$statusList,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('app','Status')],
           // 'group'=>true,  // enable grouping
        ],


        ['class' => 'kartik\grid\ActionColumn',
            'template'=>'{update}{delete}',
            'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'update') {
                        $url = Url::toRoute(['topics/get-question-info','topicslug' => $model->topic->slug,  'id' => $model->question_id, 'slug' => $model->slug]);
                        return $url;
                    }
                    if ($action === 'delete') {
                        $url = Url::toRoute(['questions/delete','question_id' => $model->question_id, 'language' => $model->questionsInfo[0]->language]);
                        return $url;
                    }
                }
        ]
    ];
    echo GridView::widget([
        'dataProvider'=>$dataProvider,
        'filterModel'=>$searchModel,
        'summary' => '',
        'showPageSummary'=>true,
        'pjax'=>true,
        'hover'=>true,
        'panel'=>['type'=>'primary', 'heading'=>Yii::t('app','Questions List')],
        'columns'=>$gridColumns

    ]);

    ?>

</div>
