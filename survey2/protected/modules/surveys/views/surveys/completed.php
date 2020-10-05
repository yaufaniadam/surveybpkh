<?php 
    $this->_pageTitle = $completedTitle.' | '.CConfig::get('name')
?>

<h1 class="title"><?= $completedHeader; ?></h1>
<div class="block-body">
    <p style="text-align:center;">
		<?= $completedText; ?>
		<br><br>
		<img src="assets/modules/surveys/images/survey_completed.png" alt="survey completed" />
	</p>
</div>

