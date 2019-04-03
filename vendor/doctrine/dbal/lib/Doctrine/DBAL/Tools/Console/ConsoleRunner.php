<?php
 namespace Doctrine\DBAL\Tools\Console; use Doctrine\DBAL\Connection; use Doctrine\DBAL\Tools\Console\Command\ImportCommand; use Doctrine\DBAL\Tools\Console\Command\ReservedWordsCommand; use Doctrine\DBAL\Tools\Console\Command\RunSqlCommand; use Symfony\Component\Console\Helper\HelperSet; use Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper; use Symfony\Component\Console\Application; use Doctrine\DBAL\Version; class ConsoleRunner { static public function createHelperSet(Connection $connection) { return new HelperSet(array( 'db' => new ConnectionHelper($connection) )); } static public function run(HelperSet $helperSet, $commands = array()) { $cli = new Application('Doctrine Command Line Interface', Version::VERSION); $cli->setCatchExceptions(true); $cli->setHelperSet($helperSet); self::addCommands($cli); $cli->addCommands($commands); $cli->run(); } static public function addCommands(Application $cli) { $cli->addCommands(array( new RunSqlCommand(), new ImportCommand(), new ReservedWordsCommand(), )); } static public function printCliConfigTemplate() { echo <<<'HELP'
You are missing a "cli-config.php" or "config/cli-config.php" file in your
project, which is required to get the Doctrine-DBAL Console working. You can use the
following sample as a template:

<?php
use Doctrine\DBAL\Tools\Console\ConsoleRunner;

// replace with the mechanism to retrieve DBAL connection in your app
$connection = getDBALConnection();

// You can append new commands to $commands array, if needed

return ConsoleRunner::createHelperSet($connection);

HELP;
} } 