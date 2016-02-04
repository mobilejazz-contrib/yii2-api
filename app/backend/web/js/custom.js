/**
    * Created by polbatllo on 13/11/15.
    */
$(function () {
    // BULK CONTENT BULK ACTIONS.
    $('#bulk-action-submit').click(function () {
        var sel = $('.grid-view').yiiGridView('getSelectedRows');
        var act = $('#bulk-dropdown').val();

        // Prevent content being deleted to happily.
        if (act == 'delete') {
            if (!confirm("Are you sure you want to delete this item/s?")) {
                return;
            }
        }
        $.ajax({
            type: 'POST',
            url: 'bulk',
            data: {selection: sel, action: act}
        });
    });

    // MODALS
    $(document).on('click', '.showModalButton', function () {
        var modal = $('#modal');
        if (modal.data('bs.modal').isShown) {
            modal.find('#modalContent')
                .load($(this).attr('data-value'));
            //dynamically set the header for the modal
            document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('label') + '</h4>';
        } else {
            modal.modal('show')
                .find('#modalContent')
                .load($(this).attr('data-value'), function () {
                    menuDataSelector(modal);
                });
            //dynamically set the header for the modal
            document.getElementById('modalHeader').innerHTML = '<h4>' + $(this).attr('label') + '</h4>';
        }
    });
});
