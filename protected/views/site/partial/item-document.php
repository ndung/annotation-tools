<?php
/* @var $this TreeController */
/* @var $data CorpusParseTreeString */
?>
<div data-placement="top"  data-toggle="tooltip" title="<?= $data->sentence ?>" class="col-md-4 btn btn-default ellipsis item-document-cloud" document-id="<?= $data->ID ?>">
    <?php if (count($data->contributors)): ?>
        <span class="pull-right">
            <i class="glyphicon glyphicon-bookmark" data-placement="left"  data-toggle="tooltip" title="<?= implode(',', CHtml::listData($data->contributors, 'ID', 'username')) ?>">
            </i>
            <?= count($data->contributors) ?>
        </span>
    <?php endif; ?>
    <i class="glyphicon glyphicon-file"></i>
    <strong>
        Dokumen: (ID: <?= $data->ID ?>)<br/>
    </strong>
    <i>
        <?= $data->sentence ?>
    </i> 
    <br/>
    <strong style="font-size: smaller !important">
        Corpus (<?= $data->corpusParseTree->name ?>)
    </strong>
</div>