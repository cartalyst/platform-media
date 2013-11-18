;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {
	};

	function MediaManager(manager, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		// Cache the form selector
		this.$form = manager;

		// Initialize the Menu Manager
		this.initializer();

	}

	MediaManager.prototype = {

		initializer : function() {

			// Avoid scope issues
			var self = this;



			self.dropzone = new Dropzone(self.$form, {
				autoProcessQueue : false,
				addRemoveLinks : true
			});

			this.events();

		},


		events : function() {

			// Avoid scope issues
			var self = this;

			var totalFiles = 0;

			var totalSize = 0;

			self.dropzone.on('addedfile', function(file) {

				totalFiles += 1;

				totalSize += file.size;

				$('[data-media-queued]').html(totalFiles);
				$('[data-media-total]').html(totalSize);

			});

			self.dropzone.on('removedfile', function(file) {

				totalFiles -= 1;

				totalSize -= file.size;

				$('[data-media-queued]').html(totalFiles);
				$('[data-media-total]').html(totalSize);

			});

			self.dropzone.on('complete', function(file) {

				self.dropzone.removeFile(file);

			});

			$('[data-media-upload]').on('click', function(e) {

				e.preventDefault();

				self.dropzone.processQueue();

			});

		}

	}

	$.mediamanager = function(manager, options) {

		return new MediaManager(manager, options);

	};

})(jQuery, window, document);
