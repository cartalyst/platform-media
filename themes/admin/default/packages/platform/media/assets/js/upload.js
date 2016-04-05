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

var Extension;

;(function(window, document, $, undefined) {
    'use strict';

    Extension = Extension || {
        Uploader: {},
    };

    Extension.Uploader = {};

    // Initialize functions
    Extension.Uploader.init = function() {
        Extension.Uploader.template = _.template($('[data-media-attachment-template]').html());
        Extension.Uploader
            .listeners()
            .dataGrid()
            .initMediaManager()
        ;
    };

    // Add Listeners
    Extension.Uploader.listeners = function() {
        Platform.Cache.$body
            .on('click', '[data-grid-checkbox]', Extension.Uploader.checkboxes)
            .on('click', '[data-media-add]', Extension.Uploader.addMedia);

        $('.upload__attachments').on('click', '.media-delete', function(e) {
            var _this = this;

            $(this).parent().parent().find('.overlay').show();
            $(this).parent().parent().find('input[name="media_ids[]"]').remove();

            var success = function() {
                $(_this).closest('li').fadeOut(300, function() {
                    $(this).remove();
                });
            };

            if (! Extension.Uploader.linkMediaRecords(success)) {
                $(_this).closest('li').fadeOut(300, function() {
                    $(this).remove();
                });
            }
        });

        return this;
    };

    // Data Grid initialization
    Extension.Uploader.dataGrid = function() {
        var config = {
            throttle: 5,
            threshold: 5,
            hash: false,
            callback: function(data) {
                if (! Extension.Uploader.multiUpload) {
                    $('[data-grid-checkbox="all"]').prop('disabled', true);
                }

                $('[data-grid-checkbox-all]').prop('checked', false);
            }
        };

        Extension.Uploader.Grid = $.datagrid('main', '#data-grid', '#data-grid_pagination', '#data-grid_applied', config);

        return this;
    };

    Extension.Uploader.initMediaManager = function() {
        Extension.Uploader.MediaManager = $.mediamanager({
            onFileQueued : function(file) {
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
                var response = $.parseJSON(xhr.response);

                $('.upload__attachments')[Extension.Uploader.action](
                    Extension.Uploader.template({
                        media: response
                    })
                );

                $('#media-modal').modal('hide');
            },
            onComplete: function() {
                Extension.Uploader.linkMediaRecords();
            },
            onRemove : function(manager, file) {
                if (manager.totalFiles == 0) {
                    $('.upload__instructions').show();
                }
            }
        });
    };

    Extension.Uploader.linkMediaRecords = function(success) {
        var url = $('[data-upload-post-url]').data('upload-post-url');

        if (! url) {
            url = window.location.origin + window.location.pathname
        }

        var modelId;
        var objectClass = $('[data-object-class]').data('object-class');

        if (modelId = $('[data-model-id]').data('model-id')) {
            var mediaIds = $('input[name="media_ids[]"]').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    model_id: modelId,
                    object_class: objectClass,
                    new_media_ids: JSON.stringify(mediaIds)
                },
                success: function(response) {
                    success();
                }
            });

            return true;
        }

        return false;
    };

    // Handle Data Grid checkboxes
    Extension.Uploader.checkboxes = function(event) {
        event.stopPropagation();

        if (! Extension.Uploader.multiUpload) {
            $('[data-grid-checkbox="all"]').prop('disabled', true);
            $('[data-grid-checkbox]').not(this).not('[data-grid-checkbox][disabled]').prop('checked', false);
        }

        var type = $(this).attr('data-grid-checkbox');

        if (type === 'all') {
            $('[data-grid-checkbox]').not(this).not('[data-grid-checkbox][disabled]').prop('checked', this.checked);

            $('[data-grid-row]').not('[data-grid-row][disabled]').not(this).toggleClass('active', this.checked);
        }

        $(this).parents('[data-grid-row]').not('[data-grid-row][disabled]').toggleClass('active');
    };

    // Handle Data Grid add media
    Extension.Uploader.addMedia = function(event) {
        event.preventDefault();

        var _this = this;
        var originalText = $(this).html();

        $(this).prop('disabled', true).html(originalText + ' <i class="fa fa-spinner fa-spin"></i>');

        var url = $('[data-upload-post-url]').data('upload-post-url');

        if (! url) {
            url = window.location.origin + window.location.pathname
        }

        var mediaIds = $('input[name="media_ids[]"]').map(function() {
            return $(this).val();
        }).get();

        if (! Extension.Uploader.multiUpload) {
            mediaIds = [];
        }

        var newMediaIds = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]'), function(event) {
            return event.value;
        });

        var newMediaIdObjects = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]'), function(event) {
            var id = event.value;

            // Skip existing media items
            if (_.indexOf(mediaIds, id) === -1) {
                return {
                    id: id,
                    name: $(event).data('name'),
                    thumbnail: $(event).data('thumbnail')
                };
            }
        });

        mediaIds = mediaIds.concat(newMediaIds);

        var modelId = $('[data-model-id]').data('model-id');
        var objectClass = $('[data-object-class]').data('object-class');

        // Only fire a request if we are editing a model and have
        // newly added media objects.
        if (mediaIds.length > 0 && newMediaIdObjects.length > 0 && modelId) {
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    model_id: modelId,
                    object_class: objectClass,
                    new_media_ids: JSON.stringify(mediaIds)
                },
                success: function(response) {
                    $(_this).html(originalText).prop('disabled', false);
                    $('[data-grid-checkbox]').prop('checked', false);

                    _.each(newMediaIdObjects, function(media) {
                        $('.upload__attachments')[Extension.Uploader.action](
                            Extension.Uploader.template({
                                media: media
                            })
                        );
                    });

                    $('#media-selection-modal').modal('hide');
                }
            });
        } else {
            $(_this).html(originalText).prop('disabled', false);
            $('[data-grid-checkbox]').prop('checked', false);

            _.each(newMediaIdObjects, function(media) {
                $('.upload__attachments')[Extension.Uploader.action](
                    Extension.Uploader.template({
                        media: media
                    })
                );
            });

            $('#media-selection-modal').modal('hide');
        }
    };

    // Multi upload setter
    Extension.Uploader.setMultiUpload = function(multiUpload) {
        Extension.Uploader.multiUpload = multiUpload;

        Extension.Uploader.action = multiUpload ? 'append' : 'html';
    };

    // Job done, lets run.
    Extension.Uploader.init();

})(window, document, jQuery);
