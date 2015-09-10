<?php
/* @var $this TreeController */
?>

<?php $this->beginContent('//layouts/parent-raw'); ?>
<div class="row" style="padding: 0px 10px">
    <div class="col-md-12">
        <div class="text-right">
            <section class="pull-left">
                <?php if (isset($this->data['contextMenu'])): ?>
                    <h4 class="text-left">
                        Menu Laman 
                    </h4>
                    <?php foreach ($this->data['contextMenu'] as $menu): ?>
                        <?= CHtml::tag('button', isset($menu['htmlOptions']) ? $menu['htmlOptions'] : [], sprintf('%s %s', CHtml::tag('i', ['class' => isset($menu['symbol']) ? $menu['symbol'] : 'glyphicon'], ' '), $menu['label'])) ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>

            <section class="pull-right">
                <h4>
                    Menu Utama
                </h4>
                <span class="dropdown">
                    <button class="btn btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        <i class="glyphicon glyphicon-user"></i>
                        Masuk sebagai (<?= UserWeb::instance()->user()->username ?>)
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">

                        <li role="presentation">
                            <a class="" role="menuitem" tabindex="-1" href="<?= $this->createUrl('authenticate/logout') ?>">
                                Keluar Sistem
                            </a>
                        </li>
                    </ul>
                </span>
                <span class="dropdown">
                    <button class="btn btn-xs dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-expanded="true">
                        <i class="glyphicon glyphicon-user"></i>
                        (<?= $this->translateMenuName($this) ?>)
                        <span class="caret"></span>
                    </button>
                    <?php if (UserWeb::instance()->isModerator()): ?>
                        <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
                            <li role="presentation">
                                <a class="" role="menuitem" tabindex="-1" href="<?= $this->createUrl('index') ?>">
                                    Anotasi
                                </a>
                            </li>
                            <li role="presentation">
                                <a class="" role="menuitem" tabindex="-1" href="<?= $this->createUrl('browser') ?>">
                                    Periksa
                                </a>
                            </li>
                        </ul>
                    <?php endif; ?>
                </span>
                <button class="btn btn-xs" type="button">
                    <i class="glyphicon glyphicon-star"></i>               
                    <?php if (UserWeb::instance()->isModerator()): ?>
                        Moderator
                    <?php else: ?>
                        Anotator
                    <?php endif; ?>
                </button>
            </section>
        </div>
    </div>
</div>
<div class="row" style="padding-right: 10px">
    <?= $content ?>
</div>

<div class="script">
    <link rel="stylesheet" type="text/css" href="<?= $this->createUrl("/styles/tree/app.css") ?>" />
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/jquery/jquery.cookie.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/file/blob.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/file/file-saver.js") ?>" ></script>

    <script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/d3.v3.min.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/dagre-d3.min.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/node.structure.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/draw.js") ?>" ></script>
    <script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/parser.js") ?>" ></script>
</div>
<?php $this->endContent(); ?>