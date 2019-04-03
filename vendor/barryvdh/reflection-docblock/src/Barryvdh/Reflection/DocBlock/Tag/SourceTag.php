<?php
 namespace Barryvdh\Reflection\DocBlock\Tag; use Barryvdh\Reflection\DocBlock\Tag; class SourceTag extends Tag { protected $startingLine = 1; protected $lineCount = null; public function getContent() { if (null === $this->content) { $this->content = "{$this->startingLine} {$this->lineCount} {$this->description}"; } return $this->content; } public function setContent($content) { parent::setContent($content); if (preg_match( '/^
                # Starting line
                ([1-9]\d*)
                \s*
                # Number of lines
                (?:
                    ((?1))
                    \s+
                )?
                # Description
                (.*)
            $/sux', $this->description, $matches )) { $this->startingLine = (int)$matches[1]; if (isset($matches[2]) && '' !== $matches[2]) { $this->lineCount = (int)$matches[2]; } $this->setDescription($matches[3]); $this->content = $content; } return $this; } public function getStartingLine() { return $this->startingLine; } public function setStartingLine($startingLine) { $this->startingLine = $startingLine; $this->content = null; return $this; } public function getLineCount() { return $this->lineCount; } public function setLineCount($lineCount) { $this->lineCount = $lineCount; $this->content = null; return $this; } } 