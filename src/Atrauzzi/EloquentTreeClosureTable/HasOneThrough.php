<?php namespace Atrauzzi\EloquentTreeClosureTable {

	use Illuminate\Database\Eloquent\Relations\BelongsToMany;


	/**
	 * Class HasOneThrough
	 *
	 * This is a very simple relation class that operates like a BelongsToMany, but
	 * only returns a single instance.
	 *
	 * @package Atrauzzi\EloquentTreeClosureTable
	 */
	class HasOneThrough extends BelongsToMany {

		/**
		 * Get the result of the relationship.
		 *
		 * @return mixed
		 */
		public function getResults() {
			return $this->get()->first();
		}

	}

}