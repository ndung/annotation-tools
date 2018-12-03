<?php
/* @var $this TreeController */
/* @var $data CorpusParseTreeSolution */

if ($data->evaluated==0){ ?>
<div data-placement="top"  data-toggle="tooltip" title="<?= $data->sentence ?>" class="col-md-4 btn btn-default ellipsis item-document-cloud" document-id="<?= $data->ID ?>">
<?php } else { ?>
<div data-placement="top"  data-toggle="tooltip" title="<?= $data->sentence ?>" class="col-md-4 btn btn-default2 ellipsis item-document-cloud" document-id="<?= $data->ID ?>">
<?php } ?>
    <i class="glyphicon glyphicon-file"></i>
    <strong>
        Dokumen: (ID: <?= $data->corpusParseTreeStringID ?> oleh <?= $data->user->username ?>)<br/>
    </strong>
    <i>
        <?= $data->sentence ?>
    </i> 

    <br/>
    <strong style="font-size: smaller !important">
        Corpus (<?= $data->corpusParseTreeString->corpusParseTree->name ?>)
    </strong>
</div>