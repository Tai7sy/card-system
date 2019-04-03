<?php
 namespace Psy\Command; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class ClearCommand extends Command { protected function configure() { $this ->setName('clear') ->setDefinition([]) ->setDescription('Clear the Psy Shell screen.') ->setHelp( <<<'HELP'
Clear the Psy Shell screen.

Pro Tip: If your PHP has readline support, you should be able to use ctrl+l too!
HELP
); } protected function execute(InputInterface $input, OutputInterface $output) { $output->write(\sprintf('%c[2J%c[0;0f', 27, 27)); } } 