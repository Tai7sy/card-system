<?php
 namespace Composer\Command; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class AboutCommand extends BaseCommand { protected function configure() { $this ->setName('about') ->setDescription('Shows the short information about Composer.') ->setHelp( <<<EOT
<info>php composer.phar about</info>
EOT
) ; } protected function execute(InputInterface $input, OutputInterface $output) { $this->getIO()->write( <<<EOT
<info>Composer - Package Management for PHP</info>
<comment>Composer is a dependency manager tracking local dependencies of your projects and libraries.
See https://getcomposer.org/ for more information.</comment>
EOT
); } } 