/**
 * Part of the Platform Media extension.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Platform Media extension
 * @version    3.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

;(function(window, document, $, undefined) {

	'use strict';

    Extension.Upload = {};

	// Initialize functions
    Extension.Upload.init = function()
	{
        Extension.Upload.field = 'media_id';
        Extension.Upload.multiple = false;
        Extension.Upload.template = _.template($('[data-media-attachment-template]').html());
        Extension.Upload.listeners();
	};

	// Add Listeners
    Extension.Upload.listeners = function()
	{
        $('.upload__attachments').on('click', '.media-delete', function(e) {
            $(this).closest('li').remove();
        });

        Extension.Upload.MediaManager = $.mediamanager({
            onFileQueued : function(file)
            {
                $('input.file-tags').not('.selectize-control').selectize({
                    delimiter: ',',
                    persist: false,
                    maxItems: 3,
                    create: function(input) {
                        return {
                            value: input,
                            text: input
                        }
                    }
                });

                $('.upload__instructions').hide();
            },
            onSuccess: function(xhr) {

                var response = $.parseJSON(xhr.response),
                    field = Extension.Upload.field + (Extension.Upload.multiple ? '[]' : ''),
                    action = Extension.Upload.multiple ? 'append' : 'html';

                $('.upload__attachments')[action](
                    Extension.Upload.template({
                        field: field,
                        media: response.media
                    })
                );

                $('#media-modal').modal('hide');
            },
            onComplete: function()
            {

            },
            onFail : function(e)
            {
                // alert(e.responseText);
            },
            onRemove : function(manager, file)
            {
                if (manager.totalFiles == 0)
                {
                    $('.upload__instructions').show();
                }
            }
        });



    };

	// Job done, lets run.
    Extension.Upload.init();

})(window, document, jQuery);
