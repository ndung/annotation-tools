/**
 * Editor Operations
 * Event Handlers
 */
(function () {

    function syncSelection(modalContent) {
        var input = $('[name="documentsID"]');
        var selecteds = input.val().split(',');
        modalContent.find('.item-document-cloud.open').removeClass('open');
        selecteds.forEach(function (selectedID, e) {
            var documentID = selectedID.split('-');
            if (documentID.length === 1) {
                modalContent.find('.item-document-cloud[document-id="' + documentID + '"]').addClass('open');
            } else if (documentID.length === 2) {
                var min = documentID[0];
                var max = documentID[1];
                for (var i = min; i <= max; i++) {
                    modalContent.find('.item-document-cloud[document-id="' + i + '"]').addClass('open');
                }
            }

        });
    }
    function saveInspectionsLocal() {
        if ($('.item-document').size()) {
            var inspects = [];
            $('.item-document').each(function (i, item) {
                inspects.push($(item).attr('document-id'));
            });
            $.cookie('strings-inspect', inspects.join(','));
        } else {
            $.removeCookie('strings-inspect');
        }
    }

    /**
     * When item document is clicked
     */
    $('.list-session').delegate('.action-close[target="context"]', 'click', function (e) {
        var item = $(this).parents('.item-document');
        if (!item.hasClass('changed') || confirm('Perubahan belum disimpan, Apakah anda yakin ingin keluar dari sistem ?')) {
            e.preventDefault();
            e.stopPropagation();
            if (item.hasClass('open')) {
                clearCurrentNode();
            }
            item.tooltip('hide');
            item.remove();
            saveInspectionsLocal();
        }

    });

    /**
     * Document Explorer Handler
     */
    $(document).ready(function () {
        var loader = $('#modal-explore-allstring').find('.modal-body').html();
        $('[name="documentsID"]').keyup(function () {
            syncSelection($('#modal-explore-allstring').find('.modal-body'));
        }).change(function () {
            syncSelection($('#modal-explore-allstring').find('.modal-body'));
        });
        $('#modal-explore-allstring .action-load').click(function () {
            var documentsID = $('[name="documentsID"]').val();
            $.post(core.getURL('load-solutions', {}), {'documentsID': documentsID}, function (html) {
                $('.list-session').append(html);
                $('#modal-explore-allstring').modal('hide');
                saveInspectionsLocal();
            });
        });
        $('#modal-explore-allstring').delegate('select, input', 'change', function () {
            var form = $(this).parents('form');
            if (form.size()) {
                var modal = $('#modal-explore-allstring');
                var body = modal.find('.partial-here');
                body.html(loader);
                $.get(form.attr('action'), form.serialize(), function (response) {
                    var bodyContent = $(response);
                    syncSelection(bodyContent);
                    body.html(bodyContent.find('.partial-here').html());
                    body.find('[data-toggle="tooltip"]').tooltip();
                });
            }

            return false;
        }).delegate('form', 'submit', function () {
            return false;
        }).delegate('a[href]', 'click', function () {
            var modal = $('#modal-explore-allstring');
            var anchor = $(this);
            var body = modal.find('.partial-here');
            body.html(loader);
            $.get(anchor.attr('href'), function (response) {
                var bodyContent = $(response);
                syncSelection(bodyContent);
                body.html(bodyContent.find('.partial-here').html());
                body.find('[data-toggle="tooltip"]').tooltip();
            });
            return false;
        }).delegate('.item-document-cloud', 'click', function () {
            var item = $(this);
            item.toggleClass('open');
            var input = $('[name="documentsID"]');
            var string = input.val();
            var currentList = string.split(',');
            var documentID = item.attr('document-id');
            var position = null;
            if (~(position = $.inArray(documentID, currentList))) {
                currentList.splice(position, 1);
            } else {
                currentList.push(documentID);
            }
            input.val(string === '' ? documentID : currentList.join(','));
        });
        $('#modal-explore-allstring').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this);
            var url = core.getURL('load-explorer', {});
            var body = modal.find('.modal-body');
            body.html(loader);
            $.get(url, function (response) {
                var bodyContent = $(response);
                body.html(bodyContent.find('.load-here').html());
                body.find('[data-toggle="tooltip"]').tooltip();
            });
        });
    });

}());