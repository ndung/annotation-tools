<?php
/* @var $this TreeController */
/* @var $user User */
/* @var $solutions CActiveDataProvider */
/* @var $corpuses CorpusParseTree */
?>
<div class="container ">
    <div class="content-section partial-here">
        <section class="list-item" type="corpus">
            <?php if (count($corpuses)): ?>
                <h1>
                    Daftar Korpus
                </h1>
                <?php foreach ($corpuses as $corpus): ?>
                    <a href="<?= $this->createUrl('explorerMyString', ['corpusID' => $corpus->ID]) ?>" class="btn btn-default">
                        <i class="glyphicon glyphicon-book"></i>
                        Korpus: 
                        <strong>
                            <?= $corpus->name ?>
                        </strong>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <h1 class="title text-center">
                    Daftar Corpus <?= CorpusParseTree::model()->findByPk($_GET['corpusID'])->name ?>
                </h1>
                <a href="<?= $this->createUrl('explorerMyString') ?>" class="btn btn-success">
                    « Kembali
                </a>
            <?php endif; ?>
        </section>

        <section class="list-item" type="document">
            <h1>
                Daftar Dokumen
            </h1>
            <?php
            $this->widget('zii.widgets.CListView', [
                'dataProvider' => $solutions,
                'emptyText' => $this->renderPartial('partial/empty-result', [], true),
                'itemView' => 'partial/item-document',
                'pagerCssClass' => 'text-center',
                'pager' => [
                    'header' => '',
                    'selectedPageCssClass' => 'active',
                    'htmlOptions' => ['class' => 'pagination'],
                    'firstPageLabel' => '&laquo;',
                    'prevPageLabel' => '&lsaquo;',
                    'nextPageLabel' => '&rsaquo;',
                    'lastPageLabel' => '&raquo;'
                ],
            ]);
            ?>
        </section>
    </div>
</div>