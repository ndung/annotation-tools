<?php
/* @var $this AuthenticateController */
/* @var $register RegisterForm */
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

                        <div class="form-group <?= $register->hasErrors('email') ? "has-error" : "" ?>">
                            <div class="col-lg-9">
                                <?= $form->textField($register, 'email', array('class' => 'form-control', 'placeholder' => "email")) ?>
                            </div>
                        </div>

                        <div class="form-group <?= $register->hasErrors('username') ? "has-error" : "" ?>">
                            <div class="col-lg-9">
                                <?= $form->textField($register, 'username', array('class' => 'form-control', 'placeholder' => "username")) ?>
                            </div>
                        </div>
                        
                        <div class="form-group <?= $register->hasErrors('password') ? "has-error" : "" ?>">
                            <div class="col-lg-9">
                                <?= $form->passwordField($register, 'password', array('class' => 'form-control', 'placeholder' => "password")) ?>
                            </div>
                        </div>

                        <div class="form-group col-lg-1">
                            <?= CHtml::submitButton('Daftar', array('class' => 'btn btn-default')); ?>

                        </div>

                        <?php $this->endWidget(); ?>
                    </div>
                    <div class="panel-footer">
                        Daftar, 
                        <small class="font-x-small">
                            Jika sudah memiliki akses silahkan    
                            &nbsp;
                        </small>                            
                        <a href="<?= $this->createUrl('login') ?>" class="btn btn-default">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>