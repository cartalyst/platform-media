$(document).ready(function() {

    $('.modal-selected-body').hide();
    $('.modal-selected-header').click(function() {
        $('.modal-selected-body').slideToggle();
    });

    var selectedArray = [];

    $('.media-results').on('click', '.media-item label', function(evt) {
        evt.preventDefault();

        var $this = $(this);
        if ($this.siblings('input[type="checkbox"]')[0].checked) {
            $this.siblings('input[type="checkbox"]').prop('checked', false);
        } else {
            $this.siblings('input[type="checkbox"]').prop('checked', true);
        }

        setTimeout(checkSelected, 0);

        function checkSelected() {
            var item = $this.parent();
            var itemInput = $this.siblings('input[type="checkbox"]');
            var itemChecked = itemInput[0].checked;
            var itemId = itemInput.val();

            if (itemChecked) {
                // Add item to selected Array
                addToSelected(item, itemId);
            } else {
                // Remove item from selected Array
                removeFromSelected(item, itemId);
            }
        };

        function addToSelected(item, itemId) {
            var newItem = item.clone();
            newItem.find('input').attr('id', 'media_selected_' + newItem.find('input').val());
            newItem.find('label').attr('for', 'media_selected_' + newItem.find('input').val());
            newItem.find('input').removeAttr('data-grid-checkbox').removeAttr('name').removeAttr('value');
            $('.modal-selected-body').append(newItem);
            selectedArray.push(itemId);

            $('input[name="selected_media[]"]').val(selectedArray);
            $('.selected-index').text(selectedArray.length);
        }

        function removeFromSelected(item, itemId) {
            selectedArray = jQuery.grep(selectedArray, function(value) {
                return value != itemId;
            });

            $('#media_' + itemId).prop('checked', false);
            $('#media_selected_' + itemId).parent().remove();

            $('input[name="selected_media[]"]').val(selectedArray);
            $('.selected-index').text(selectedArray.length);
        }

        //console.log($(this).html());
    });
});