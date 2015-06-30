

jQuery('#gtreetable').gtreetable({
    'source': function (id) {
        return {
            type: 'POST',
            url: '/admin/categories',
            data: {'id': id},
            dataType: 'json',
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        }
    },
    'onSave': function (oNode) {
        return {
            type: 'POST',
            url: '/admin/categories/' + (!oNode.isSaved() ? 'add' : 'update'),
            data: {
                name: oNode.getName(),
                id: (!oNode.isSaved() ? oNode.getParent() : oNode.getId())
            },
            dataType: 'json',
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        };
    },
    'onDelete': function (oNode) {
        return {
            type: 'POST',
            url: '/admin/categories/delete',
            data: {
                id: oNode.getId()
            },
            dataType: 'json',
            error: function (XMLHttpRequest) {
                alert(XMLHttpRequest.status + ': ' + XMLHttpRequest.responseText);
            }
        };
    },
    "selectLimit": 0,
    "manyroots": true,
    "language": "ru-RU",
    "inputWidth": "200px"
});

$('#add-root').click(function () {
    $('tr.node .node-action-0').eq(0).trigger('click');
})
