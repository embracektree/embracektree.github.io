/*Script file to global js
 * version 100
 *kt173
 *Created on 20-11-2015
 */
var topicId = '';
var firstNodeId = '';
var selectedNode = '';
var action = '';
const SIBLING = 'sibling';
const CHILD = 'child';

$(window).load(function () {

    var currentUrl = $(location).attr('href');
    var urlArray = currentUrl.split('/');
    var treeVisible = $('.sidebar').is(':visible');
    topicId = urlArray[urlArray.length - 2];
//condition to check add css only for topics
    if (treeVisible && urlArray.length >= 7) {
        var selector = $('#topic_id_' + topicId);
        selector.parents('ul').css('display', 'block');
        selector.css('background-color', '#d9e8fb');
    }
    if (current_user && $('#fancyree_w0').is(':visible')) {
        /*Code to show active child nodes*/
        firstNodeId = $('.firstNodeId').val();

        $("#fancyree_w0").fancytree("getRootNode").visit(function (node) {
            node.setExpanded(true);
        });
        var node = $("#fancyree_w0").fancytree("getTree").getNodeByKey(topicId);
        $(node.span.lastChild).addClass("alert-success");


    }
    //disabling the selected language from language dropdown
    var selectorForLanguage = $('ul.dropdown-menu li#' + language);
    selectorForLanguage.addClass('disabled');
    selectorForLanguage.attr('disabled', 'disabled');

});


function addLoader() {
    $('body').append('<div class="global-loader"><div class="ui-widget-overlay"></div><div class="content-loader">Please Wait...</div></div>');
}
function removeLoader() {
    $('.global-loader').remove();
}

/*code for language change */
$('.language_selected').on('click', function (e) {
    var language_selected = $(this).attr('id');
    if (language == language_selected) {
        return false;
    } else {
        $.ajax({
            type: "POST",
            url: ajaxUrl + "topics/set-language",
            data: {'language': language_selected },
            beforeSend: function () {
                addLoader();
            },
            success: function (data) {
                removeLoader();
                if (data == 1){
                    location.reload();
                }

            }
        });
        e.preventDefault();
    }
});

$(document).on('click', '.file_remove', function () {
    $("#dynamicmodel-file_input").attr('value', 1);
});

//code to open in new tab when click on preview button
function previewButton() {
    var url = $(location).attr('href') + '?preview=' + true;

    window.open(url, '_blank');
}

function createSibling() {

    quickCreateTopics(SIBLING);
}
function createChild() {

    quickCreateTopics(CHILD);
}
function quickCreateTopics(action) {
    var selectedQuestion = $("#treeSelectedTopicId").val();
        if(selectedQuestion == ''){
             selectedQuestion = $(".topic_id_hidden").val();
        }

    var topicId = $('.selectedCourseId').val();

    if(selectedQuestion == '' || topicId == ''){

        dialogBox('Please select the question');

        return false;
    }{
        $.ajax({
            type: "POST",
            url: ajaxUrl + "questions/quick-create-questions",
            data: {'topicId': topicId, 'selectedQuestion': selectedQuestion, 'action': action},
            beforeSend: function () {
                addLoader();
            },
            success: function (data) {
                removeLoader();
                $('.quick_create_topic').prop('disabled', false);
                var ajaxResponse = $.parseJSON(data);
                if (ajaxResponse.status) {

                    $(this).prop('disabled', false);

                    window.location.href = baseUrl + '/topics' + '/' + ajaxResponse.topicSlug + '/' + ajaxResponse.message.question_id + '/' + ajaxResponse.message.slug;
                }
             }
        });
    }
}

$(document).on('click', '.category_image_remove', function () {
    $(".hiddenImageCategory").val('');
});
$(document).on('click', '.course_image_remove', function () {
    $(".hiddenImageTopic").val('');
});

$("input#categories-category_images").on('change', function (event) {

    var data = new FormData();

    data.append('file', event.target.files[0]);

    $.ajax({
        url: ajaxUrl + "questions/move-image-to-folder",
        type: 'POST',
        data: data,
        contentType: false,
        processData: false,
        cache: false,
        success: function () {
        }
    });

});

$(document).ready(function () {
    $('.menu-responsive-icon').on('click', function () {
        $('.main-sidebar').toggle();
    });
    if (current_user) {
        $(".treeview-menu").sortable({
            update: function (event, ui) {
                var elementInfo = ui.item.context;

                var currentElementId = elementInfo.id;
                var previousElementId = (elementInfo.previousSibling != null) ? elementInfo.previousSibling.id : '';
                var nextElementId = (elementInfo.nextSibling != null) ? elementInfo.nextSibling.id : '';
                var topicId = $('.selectedCourseId').val();

                $.ajax({
                    url: ajaxUrl + "questions/update-topic-sort-order",
                    type: 'POST',
                    data: {currentElementId: currentElementId, previousElementId: previousElementId, nextElementId: nextElementId, topicId: topicId},
                    success: function (data) {
                        var ajaxResponse = $.parseJSON(data);
                        console.log(ajaxResponse);
                    }
                });
            }
        });
        $(".treeview-menu").disableSelection();
    } else {
        return false;
    }

});

$(window).scroll(function () {
    if ($(this).scrollTop() > 60) {
        $(".go-up").fadeIn();
        $(".go-up").css("right", "20px");
    } else {
        $(".go-up").fadeOut();
        $(".go-up").css("right", "-60px");

    }
});
$(".go-up").click(function () {
    $("html,body").animate({scrollTop: 0}, 500);
    return false;
});
//to reset the topic form of dialog box.
$('#kartik-modal-topic button.close').click(function () {
    $('#topic_quick_create_form').trigger("reset");
});

$(document).on('click', '.close-filemanager', function () {
    $('.filemanager-modal').modal("hide");
});

function createQuestion(id) {

    addLoader();
    $.ajax({
        url: ajaxUrl + "questions/create-new-question",
        type: 'POST',
        data: {topicId: id},
        success: function (data) {
            removeLoader();
            var ajaxResponse = $.parseJSON(data);
            if (ajaxResponse.status) {
                window.location.href = '/topics' + '/' + ajaxResponse.topicslug + '/' + ajaxResponse.questionId + '/' + ajaxResponse.questionSlug;
            } else {
                dialogBox(ajaxResponse.message);

            }

        }
    });
}
function dialogBox(message){
    if ($('#errorMessageAlert').length == 0) {
        $('body').append('<div id="errorMessageAlert"></div>');
        $('#errorMessageAlert').dialog({
            title: '<span align="left">Alert info ' ,
            autoOpen: true,
            width: '500',
            modal: true,
            draggable: true,
            resizable: false,
            closeOnEscape: true,
            top: 20,
            open: function (event, ui) {
                $(event.target).dialog('widget').css({position: 'fixed'}).position({my: 'center', at: 'center', of: window });
            },
            close: function (event) {
                $(this).empty();
                $(this).dialog('destroy');
                $('#errorMessageAlert').remove();
            }
        });
        $('#errorMessageAlert').append('<h4>'+message+'</h4><br><br>');
        $('#errorMessageAlert').append('<div class="btns_container txt_rgt"><button class="act_btn Save dark icon_w apply" type="button" id="error_alert_ok"><span><span><span>Ok</span></span></span></button> </div>');
      }
}

$(document).on('click', '#error_alert_ok', function () {
    $('#errorMessageAlert').remove();
});