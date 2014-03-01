<?php namespace Atrauzzi\EloquentTreeClosureTable\Command {

	use Illuminate\Console\Command;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Illuminate\Database\Migrations\MigrationCreator;


	class GenerateClosureTableMigration extends Command {

		/**
		 * The console command name.
		 *
		 * @var string
		 */
		protected $name = 'eloquent-tree-closure-table:make-migration';

		/**
		 * The console command description.
		 *
		 * @var string
		 */
		protected $description = 'Create a new closure table migration for a model.';

		/**
		 * The migration creator instance.
		 *
		 * @var \Illuminate\Database\Migrations\MigrationCreator
		 */
		protected $creator;

		/**
		 * The path to the packages directory (vendor).
		 *
		 * @var string
		 */
		protected $packagePath;


		/**
		 * @var string
		 */
		protected $modelClass;

		public function __construct(MigrationCreator $creator, $packagePath) {

			parent::__construct();

			$this->creator = $creator;
			$this->packagePath = $packagePath;

		}

		protected function execute(InputInterface $input, OutputInterface $output) {

			$this->modelClass = $input->getArgument('class');




		}

		protected function getArguments() {
			return [
				['class', InputOption::VALUE_REQUIRED, 'The fully qualified name of the class the migration will be for.']
			];
		}

	}

}