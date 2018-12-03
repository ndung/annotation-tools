/**
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
    function saveWorkspaceLocal() {
        if ($('.item-document').size()) {
            var workspace = [];
            $('.item-document').each(function (i, item) {
                workspace.push($(item).attr('document-id'));
            });
            $.cookie('workspace', workspace.join(','));
        } else {
            $.removeCookie('workspace');
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
            saveWorkspaceLocal();
        }
    });
    /**
     * Download handler
     */
    $('.action-download').click(function () {
        var content = [];

        $('.item-document[document-id]').each(function (i, item) {
            content.push(decodeURIComponent($(item).attr('document-parse')));
        });

        if (content.length) {
            var blob = new Blob([content.join('\n')], {type: "text/plain;charset=utf-8"});
            var now = new Date();
            var formatDate = now.getFullYear() + "-" + (now.getMonth() + 1) + "-" + now.getDate() + " " + now.getHours() + ":" + now.getMinutes() + ":" + now.getSeconds();
            var fileName = formatDate + '.bracket';
            saveAs(blob, fileName);
        } else {
            alert('Pastikan ada berkas yang dibuka saat mengunduh. saat ini sistem tidak dapat memberikan berkas karena workspace kosong.');
        }
    });

    /**
     * Save handler
     */
    $('.action-save').click(function (e) {
        if (confirm('Apakah anda yakin ingin menyimpan semua Perubahan ini?')) {
            switch ($(this).attr('method')) {
                case 'current':
                    var URL = core.getURL('write', {'stringID': currentNode.attr('document-id')});
                    $.post(URL, {'value': decodeURIComponent(currentNode.attr('document-parse'))}, function (response) {
                        alert(response.message);
                        currentNode.removeClass('bold changed');
                        currentNode.find('.note').remove();
                    }).error(function (r) {
                        console.log(r);
                    });
                    break;

                case 'all':
                    var counter = 0;
                    var size = $('.item-document.changed').size();
                    $('.item-document.changed').each(function (i, item) {
                        var URL = core.getURL('write', {'stringID': $(item).attr('document-id')});
                        $.post(URL, {'value': decodeURIComponent($(item).attr('document-parse'))}, function (response) {
                            $(item).removeClass('bold changed');
                            $(item).find('.note').remove();
                            counter++;
                            if (counter >= size) {
                                alert('Semua sudah tersimpan');
                            }
                        }).error(function (r) {
                            console.log(r);
                        });
                    });
                    break;

                case 'context':
                    e.preventDefault();
                    e.stopPropagation();
                    var counter = 0;
                    var item = $(this).parents('.item-document');
                    var URL = core.getURL('write', {'stringID': item.attr('document-id')});
                    $.post(URL, {'value': decodeURIComponent(item.attr('document-parse'))}, function (response) {
                        item.removeClass('bold changed');
                        item.find('.note').remove();
                        alert('Dokumen sudah tersimpan');
                    }).error(function (r) {
                        console.log(r);
                    });
                    break;
            }
        }
    });

    /**
     * Simply Reload the browser madafaka~
     */
    $('.action-clear-workspace').click(function (e) {
        if (confirm("Apakah anda yakin mengeluarkan semua dokumen di workspace anda? silahkan simpan dahulu semua sesi anda")) {
            $.removeCookie('workspace');
            document.location.reload();
        }
    });

    /**
     * Document Explorer Handler
     */
    $(document).ready(function () {
        var loader = $('#modal-explore-mystring').find('.modal-body').html();
        $('[name="documentsID"]').keyup(function () {
            syncSelection($('#modal-explore-mystring').find('.modal-body'));
        }).change(function () {
            syncSelection($('#modal-explore-mystring').find('.modal-body'));
        });
        $('#modal-explore-mystring .action-load').click(function () {
            var documentsID = $('[name="documentsID"]').val();
            $.post(core.getURL('load-documents', {}), {'documentsID': documentsID}, function (html) {
                $('.list-session').append(html);
                $('#modal-explore-mystring').modal('hide');
                saveWorkspaceLocal();
            });
        });
        $('#modal-explore-mystring').delegate('a[href]', 'click', function () {
            var modal = $('#modal-explore-mystring');
            var anchor = $(this);
            var body = modal.find('.modal-body');
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
        $('#modal-explore-mystring').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget) // Button that triggered the modal
            var modal = $(this);
            var url = core.getURL('load-explorer', {});
            var body = modal.find('.modal-body');
            body.html(loader);
            $.get(url, function (response) {
                var bodyContent = $(response);
                body.html(bodyContent.find('.partial-here').html());
                body.find('[data-toggle="tooltip"]').tooltip();
            });
        });
		$('.item-document').click(function () {
			var item = $(this);		
			var sentence = item.attr('document-sentence');
			$('#modal_detail').html("<label>"+decodeURIComponent(sentence)+"</label>");
        });
    });

}());
