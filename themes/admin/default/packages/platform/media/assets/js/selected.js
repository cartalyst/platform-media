$(document).ready(function() {
    $('.modal-selected-body').hide();

	$('.modal-selected-header').click(function() {
		var el = $('.modal-selected-body');

        $('.modal-selected-body').slideToggle();

		// if (!$('.modal-selected-body').hasClass('open')) {
		// 	var curHeight = el.height(),
		// 	autoHeight = el.css('height', 'auto').height() + 40;
		// 	el.height(curHeight).animate({
		// 		height: autoHeight
		// 	}, 300);
		// } else {
		// 	el.animate({
		// 		height: 0
		// 	}, 300);
		// }
		
        //$('.modal-selected-body').toggleClass('open');
	});


    $('.media-results').on('click', '.media-item label', function(evt){
        var $this = $(this);

        setTimeout(checkSelected, 1);

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
        }
        function removeFromSelected(item, itemId){
            let idToRemove = item.find('input').val();
            $('#media_' + idToRemove).prop('checked', false);

            $('#media_selected_' + idToRemove).parent().remove();
        }

        //console.log($(this).html());
    });
});