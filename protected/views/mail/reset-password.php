<?php
/* @var $user User */
/* @var $password string */
?>
Dear <?= $user->username ?>,<br />
Password akun anda telah direset oleh sistem atas permintaan pada situs <?= Yii::app()->getBaseUrl(true) ?>.<br />
Segera ubah password anda setelah login dan jaga dan simpan password anda dengan baik.<br />
<br />
Email : <?= $user->email ?><br />
Username : <?= $user->username ?><br />
Password : <?= $password ?><br />
<br />
<?= CHtml::link(Yii::app()->name, Yii::app()->getBaseUrl(true)) ?> #<?= Yii::app()->params['social']['twitter']['hashtags'] ?>