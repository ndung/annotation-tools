/**
 * Global uniqueID for each nodes.
 * @type Number|Number|@exp;Node@pro;uniqueID
 */
Node.uniqueID = 0;
Node.lastLocation = 1;

/**
 * Constructor for Node class
 * @param {String} name for the node
 * @param {String} type class type for node
 * @returns {Node}
 */
function Node(name, type) {
    this.name = name;
    /**
     * @type {Node}
     */
    this.parent = null;
    this.childs = [];

    if (!type || type !== 'forest') {
        this.ID = Node.uniqueID++;
    }
    this.type = type ? type : 'default';
}

/**
 * Go Up or return the parent.
 * @returns {Node.parent}
 */
Node.prototype.up = function () {
    return this.parent !== null ? this.parent : null;
};

/**
 * Register node as a child.
 * @param {Node} node node to be inserted
 * @param {integer} index start from 0 as first index
 * @returns {undefined}
 */
Node.prototype.birth = function (node, index) {
    if (typeof index === "undefined" || index === null) {
        node.parent = this;
        this.childs.push(node);
    } else {
        this.childs[index].insertBefore(node);
    }
};

/**
 * Give a birth to some nodes via bracket strings.
 * @param {String[]} strings
 */
Node.prototype.birthViaStrings = function (strings) {
    for (var i = 0; i < strings.length; i++) {
        Node.tokenize(strings[i], this, {i: 0});
    }
};
/**
 * Give a birth to some nodes via bracket string.
 * @param {String} string
 */
Node.prototype.birthViaString = function (string) {
    Node.tokenize(string, this, {i: 0});
};

/**
 * Register node as a child.
 * @param {Node} node
 */
Node.prototype.insertAfter = function (node) {
    parent = this.parent;
    var index = this.indexChild();
    var cursedID = this.ID;

    if (index !== null) {
        parent.childs.splice(index + 1, 0, node);
        node.ID = ++cursedID;
        node.parent = parent;
        for (var i = index + 1; i < parent.childs.length; i++) {
            parent.childs[i].ID = ++cursedID;
        }
    }

};

/**
 * Register node as a child.
 * @param {Node} node
 */
Node.prototype.insertBefore = function (node) {
    parent = this.parent;
    var cursedID = this.ID;
    var index = this.indexChild();

    if (index !== null) {
        node.ID = cursedID;
        node.parent = parent;
        for (var i = index; i < parent.childs.length; i++) {
            parent.childs[i].ID = ++cursedID;
        }
        parent.childs.splice(index - 1, 0, node);
    }

};

/**
 * Find the index of array where this node stored as a child of it's parent
 * @param {Integer} index of array where this node stored as a child of 
 *                  it's parent
 */
Node.prototype.indexChild = function () {
    var index = null;
    if (this.parent) {
        var parent = this.parent;
        for (var i = 0; i < parent.childs.length; i++) {
            if (parent.childs[i].ID === this.ID) {
                index = i;
                break;
            }
        }
    }
    return index;

};

/**
 * Abort all childs. empty the childs
 */
Node.prototype.abort = function () {
    this.childs = [];
};

/**
 * Get a sort score of this node. It basically find the minimal score of it's
 * Childs and itself. The score based on it's ID.
 * This function used for sorted out childrens.
 * @returns {Integer}
 */
Node.prototype.sortScore = function () {
    var thisScore = parseInt(this.ID);
    if (this.childs.length) {
        for (var i = 0; i < this.childs.length; i++) {
            var child = this.childs[i];
            var childScore = child.sortScore();
            if (childScore < thisScore) {
                thisScore = childScore;
            }
        }
    }
    return thisScore;
};

Node.prototype.locationScore = function () {
    var thisScore = [null, null];
    if (this.childs.length) {
        for (var i = 0; i < this.childs.length; i++) {
            var childScore = this.childs[i].locationScore();
            if (childScore[0] !== null && (thisScore[0] === null || childScore[0] < thisScore[0])) {
                thisScore[0] = childScore[0];
            }
            if (childScore[1] !== null && (thisScore[1] === null || childScore[1] > thisScore[1])) {
                thisScore[1] = childScore[1];
            }
        }
    } else {
        thisScore = [this.location, this.location];
    }
    return thisScore;
};

/**
 * Reset all ID of each nodes from top to the bottom using DFS
 * traverse.
 */
Node.prototype.resetID = function () {
    Node.uniqueID = this.parent === null ? 0 : Node.uniqueID;
    this.ID = Node.uniqueID;
    if (!this.childs.length) {
        this.location = Node.lastLocation;
        Node.lastLocation++;
    } else {
        for (var i = 0; i < this.childs.length; i++) {
            this.childs[i].resetID(++Node.uniqueID);
        }
    }
};

/**
 * Balancing out the Node and it's children by sorting out based on it's ID. 
 * This function make sure the original sentence/leaves have the right order.
 */
Node.prototype.balance = function () {
    /**
     * i believe this is the right order. 
     * don't have any prove yet
     * @type {Number}
     */
    this.type = !this.childs.length ? 'leaf' : 'default';

//    if (!this.childs.length) {
//        this.location = Node.lastLocation;
//        Node.lastLocation++;
//    } else {
    for (var i = 0; i < this.childs.length; i++) {
        this.childs[i].balance();
    }
    this.childs.sort(function (nodeA, nodeB) {
        var compareValue = nodeA.sortScore() - nodeB.sortScore();
        return compareValue < 0 ? -1 : (compareValue > 0 ? 1 : 0);
    });
//    }

};

/**
 * Change the parent into the new one and also make sure the old parent remove
 * access to this node.
 * @param {Node} newParent new parent Node
 */
Node.prototype.changeParent = function (newParent, newIndex) {
    var isRegisterAsChild = false;
    var parent = this.parent;
    for (var i = 0; i < parent.childs.length; i++) {
        var child = parent.childs[i];
        if (child.ID === this.ID) {
            parent.childs.splice(i, 1);
            isRegisterAsChild = true;
            break;
        }
    }
    if (isRegisterAsChild) {
        newParent.birth(this, newIndex);
    }
};

/**
 * Remove the child from the collections
 * @param {Node} removedChild removed children/node
 */
Node.prototype.removeChild = function (removedChild) {
    for (var i = 0; i < this.childs.length; i++) {
        var child = this.childs[i];
        if (child.ID === removedChild.ID) {
            this.childs.splice(i, 1);
            break;
        }
    }
};

/**
 * Return the node with right ID
 * @param {Integer} nodeID search ID for the Node
 * @returns {Node} return the node if found, otherwise null
 */
Node.prototype.trace = function (nodeID) {
    var found = null;
    if (this.ID === nodeID) {
        found = this;
    } else {
        for (var i = 0; i < this.childs.length; i++) {
            var lookupChild = this.childs[i].trace(nodeID);
            if (lookupChild !== null) {
                found = lookupChild;
                break;
            }
        }
    }
    return found;
};

/**
 * Return the all leaves' name as a string with right order.
 * @returns {@param;Node|@param;Node:name|String}
 */
Node.prototype.leafString = function () {
    var string = '';
    if (this.childs.length) {
        for (var i = 0; i < this.childs.length; i++) {
            string += ' ' + this.childs[i].leafString();
        }
    } else {
        string = this.name;
    }
    return string;
};

/**
 * Return the Bracket version of this Sub-Tree as a string.
 * @returns {String} Bracket version of this Sub-Tree as a string.
 */
Node.prototype.toString = function (isParentIncluded) {
    var childString = '';
    for (var i = 0; i < this.childs.length; i++) {
        childString += ' ' + this.childs[i].toString();
    }
    var name = this.name.replace(/[(]/g, '&brl;').replace(/[)]/g, '&brr;');
    return !isParentIncluded ? ('(' + name + (this.childs.length ? (' ' + childString) : '') + ')') : (childString);
};

/**
 * Return the Bag version of this Sub-Tree. This Structure data is for the 
 * 3D.Digraph
 * @returns {Node.prototype.toRawTree.bag}
 */
Node.prototype.toRawTree = function () {
    var bag = {nodes: [], links: []};
    this.stole(bag);
    return bag;
};

/**
 * Return the Raw Node version of this Sub-Tree. This Structure data is for the 
 * 3D.Digraph
 * @returns {Node.prototype.toRawNode.node.structureAnonym$0}
 */
Node.prototype.toRawNode = function () {
    return {
        name: this.name,
        ID: this.ID,
        type: this.type,
    };
};


/**
 * Collects all nodes using DFS traverse of this Sub-Tree and convert it into
 * suitable structure data for 3D.Digraph.
 * @param {Object} bag
 */
Node.prototype.stole = function (bag) {
    var parent = this;
    if (typeof bag.nodes[parent.ID] == 'undefined') {
        bag.nodes[parent.ID] = parent.toRawNode();
    }
    $.each(parent.childs, function (i, child) {
        if (typeof bag.nodes[child.ID] == 'undefined') {
            bag.nodes[child.ID] = child.toRawNode();
        }
        bag.links.push({
            source: bag.nodes[parent.ID],
            target: bag.nodes[child.ID],
            left: false,
            right: true
        });
        child.stole(bag);
    });
};


/**
 * Trace the string and convert it into Node structure data.
 * @param {String} parseChar
 * @param {Node} selectedNode
 * @param {type} current
 * @returns {undefined}
 */
Node.tokenize = function (parseChar, selectedNode, current) {
    var cursor = parseChar[current.i];
    current.i++;
    if (cursor === '(') {
        child = new Node(naming(parseChar, current));
        selectedNode.birth(child);
        Node.tokenize(parseChar, child, current);
    } else if (cursor === ')') {
        if (!selectedNode.childs.length) {
            selectedNode.location = Node.lastLocation;
            Node.lastLocation++;
        }
        Node.tokenize(parseChar, selectedNode.up(), current);
    } else if (current.i < parseChar.length) {
        Node.tokenize(parseChar, selectedNode, current);
    }
}

/**
 * Return toString on all childs
 * @returns {Array|Node.childsString.strings}
 */
Node.childString = function (root) {
    var string = '';
    for (var i = 0; i < root.childs.length; i++) {
        string += " " + root.childs[i].toString();
    }
    return string;
};
/**
 * Return toString on all childs
 * @returns {Array|Node.childsString.strings}
 */
Node.childsString = function (roots) {
    var strings = [];
    for (var i = 0; i < roots.length; i++) {
        strings.push(Node.childString(roots[i]));
    }
    return strings;
};
/**
 * 
 */
Node.toStringsCookie = function (forest) {
    var stringsCookie = [];
    forest.forEach(function (tree) {
        stringsCookie.push(Node.childString(tree));
    });
    return stringsCookie;
};
/**
 * 
 */
Node.toForest = function (stringsCookie) {
    Node.uniqueID = 1;
    var forest = [];
    stringsCookie.forEach(function (stringCookie) {
        var tree = new Node('Root');
        tree.birthViaString(stringCookie);
        forest.push(tree);
    });
    return forest;
};

function naming(parseChar, current) {
    var i = current.i;
    var name = '';
    while (parseChar[i] !== '(' && parseChar[i] !== ')') {
        name += parseChar[i];
        i++;
    }
    current.i = i;
    return name.replace(/&brl;/g, '(').replace(/&brr;/g, ')');
}

function cleanText(string) {
    return string.trim().replace(/[ ][ ]+?/g, ' ').replace(/\n/g, '').replace(/[ ][ ]+?/g, ' ');
}

function logNode(node) {
    var list = $('<ul>');
    var messages = [
        'sort = ' + node.sortScore(),
        'ID = ' + node.ID
    ];
    if (node.location) {
        messages.push('location = ' + node.location);
    }
    list.append($('<li>').html('[ ' + messages.join(',') + ']' + node.name));
    for (var i = 0; i < node.childs.length; i++) {
        var child = node.childs[i];
        list.append(logNode(child));
    }
    return list;
}