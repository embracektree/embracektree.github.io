<?php
use yii\widgets\Breadcrumbs;
use app\vendor\KTComponents\Alert;

?>

<div class="content-wrapper">
    <?php
    if ($this->title !== null) {
        echo '<section class="content-header content-search-header">';
        //echo '<h1>' . \yii\helpers\Html::encode($this->title) . '</h1>';
        if (Yii::$app->controller->id == 'site') {
            echo '<div class="knx-auto-search-main">';
            echo '<i class="fa fa-search search-btn"></i>';
            echo $this->render('autocomplete');
            echo '<button class="search_button"><i class="fa fa-search search-btn"></i> <span>Search</span></button>';
            echo '</div>';
        }
        echo Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        );
        echo '</section>';

    }
    ?>

    <section class="content">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<div class="go-up" style="right: 20px;display:none;">
    <i class="fa fa-chevron-up"></i>
</div>


<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
