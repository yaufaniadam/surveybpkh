<?php
    $this->_pageTitle = A::t('surveys', 'Surveys Management').' - '.A::t('surveys', 'Survey Results').' | '.CConfig::get('name');
    $this->_activeMenu = 'surveys/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('surveys', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('surveys', 'Surveys'), 'url'=>'modules/settings/code/surveys'),
        array('label'=>A::t('surveys', 'Surveys Management'), 'url'=>'surveys/manage'),
        array('label'=>A::t('surveys', 'Survey Results')),
    );
    
    A::app()->getClientScript()->registerCssFile('assets/modules/surveys/css/surveys.css');    
?>

<h1><?= A::t('surveys', 'Surveys Management'); ?></h1>
<div class="bloc">
    <?= $tabs; ?>
        
    <div class="sub-title">
        <a class="sub-tab active" href="surveys/results/surveyId/<?= $surveyId; ?>"><?= strip_tags($surveyName); ?></a>
        <?= A::t('surveys', 'Survey Results'); ?>        
        <a href="surveys/clearResults/surveyId/<?= $surveyId; ?>" onclick="return confirm($(this).data('msg'));" data-msg="<?= A::t('surveys', 'Are you sure you want to clear results?'); ?>" class="back-link"><?= A::t('surveys', 'Clear Results'); ?></a>
    </div>
	
    <div class="content">
    <?= $actionMessage; ?>
    
    <div class="results-left-panel">
        <div class="panel-header">
            <h4 class="sub-title"><?= A::t('surveys', 'Select Questionnaire'); ?></h4>
            <div class="ph-dropdown">
            <?= CHtml::dropDownList('selQuestionarie', $questionnaireId, $questionnaires, array('onchange'=>"$(location).attr('href','surveys/results/surveyId/".$surveyId."/questionnaireId/'+this.value);"), array()); ?>
            </div>
        </div>
        <br>
            
        <?php
            $count = 0;            

            //--------------------------------------------------
            // 1. Get all item variants
            //--------------------------------------------------            
            $itemVariantsResult = SurveyQuestionnaireItemVariants::model()->findAll(
                array(
                    'condition'=>'',
                    'order'=>'id ASC'
                )
            );
            $itemVariants = CArray::flipByField($itemVariantsResult, 'entity_questionnaire_item_id', true);
			//CDebug::d($itemVariants);
                  

            //--------------------------------------------------
            // 2. Get all answers with 'Option Other'
            //--------------------------------------------------            
            $itemOtherVariantsResult = SurveyAnswers::model()->count(
                array(
                    'condition'=>'
                        questionnaire_item_variant_id = 0 AND
                        questionnaire_item_other != ""
                    ',
                    'select'=>'questionnaire_item_id',
                    'group'=>'questionnaire_item_id',
                    'allRows'=>true
                )
            );
            $itemOtherVariants = CArray::flipByField($itemOtherVariantsResult, 'questionnaire_item_id', true);
            //CDebug::d($itemOtherVariants,1);
            
            //--------------------------------------------------
            // 3. Get all answers with a 'Free Text'
            //--------------------------------------------------            
            $itemTextVariantsResult = SurveyAnswers::model()->count(
                array(
                    'condition'=>'
                        questionnaire_item_variant_id = 0 AND
                        answer_text != ""
                    ',
                    'select'=>'questionnaire_item_id',
                    'group'=>'questionnaire_item_id',
                    'allRows'=>true
                )
            );
            $itemTextVariants = CArray::flipByField($itemTextVariantsResult, 'questionnaire_item_id', true);

            //--------------------------------------------------
            // 4. Get all answers with 'Date'
            // 14 - Matrix Choice (Date/Time)
            //--------------------------------------------------                        
            $itemMultiDateVariantsResult = SurveyAnswers::model()->findAll(
                array(
                    'condition'=>'
                        questionnaire_item_variant_id < 0 AND
                        answer_text != ""
                    ',
                    'order'=>'id ASC'
                )
            );
            $itemMultiDateVariants = CArray::flipByField($itemMultiDateVariantsResult, 'questionnaire_item_id', true);
            
            
            foreach($questionnaireItems as $key => $val):

                // Continue if this is only a text/HTML or Action Script types
                if(in_array($val['question_type_id'], array('17', '18'))) continue;
                
                echo '<table class="result-control">';
                echo '<tbody>';
                echo '<tr class="row-control-header">';
                echo '<td>
                        <b class="label-control-number">'.++$count.'.&nbsp;</b>
                        <b class="label-control-mandatory">'.($val['is_required'] ? '*' : '').'&nbsp;</b>
                        <b class="header-control-content">'.(($val['question_text'] !== '') ? $val['question_text'] : $val['question_text_f']).'</b>                        
                        '.(!$val['is_active'] ? '<span class="label-red align-right mlr5">'.A::t('surveys', 'Inactive').'</span>' : '').'
                      </td>';
                echo '</tr>';
                echo '<tr class="row-result">';
                echo '<td>';                
                
                    echo '<table class="tbl-control-content">';
                    // Header row percent/count
                    echo '<tr class="row-sub-header">';
                    echo '<td colspan="2"></td>';
                    echo '<td class="row-sub-column">'.A::t('surveys', 'Percent').'</td>';
                    echo '<td class="row-sub-column">'.A::t('surveys', 'Count').'</td>';
                    echo '</tr>';
    
                    // Preapre data for: 14 - Matrix Choice (Date/Time)
                    $itemMultiDateVariantsInfo = array();
                    if(isset($itemMultiDateVariants[$val['id']])):
                        foreach($itemMultiDateVariants[$val['id']] as $dKey => $dVal):
                            if(!isset($itemMultiDateVariantsInfo[$dVal['questionnaire_item_variant_id']])):
                                $itemMultiDateVariantsInfo[$dVal['questionnaire_item_variant_id']] = 0;
                            endif;
                            $itemMultiDateVariantsInfo[$dVal['questionnaire_item_variant_id']]++;
                        endforeach;
                    endif;
					
                    if(isset($itemVariants[$val['id']]) && count($itemVariants[$val['id']]) > 0):
                        // *****************************************
                        // Answers with multiple selection
                        // *****************************************
						
                        // Row results
                        $rowTitle = '';
                        $votesArray = array();
                        $votesTotal = 0;
                        $questionText = '';
                        $otherOptionFound = false;
                        $rowCount = 0;
                        
                        foreach($itemVariants[$val['id']] as $vKey => $vVal):
                            if(isset($vVal['row_title']) && $rowTitle != $vVal['row_title']):
                                $questionText .= '<tr class="row-header">';
                                $questionText .= '<td colspan="4">&nbsp; '.$vVal['row_title'].' &nbsp;</td>';
                                $questionText .= '</tr>';                                
                                $rowCount++;
                            endif;
                            
                            $content = isset($vVal['content']) ? $vVal['content'] : '';
                            if(preg_match('/#/', $content)):
                                $content = str_replace('#', '', $content).' [ '.A::t('surveys', 'Other Option').' ]';
                                $otherOptionFound = true;
                            endif;
                            
                            $votes = isset($vVal['votes']) ? $vVal['votes'] : '';
                            
                            // 14 - Matrix Choice (Date/Time)
                            if($val['question_type_id'] == 14):
                                $votes = isset($itemMultiDateVariantsInfo[$rowCount*(-1)]) ? $itemMultiDateVariantsInfo[$rowCount*(-1)] : 0;
                            endif;
							
							$contentValue = isset($vVal['content_value']) ? $vVal['content_value'] : '';
    
                            $questionText .= '<tr>';							
                            $questionText .= '<td class="row-content rc-first">'.($contentValue !== '' ? $contentValue.' &nbsp;' : '').$content.'</td>';                        
                            $questionText .= '<td class="row-content"><div class="col-progress" style="width:{percent_width_'.$vKey.'}px;"></div></td>';
                            $questionText .= '<td class="row-content rc-last">{key_'.$vKey.'}</td>';
                            $questionText .= '<td class="row-content rc-last">'.$votes.'</td>';
                            $questionText .= '</tr>';
                            
                            $votesArray[$vKey] = $votes;
                            $votesTotal += $votes;
                            $rowTitle = isset($vVal['row_title']) ? $vVal['row_title'] : '';
                        endforeach;
                        
                        if(in_array($val['question_type_id'], array(2, 4, 11, 13)) && !$otherOptionFound):                            
                            $itemOtherVariant = isset($itemOtherVariants[$val['id']][0]['cnt']) ? $itemOtherVariants[$val['id']][0]['cnt'] : '';
                            
                            $questionText .= '<tr>';
                            $questionText .= '<td class="row-content rc-first">[ '.A::t('surveys', 'Other Option').' ]</td>';
                            $questionText .= '<td class="row-content"><div class="col-progress" style="width:{percent_width_other}px;"></div></td>';
                            $questionText .= '<td class="row-content rc-last">{key_other}</td>';
                            $questionText .= '<td class="row-content rc-last">'.$itemOtherVariant.'</td>';
                            $questionText .= '</tr>';
    
                            $votesArray['other'] = $itemOtherVariant;
                            $votesTotal += $itemOtherVariant;
                        endif;

                        // Fill column percent
                        foreach($votesArray as $voteKey => $voteVal):
                            $percent = ($votesTotal) ? round($voteVal / $votesTotal * 100, 1) : '0';
                            $questionText = preg_replace('/{key_'.$voteKey.'}/i', $percent.'%', $questionText);
                            $questionText = preg_replace('/{percent_width_'.$voteKey.'}/i', round(($percent / 100) * 120) + 1, $questionText);                        
                        endforeach;
                        echo $questionText;                
                    
                    else:
                        // *****************************************
                        // Answers with free text
                        // *****************************************

                        $itemTextVariant = isset($itemTextVariants[$val['id']][0]['cnt']) ? $itemTextVariants[$val['id']][0]['cnt'] : '';
                        
                        // Row results
                        $percent = ($itemTextVariant) ? round($itemTextVariant / $summaryResponses * 100, 1) : '0';
                        
                        $questionText  = '<tr>';
                        $questionText .= '<td class="row-content rc-first">'.A::t('surveys', 'Text').'</td>';
                        $questionText .= '<td class="row-content"><div class="col-progress" style="width:'.(round(($percent / 100) * 120) + 1).'px;"></div></td>';
                        $questionText .= '<td class="row-content rc-last">'.$percent.'%</td>';
                        $questionText .= '<td class="row-content rc-last">'.$itemTextVariant.'</td>';
                        $questionText .= '</tr>';
                        echo $questionText;                
                    endif;
                    
                    echo '</table>';                    

                echo '</td>';
                echo '</tr>';
                echo '</tbody>';
                echo '</table>';
                echo '<br>';
            endforeach;
        ?>
    </div>
	
    <div class="results-right-panel">
        <div class="box-summary">
            <h4 class="sub-title"><?= A::t('surveys', 'Summary'); ?></h4>
            <div class="bs-inner">
                <ul class="results-summary">                    
                    <li><?= A::t('surveys', 'Total Participants'); ?>: <strong><?= $summaryParticipants; ?></strong></li>
                    <li><?= A::t('surveys', 'Participant Responses'); ?>: <strong><?= $summaryResponses; ?></strong></li>
                    <li><?= A::t('surveys', 'Total Questionnaires'); ?>: <strong><?= $summaryTotal; ?></strong></li>
                    <li><?= A::t('surveys', 'Completed Questionnaires'); ?>: <strong><?= $summaryCompleted; ?></strong></li>
                    <li><?= A::t('surveys', 'Incomplete Questionnaires'); ?>: <strong><?= $summaryIncomplete; ?></strong></li>
                    <li><?= A::t('surveys', 'Completion Rate'); ?>: <strong><?= $summaryCompletionRate; ?>%</strong></li>
                    <li><?= A::t('surveys', 'Avg. Time Taken'); ?>: <strong><?= ($summaryAverageTime == '' || $summaryAverageTime != '00:00:00') ? $summaryAverageTime : A::t('surveys', '< 1 min.'); ?></strong></li>
                </ul>
            </div>
        </div>
        
        <div class="box-export">
            <div>
                <h4 class="sub-title"><?= A::t('surveys', 'Export Results'); ?></h4>
                <div class="be-inner">
                    <div><?= A::t('surveys', 'To export results, select your preferred download format and click Export.'); ?></div>
                    <br>
                    <div>
                        <select id="select_export_format">
                            <option value="csv_columnar">CSV (columnar)</option>
                            <option value="csv_tabular" selected="selected">CSV (tabular)</option>
                        </select>
                    </div>
                    <div>
                        <?php if($summaryResponses): ?>
                            <a id="button-export" href="surveys/export/surveyId/<?= $surveyId; ?>" onclick="javascript:addExportFormat();" class="export-data" title="<?= A::t('surveys', 'Export'); ?>"><span><b class="icon-down">&nbsp;</b><?= A::t('surveys', 'Export'); ?></span></a>
                        <?php else: ?>
                            <a class="export-data disabled" title="<?= A::t('surveys', 'No export file ready for download'); ?>"><span><b class="icon-down">&nbsp;</b><?= A::t('surveys', 'Export'); ?></span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>            
        </div>        
    </div>
	<div class="clearfix"></div>
    </div>
</div>

<?php
	A::app()->getClientScript()->registerScript(
		'submit-export',
		'function addExportFormat(){
            var href = $("#button-export").attr("href");
            $("#button-export").attr("href", href + "/format/" + $("#select_export_format").val());
		};',
		0
	); 
?>