<?php 
    $this->_pageTitle = $errorTitle.' | '.CConfig::get('name')
?>

<h1 class="title"><?= $errorHeader; ?></h1>
<div class="block-body">
    <p style="text-align:center;"><?= $errorText; ?></p>
</div>

