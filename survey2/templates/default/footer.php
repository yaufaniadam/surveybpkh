<br>
<hr>

<footer>
    <div id="footer">
        <div class="footer">
            <div class="left-side">
                <?= $this->siteFooter; ?>
            </div>
			
			<div class="central-part">
				<?= FrontendMenu::draw('bottom', $this->_activeMenu); ?>
			</div>            
			
            <div class="right-side">
				<?php if(APPHP_MODE == 'debug' || APPHP_MODE == 'demo'): ?>
					<a href="backend/login"><?= (!CAuth::isLoggedInAsAdmin() ? A::t('app', 'Admin Login') : ''); ?></a>
				<?php endif; ?>
            </div>
            <div class="clear"></div>
         </div>
    </div>
</footer>
