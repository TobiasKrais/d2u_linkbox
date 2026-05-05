(function ($) {
	function updateOffsetTarget($wrapper) {
		var $source = $wrapper.find('[data-d2u-linkbox-offset-source]').first();
		var $target = $wrapper.find('[data-d2u-linkbox-offset-target]').first();

		if (!$source.length || !$target.length) {
			return;
		}

		if ($source.val() === '12') {
			$target.stop(true, true).slideUp();
			return;
		}

		$target.stop(true, true).slideDown();
	}

	function updatePictureOnlyTarget($wrapper) {
		var $source = $wrapper.find('[data-d2u-linkbox-picture-only-source]').first();
		var $target = $wrapper.find('[data-d2u-linkbox-picture-only-target]').first();

		if (!$source.length || !$target.length) {
			return;
		}

		if ($source.is(':checked')) {
			$target.stop(true, true).slideUp();
			return;
		}

		$target.stop(true, true).slideDown();
	}

	function initModuleConfig(index, element) {
		var $wrapper = $(element);

		updateOffsetTarget($wrapper);
		updatePictureOnlyTarget($wrapper);

		$wrapper.find('[data-d2u-linkbox-offset-source]')
			.off('change.d2uLinkboxBackend')
			.on('change.d2uLinkboxBackend', function () {
				updateOffsetTarget($wrapper);
			});

		$wrapper.find('[data-d2u-linkbox-picture-only-source]')
			.off('change.d2uLinkboxBackend')
			.on('change.d2uLinkboxBackend', function () {
				updatePictureOnlyTarget($wrapper);
			});
	}

	$(function () {
		$('.d2u-linkbox-module-config').each(initModuleConfig);
	});
})(jQuery);