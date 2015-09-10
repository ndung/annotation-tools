var bag = {nodes: [], links: []};
var forestString = [];
var sentenceString = [];
var selectedIndex = 0;
var superRoot = new Node('__Root__');
forestString.push(superRoot.toString(true));

var currentNode = $('<li>').attr('document-parse', '').attr('document-id', '');

/**
 * Change currentNode variable into the given one.
 * @param {Object} treeNode
 */
function setCurrentTreeNode(treeNode) {
    $('.item-document.open').removeClass('open');
    treeNode.addClass('open');
    currentNode = treeNode;
    initTreeStructure();
    renderTree();
}

/**
 * Update the current Node
 */
function clearCurrentNode() {
    setCurrentTreeNode($('<li>').attr('document-parse', '').attr('document-id', ''));
}

/**
 * Update the current Node
 */
function updateCurrentNode() {
    var current = Node.childString(superRoot).trim().replace(/(\s)\s+/g, '$1');
    var stored = decodeURIComponent(currentNode.attr('document-parse')).trim().replace(/(\s)\s+/g, '$1');
    var isChanged = current !== stored;

    if (isChanged) {
        currentNode.attr('document-parse', encodeURIComponent(current));
        currentNode.addClass('bold changed');
        if (currentNode.find('.label-document-ID').size()) {
            var labelID = currentNode.attr('document-id') === '' ? 'Baru' : ('(ID ' + currentNode.attr('document-id') + ')');
            var html = 'Kalimat ' + labelID + '<br/>';
            currentNode.find('.label-document-ID').html(html);
        }
        var sentence = currentNode.find('.content-string').text();
        currentNode.find('.content-string').html((currentNode.find('.note').size() ? '' : '<b class="note">*</b> ') + sentence);
    }
}

/**
 * Reset All global variables and settings
 */
function resetAll() {
    bag = {nodes: [], links: []};
    superRoot.abort();
    Node.uniqueID = 1;
}

/**
 * Reset the bag which collect the rendering information.
 */
function resetBag() {
    bag = {nodes: [], links: []};
    Node.lastLocation = 1;
    superRoot.balance();
    superRoot.resetID();
    for (var i = 0; i < superRoot.childs.length; i++) {
        superRoot.childs[i].stole(bag);
    }
    $('.panel-debug').append($('<pre>').html(logNode(superRoot)));
}

/**
 * Reset the UI state
 */
function resetUI() {
    $.each($('.node.selected'), function (i, e) {
        e.classList.toggle("selected");
    });
    $('input').val('');
    $('.input-group-action').hide();
    $('.parent-node').val('');
    $('.new-node').val('');
}

/**
 * Render the Tree
 */
function renderTree() {
    resetBag();
    drawTree(bag);
    resetUI();

    updateCurrentNode();
}

/**
 * Initialize Tree Structure
 */
function initTreeStructure() {
    resetAll();
    Node.tokenize(decodeURIComponent(currentNode.attr('document-parse')), superRoot, {i: 0});
}
/**
 * Main function
 */
(function () {

    /**
     * Initial configurations
     * @returns {undefined}
     */
    (function () {
        var documentNodes = $('[document-id]');
        if (documentNodes && documentNodes.length) {
            var documentNode = $(documentNodes[0]);
            currentNode = documentNode;
            documentNode.addClass('open');
            initTreeStructure();
        }
        renderTree();
        $('.input-group-action').hide();
    })();

    /**
     * Parse Tree Operations
     * Event Handlers
     */
    (function () {
        /**
         * Rename the Node
         */
        $('.apply-rename').click(function () {
            var nodeID = parseInt($('.node.selected').attr('node-id'));
            var node = superRoot.trace(nodeID);
            node.name = $('.text-node').val();
            renderTree();
        });

        /**
         * Apply the new parent into Node.
         */
        $('.apply-parent').click(function () {
            var newParent = new Node($('.parent-node').val());
            $.each($('.node.selected'), function (i, e) {
                var nodeID = parseInt($(e).attr('node-id'));
                var child = superRoot.trace(nodeID);
                child.changeParent(newParent);
            });
            superRoot.birth(newParent);
            renderTree();
        });

        /**
         * Remove the Node
         */
        $('.clear-parent').click(function () {
            var nodeElement = $('.node.selected:first');
            if (nodeElement && nodeElement.size() === 1) {
                var nodeID = parseInt(nodeElement.attr('node-id'));
                var node = superRoot.trace(nodeID);
                if (node !== null) {
                    if (confirm('Apakah anda yakin ingin menghapus Node ' + node.name + ' ?')) {
                        var newParent = node.parent;
                        var childs = node.childs;
                        for (var i = 0; i < childs.length; i++) {
                            childs[i].parent = newParent;
                            newParent.birth(childs[i]);
                        }
                        newParent.removeChild(node);
                        node = null;
                        renderTree();
                    }
                }
            }
        });

        /**
         * Assign the new Parent for single Node
         */
        $('.action-assign-parent').click(function () {
            var nodeElement = $('.node.selected');
            if (nodeElement && nodeElement.size() === 1 || $('.node.stacked').size() === 1) {
                switch ($(this).attr('action')) {
                    case'apply':
                        $('.input-type-ungroup, .input-type-insert, .panel-interaction[interact="group-nodes"]').hide();
                        switch ($(this).attr('state')) {
                            case 'choice':
                                $(this).html('Silahkan pilih node lalu tekan ini..');
                                $(this).attr('state', 'apply');
                                $(this).attr('child-id', nodeElement.attr('node-id'));
                                $('.action-assign-parent[action="cancel"]').removeClass('hidden');
                                nodeElement[0].classList.toggle("selected");
                                nodeElement[0].classList.toggle("stacked");
                                break;
                            case 'apply' :
                                var parentID = parseInt(nodeElement.attr('node-id'));
                                var childID = parseInt($(this).attr('child-id'));
                                var parent = superRoot.trace(parentID);
                                var child = superRoot.trace(childID);
                                var childScore = child.locationScore();
                                var parentScore = parent.locationScore();
                                var index = null;
                                var isValid = childScore[0] !== parentScore[0] && childScore[1] !== parentScore[1] && (
                                        (parentScore[0] - 1 <= childScore[0]) && (childScore[0] <= parentScore[1] + 1) ||
                                        (parentScore[0] - 1 <= childScore[1]) && (childScore[1] <= parentScore[1] + 1));
                                if (isValid) {
                                    for (var i = parent.childs.length - 1; i >= 0; i--) {
                                        var traceScore = parent.childs[i].locationScore();
                                        isValid = !(
                                                (traceScore[0] < childScore[0] && childScore[0] < traceScore[1]) &&
                                                (traceScore[0] < childScore[1] && childScore[1] < traceScore[1]));
                                        if (!isValid) {
                                            break;
                                        } else {
                                            if (childScore[0] <= traceScore[0]) {
                                                index = i;
                                            }
                                        }
                                    }

                                }

                                if (isValid) {
                                    if (child.parent.ID !== parent.ID) {
                                        child.changeParent(parent, index);
                                    }
                                } else {
                                    alert("Perpindahan seperti itu dilarang oleh sistem");
                                }


                                $(this).attr('child-id', null);
                                $(this).attr('state', 'choice');
                                $(this).html('Pilih Parent Baru');
                                nodeElement[0].classList.toggle("selected");
                                var nodeStacked = $('.node.stacked');
                                nodeStacked && nodeStacked.size() === 1 && nodeStacked[0].classList.toggle("stacked");
                                renderTree();
                                break;
                        }
                        break;
                    case 'cancel' :
                        var buttonApply = $('[action="apply"]');
                        buttonApply
                                .attr('state', 'choice')
                                .attr('child-id', null)
                                .html('Pilih Parent Baru');
                        ['selected', 'stacked'].forEach(function (className) {
                            $.each($('.node.' + className), function (i, e) {
                                e.classList.remove(className);
                            });
                        });
                        $('.input-group-action').hide();
                        $(this).addClass('hidden');
                        break;
                }

            }
        });

        /**
         * Insert new node
         */
        $('.action-insert-node').click(function () {
            var nodeElement = $('.node.selected:first');
            if (nodeElement && nodeElement.size() === 1) {
                var nodeID = parseInt(nodeElement.attr('node-id'));
                var node = superRoot.trace(nodeID);
                var newNode = new Node($('.new-node').val());

                switch ($(this).attr('where')) {
                    case 'after':
                        node.insertAfter(newNode);
                        break;

                    case 'before':
                        node.insertBefore(newNode);
                        break;
                }
                renderTree();
            }
        });
    }());


    /**
     * Common Editor Operations
     * Event Handlers
     */
    (function () {

        /**
         * When item document is clicked
         */
        $('.list-session').delegate('.list-group-item', 'click', function (e) {
            if (e.shiftKey) {
                /**
                 * TO-DO: bulk documents operation
                 */
            } else {
                setCurrentTreeNode($(this));
            }
        });

        /**
         * When Search form submited
         */
        $('.form-search').submit(function (e) {
            var term = $(this).find('input[name="search"]').val().trim();
            var button = $(this).find('[type="submit"]');
            if (term !== '') {
                var regex = term.replace(/\(/g, "\\(").replace(/\)/g, "\\)").replace(/[ ]+/g, "\\s+").replace(/\*/, '.*');
                try {
                    var pattern = new RegExp(regex);
                    var total = 0;
                    $('.item-document[document-id]').each(function (i, item) {
                        var itemElement = $(item);
                        var bracket = decodeURIComponent(itemElement.attr('document-parse'));
                        console.log(pattern, bracket);
                        if (!pattern.test(bracket)) {
                            itemElement.addClass('hidden');
                        } else {
                            total++;
                            itemElement.removeClass('hidden');
                        }
                    });
                    button.html((total ? ('Ditemukan ' + total) : 'Tidak ditemukan') + ' <i class="glyphicon glyphicon-search"></i>');
                } catch (e) {
                    alert('Maaf ekspresi tsb tidak dapat digunakan pada sistem ini.');
                }
            } else {
                button.html('Cari <i class="glyphicon glyphicon-search"></i>');
                $('.list-session .list-group-item').removeClass('hidden');
            }
            e.preventDefault();
            return false;
        });
    }());

    /**
     * Handling: Validate Node Clicking
     */
    $('#svg-wrapper').delegate('.node', 'click', function () {
        this.classList.toggle("selected");
        var totalSelected = $('.node.selected').size();
        var totalStacked = $('.node.stacked').size();
        if (totalSelected + totalStacked) {
            $('.input-group-action').show();
            $('.input-type-ungroup, .input-type-insert, .panel-interaction[interact="group-nodes"], .input-type-assign, .panel-interaction[interact="rename-node"]').hide();
            var isGroupAble = true;
            var parent = null;
            var candidates = [];
            $('.node.selected').each(function (i, element) {
                if (isGroupAble) {
                    var nodeID = parseInt($(element).attr('node-id'));
                    var node = superRoot.trace(nodeID);
                    isGroupAble = node !== null && node.parent.name === '__Root__';
                    if (isGroupAble) {
                        candidates.push(parseInt(node.indexChild()));
                    }
                }
            });

            candidates.sort(function (indexA, indexB) {
                return indexA - indexB;
            });
            for (var i = 1; i < candidates.length; i++) {
                if (candidates[i - 1] + 1 !== candidates[i]) {
                    isGroupAble = false;
                    break;
                }
            }
            if (totalSelected === 1) {
                if (totalStacked === 1) {
                    $('.input-type-assign').show();
                    var childID = $('.node.stacked').attr('node-id');
                    var parentID = $('.node.selected:not(.stacked)').attr('node-id');
                } else if (totalStacked > 1) {
                } else {
                    $('.input-type-ungroup, .input-type-insert, .input-type-assign, .panel-interaction[interact="rename-node"]').show();
                    var nodeID = parseInt($('.node.selected').attr('node-id'));
                    var node = superRoot.trace(nodeID);
                    $('.text-node').val(node.name);
                    if (isGroupAble) {
                        $('.panel-interaction[interact="group-nodes"]').show();
                    }
                }
            } else if (totalSelected > 1) {
                if (totalStacked === 1) {
                } else if (totalStacked > 1) {
                } else {
                    if (isGroupAble) {
                        $('.panel-interaction[interact="group-nodes"]').show();
                    }
                }
            } else {
                if (totalStacked === 1) {
                } else if (totalStacked > 1) {
                } else {
                }
            }
        } else {
            $('.input-group-action').hide();
        }
    });
    
})();
