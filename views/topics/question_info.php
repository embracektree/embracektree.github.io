<?php
/**
 * Created by PhpStorm.
 * User: satish.banda
 * Date: 28/12/15
 * Time: 11:29 AM
 */

use Yii\helpers\Html;


?>

<div class="container-fluid courses-div">
    <div class="row-fluid">

        <?php $this->beginBlock('topiclist'); ?>
        <?php echo $this->render('question_list', ['model' => $model->topic->topicsInfo[0], 'topicId' => $model->topic->topic_id, 'topicslug' => $model->topic->slug, 'infoModel' => $infoModel, 'previewParameter' => $previewParameter]) ?>
        <?php $this->endBlock(); ?>

        <div class="contentSaveResponse"></div>

        <div class="heading">
            <?php if (!Yii::$app->user->isGuest && !$previewParameter) { ?>
                <div class="topic_error_desc help-block"></div>
                <div class="wid-block">
                    <h3 class="editable_div" contenteditable="true"><?= $infoModel['question_name'] ?></h3>
                </div>
            <?php } else { ?>
                <h2><?= $infoModel['question_name'] ?></h2>
            <?php } ?>

        </div>
        <?= Html::hiddenInput('topicId', $model['question_id'], ['class' => 'topic_id_hidden']); ?>
        <?= Html::hiddenInput('firstNode', $firstNode, ['class' => 'firstNodeId']); ?>
        <?= Html::hiddenInput('topicLanguage', $infoModel['language'], ['class' => 'topic_language_hidden']); ?>
    </div>

    <div class="topic-desc content-description">
        <div class="<?= (!Yii::$app->user->isGuest && !$previewParameter) ? 'editor' : ''; ?>">

            <?= $infoModel['question_description'] ?>

        </div>
    </div>

</div>
