<?php 
    $this->_pageTitle = $expiredTitle.' | '.CConfig::get('name')
?>

<h1 class="title"><?= $expiredHeader; ?></h1>
<div class="block-body">
    <p style="text-align:center;"><?= $expiredText; ?></p>
    
    <img src="assets/modules/surveys/images/survey_expired.png" alt="survey expired" />
</div>

