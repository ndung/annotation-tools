<?php
/* @var $this TreeController */
/* @var $solutions CorpusParseTreeSolution */
/* @var $user User */
?>
<?php foreach ($solutions as $solution): ?>
    <li data-toggle="tooltip"            
        class="list-group-item item-document" 
        document-id="<?= $solution->ID ?>" 
        title="Modifikasi Terakhir: <?= $solution->dateModified ?>" 
		document-sentence="<?= $solution->sentence ?>" 
        document-parse="<?= rawurlencode($solution->string) ?>"
        data-placement="right">

        <?php if ($user->moderator): ?>
            <a class="pull-right action-close" target="context">
                <i class="glyphicon glyphicon-remove"></i>
            </a>
        <?php endif; ?>

        <a class="content">
            <span class="label-document-ID">
                <strong>
                    Kalimat (ID <?= $solution->ID ?> oleh <?= $solution->user->username ?>)
                </strong>
                <br/>
            </span>
            <span>
                <strong>
                    Korpus <?= $solution->corpusParseTreeString->corpusParseTree->name ?>
                </strong>

                <br/>
            </span>
            <span class="content-string">
                <?= $solution->sentence ?>
            </span>
        </a>
    </li>
<?php endforeach; ?>