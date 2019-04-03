<?php
 namespace Barryvdh\Reflection\DocBlock\Tag; use Barryvdh\Reflection\DocBlock\Tag; class ExampleTag extends SourceTag { protected $filePath = ''; protected $isURI = false; public function getContent() { if (null === $this->content) { $filePath = ''; if ($this->isURI) { if (false === strpos($this->filePath, ':')) { $filePath = str_replace( '%2F', '/', rawurlencode($this->filePath) ); } else { $filePath = $this->filePath; } } else { $filePath = '"' . $this->filePath . '"'; } $this->content = $filePath . ' ' . parent::getContent(); } return $this->content; } public function setContent($content) { Tag::setContent($content); if (preg_match( '/^
                # File component
                (?:
                    # File path in quotes
                    \"([^\"]+)\"
                    |
                    # File URI
                    (\S+)
                )
                # Remaining content (parsed by SourceTag)
                (?:\s+(.*))?
            $/sux', $this->description, $matches )) { if ('' !== $matches[1]) { $this->setFilePath($matches[1]); } else { $this->setFileURI($matches[2]); } if (isset($matches[3])) { parent::setContent($matches[3]); } else { $this->setDescription(''); } $this->content = $content; } return $this; } public function getFilePath() { return $this->filePath; } public function setFilePath($filePath) { $this->isURI = false; $this->filePath = trim($filePath); $this->content = null; return $this; } public function setFileURI($uri) { $this->isURI = true; if (false === strpos($uri, ':')) { $this->filePath = rawurldecode( str_replace(array('/', '\\'), '%2F', $uri) ); } else { $this->filePath = $uri; } $this->content = null; return $this; } } 