<?php

/*File used to display child categories based kt173*/
use yii\helpers\Url;
use app\vendor\KTComponents\Admin;
use \yii\helpers\Html;

?>

<div class=" col-md-4 ">
    <?php
    $boxClassNameList = Admin::getBoxClass();
    $class = Admin::getClassName($key);
    $className = $boxClassNameList[$class];
    $mainModel = $model;
    $model = $model['topicsInfo'][0];
    $topQuestionList = json_decode(Admin::getTopQuestions($model['topic_id']), true);
    ?>
    <div class="box <?= $className ?>">
        <div class="box-header with-border">

            <h3 class="box-title">
                <strong>
                    <?= $model['topic_name'] ?>
                </strong>
            </h3>

        </div>
        <!-- /.box-header -->
        <div class="box-body category-section inner-box">
            <?php
            $topQuestionList = json_decode(Admin::getTopQuestions($model['topic_id']), true);
            if ($topQuestionList) {
                echo '<ul>';
                foreach ($topQuestionList as $question) {
                    $questionMainModel = $question;
                    $question = $question['questionsInfo'][0];
                    $questionUrl = Url::toRoute(['topics/get-question-info', 'topicslug' => $mainModel['slug'], 'id' => $questionMainModel['question_id'], 'slug' => $questionMainModel['slug']]);

                    if ($question) {
                        ?>
                        <li>
                            <a href="<?= $questionUrl ?>"
                               title="<?= Yii::t('app', 'Question Name') ?>"><?= $question['question_name'] ?></a>
                        </li>
                    <?php
                    }
                }
                echo '</ul>';
            } else {
                $link = (Yii::$app->user->IsGuest) ? '' : '<a href="javascript:void(0);" onclick="createQuestion(' . $model['topic_id'] . ')"> ' . Yii::t('app', 'Create a new question') . '</a>';
                echo '<div class="no-questions" style="color: #f39c12">
                    ' . Yii::t('app', 'Questions not available') . '
                    <p>' . $link . '
                    </div>';
            }

            ?>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>
