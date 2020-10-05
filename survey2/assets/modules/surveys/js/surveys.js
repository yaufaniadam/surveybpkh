/**
 * OnChange handler for question type dropdown box
 * @param frmPrefix
 * @param rowInd
 * @param val
 */
function questionTypeId_OnChange(frmPrefix, rowInd, val){
    
    var multiVariants = new Array('1', '2', '3', '4',  '5', '7', '10', '11', '12', '13', '14', '15', '16');
    var elId = '#'+frmPrefix;
	var codeExampleText = '';
    
    $(elId+parseInt(rowInd)).hide();    
    $(elId+(parseInt(rowInd)+1)).hide();
    $(elId+(parseInt(rowInd)+2)).hide();
    $(elId+(parseInt(rowInd)+3)).hide();
    $(elId+(parseInt(rowInd)+4)).hide();
    $(elId+(parseInt(rowInd)+5)).hide();
    $(elId+(parseInt(rowInd)+7)).show();
    
    if(multiVariants.indexOf(val) != -1){
        $(elId+parseInt(rowInd)).show();
    }
	
	$('.code-example').html('').hide();
    
    switch(val){
        case '1':
			// 	Multiple Choice (only one answer)
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = 'Chocolate<br>Vanilla<br>Strawberry';
			break;
        case '2':
			// 	Multiple Choice (only one answer with other option)	
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = 'Chocolate<br>Vanilla<br>Strawberry<br>#Other, please specify';
			break;
        case '3':
			// Multiple Choice (multiple answers)	
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = 'Chocolate<br>Vanilla<br>Strawberry';
			break;
        case '4':
			// Multiple Choice (multiple answers with other option)	
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = 'Chocolate<br>Vanilla<br>Strawberry<br>#Other, please specify';
			break;
        case '5':
			// Dropdown (only one answer)
			codeExampleText = 'Chocolate<br>Vanilla<br>Strawberry';
			break;
        case '6':
			// Single Textbox
			$(elId+(parseInt(rowInd)+3)).show();
			break;
        case '7':
			// Multiple Textboxes
			$(elId+(parseInt(rowInd)+3)).show();
			codeExampleText = 'Your favorite<br>Second favorite<br>Third favorite';			
			break;
        case '8':
			// Comment/Essay Box
			break;
        case '9':
			// Date/Time
			$(elId+(parseInt(rowInd)+1)).show();
			break;
        case '10':
			// Matrix Choice (only one answer per row)
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = '[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Brother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry';
			break;
		case '11':
			// Matrix Choice (only one answer per row with other option)
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = '[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other';
			break;
		case '12':
			// Matrix Choice (multiple answers per row)
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = '[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>===<br>[Brother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry';
			break;
		case '13':
			// Matrix Choice (multiple answers per row with other option)
			$(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = '[Mother]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other<br>===<br>[Father]<br>1|Chocolate<br>2|Vanilla<br>3|Strawberry<br>#Other';
			break;
        case '14':
			// Matrix Choice (Date/Time)
            $(elId+(parseInt(rowInd)+1)).show();
			codeExampleText = '[Chocolate]<br>The Time (DD/MM/YYYY)<br>===<br>[Vanilla]<br>The Time (DD/MM/YYYY)';
            break;
        case '15':
			// Rating Scale (Multiple/Matrix Choice)
            $(elId+(parseInt(rowInd)+5)).show();
			codeExampleText = '1|Important<br>2|<br>3|Moderately Important<br>4|<br>5|Not Important';
            break;                
        case '16':
			// Ranking
			codeExampleText = '[Chocolate]<br>1<br>2<br>3<br>[Vanilla]<br>1<br>2<br>3<br>[Strawberry]<br>1<br>2<br>3';
            break;                
        case '17':
			// Text/HTML Code
            $(elId+(parseInt(rowInd)+4)).show();
            $('input[name=is_required]').attr('checked', false);
            $(elId+(parseInt(rowInd)+7)).hide();
			codeExampleText = '<img src="assets/modules/surveys/images/icon.png"> <b>Lorem</b> <u>ipsum</u> dolor sit amet, consectetur adipiscing elit. Nam ligula justo, tristique a tellus ac, sollicitudin accumsan diam.';
            break;
        case '18':
            $(elId+(parseInt(rowInd)+2)).show();
			codeExampleText = '';
            break;
        default:
            break;
    }
	
	$('.code-example').html($('.code-example').data('caption') + '<hr><br>' + codeExampleText).show();
}

/**
 * OnChange handler for gender formulation dropdown box
 * @param frmPrefix
 * @param rowInd
 * @param val
 */
function genderFormulation_OnChange(frmPrefix, rowInd, val){
    var elId = '#'+frmPrefix;
    
    switch(val){
        case '1':
            $(elId+(parseInt(rowInd)+1)).show();
            $(elId+(parseInt(rowInd)+3)).show();
            break;
        case '0':
        default:
            $(elId+(parseInt(rowInd)+1)).hide();
            $(elId+(parseInt(rowInd)+3)).hide();
            break;
    }
}

/**
 * Checks if there are empty questions that still not answered on current page
 * @return bool
 */
function checkEmptyQuestionsExist(){
    
    // Check if all required questions are answered, then check if there is at least one optional question is not answered!!!
    var reqiredQuestions = 0,
        reqiredQuestionsAnwered = 0,
        optionalQuestions = 0,
        optionalQuestionsAnwered = 0;

    var errorDivs = [];

    $('.question-wrapper').each(function (index, element){
        
        var questionCode = $(element).data('question-code');
        var questionType = $(element).data('question-type');
        var questionId = $(element).data('question-id');
        var isRequiredField = ($(element).find('.required-field').length > 0) ? true : false;
        var doCount = false;        
        var otherCondition = false;
        var total = 0;
        var i = 0;
        
        switch(questionType){
            
            case 1:
            case 2:
                // 1. Multiple Choice (only one answer)
                // 2. Multiple Choice (only one answer with other option)                
                otherCondition = (questionType == 2) ? ($('input[name='+questionCode+'_other]').attr('type') == 'text' && $('input[name='+questionCode+'_other]').val() != '') : false;
                if($('input:radio[name="'+questionCode+'"]').is(':checked') || otherCondition){
                    doCount = true;                    
                }else{
                    if(!isRequiredField) errorDivs.push(questionId);
                }                
                break;
            
            case 3:
            case 4:
                // 3. Multiple Choice (multiple answers)
                // 4. Multiple Choice (multiple answers with other option)                
                otherCondition = (questionType == 4) ? ($('input[name='+questionCode+'_other]').attr('type') == 'text' && $('input[name='+questionCode+'_other]').val() != '') : false;
                if($('input:checkbox[name="'+questionCode+'[]"]').is(':checked') || otherCondition){
                    doCount = true;
                }else{
                    if(!isRequiredField) errorDivs.push(questionId);
                }            
                break;
            
            case 5:
                // 5. Dropdown (only one answer)                
                if($('select[name='+questionCode+']').val() != ''){                
                    doCount = true;
                }
                break;
            
            case 6:
                // 6. Single Textbox
                if($('input[name='+questionCode+']').val() != ''){                
                    doCount = true;
                }
                break;

            case 7:
                // 7. Multiple Textboxes: name='item_153[1058]'
                $('input[name^="'+questionCode+'["]').each(function (ind, el){
                    if($(el).val() != ''){
                        doCount = true;
                    }else if(isRequiredField){
                        doCount = false;
                    }
                });
                break;
            
            case 8:
                // 8. Comment/Essay Box
                if($('textarea[name="'+questionCode+'"]').val() != ''){                    
                    doCount = true;
                }
                break;
            
            case 9:
                // 9. Date/Time
                if($('input[name='+questionCode+'_dd]').val() != '' && $('input[name='+questionCode+'_mm]').val() != '' && $('input[name='+questionCode+'_yy]').val() != ''){
                    doCount = true;
                }
                break;
            
            case 10:
            case 11:
            case 15:
                // 10. Matrix Choice (only one answer per row)
                // 11. Matrix Choice (only one answer per row with other option)
                // 15. Rating Scale (Multiple/Matrix Choice)                
                total = $('input:radio[name^="'+questionCode+'_row"]').length;
                for(i=0; i < total; i++){
                    
                    if($('[name="'+questionCode+'_row_'+(i + 1)+'"]').is('input:radio')){

                        otherCondition = (questionType == 11) ? ($('input[name='+questionCode+'_row_'+(i + 1)+'_other]').attr('type') == 'text' && $('input[name='+questionCode+'_row_'+(i + 1)+'_other]').val() != '') : false;
                        if($('input:radio[name="'+questionCode+'_row_'+(i + 1)+'"]').is(':checked') || otherCondition){
                            doCount = true;
                        }else if(isRequiredField){
                            doCount = false;
                            break;
                        }
                    }else{
                        break;
                    }                    
                }
                break;
            
            case 12:
            case 13:
                // 12. Matrix Choice (multiple answers per row)	
                // 13. Matrix Choice (multiple answers per row with other option)                
                total = $('input:checkbox[name^="'+questionCode+'_row"]').length;               
                for(i=0; i < total; i++){
                    
                    if($('[name="'+questionCode+'_row_'+(i + 1)+'[]"]').is('input:checkbox')){

                        otherCondition = (questionType == 13) ? ($('input[name='+questionCode+'_row_'+(i + 1)+'_other]').attr('type') == 'text' && $('input[name='+questionCode+'_row_'+(i + 1)+'_other]').val() != '') : false;                    
                        if($('input:checkbox[name="'+questionCode+'_row_'+(i + 1)+'[]"]').is(':checked') || otherCondition){
                            doCount = true;
                        }else if(isRequiredField){
                            doCount = false;
                            break;
                        }
                    }else{
                        break;
                    }                    
                }                
                break;

            case 14:                
                // 14. Matrix Choice (Date/Time)                
                total = $('input[name^="'+questionCode+'_yy_row"]').length;
                for(i=0; i < total; i++){
                    
                    if($('input[name='+questionCode+'_dd_row_'+(i + 1)+']').val() != '' && $('input[name='+questionCode+'_mm_row_'+(i + 1)+']').val() != '' && $('input[name='+questionCode+'_yy_row_'+(i + 1)+']').val() != ''){
                        doCount = true;
                    }else if(isRequiredField){
                        doCount = false;
                        break;
                    }                    
                }                
                break;

            case 16:
                // 16. Ranking                
                total = $('select[name^="'+questionCode+'_row"]').length;
                for(i=0; i < total; i++){                
                    if($('[name="'+questionCode+'_row_'+(i + 1)+'"]').val() != ''){
                        doCount = true;
                    }else if(isRequiredField){
                        doCount = false;
                        break;
                    }                    
                }                
                break;
            
            default:
                break;
        }
        
        // 17. Text/HTML Code	
        if(questionType != 17){            
            if(isRequiredField){
                reqiredQuestions++;
                if(doCount) reqiredQuestionsAnwered++;
            }else{
                optionalQuestions++;
                if(doCount) optionalQuestionsAnwered++;
            }
        }        
    });

    // Remove error class from all DIVs
    $('div.question-error').removeClass('question-error');
    
    // There are still reqired questions that are not answered
    if(reqiredQuestions == reqiredQuestionsAnwered && optionalQuestions > optionalQuestionsAnwered){

        // Add error class to DIVs with missing answers
        if(errorDivs[0] != null && reqiredQuestions > 0){
            
            // Color 1st error 
            $('#q-inner-wrapper-'+errorDivs[0]).addClass('question-error');
            
            // Scroll up to the 1st error question
            $('html, body').animate({
                scrollTop:$('#q-'+errorDivs[0]).offset().top
            }, 0);
        }
        
        return true;
    }
    
    return false;
}
