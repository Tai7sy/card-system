<?php
 namespace Psy\Command; use PhpParser\NodeTraverser; use PhpParser\PrettyPrinter\Standard as Printer; use Psy\Input\CodeArgument; use Psy\ParserFactory; use Psy\Readline\Readline; use Psy\Sudo\SudoVisitor; use Symfony\Component\Console\Input\InputInterface; use Symfony\Component\Console\Output\OutputInterface; class SudoCommand extends Command { private $readline; private $parser; private $traverser; private $printer; public function __construct($name = null) { $parserFactory = new ParserFactory(); $this->parser = $parserFactory->createParser(); $this->traverser = new NodeTraverser(); $this->traverser->addVisitor(new SudoVisitor()); $this->printer = new Printer(); parent::__construct($name); } public function setReadline(Readline $readline) { $this->readline = $readline; } protected function configure() { $this ->setName('sudo') ->setDefinition([ new CodeArgument('code', CodeArgument::REQUIRED, 'Code to execute.'), ]) ->setDescription('Evaluate PHP code, bypassing visibility restrictions.') ->setHelp( <<<'HELP'
Evaluate PHP code, bypassing visibility restrictions.

e.g.
<return>>>> $sekret->whisper("hi")</return>
<return>PHP error:  Call to private method Sekret::whisper() from context '' on line 1</return>

<return>>>> sudo $sekret->whisper("hi")</return>
<return>=> "hi"</return>

<return>>>> $sekret->word</return>
<return>PHP error:  Cannot access private property Sekret::$word on line 1</return>

<return>>>> sudo $sekret->word</return>
<return>=> "hi"</return>

<return>>>> $sekret->word = "please"</return>
<return>PHP error:  Cannot access private property Sekret::$word on line 1</return>

<return>>>> sudo $sekret->word = "please"</return>
<return>=> "please"</return>
HELP
); } protected function execute(InputInterface $input, OutputInterface $output) { $code = $input->getArgument('code'); if ($code === '!!') { $history = $this->readline->listHistory(); if (\count($history) < 2) { throw new \InvalidArgumentException('No previous command to replay'); } $code = $history[\count($history) - 2]; } if (\strpos('<?', $code) === false) { $code = '<?php ' . $code; } $nodes = $this->traverser->traverse($this->parse($code)); $sudoCode = $this->printer->prettyPrint($nodes); $shell = $this->getApplication(); $shell->addCode($sudoCode, !$shell->hasCode()); } private function parse($code) { try { return $this->parser->parse($code); } catch (\PhpParser\Error $e) { if (\strpos($e->getMessage(), 'unexpected EOF') === false) { throw $e; } return $this->parser->parse($code . ';'); } } } 