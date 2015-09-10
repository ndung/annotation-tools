<?php
/* @var $this AuthenticateController */
/* @var $form CActiveForm */
?>

<div class="container center-wrapper" style="margin-top: 200px">
    <div class="row">
        <div class="col-xs-offset-4 col-md-4">
            <div class="center-block">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h2 class="Font-TwentytwelveSlab-Regular">
                            Halaman Lupa Kata Sandi
                        </h2>

                        <hr>

                        <div class="text-left">
                            Masukkan username atau alamat email anda. Sistem akan mengirimkan password baru ke email anda.
                        </div>
                            <br/>

                        <div class="" style="border-radius:0px; margin-bottom:10px;">        
                            <?php
                            $this->beginWidget('CActiveForm', [
                                'id' => 'forget-password-form',
                            ])
                            ?>
                            <fieldset>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-envelope"></i>
                                                </div>
                                                <?= CHtml::textField('reference', '', ['class' => 'form-control', 'placeholder' => 'Enter email / username', 'required' => true, 'autocomplete' => 'off']) ?>
                                                <span class="input-group-btn">
                                                    <button class="btn btn-default" type="submit">Bantu</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <?php $this->endWidget() ?>
                        </div>

                        <div class="text-left small">
                            <em>Pastikan alamat email/username anda benar.</em>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <a href="<?= $this->createUrl('login') ?>">
                            Login
                        </a>, 
                        <small class="font-x-small">
                            Jika belum memiliki akses silahkan    
                            &nbsp;
                        </small>                            
                        <a href="<?= $this->createUrl('register') ?>" class="btn btn-default">
                            Daftar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>