<?php

use pendalf89\filemanager\widgets\FileInput;

/**
 * Created by PhpStorm.
 * User: satish.banda
 * Date: 4/2/16
 * Time: 8:06 PM
 */  ?>

<!--<div id="media-editor-modal" class="modal fade" role="dialog" data-backdrop="static" data-keyboard="false" style="display: none">
    <div class="modal-dialog">
    <div class="modal-content">
    <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?= Yii::t('app', 'Upload Image') ?></h4>
    </div>
    <div class="modal-body"> -->
    <div class="media-model">
    <form name="image-widget" action="/questions/move-image-to-folder" method="post" enctype="multipart/form-data">

        <?php
        echo FileInput::widget([
            'name' => 'mediafile',
            'id'=>'mediainput',
            'buttonTag' => 'button',
            'buttonName' => Yii::t('app','Browse'),
            'buttonOptions' => ['class' => 'btn btn-default'],
            'options' => ['class' => 'form-control'],
            // Widget template
            'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
            // Optional, if set, only this image can be selected by user
            'thumb' => 'original',
            // Optional, if set, in container will be inserted selected image
            'imageContainer' => '.img',
            // Default to FileInput::DATA_URL. This data will be inserted in input field
            'pasteData' => FileInput::DATA_URL,
            // JavaScript function, which will be called before insert file data to input.
            // Argument data contains file data.
            // data example: [alt: "Ведьма с кошкой", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
            'callbackBeforeInsert' => 'function(e, data) {
                $(".filemanager-modal").modal("hide");
                $(".media-model").hide();
                $formUrl = $(this).closest("form").attr("action");
                button.trigger("mediaReceieved", [baseUrl+data.url,$formUrl]);
        }',
        ]);
        ?>
    </form>
    </div>
   <!--
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
    </div>

    </div>
    </div>-->