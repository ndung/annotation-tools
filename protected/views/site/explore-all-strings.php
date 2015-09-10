<?php
/* @var $this TreeController */
/* @var $user User */
/* @var $solutions CActiveDataProvider */
/* @var $corpuses CorpusParseTree */
?>
<div class="container">
    <div class="content-section load-here">
        <section>
            <h1>
                Saring berdasarkan
            </h1>
            <form action="<?= $this->createUrl('explorerAllString') ?>">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    Korpus
                                </button>
                            </span>
                            <?= CHtml::dropDownList('corpusID', null, CHtml::listData(CorpusParseTree::model()->findAll(), 'ID', 'name'), ['class' => 'form-control', 'empty' => '-- Pilih korpus --']) ?>

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    Pengguna
                                </button>
                            </span>
                            <?= CHtml::dropDownList('userID', null, CHtml::listData(User::model()->findAll(), 'ID', 'username'), ['class' => 'form-control', 'empty' => '-- Pilih user --']) ?>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button">
                                    ID Kalimat
                                </button>
                            </span>
                            <?= CHtml::textField('stringID', null, ['class' => 'form-control', 'empty' => '-- Pilih user --']) ?>
                        </div>
                    </div>
                </div>
            </form>
        </section>
        <section class="list-item partial-here" type="solution">
            <h1>
                Daftar Dokumen
            </h1>
            <?php
            $this->widget('zii.widgets.CListView', [
                'dataProvider' => $solutions,
                'emptyText' => $this->renderPartial('partial/empty-result', [], true),
                'itemView' => 'partial/item-solution',
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