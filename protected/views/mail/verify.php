<?php
/* @var $user User */
/* @var $token string */
?>
<?php $this->beginContent('/mail/layouts/main') ?>
Dear <?= $user->username ?>,<br />
Untuk mengaktifkan akun anda, anda perlu memverifikasi email anda.<br />
Silahkan klik tautan berikut untuk verifikasi.<br />
<?= CHtml::link('Verifikasi Email', Yii::app()->createAbsoluteUrl('/parser/authenticate/confirm', ['userID' => $user->ID, 'token' => $token])) ?>
<?php $this->endContent() ?>
