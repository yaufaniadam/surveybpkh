<?php

$nl = "\n";

A::app()->getClientScript()->registerScriptFile('assets/modules/surveys/js/surveys.js');

echo CHtml::openForm('surveys/show/code/' . $code . ($page ? '/page/' . $page : ''), 'post', array('id' => 'survey'));
echo CHtml::hiddenField('act', 'send');
echo CHtml::hiddenField('questionnaire_item_data', '');
echo CHtml::hiddenField('questionnaire_item_score', '');
echo CHtml::hiddenField('questionnaire_item_total_score', '');

echo $content;

$count = $startCount;
$showButton = (count($questionnaireItems) == 1 && isset($questionnaireItems[0]['question_type_id']) && $questionnaireItems[0]['question_type_id'] == '18') ? false : true;
foreach ($questionnaireItems as $key => $val) :

    // This item is not for current gender - continue
    if (!SurveysComponent::validateQuestionByGender($genderFormulation, $loggedGender, $val['question_text'], $val['question_text_f'])) :
        continue;
    endif;

    echo '<div id="q-' . $val['id'] . '" class="question-wrapper" data-question-id="' . $val['id'] . '" data-question-code="item_' . $val['id'] . '" data-question-type="' . $val['question_type_id'] . '">' . $nl;
    if ($errorItem == $val['id']) echo $actionMessage;
    echo '<div id="q-inner-wrapper-' . $val['id'] . '" class="question-type-example' . ($errorItem == $val['id'] ? ' question-error' : '') . '">' . $nl;

    echo '<strong>';
    echo (++$count) . '. ';
    echo ($val['is_required']) ? '<span class="required-field">&#42;</span>' : '';
    if ($genderFormulation) :
        echo ($loggedGender == 'm') ? $val['question_text'] : $val['question_text_f'];
    else :
        echo $val['question_text_formatted'];
    endif;
    if ($val['question_type_id'] != 18 && $val['help_text']) echo '<span class="tooltip-link" title="' . CHtml::encode($val['help_text']) . '."><img src="templates/default/images/question.png" alt="help"></span>';
    echo '</strong>' . $nl;

    echo '<div>' . $nl;
    echo SurveysComponent::drawQuestion($val);
    echo '</div>' . $nl;
    echo '</div>' . $nl;
    echo '</div>' . $nl;
    echo '<hr>' . $nl;
endforeach;

echo '<br><br>' . $nl;
if ($isSurveyComplete) :
    echo '<input type="submit" class="btn btn-lg btn-success" id="questionnaire-next" value="' . A::t('surveys', 'Klik untuk Mengakhiri') . '" />' . $nl;
else :
    echo '<input type="submit" class="btn btn-warning btn-lg"' . (!$showButton ? ' style="display:none;"' : '') . ' id="questionnaire-next" value="' . A::t('surveys', 'Lanjut') . '" />' . $nl;
endif;

echo CHtml::closeForm();
?>

<?php
if ($errorItem) :
    A::app()->getClientScript()->registerScript(
        'question-error-scroll',
        '$("html, body").animate({
                scrollTop:$("#q-' . $errorItem . '").offset().top - 80
            }, 0);',
        3
    );
endif;

// Check whether to show page complete alert
if (ModulesSettings::model()->param('surveys', 'show_page_complete_alert') == 1) :
    A::app()->getClientScript()->registerScript(
        'complete-page-alert',
        '$("#questionnaire-next").click(function(){                
                if(checkEmptyQuestionsExist()){
                    $(".alert-error").hide();
                    if(confirm("' . A::t('surveys', 'There are still some questions to be answered. Do you want to complete them?') . '")){
                        return false;
                    }                        
                }
                return true;
            });',
        3
    );
endif;
?>