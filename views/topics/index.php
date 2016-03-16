<?php

use yii\helpers\Html;
use app\vendor\KTComponents\Admin;
use \kartik\grid\GridView;
use yii\helpers\Url;
use app\models\Topics;


/* @var $this yii\web\View */
/* @var $searchModel app\models\TopicsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Topics');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="courses-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php

    $statusList = json_decode(Admin::getStatusList(), true);
    $languageList = json_decode(Admin::getLanguagesList(), true);
    $categoriesList = $categoriesData['categoryList'];

    ?>


    <?php
    $gridColumns = [
        [
            'attribute'=>'topic_name',
            'value'=> function ($data) {
                        return $data->topicsInfo[0]->topic_name;
                      }
        ],
        [
            'attribute'=>'language',
            // 'width'=>'310px',
            'label'=>Yii::t('app','Language'),
            'value'=>function ($data){
                    $languagesInfo = Topics::getTopicsLanguagesInfo($data->topic_id);
                    return $languagesInfo;
                },
            'filterType'=>'',
            'filter'=>'',
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('app','Languages')],
            //'group'=>true,  // enable grouping
           // 'subGroupOf'=>1
        ],
        [
            'attribute'=>'topic_status',
            // 'width'=>'310px',
            'label'=>Yii::t('app','Topic Status'),
            'value'=>function ($data) use ($statusList) {
                    return $statusList[$data->topicsInfo[0]->topic_status];
                },
            'filterType'=>GridView::FILTER_SELECT2,
            'filter'=>$statusList,
            'filterWidgetOptions'=>[
                'pluginOptions'=>['allowClear'=>true],
            ],
            'filterInputOptions'=>['placeholder'=>Yii::t('app','Status')],
          //  'group'=>true,  // enable grouping
        ],

        ['class' => 'kartik\grid\ActionColumn',
            'template'=>'{update}{delete}',
            'urlCreator' => function ($action, $model) {
                    if ($action === 'update') {
                        $url = Url::toRoute(['/topics/update/','topic_id' => $model->topic_id, 'language' => $model->topicsInfo[0]->language]);
                        return $url;
                    }
                    if ($action === 'delete') {
                         $url = Url::toRoute(['/topics/delete','topic_id' => $model->topic_id, 'language' => $model->topicsInfo[0]->language]);
                        return $url;
                    }
                }
        ]
    ];
    echo GridView::widget([
        'dataProvider'=>$dataProvider,
        'filterModel'=>$searchModel,
        'summary' => '',
        'pjax'=>true,
        'striped'=>true,
        'hover'=>true,
        'panel'=>['type'=>'primary', 'heading'=>Yii::t('app','Topics')],
        'columns'=>$gridColumns,
    ]);

    ?>
</div>
