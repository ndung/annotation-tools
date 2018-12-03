<?php
/* @var $this TreeController */
/* @var $documents CorpusParseTreeString */
/* @var $user User */
?>
<?php foreach ($documents as $document): ?>
    <li data-toggle="tooltip"            
        class="list-group-item item-document" 
        document-id="<?= $document->ID ?>" 
        <?php if ($this->userSolution($document)): ?>
            title="Modifikasi Terakhir: <?= $this->userSolution($document, $user)->dateModified ?>" 
        <?php endif; ?>

document-sentence="<?= rawurlencode($document->sentence) ?>" 
document-parse="<?= rawurlencode($this->userSolution($document, $user) ? $this->userSolution($document, $user)->string : $document->string) ?>"
        data-placement="right">

        <?php if ($user->verifiedUser): ?>
            <?php if ($user->moderator): ?>
                <a class="pull-right action-close" target="context">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
            <?php endif; ?>

            <a class="pull-right action-save" method="context">
                <i class="glyphicon glyphicon-open" data-toggle="tooltip" title="Simpan berkas ini" data-placement="left"></i>
                &nbsp;
            </a>
        <?php endif; ?>


        <a class="content">
            <?php if (UserWeb::instance()->isModerator()) : ?>
                <span class="label-document-ID">
                    Kalimat (ID <?= $document->ID ?>)
                    <br/>
                </span>
            <?php endif; ?>
            <span class="content-string">
                <?= $this->userSolution($document) ? $this->userSolution($document)->sentence : $document->sentence ?>
            </span>
        </a>
    </li>
<?php endforeach; ?>
