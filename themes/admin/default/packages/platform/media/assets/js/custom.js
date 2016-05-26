$(document).ready(function(){

    $('.modal-header-icon').click(function(){
        $('.modal-header-icon.active').removeClass('active');
        $(this).addClass('active');

        var view = $(this).data('view');

        if (view == 'list') {
            $('.media-results').addClass('display-column');
        } else {
            $('.media-results').removeClass('display-column');
        }
    });
});