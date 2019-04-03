<?php
 namespace Composer\Command; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class ProhibitsCommand extends BaseDependencyCommand { protected function configure() { parent::configure(); $this ->setName('prohibits') ->setAliases(array('why-not')) ->setDescription('Shows which packages prevent the given package from being installed.') ->setHelp( <<<EOT
Displays detailed information about why a package cannot be installed.

<info>php composer.phar prohibits composer/composer</info>

EOT
) ; } protected function execute(InputInterface $input, OutputInterface $output) { return parent::doExecute($input, $output, true); } } 