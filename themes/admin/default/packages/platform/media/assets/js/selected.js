$(document).ready(function() {
	$('.modal-selected-header').click(function() {
		var el = $('.modal-selected-body');

		if (!$('.modal-selected-body').hasClass('open')) {
			var curHeight = el.height(),
			autoHeight = el.css('height', 'auto').height() + 40;
			el.height(curHeight).animate({
				height: autoHeight
			}, 300);
		} else {
			el.animate({
				height: 0
			}, 300);
		}
		$('.modal-selected-body').toggleClass('open');
	});
});