<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\adminSettings\models\Users */

$this->title = 'Create Users: ' . ' ' . $model->user_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="users-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
