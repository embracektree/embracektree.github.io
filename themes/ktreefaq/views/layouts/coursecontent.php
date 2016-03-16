<?php
use yii\widgets\Breadcrumbs;
use app\vendor\KTComponents\Alert;

?>
<div class="content-wrapper two-column">

    <?php
    if ($this->title !== null) {
        echo '<section class="content-header">';
        echo Breadcrumbs::widget(
            [
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]
        );
        echo '</section>';

    }
    ?>

    <section class="content course-view">
        <?= Alert::widget() ?>
        <?= $content ?>
    </section>
</div>

<div class="go-up" style="right: 20px;display:none;">
    <i class="fa fa-chevron-up"></i>
</div>

<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>
