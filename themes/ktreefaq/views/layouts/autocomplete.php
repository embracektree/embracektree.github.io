<?php
use kartik\widgets\Typeahead;
use yii\web\JsExpression;
use yii\helpers\Url;

$template = '<a href={{url}}>{{value}}</a>';
echo Typeahead::widget([
    'name' => 'topic-info',
    'options' => ['placeholder' => Yii::t('app', 'Enter a question or keyword'), ''],
    'pluginOptions' => ['highlight' => true],
    'pluginEvents' => [
        'typeahead:selected' => 'function(obj,event) {
                     window.location.href = event.url;
                 }',

        'typeahead:autocomplete' => 'function(obj,event) {
                     window.location.href = event.url;
                 }',

    ],
    'scrollable' => true,
    'dataset' => [
        [
            'datumTokenizer' => "Bloodhound.tokenizers.obj.whitespace('value')",
            'display' => 'value',
            'limit' => '15',
            'templates' => [
                'notFound' => '<div class="text-warning" style="padding:0 8px">' . Yii::t('app', 'No result found') . '</div>',
                'suggestion' => new JsExpression("Handlebars.compile('{$template}')"),
            ],
            'remote' => [
                'url' => Url::to(['/questions/get-doc-info']) . '?searchString=%QUERY',
                'wildcard' => '%QUERY'
            ]
        ]
    ]
]);

?>