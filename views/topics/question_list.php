<?php
/**
 * Created by PhpStorm.
 * User: satish.banda
 * Date: 28/12/15
 * Time: 12:03 PM
 */
use app\vendor\KTComponents\widgets\QuestionsTree;
use \yii\helpers\Html;
use yii\web\JsExpression;
use wbraganca\fancytree\FancytreeWidget;
use app\vendor\KTComponents\Admin;

$this->title = $model->topic_name;
?>
<aside class="main-sidebar">
    <div class="courses-left-content">

        <section class="sidebar">

            <?php

            if (Yii::$app->user->id == Admin::ADMIN_USER_ID) {

            echo '<div class="topic-sidebar">';?>
            <div class="course-title" title="<?= $model->topic_name ?>"
                 contenteditable=<?= (!Yii::$app->user->isGuest) ? "true" : "false"; ?>>
                <h3 class="title-header-class">
                    <?= $model->topic_name ?>
                </h3>
            </div>
            <?php
            if (!Yii::$app->user->isGuest && !$previewParameter) {
            ?>

            <div class="topics_buttons_list">
                <div class="topics-creation">
                    <div class="dropdown">
                        <a href="#" class="btn btn-primary dropdown-toggle"
                           data-toggle="dropdown"><?= Yii::t('app', 'Manage Topic') ?>   <span class="caret"></span></a>
                        <ul role="menu" class="dropdown-menu">
                            <li><a href='javascript:void(0);'
                                   onclick="createSibling()"> <?= Yii::t('app', 'Create a sibling question') ?> </a>
                            </li>
                            <li><a href='javascript:void(0);'
                                   onclick="createChild()">  <?= Yii::t('app', 'Create a child question') ?>  </a></li>
                            <li><a href='javascript:void(0);'
                                   onclick="previewButton()">  <?= Yii::t('app', 'Preview') ?>  </a></li>
                        </ul>
                    </div>

                    <?php
                    echo Html::hiddenInput('courseIdHidden', $topicId, ['class' => 'selectedCourseId']);
                    echo Html::hiddenInput('treeSelectedTopicId', '', ['class' => 'selectedTopicId', 'id' => 'treeSelectedTopicId']);

                    echo '</div>';

                    ?>
                    <div class="preview-content">

                    </div>

                    <div class='save-buttons'>
                        <div class="col-md-6 col-sm-6 col-xs-6 publish_btn_container">
                            <?php $statusList = json_decode(Admin::getStatusList(), true); ?>
                            <?= Html::dropDownList('status', $infoModel['question_status'], $statusList, ['class' => 'form-control topic_status_change']); ?>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-6 savetopic_btn_container">
                            <?= Html::button(Yii::t('app', 'Save Question'), ['class' => 'btn btn-info save_topic_content']); ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>

                    <?php echo '</div>';


                    }


                    $data = Admin::getQuestionsMenu($topicId, $topicslug, $previewParameter);

                    echo FancytreeWidget::widget([
                        'options' => [

                            'source' => $data,

                            'toggleExpanded' => true,


                            'nodeSetExpanded' => true,
                            'extensions' => ['dnd'],
                            'dnd' => [
                                'preventVoidMoves' => true,
                                'preventRecursiveMoves' => true,
                                'autoExpandMS' => 400,

                                'dragStart' => new JsExpression('function(node, data) {
				                if(node.data.preview == "true")
                                    return false;
                                else
                                    return true;
		        	         }'),
                                'dragEnter' => new JsExpression('function(node, data) {
                                if(node.data.preview == "true")
                                    return false;
                                else
                                    return true;
                            }'),
                                'dragDrop' => new JsExpression('function(node, data) {

                                var dataVariable = data;
                                var nodeVariable = node;
                                var draggedNodeId = data.otherNode.data.id;
                                var droppedOnNodeId = data.node.data.id;
                                var draggedScenario = data.hitMode;
                                var language = node.data.language;

                                if(draggedNodeId && droppedOnNodeId ){

                                var node = $("#fancyree_w0").fancytree("getTree").getNodeByKey(droppedOnNodeId)
                                var addLoader = $(node.span);
                                addLoader.before("<span class=loader-inner></span>")



                                 $.ajax({
                                    type: "POST",
                                    url: ajaxUrl + "questions/update-node",
                                    data: {"draggedNodeId": draggedNodeId, "droppedOnNodeId":droppedOnNodeId,"draggedScenario":draggedScenario,"language":language},
                                    success: function (data) {
                                        $(".loader-inner").remove();
                                        var response = $.parseJSON(data)

                                        if(!response.status){
                                               alert(response.message);
                                        }else
                                            {
                                                dataVariable.otherNode.moveTo(nodeVariable, dataVariable.hitMode);
                                            }
                                        }
                                    });
                                }
                            }'),
                            ],
                            'dblclick' => new JsExpression('function(node, data){ // On click topic load\'s the selected topic
                           var node = data.node;
                           orgEvent = data.originalEvent;
                            if(node.isActive() && node.data.url){
                                  window.location.href = node.data.url;
                            }

                    }'),
                            'activate' => new JsExpression('function(node, data) {

                                  if(data.node.data.preview == "true"){
                                        window.location.href = data.node.data.url;
                                   }else{
                                        selectedNode= data.node.key
                                        $("#treeSelectedTopicId").val("");
                                        $("#treeSelectedTopicId").val(data.node.key);
                                       }

                    }'),
                        ]
                    ]);
                    echo '</div>';
                    } else if (Yii::$app->user->isGuest) {
                        ?>
                        <?=
                        QuestionsTree::widget(
                            [
                                'title' => $model->topic_name,
                                'topic_id' => $topicId,
                                'topic_slug' => $topicslug,
                            ]
                        ) ?>
                    <?php } ?>

        </section>
    </div>

</aside>
