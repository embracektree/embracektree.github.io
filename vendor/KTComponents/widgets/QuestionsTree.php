<?php
namespace app\vendor\KTComponents\widgets;

use app\vendor\KTComponents\Admin;
use Yii;
use yii\base\Widget;
use yii\widgets;
use yii\helpers\Html;

/**
 * Widget is for displaying Questions in a tree structure
 * Class QuestionsTree
 * @package app\vendor\KTComponents\widgets
 */
class QuestionsTree extends Widget
{
    public $message;
    public $title = 'Topics';
    public $htmlOptions;
    public $topic_id;
    public $topic_slug;

    /**
     * Init function to  set the default attributes
     */
    public function init()
    {
        parent::init();
        $this->htmlOptions = array(
            'id' => 'topicsContainer',
            'class' => 'topicsTree'
        );
        if ($this->title === null) {
            $this->title = 'Questions List';
        }
    }

    /**
     * Retuns the tree structure of questions when QuestionsTree widget is called
     * @return string
     */
    public function run()
    {
        $data = json_decode(Admin::getRecursiveQuestions($this->topic_id,0), true);

        if ($data) {
            $return = Html::beginTag('div', ['class' => 'topics-tree']);

            $return .= '<h3>' . $this->title . '</h3>';

            $return .= Html::beginTag('ul', ['class' => 'sidebar-menu', 'style' => 'display:block']);
            $return .= Html::beginTag('li', ['class' => 'treeview active']);

            $return .= self::_buildTree($data);

            $return .= Html::endTag('li');


            $return .= Html::endTag('ul');

            $return .= Html::endTag('div');

        } else {
            $return = Html::beginTag('div', ['class' => 'no-topics-found alert']);

            if (!(Yii::$app->user->isGuest)) {

                $return .= Html::beginTag('div', ['class' => 'new-topic-creation']);

                $return .= Html::button('<i class="fa fa-plus"> Create Topics </i>', ['class' => 'btn btn-sm btn-primary quick_create_topic', 'id' => 'create_topics']);
                $return .= Html::hiddenInput('courseIdHidden', $this->topic_id, ['class' => 'selectedCourseId']);

                $return .= Html::endTag('div');

            }
            $return .= '<h4><i class="icon fa fa-info"></i>No Topics Found</h4>';
            $return .= Html::endTag('div');
        }

        return $return;
    }

    /**
     * function to build tree structure for the questions
     * @param $data
     * @return string
     */
    private function _buildTree($data)
    {

        $return = Html::beginTag('ul', ['class' => 'treeview-menu', 'style' => 'display:block']);
        foreach ($data as  $topics) {
            $children = json_decode(Admin::getRecursiveQuestions($this->topic_id,$topics['question_id']), true);
            $liclss = ($children != null) ? 'active' : '';
            $return .= Html::beginTag('li', ['class' => $liclss, 'id' => $topics['topic_id']]);

            $return .= self::_getItem($topics, $children);
            if ($children != null) {
                $return .= self::_buildTree($children);
            }
            $return .= Html::endTag('li');
        }
        $return .= Html::endTag('ul');

        return $return;
    }

    /**
     * Returns the question item which is called internally while building widget
     * @param $topics
     * @param null $children
     * @return string
     */
    private function _getItem($topics, $children = null)
    {
        $class = ($children) ? 'fa fa-angle-right pull-left expand-child' : 'fa fa-square pull-left expand-child';

        $return = Html::a('<i class="' . $class . '"></i> <span>' . Html::encode($topics['questionsInfo'][0]['question_name']) . '</span>', ['topics/get-question-info', 'topicslug' => $this->topic_slug, 'id' => $topics['question_id'], 'slug' => $topics['slug']], ['class' => 'topic-content', 'id' => 'topic_id_' . $topics['question_id']]);

        return $return;
    }
}
