;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {
		autoProcessQueue : false,
		addRemoveLinks : true,
		parallelUploads : 6,

		languages : {
			file : 'File',
			files : 'Files',
			inQueue : ':amount :files in the queue'
		}
	};

	function MediaManager(manager, options) {

		// Extend the default options with the provided options
		this.opt = $.extend({}, defaults, options);

		// Create a language dictionary
		this.langDict = defaults.languages;

		// Cache the form selector
		this.$form = manager;

		// Initialize the Media Manager
		this.initializer();

	}

	MediaManager.prototype = {

		/**
		 * Initializes the Media Manager.
		 *
		 * @return void
		 */
		initializer : function() {

			// Avoid scope issues
			var self = this;

			// Prepare Dropzone
			self.dropzone = new Dropzone(self.$form, self.opt);

			// Initialize the event listeners
			this.events();

		},

		/**
		 * Initializes all the event listeners.
		 *
		 * @return void
		 */
		events : function() {

			// Avoid scope issues
			var self = this;

			var totalFiles = 0;

			var totalSize = 0;

			$('[data-media-queued]').html(self.totalFiles(totalFiles));
			$('[data-media-total]').html(self.dropzone.filesize(totalSize));

			self.dropzone.on('addedfile', function(file) {

				totalFiles += 1;

				totalSize += file.size;

				$('[data-media-queued]').html(self.totalFiles(totalFiles));
				$('[data-media-total]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('removedfile', function(file) {

				totalFiles -= 1;

				totalSize -= file.size;

				$('[data-media-queued]').html(self.totalFiles(totalFiles));
				$('[data-media-total]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('complete', function(file) {

				self.dropzone.removeFile(file);

				self.dropzone.processQueue();

			});

			$('[data-media-upload]').on('click', function(e) {

				e.preventDefault();

				self.dropzone.processQueue();

			});

		},

		totalFiles : function(totalFiles) {

			// Avoid scope issues
			var self = this;

			return self.langDict.inQueue
				.replace(':amount', totalFiles)
				.replace(':files', (totalFiles == 1 ? self.langDict.file : self.langDict.files));

		}

	}

	$.mediamanager = function(manager, options) {

		return new MediaManager(manager, options);

	};

})(jQuery, window, document);
