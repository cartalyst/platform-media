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
 * @version    5.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2016, Cartalyst LLC
 * @link       http://cartalyst.com
 */

var Extension;

;(function(window, document, $, undefined) {
    'use strict';

    Extension = Extension || {
        Uploader: {},
    };

    Extension.Uploader = {};

    Extension.Uploader.selectedArray = [];

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
            .on('click', '.media-item:not(.grid-layout .media-item)', Extension.Uploader.checkboxes)
            .on('click', '[data-view]', Extension.Uploader.handleLayouts)
            .on('click', '[data-media-add]', Extension.Uploader.addMedia)
            .on('click', '[data-media-delete]', Extension.Uploader.deleteMedia)
            .on('click', '.modal-selected-header', Extension.Uploader.toggleSelectedMedia)
            .on('click', '.media-item label', Extension.Uploader.selectMedia)
            .on('focusin', '.modal-header-right input', Extension.Uploader.preventSubmit)
        ;

        return this;
    };

    // Data Grid initialization
    Extension.Uploader.dataGrid = function() {
        var config = {
            throttle: 20,
            threshold: 20,
            url: {
                hash: false
            },
            callback: function(data) {
                if (! Extension.Uploader.multiUpload) {
                    $('[data-grid-checkbox="all"]').prop('disabled', true);
                }

                $('[data-grid-checkbox-all]').prop('checked', false);
            },
            events: {
                'fetched': function(grid) {
                    var selectedMedia = $('input[name="selected_media[]"]').val();

                    if (selectedMedia != null) {
                        var selectedArray = selectedMedia.split(',');

                        // Convert all of the array items to integers
                        for (var i = 0; i < selectedArray.length; i++) {
                            selectedArray[i] = parseInt(selectedArray[i], 10);
                        }

                        $('.modal-body .media-results').children('.media-item').each(function() {
                            var elementId = parseInt($(this).find('input').val());

                            if (jQuery.inArray(elementId, selectedArray) !== parseInt('-1')) {
                                $(this).find('input').prop('checked', true);
                            }
                        });
                    }
                }
            }
        };

        Extension.Uploader.DataGridManager = new DataGridManager();

        Extension.Uploader.Grid = Extension.Uploader.DataGridManager.create('main', config);

        return this;
    };

    // Media manager initialization
    Extension.Uploader.initMediaManager = function() {
        // Hide selected modal
        $('.modal-selected-body').hide();

        Extension.Uploader.MediaManager = $.mediamanager({
            onFileQueued: function(file) {
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

                Extension.Uploader.Grid.refresh(true);
            },
            onComplete: function() {
                Extension.Uploader.linkMediaRecords();
            },
            onRemove: function(manager, file) {
                if (manager.totalFiles == 0) {
                    $('.upload__instructions').show();
                }
            }
        });

        return this;
    };

    // Initialize sorting
    Extension.Uploader.initSorting = function() {
        var mediaList = $('#mediaList')[0];

        Sortable.create(mediaList, {
            handle: '.fa-arrows',
            animation: 150,
            onEnd: function(evt) {
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
            $('[data-grid-checkbox]')
                .not($(this).find('[data-grid-checkbox]'))
                .not('[data-grid-checkbox][disabled]')
                .prop('checked', false)
            ;
        }

        if (type === 'all') {
            $('[data-grid-checkbox]')
                .not(this)
                .not('[data-grid-checkbox][disabled]')
                .prop('checked', this.checked)
            ;

            $('[data-grid-row]')
                .not('[data-grid-row][disabled]')
                .not(this)
                .toggleClass('active', this.checked)
            ;
        }

        $(this).parents('[data-grid-row]').not('[data-grid-row][disabled]').toggleClass('active');
    };

    // Handle modal layouts
    Extension.Uploader.handleLayouts = function(event) {
        $('[data-view].active').removeClass('active');

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
        var $this = this;
        var originalText = $(this).html();
        var url = $('[data-upload-post-url]').data('upload-post-url');

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

        newMediaIds = $('.modal-selected input').map(function() {
            return $(this).val()
        }).get();

        newMediaIdObjects = $('.modal-selected input').map(function() {
            // Skip existing media items
            if (_.indexOf(mediaIds, $(this).val()) === -1) {
                return {
                    id: $(this).val(),
                    name: $(this).data('name'),
                    preset_paths: {'thumb': $(this).data('thumbnail')},
                    mime: $(this).data('mime'),
                    is_image: $(this).data('is_image')
                };
            }
        });

        mediaIds = mediaIds.concat(newMediaIds);

        modelId = $('[data-model-id]').data('model-id');

        // Only fire a request if we are editing a model and have
        // newly added media objects.
        if (mediaIds.length > 0 && newMediaIdObjects.length > 0 && modelId) {
            success = function() {
                $($this).html(originalText).prop('disabled', false);

                $('[data-grid-checkbox]').prop('checked', false);

                _.each(newMediaIdObjects, function(media) {
                    $('.upload__attachments')[Extension.Uploader.action](
                        Extension.Uploader.template({
                            media: media
                        })
                    );
                });

                $('#media-selection-modal').modal('hide');

                Extension.Uploader.resetSelectedArea();
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

            Extension.Uploader.resetSelectedArea();
        }

        Extension.Uploader.refreshGrid();
    };

    // Handle media deletion
    Extension.Uploader.deleteMedia = function() {
        var success;
        var $this = this;

        $(this).parent().parent().find('.overlay').show();
        $(this).parent().parent().find('input[name="_media_ids[]"]').remove();
        $(this).addClass('disabled');

        success = function() {
            $($this).closest('li').fadeOut(500, function() {
                $(this).remove();

                Extension.Uploader.refreshGrid();
            });
        };

        if (! Extension.Uploader.linkMediaRecords(null, success)) {
            $($this).closest('li').fadeOut(300, function() {
                $(this).remove();
            });
        }
    };

    // Link media records
    Extension.Uploader.linkMediaRecords = function(mediaIds, success) {
        var modelId;
        var objectClass = $('[data-object-class]').data('object-class');
        var url = $('[data-upload-post-url]').data('upload-post-url');

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

    // Toggle selected media
    Extension.Uploader.toggleSelectedMedia = function(event) {
        $('.modal-selected-body').slideToggle();
    };

    // Select media
    Extension.Uploader.selectMedia = function(event) {
        event.preventDefault();

        var $this = $(this);

        var siblings = $this.siblings('input[type="checkbox"]');

        siblings.prop('checked', ! siblings[0].checked);

        setTimeout(Extension.Uploader.checkSelected($this), 0);
    };

    // Check selected
    Extension.Uploader.checkSelected = function($this) {
        var item = $this.parent();
        var itemInput = $this.siblings('input[type="checkbox"]');
        var itemId = itemInput.val();
        var itemChecked = itemInput[0].checked;

        if (itemChecked) {
            // Add item to selected Array
            Extension.Uploader.addToSelected(item, itemId);
        } else {
            // Remove item from selected Array
            Extension.Uploader.removeFromSelected(item, itemId);
        }
    };

    // Add to selected
    Extension.Uploader.addToSelected = function(item, itemId) {
        if (! Extension.Uploader.multiUpload) {
            $('.modal-selected-body').html('');
            Extension.Uploader.selectedArray = [];
        }

        $('.no-results').hide();

        var newItem = item.clone();

        newItem.find('input').attr('id', 'media_selected_' + newItem.find('input').val());
        newItem.find('label').attr('for', 'media_selected_' + newItem.find('input').val());
        newItem.find('input').removeAttr('data-grid-checkbox').removeAttr('name');

        $('.modal-selected-body').append(newItem);

        Extension.Uploader.selectedArray.push(itemId);

        $('input[name="selected_media[]"]').val(Extension.Uploader.selectedArray);
        $('.selected-index').text(Extension.Uploader.selectedArray.length);
    };

    // Remove from selected
    Extension.Uploader.removeFromSelected = function(item, itemId) {
        Extension.Uploader.selectedArray = jQuery.grep(Extension.Uploader.selectedArray, function(value) {
            return value != itemId;
        });

        if (Extension.Uploader.selectedArray.length == 0) {
            $('.no-results').show();
        }

        $('#media_' + itemId).prop('checked', false);
        $('#media_selected_' + itemId).parent().remove();

        $('input[name="selected_media[]"]').val(Extension.Uploader.selectedArray);
        $('.selected-index').text(Extension.Uploader.selectedArray.length);
    };

    // Refresh grid
    Extension.Uploader.refreshGrid = function() {
        Extension.Uploader.selectedArray = [];

        Extension.Uploader.Grid.refresh(true);
    };

    // Resets the selected area
    Extension.Uploader.resetSelectedArea = function() {
        $('input[name="selected_media[]"]').val('');

        $('.modal-selected-body').html('');

        $('.selected-index').text('0');
    };

    Extension.Uploader.preventSubmit = function() {
        $(window).keydown(function(event) {
            if (event.target.classList.contains('search-media') && event.keyCode == 13) {
                event.preventDefault();

                return false;
            }
        });
    };

    // Job done, lets run.
    Extension.Uploader.init();

})(window, document, jQuery);
