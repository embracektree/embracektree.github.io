<div role="filemanager-modal" class="modal filemanager-modal" tabindex="-1"
     data-frame-id="<?= $frameId ?>"
     data-frame-src="<?= $frameSrc ?>"
     data-btn-id="<?= $btnId ?>"
     data-input-id="<?= $inputId ?>"
     data-image-container="<?= isset($imageContainer) ? $imageContainer : '' ?>"
     data-paste-data="<?= isset($pasteData) ? $pasteData : '' ?>"
     data-thumb="<?= $thumb ?>"
     data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button data-dismiss="filemanager-modal" class="close close-filemanager " type="button">×</button>
                <h4 class="modal-title"><span class="glyphicon glyphicon-picture"></span> <?= Yii::t('app', 'File manager') ?></h4>
            </div>
            <div class="modal-body"></div>
        </div>
    </div>
</div>