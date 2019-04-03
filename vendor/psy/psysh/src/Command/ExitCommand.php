<?php
 namespace Psy\Command; use Psy\Exception\BreakException; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class ExitCommand extends Command { protected function configure() { $this ->setName('exit') ->setAliases(['quit', 'q']) ->setDefinition([]) ->setDescription('End the current session and return to caller.') ->setHelp( <<<'HELP'
End the current session and return to caller.

e.g.
<return>>>> exit</return>
HELP
); } protected function execute(InputInterface $input, OutputInterface $output) { throw new BreakException('Goodbye'); } } 