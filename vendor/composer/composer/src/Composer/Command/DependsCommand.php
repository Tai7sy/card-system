<?php
 namespace Composer\Command; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class DependsCommand extends BaseDependencyCommand { protected function configure() { parent::configure(); $this ->setName('depends') ->setAliases(array('why')) ->setDescription('Shows which packages cause the given package to be installed.') ->setHelp( <<<EOT
Displays detailed information about where a package is referenced.

<info>php composer.phar depends composer/composer</info>

EOT
) ; } protected function execute(InputInterface $input, OutputInterface $output) { return parent::doExecute($input, $output, false); } } 