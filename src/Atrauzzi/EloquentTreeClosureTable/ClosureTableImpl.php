<?php namespace Atrauzzi\EloquentTreeClosureTable {

	use Illuminate\Database\Query\JoinClause;


	trait ClosureTableImpl {

		//
		// Configuration
		//

		/**
		 * @var string
		 */
		protected $positionColumn = 'position';

		/**
		 * @var string
		 */
		protected $ancestorColumn = 'ancestor';

		/**
		 * @var string
		 */
		protected $descendantColumn = 'descendant';

		/**
		 * @var string
		 */
		protected $depthColumn = 'depth';

		/**
		 * @var string
		 */
		protected $closureTableSuffix = '_closure';

		/**
		 * @var string
		 */
		protected $closureTable;

		//
		// Interface
		//

		public function parent() {

		}

		public function ancestors() {

		}

		public function children() {

		}

		public function descendants() {
			return $this
				->newQuery()
				->join($this->getClosureTable(), function (JoinClause $join) {
					$join->on($this->getQualifiedKeyName(), '=', $this->getQualifiedDescendant());
				})
				->where($this->getQualifiedAncestor(), $this->getKey())
			;
		}

		public function siblings() {

		}

		//
		// Utility Methods
		//

		/**
		 * Generates the closure table name when not specified and returns it.
		 *
		 * @return string
		 */
		protected function getClosureTable() {

			// Configure our closure table.
			if(empty($this->closureTable))
				$this->closureTable = $this->getTable() . $this->closureTableSuffix;

			return $this->closureTable;

		}

		/**
		 * Returns the corresponding fully qualified closure table column name.
		 *
		 * @param string $column
		 * @return string
		 */
		protected function getQualifiedClosureTableColumn($column) {
			return sprintf('%s.%s', $this->getClosureTable(), $column);
		}

		/**
		 * @return string
		 */
		protected function getQualifiedDescendant() {
			return $this->getQualifiedClosureTableColumn($this->descendantColumn);
		}

		/**
		 * @return string
		 */
		protected function getQualifiedAncestor() {
			return $this->getQualifiedClosureTableColumn($this->ancestorColumn);
		}

	}

}


