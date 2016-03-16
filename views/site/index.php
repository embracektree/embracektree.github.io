<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */

$this->title = 'Ktree LMS';
?>
 <?php $form = ActiveForm::begin([
    'id' => 'registration-form',
	'enableClientValidation'=>true,
	'validateOnSubmit'=>true,
	'options' => ['enctype'=>'multipart/form-data'],
	/*'fieldConfig' => [
        'errorOptions' => ['encode' => false],
    ],*/
]); ?>
<div class="site-index">

    <div class="jumbotron">
        
    </div>
	<?php if(Yii::$app->user->id){ ?>
    <div class="body-content">
      <div class="row">
	<div class="col-md-12 ">
             <ul class="nav-tabs-simple  bg-white col-md-3 " id="tab-3">
			 <li class="active"><a data-toggle="tab" href="#admin_settings">Admin Settings</a>                          
                        </li>
            </ul>
      		 <div class="tab-content bg-white col-md-9" id="account-settings-content">
			<div class="form-group pull-right submit_settings_button">
			<?php // Html::submitButton('<i class="fa fa-check-square-o"></i>  Save settings', ['class' => 'btn btn-primary ']) ?>
			</div>
                        	<div class="tab-pane active" id="admin_settings">   <!-- KT192 code to render change password form view -->
			     	<?php // $this->render('general', [
       						     //         'model' => $model,'form'=>$form
   				                  // ]); ?>
                        	</div>
                 </div>
       </div> 
<?php }?>
     </div>
   </div>
</div>
<?php ActiveForm::end(); ?>




