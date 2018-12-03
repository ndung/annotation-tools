<?php
/* @var $this TreeController */
/* @var $documents CorpusParseTreeString */
/* @var $user User */
?>

<div class="col-md-2">
    <section class="sidebar">
        <h4>
            <i class="glyphicon glyphicon-tasks"></i>
            Daftar Kalimat 
            Anda
        </h4>
        <div class="sidebar-content">
            <h5>    
                <form class="form-search">
                    <input type="text" name="search" class="form-control small" placeholder="Pencarian bracket... "/>
                    <button type="submit" class="form-control small btn-sm btn btn-default">
                        Cari <i class="glyphicon glyphicon-search"></i>
                    </button>
                </form>
            </h5>
            <ul class="list-group list-session">
                <?php $this->renderPartial('partial/list-my-strings', ['documents' => $documents, 'user' => $user]) ?>
            </ul>
        </div>
    </section>
</div>
<div class="col-md-10">
    <div class="">
        <section id="description-corpus" class="container content-section text-center" style="padding-top: 15px;">
            <div class="row">
                <div class="col-lg-8 col-lg-offset-0">
                    <fieldset>
                        <legend>
                            Edit Parse Tree
                        </legend>
                    </fieldset>
                    <section class="wrapper-interaction-input">
                        <section class="interaction-input input-file hidden">
                            <input type="file" name="file" class="form-control btn btn-xs action-load"/>
                        </section>
                        <section class="interaction-input input-text hidden">
                            <input type="text" class="form-control " id="input-raw-string" value="" />
                            <button class="parse-graph btn">
                                Tampilkan / Ulang
                            </button>
                        </section>
                    </section>
                </div>
            </div>
        </section>
            <div class="row">
                <div id='modal_detail' class="col-lg-8 col-lg-offset-0">
				<?php if (isset($documents[0])){ ?>
					<label><?php echo $documents[0]->sentence ?> </label>                    
				<?php } ?>  
                </div>
            </div>
        </section>

        <section class="content-section section-option text-center" style="padding-top: 10px !important;">
            <div class="row">  
                <div id="svg-wrapper" class="col-md-9" style="overflow: scroll; height: 600px; ">
                    <svg id="svg-canvas" class="display" ></svg>
                </div>
                <div class="col-md-3 input-group-action">
                    <section class="panel-interaction" interact="rename-node">
                        <p style="margin-bottom: 0px" class="font-xx-small">
                            ubah nama node.
                        </p>
                        <div class="input-group input-group-sm">
                            <input class="form-control text-node small" type="text" />
                            <span class="input-group-addon btn btn-default btn-sm apply-rename">
                                Ubah
                            </span>
                        </div>
                        <hr/>
                    </section>
                    <section class="panel-interaction" interact="group-nodes">
                        <p style="margin-bottom: 0px" class="font-xx-small">
                            anda dapat membuat elemen baru di bawah ini menjadi 
                            Parent dari node yang anda pilih.
                        </p>
                        <div class="input-group input-group-sm">
                            <input class="form-control parent-node small" type="text" />
                            <span class="input-group-addon btn btn-default btn-sm apply-parent">
                                Gabung
                            </span>
                        </div>
                        <hr/>
                    </section>

                    <section class="input-type-ungroup text-center">
                        <p style="margin-bottom: 0px">
                            atau anda dapat melepas status Parent pada node yang 
                            anda pilih. Sehingga anak-anak dari node tsb akan menjadi
                            Sub-Tree sendiri
                        </p>
                        <button class="form-control btn btn-default btn-xs clear-parent">
                            Lepas
                        </button>
                        <hr/>
                    </section>

                    <section class="input-type-insert text-center">
                        <p style="margin-bottom: 0px">
                            atau anda dapat menambah node baru
                        </p>
                        <div class="input-group input-group-sm">
                            <span class="input-group-addon btn btn-default action-insert-node" where="before">
                                Sebelum
                            </span>
                            <input class="form-control new-node" type="text" />
                            <span class="input-group-addon btn btn-default action-insert-node" where="after">
                                Sesudah
                            </span>
                        </div>    
                        <hr/>
                    </section>
                    <section class="input-type-assign text-center">
                        <p style="margin-bottom: 0px">
                            atau anda dapat mengarahkan ke Parent terbaru.
                        </p>
                        <div>
                            <button class="btn btn-default btn-sm action-assign-parent" state="choice" action="apply">
                                Pilih Parent Baru
                            </button>
                            <button class="btn btn-default btn-sm action-assign-parent hidden" action="cancel">
                                Batal
                            </button>
                        </div>    
                        <hr/>
                    </section>
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
</div>
<script type="text/javascript" src="<?= $this->createUrl("/scripts/tree/manage-mystring.js") ?>" ></script>

<div class="modal fade" id="modal-explore-mystring" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    Buka atau Buat Jawaban sendiri
                </h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span>
                        <b> Mengambil daftar Dokumen</b>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
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
