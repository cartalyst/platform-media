;(function($, window, document, undefined) {

	'use strict';

	/**
	 * Default settings
	 *
	 * @var array
	 */
	var defaults = {
		onComplete : function() {},
		onSuccess : function() {},

		autoProcessQueue : false,
		addRemoveLinks : true,
		parallelUploads : 6,
		dictRemoveFile : 'Cancel',
		dictCancelUpload : 'Cancel',

		languages : {
			file : 'File',
			files : 'Files',
			inQueue : '<strong>:amount</strong> :files in the queue'
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

			$('[data-media-total-files]').html(self.totalFiles(totalFiles));
			$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			self.dropzone.on('addedfile', function(file) {

				totalFiles += 1;

				totalSize += file.size;

				$('[data-media-total-files]').html(self.totalFiles(totalFiles));
				$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('removedfile', function(file) {

				totalFiles -= 1;

				totalSize -= file.size;

				$('[data-media-total-files]').html(self.totalFiles(totalFiles));
				$('[data-media-total-size]').html(self.dropzone.filesize(totalSize));

			});

			self.dropzone.on('success', function(file) {

				self.dropzone.removeFile(file);

				self.opt.onSuccess();

			});

			self.dropzone.on('complete', function(file) {

				self.dropzone.processQueue();

				self.opt.onComplete();

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
