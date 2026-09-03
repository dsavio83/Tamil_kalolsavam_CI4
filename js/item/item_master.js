function editItem(itemId) {
    if (typeof openEditFromData === 'function') {
        const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
        if (row) {
            const editBtn = row.querySelector('button[title="Edit Item"]');
            if (editBtn) {
                editBtn.click();
                return;
            }
        }
    }
    var f = document.createElement('form');
    f.method = 'POST';
    f.action = (typeof path !== 'undefined' ? path : '') + 'item/item_master/edit_item_detials';
    var h = document.createElement('input');
    h.type = 'hidden';
    h.name = 'ItemIdty';
    h.value = itemId;
    f.appendChild(h);
    document.body.appendChild(f);
    f.submit();
}

function deleteItem(itemId, itemCode) {
    if (confirm('Are you sure you want to delete Item Code ' + itemCode + '?')) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = (typeof path !== 'undefined' ? path : '') + 'item/item_master/delete_item_detials';
        var h = document.createElement('input');
        h.type = 'hidden';
        h.name = 'ItemIdty';
        h.value = itemId;
        f.appendChild(h);
        document.body.appendChild(f);
        f.submit();
    }
}
