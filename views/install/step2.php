<?php
$this->title = Yii::t('app','KTreeFAQ installation step 2');
?>

<?= $this->render('_steps', ['currentStep' => 2])?>
<div class="col-lg-12">
    <?php if(Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
</div>
<div class="col-md-6 col-md-offset-3">
    <div class="text-center"><h2>Database Configuration</h2></div>
    <br/>
      
    <div class="well">
        <?= $this->render('@easyii/views/install/_form', ['model' => $model])?>
    </div>
</div>
