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
 * @version    3.2.0
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
            .initSorting()
        ;
    };

    // Add Listeners
    Extension.Uploader.listeners = function() {
        Platform.Cache.$body
            .on('click', '.media-item', Extension.Uploader.checkboxes)
            .on('click', '.modal-header-icon', Extension.Uploader.handleLayouts)
            .on('click', '[data-media-add]', Extension.Uploader.addMedia)
            .on('click', '[data-media-delete]', Extension.Uploader.deleteMedia);

        return this;
    };

    // Data Grid initialization
    Extension.Uploader.dataGrid = function() {
        var config = {
            throttle: 10,
            threshold: 10,
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

    // Media manager initialization
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

        return this;
    };

    // Initialize sorting
    Extension.Uploader.initSorting = function()
    {
        var mediaList = $('#mediaList')[0];

        Sortable.create(mediaList, {
            handle: '.fa-arrows',
            animation: 150,
            onEnd: function (evt) {
                var arr = new Array();
                var children = evt.to.children;

                for (var i = 0; i < children.length; i++) {
                  var tableChild = children[i];
                  arr.push(tableChild.id);
                }

                $('#mediaArray').val(arr);

                Extension.Uploader.linkMediaRecords();
            }
        });

        return this;
    };

    // Handle Data Grid checkboxes
    Extension.Uploader.checkboxes = function(event) {
        event.stopPropagation();

        var type = $(this).attr('data-grid-checkbox');

        if (! Extension.Uploader.multiUpload) {
            $('[data-grid-checkbox="all"]').prop('disabled', true);
            $('[data-grid-checkbox]').not($(this).find('[data-grid-checkbox]')).not('[data-grid-checkbox][disabled]').prop('checked', false);
        }

        if (type === 'all') {
            $('[data-grid-checkbox]').not(this).not('[data-grid-checkbox][disabled]').prop('checked', this.checked);

            $('[data-grid-row]').not('[data-grid-row][disabled]').not(this).toggleClass('active', this.checked);
        }

        $(this).parents('[data-grid-row]').not('[data-grid-row][disabled]').toggleClass('active');
    };

    // Handle modal layouts
    Extension.Uploader.handleLayouts = function(event) {
        $('.modal-header-icon.active').removeClass('active');
        $(this).addClass('active');

        var view = $(this).data('view');

        if (view == 'list') {
            $('.media-results').addClass('display-column');
        } else {
            $('.media-results').removeClass('display-column');
        }
    };

    // Handle Data Grid add media
    Extension.Uploader.addMedia = function(event) {
        event.preventDefault();

        var mediaIds;
        var modelId;
        var newMediaIdObjects;
        var newMediaIds;
        var success;
        var _this        = this;
        var originalText = $(this).html();
        var url          = $('[data-upload-post-url]').data('upload-post-url');

        if (! url) {
            url = window.location.origin + window.location.pathname
        }

        $(this).prop('disabled', true).html(originalText + ' <i class="fa fa-spinner fa-spin"></i>');

        mediaIds = $('input[name="_media_ids[]"]').map(function() {
            return $(this).val();
        }).get();

        if (! Extension.Uploader.multiUpload) {
            mediaIds = [];
        }

        newMediaIds = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]'), function(event) {
            return event.value;
        });

        newMediaIdObjects = $.map($('[data-grid-checkbox]:checked').not('[data-grid-checkbox="all"]').not('[data-grid-checkbox][disabled]'), function(event) {
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

        modelId = $('[data-model-id]').data('model-id');

        // Only fire a request if we are editing a model and have
        // newly added media objects.
        if (mediaIds.length > 0 && newMediaIdObjects.length > 0 && modelId) {
            success = function() {
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
            };

            Extension.Uploader.linkMediaRecords(mediaIds, success);
        } else {
            $(this).html(originalText).prop('disabled', false);
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

    // Handle media deletion
    Extension.Uploader.deleteMedia = function() {
        var success;
        var _this = this;

        $(this).parent().parent().find('.overlay').show();
        $(this).parent().parent().find('input[name="_media_ids[]"]').remove();
        $(this).addClass('disabled');

        success = function() {
            $(_this).closest('li').fadeOut(500, function() {
                $(this).remove();
            });
        };

        if (! Extension.Uploader.linkMediaRecords(null, success)) {
            $(_this).closest('li').fadeOut(300, function() {
                $(this).remove();
            });
        }
    };

    // Link media records
    Extension.Uploader.linkMediaRecords = function(mediaIds, success) {
        var modelId;
        var objectClass = $('[data-object-class]').data('object-class');
        var url         = $('[data-upload-post-url]').data('upload-post-url');

        if (! url) {
            url = window.location.origin + window.location.pathname
        }

        if (modelId = $('[data-model-id]').data('model-id')) {
            mediaIds = mediaIds ? mediaIds : $('input[name="_media_ids[]"]').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    model_id: modelId,
                    object_class: objectClass,
                    _new_media_ids: JSON.stringify(mediaIds)
                },
                success: success
            });

            return true;
        }

        return false;
    };

    // Multi upload setter
    Extension.Uploader.setMultiUpload = function(multiUpload) {
        Extension.Uploader.multiUpload = multiUpload;

        Extension.Uploader.action = multiUpload ? 'append' : 'html';
    };

    // Job done, lets run.
    Extension.Uploader.init();

})(window, document, jQuery);
