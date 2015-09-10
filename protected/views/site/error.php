<?php
/* @var $this SiteController */
/* @var $error array */
?>
<section class="container text-center">
    <legend>
        <h2>Error <?php echo $code; ?></h2>
    </legend>

    <div class="error">
        <?php echo CHtml::encode($message); ?>
    </div>
</section>