<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Topics */

$this->title = Yii::t('app', 'Update Topics') . ' ' . $model->topic_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Topics'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="courses-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'mainModel' => $mainModel,
        'mediaModel' => $mediaModel
    ]) ?>

</div>
