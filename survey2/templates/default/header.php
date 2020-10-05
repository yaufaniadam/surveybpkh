<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <?php
                if(CAuth::isLoggedInAsAdmin()){
                    echo CHtml::link(A::t('app', 'Back to Admin Panel'), 'backend/index', array('class'=>'back-to'));
                }
            ?>
            <a class="brand" id="logo" href="<?= $this->defaultPage; ?>" title="<?= $this->siteTitle; ?>"><?= $this->siteTitle; ?></a>
            <?php if(!CAuth::isLoggedInAsAdmin()){ ?>
                <span id="slogan"><?= $this->siteSlogan; ?></span>
            <?php } ?>			
			
            <div class="pull-right">
				<?php if(CAuth::isLoggedInAs('participant') && A::app()->getSession()->get('surveyAccessMode') == 'r'){ ?>
					<span class="survey-login"><a class="logout" href="surveys/logout/code/<?= A::app()->getSession()->get('surveyCode'); ?>"><?= A::t('surveys', 'Logout'); ?></a></span>
				<?php } ?>
            </div>

			<div class="pull-right nav-collapse collapse">
				<?php
					echo FrontendMenu::draw('top',
						$this->_activeMenu,
						array('menuClass'=>'nav navbar-nav', 'subMenuClass'=>'dropdown-menu', 'dropdownItemClass'=>'dropdown')
					);
				?>
			</div>    
        </div>
    </div>
</div>

<?php if($this->getAction() == 'show'){ ?>
<div id="submenu">
    <div class="submenu-inner">
        <div class="container">
            <div class="survey-title">
                <?php        
                    if(CAuth::isLoggedInAs('participant')){                
                        echo '<span>'.$this->surveyName.'</span>';    
                    }            
                ?>
            </div>
            <div class="questionnaire-title">
                <span><?= ($this->questionnaireName ? '&raquo; &nbsp;'.$this->questionnaireName : ''); ?></span>
            </div>
        </div>
    </div>
</div>
<?php } ?>
