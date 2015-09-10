<?php
/* @var $this RichText */
/* @var $textarea string */
/* @var $emoticonCategories string[] */
?>
<div class="row">
    <div class="col-md-12">
        <?= $textarea ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-right" style="padding: 10px;">
        <button type="button" class="btn btn-default btn-lg" data-toggle="modal" data-target="#emoticon-modal">
            <i class="fa fa-lg fa-smile-o"></i>
        </button>
    </div>
</div>

<?php if (count($emoticonCategories)): ?>
    <div class="modal fade" id="emoticon-modal" tabindex="-1" role="dialog" aria-labelledby="Emoticon Modal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header draggable">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h2 class="modal-title">Emoticon</h2>
                </div>
                <div class="modal-body" style="max-height: 420px; overflow-y: auto;">
                    <?php foreach ($emoticonCategories as $emoticonCategory): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <h4><?= $emoticonCategory->name ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach ($emoticonCategory->emoticons as $emoticon): ?>
                                <div class="col-md-3 text-center">
                                    <a href="javascript:;" title="<?= $emoticon->name ?>" keyword="<?= $emoticon->keyword ?>">
                                        <?= HTML::image($emoticon->URL, $emoticon->name) ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <hr />
                    <?php endforeach; ?>
                </div>
                <div class="modal-footer draggable">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script type="text/javascript">
    $('#markitup').markItUp(BBCodeSetting);
    $.each($('li.markItUpButton'), function(index, element) {
        $(element).attr('title', $(element).children('a').text());
    });

    $('#emoticon-modal').draggable({
        handle: '.draggable'
    });

    var lastPivot = null;
    $('#markitup').focus(function() {
        lastPivot = null;
    });

    $('#emoticon-modal a').click(function() {
        var keyword = $(this).attr('keyword') + ' ';
        var selector = $('#markitup');
        var pivot = lastPivot ? lastPivot : selector.caret();
        var text = selector.val();
        var prepend = text.substring(0, pivot);
        var postpend = text.substring(pivot, text.length);
        lastPivot = pivot + keyword.length;
        selector.val(prepend + keyword + postpend);
        toastr.success(keyword + 'inserted');
    });

    $('[data-target="#emoticon-modal"]').click(function() {
        $('#emoticon-modal').css({"left": 0, "top": 0});
    });
</script>