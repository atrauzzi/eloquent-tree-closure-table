<?php namespace Atrauzzi\EloquentTreeClosureTable {

	use Illuminate\Database\Eloquent\Relations\HasManyThrough;
	use Illuminate\Database\Query\Expression;
	use Illuminate\Database\Query\JoinClause;


	trait ClosureTableImpl {

		//
		// Configuration
		//

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
		protected $lengthColumn = 'length';

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

		/**
		 * Returns the parent node which is the nearest ancestor.
		 *
		 * @return \Atrauzzi\EloquentTreeClosureTable\HasOneThrough
		 */
		public function parent() {
			return
				(new HasOneThrough(
					$this->newQuery(),
					$this,
					$this->getClosureTable(),
					$this->descendantColumn,
					$this->ancestorColumn,
					get_called_class()
				))
				->where($this->lengthColumn, 1)
			;
		}

		/**
		 * Obtain all descendants that are only one-away.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function children() {
			return $this
				->descendants()
				->where($this->lengthColumn, 1)
			;
		}

		/**
		 * ARRRGHH!
		 */
		public function siblings() {

			/*
				select
					`item`.*,
					`item_closure`.`descendant` as `pivot_descendant`,
					`item_closure`.`ancestor` as `pivot_ancestor`
				from

					`item`

					inner join
						`item_closure`
							ON `item`.`id` = `item_closure`.`descendant`

					inner join
						`item_closure` as `item_closure_parent`
							ON `item_closure`.`ancestor` = `item_closure_parent`.`ancestor`
							and `item_closure`.`descendant` != `item_closure_parent`.`ancestor`

				where
					`item_closure_parent`.`descendant` = 18
					and `item_closure_parent`.`ancestor` != 18
					and `item`.`id` != 18
			*/

			$relation = $this
				->belongsToMany(
					get_called_class(),
					$this->getClosureTable(),
					$this->descendantColumn,
					$this->descendantColumn
				)
				->join($this->getClosureTable() . ' AS item_closure_parent', function (JoinClause $join) {
					$join
						->on($this->getQualifiedAncestor(), '=', 'item_closure_parent.' . $this->ancestorColumn)
						->on($this->getQualifiedDescendant(), '!=', 'item_closure_parent.' . $this->ancestorColumn)
					;
				})
				->where('item_closure_parent.' . $this->descendantColumn, '=', $this->getKey())
				->where('item_closure_parent.' . $this->ancestorColumn, '!=', $this->getKey())
				->where($this->getQualifiedKeyName(), '!=', $this->getKey())
			;

			//dd($relation->getQuery()->getQuery()->wheres);

			// The first one gets the Eloquent query, the second gets the basic query from it.
			$builder = $relation->getQuery()->getQuery();

			// Clear the one that's enforcing the key of the instance on the pivot.
			foreach($builder->wheres as $index => $where)
				if($where['column'] == sprintf('%s.%s', $this->getClosureTable(), $this->descendantColumn))
					unset($builder->wheres[$index]);

			return $relation;

		}

		/**
		 * Fetches all ancestors of the current node.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function ancestors() {
			return $this
				->belongsToMany(
					get_called_class(),
					$this->getClosureTable(),
					$this->descendantColumn,
					$this->ancestorColumn
				)
				// Make sure to exclude the record's reflexive entry as we only want it's children.
				->where($this->getQualifiedKeyName(), '!=', $this->getKey())
			;
		}

		/**
		 * Fetches all descendant nodes of the current node.
		 *
		 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
		 */
		public function descendants() {
			return $this
				->belongsToMany(
					get_called_class(),
					$this->getClosureTable(),
					$this->ancestorColumn,
					$this->descendantColumn
				)
				// Make sure to exclude the record's reflexive entry as we only want it's children.
				->where($this->getQualifiedKeyName(), '!=', $this->getKey())
			;
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


