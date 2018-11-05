<?php
/* @var $this TreeController */
/* @var $solutions CorpusParseTreeSolution */
?>

<div class="col-md-3">
    <section class="sidebar">
        <div class="sidebar-content">
            <h4>
                <i class="glyphicon glyphicon-tasks"></i>
                Daftar Kalimat 
                Saat ini
            </h4>
            <ul class="list-group list-session">
                <?php $this->renderPartial('partial/list-all-strings', ['solutions' => $solutions, 'user' => $user]) ?>
            </ul>
        </div>
    </section>
</div>
<div class="col-md-9">
    <section id="description-corpus" class="container content-section text-center" style="padding-top: 15px;">
        <div class="row">
            <div class="col-lg-11 ">
                <fieldset>
                    <legend>
                        Edit Parse Tree
                    </legend>
                </fieldset>
            </div>
        </div>
    </section>
	
			<section id="string-corpus" class="container content-section text-center" style="padding-top: 15px;">
            <div class="row">
                <div id='modal_detail' class="col-lg-8 col-lg-offset-0">
					<label><?php echo $documents[0]->sentence ?> </label>                    
                </div>
            </div>
        </section>

    <section class="content-section section-option text-center" style="padding-top: 10px !important; padding-right: 10px">
        <div class="row">  
            <div id="svg-wrapper" class="col-md-12" style="overflow: scroll; height: 600px; ">
                <svg id="svg-canvas" class="display" width="2000" height="600"></svg>
            </div>
        </div>
    </section>

    <section class="content-section text-center <?= UserWeb::instance()->id !== 1 ? 'hidden' : '' ?>" style="padding-top: 10px !important;">
        <button class="btn btn-default show-bracket">
            <h5>
                Tampilan Bracket
            </h5>
        </button>
        <pre class="panel-bracket"></pre>
        <div class="panel-debug text-left">

        </div>
    </section>
</div>
<script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/manage-allstring.js") ?>" ></script>

<div class="modal fade" id="modal-explore-allstring" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Buka Jawaban
                </h4>
            </div>
            <div class="modal-body clear">
                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
                        <b> Mengambil daftar Dokumen</b>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <br/>
                <div class="row">
                    <div class="col-md-2">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                    </div>
                    <div class="col-md-10">
                        <div class="input-group">
                            <input type="text" class="form-control" name="documentsID" value="" />
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-primary action-load">
                                    Buka
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>