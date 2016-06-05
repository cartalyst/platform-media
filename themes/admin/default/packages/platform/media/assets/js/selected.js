$(document).ready(function() {

    $('.modal-selected-body').hide();
	$('.modal-selected-header').click(function() {
        $('.modal-selected-body').slideToggle();
	});

var selectedArray = [];

    $('.media-results').on('click', '.media-item label', function(evt){
        var $this = $(this);

        setTimeout(checkSelected, 0);

        function checkSelected(){
            let item = $this.parent();
            let itemInput = $this.siblings('input[type="checkbox"]');
            let itemChecked = itemInput[0].checked;
            let itemId = itemInput.val();

            if(itemChecked){
                // Add item to selected Array
                addToSelected(item, itemId);
            } else {
                // Remove item from selected Array
                removeFromSelected(item, itemId);
            }
        };

        function addToSelected(item, itemId){
            let newItem = item.clone();
            newItem.find('input').attr('id', 'media_selected_' + newItem.find('input').val());
            newItem.find('label').attr('for', 'media_selected_' + newItem.find('input').val());
            $('.modal-selected-body').append(newItem);
            console.log(itemId);
            selectedArray.push(itemId);

            $('input[name="_media_ids[]"]').val(selectedArray);
            $('.selected-index').text(selectedArray.length);
        }

        function removeFromSelected(item, itemId){
            console.log(itemId);
            selectedArray = jQuery.grep(selectedArray, function(value) {
              return value != itemId;
            });

            $('#media_' + itemId).prop('checked', false);
            $('#media_selected_' + itemId).parent().remove();

            $('input[name="_media_ids[]"]').val(selectedArray);
            $('.selected-index').text(selectedArray.length);
        }

        //console.log($(this).html());
    });
});