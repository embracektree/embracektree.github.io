<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use \kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\users\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="users-index">
    <?php $statusList = ['0' => 'Inactive', '1' => 'Active']; ?>
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Users', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => '',
        'pjax'=>true,
        'striped'=>true,
        'hover'=>true,
        'panel'=>['type'=>'primary', 'heading'=>Yii::t('app','Users')],
        'columns' => [
            'user_username',
            'user_firstname',
            'user_email:email',
            [
                'attribute' => 'user_status',
                'label' => 'Status',
                'value' => function ($data, $row) use ($statusList) {
                        return $statusList[$data->user_status];
                    },
                'filter' => Html::activeDropDownList($searchModel, 'user_status', $statusList, ['class' => 'form-control', 'prompt' => '']),
            ],

            ['class' => 'kartik\grid\ActionColumn',
                'template' => '{update}{delete}'
            ],
        ],
    ]); ?>

</div>
