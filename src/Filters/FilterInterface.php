<?php namespace Platform\Media\Filters;

interface FilterInterface {

	/**
	 * Executes the filter.
	 *
	 * @param  ..  $data
	 * @return
	 */
	public function run($data);

}
