<?php
 namespace Barryvdh\Reflection\DocBlock\Tag; use Barryvdh\Reflection\DocBlock\Tag; class MethodTag extends ReturnTag { protected $method_name = ''; protected $arguments = ''; protected $isStatic = false; public function getContent() { if (null === $this->content) { $this->content = ''; if ($this->isStatic) { $this->content .= 'static '; } $this->content .= $this->type . " {$this->method_name}({$this->arguments}) " . $this->description; } return $this->content; } public function setContent($content) { Tag::setContent($content); if (preg_match( '/^
                # Static keyword
                # Declates a static method ONLY if type is also present
                (?:
                    (static)
                    \s+
                )?
                # Return type
                (?:
                    ([\w\|_\\\\]+)
                    \s+
                )?
                # Legacy method name (not captured)
                (?:
                    [\w_]+\(\)\s+
                )?
                # Method name
                ([\w\|_\\\\]+)
                # Arguments
                \(([^\)]*)\)
                \s*
                # Description
                (.*)
            $/sux', $this->description, $matches )) { list( , $static, $this->type, $this->method_name, $this->arguments, $this->description ) = $matches; if ($static) { if (!$this->type) { $this->type = 'static'; } else { $this->isStatic = true; } } else { if (!$this->type) { $this->type = 'void'; } } $this->parsedDescription = null; } return $this; } public function setMethodName($method_name) { $this->method_name = $method_name; $this->content = null; return $this; } public function getMethodName() { return $this->method_name; } public function setArguments($arguments) { $this->arguments = $arguments; $this->content = null; return $this; } public function getArguments() { if (empty($this->arguments)) { return array(); } $arguments = explode(',', $this->arguments); foreach ($arguments as $key => $value) { $arguments[$key] = explode(' ', trim($value)); } return $arguments; } public function isStatic() { return $this->isStatic; } public function setIsStatic($isStatic) { $this->isStatic = $isStatic; $this->content = null; return $this; } } 