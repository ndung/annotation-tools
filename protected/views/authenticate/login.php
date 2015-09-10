<?php
/* @var $this AuthenticateController */
/* @var $login LoginUser */
/* @var $form CActiveForm  */
?>
<div class="container center-wrapper" style="margin-top: 200px">
    <div class="row">
        <div class="col-xs-offset-4 col-md-4">
            <div class="center-block">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?php
                        $form = $this->beginWidget('CActiveForm', array(
                            'id' => 'login-form',
                            'enableClientValidation' => true,
                            'clientOptions' => array(
                                'validateOnSubmit' => true,
                            ),
                        ));
                        ?>

                        <div class="form-group <?= $login->hasErrors('username') ? "has-error" : "" ?>">
                            <div class="col-lg-9">
                                <?= $form->textField($login, 'username', array('class' => 'form-control', 'placeholder' => "username")) ?>
                            </div>
                        </div>

                        <div class="form-group <?= $login->hasErrors('password') ? "has-error" : "" ?>">
                            <div class="col-lg-9">
                                <?= $form->passwordField($login, 'password', array('class' => 'form-control', 'placeholder' => "password")) ?>
                            </div>
                        </div>

                        <div class="form-group col-lg-1">
                            <?= CHtml::submitButton('Login', array('class' => 'btn btn-default')); ?>

                        </div>

                        <?php $this->endWidget(); ?>
                    </div>
                    <div class="panel-footer">
                        Login, 
                        <small class="font-x-small">
                            Jika belum memiliki akses silahkan    
                            &nbsp;
                        </small>                            
                        <a href="<?= $this->createUrl('register') ?>" class="btn btn-default">
                            Daftar
                        </a>
                        <br/>
                        
                        <small class="font-x-small">
                            atau    
                            &nbsp;
                        </small>                            
                        <a href="<?= $this->createUrl('forget') ?>" class="btn btn-default">
                            Bantu Lupa Password
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>