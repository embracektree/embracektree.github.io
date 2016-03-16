<?php
use yii\widgets\ListView;
use yii\bootstrap\Modal;

?>

<?php

$this->title = '';

?>

<div class="main-topic-list">
    <?php if (!Yii::$app->user->isGuest) { ?>
        <div class="course-creation">
            <?php
            Modal::begin([
                'options' => [
                    'id' => 'kartik-modal-topic',
                ],
                'clientOptions' => [
                    'backdrop' => 'static',
                    'keyboard' => false,
                ],
                'header' => '<h4 style="margin:0; padding:0">' . Yii::t('app', 'Create Topics') . '</h4>',
                'toggleButton' => ['label' => '<i class="fa fa-plus"> ' . Yii::t('app', 'Create Topics') . '</i>', 'class' => 'btn btn-sm btn-primary quick_topic_creation', 'id' => 'create_courses'],
            ]);
            echo $this->render('/topics/_quick_course_create', ['model' => $mainModel, 'topicInfoModel' => $infoModel]);

            Modal::end();
            ?>
        </div>
    <?php } ?>
    <div class="main-content-title"><strong><p> <?= Yii::t('app', 'Browse Popular Topics') ?></p></strong></div>
    <div class="box-body category-section inner-box">
        <?php if ($dataProvider->totalCount > 0) { ?>
            <?=
            ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{items}{pager}',
                'itemView' => '/topics/topics_list_view',
            ]);
            ?>
        <?php } else { ?>
            <div class="col-md-4">
                <div class=" no-courses-found alert ">
                    <h4><i class="icon fa fa-info"></i></h4>
                    No Topics Found
                </div>
            </div>

        <?php } ?>
    </div>
</div>
