<?php
 namespace Barryvdh\Reflection; use Barryvdh\Reflection\DocBlock\Tag; use Barryvdh\Reflection\DocBlock\Context; use Barryvdh\Reflection\DocBlock\Location; class DocBlock implements \Reflector { protected $short_description = ''; protected $long_description = null; protected $tags = array(); protected $context = null; protected $location = null; protected $isTemplateStart = false; protected $isTemplateEnd = false; public function __construct( $docblock, Context $context = null, Location $location = null ) { if (is_object($docblock)) { if (!method_exists($docblock, 'getDocComment')) { throw new \InvalidArgumentException( 'Invalid object passed; the given reflector must support ' . 'the getDocComment method' ); } $docblock = $docblock->getDocComment(); } $docblock = $this->cleanInput($docblock); list($templateMarker, $short, $long, $tags) = $this->splitDocBlock($docblock); $this->isTemplateStart = $templateMarker === '#@+'; $this->isTemplateEnd = $templateMarker === '#@-'; $this->short_description = $short; $this->long_description = new DocBlock\Description($long, $this); $this->parseTags($tags); $this->context = $context; $this->location = $location; } protected function cleanInput($comment) { $comment = trim( preg_replace( '#[ \t]*(?:\/\*\*|\*\/|\*)?[ \t]{0,1}(.*)?#u', '$1', $comment ) ); if (substr($comment, -2) == '*/') { $comment = trim(substr($comment, 0, -2)); } $comment = str_replace(array("\r\n", "\r"), "\n", $comment); return $comment; } protected function splitDocBlock($comment) { if (strpos($comment, '@') === 0) { return array('', '', '', $comment); } $comment = preg_replace('/\h*$/Sum', '', $comment); preg_match( '/
            \A
            # 1. Extract the template marker
            (?:(\#\@\+|\#\@\-)\n?)?

            # 2. Extract the summary
            (?:
              (?! @\pL ) # The summary may not start with an @
              (
                [^\n.]+
                (?:
                  (?! \. \n | \n{2} )     # End summary upon a dot followed by newline or two newlines
                  [\n.] (?! [ \t]* @\pL ) # End summary when an @ is found as first character on a new line
                  [^\n.]+                 # Include anything else
                )*
                \.?
              )?
            )

            # 3. Extract the description
            (?:
              \s*        # Some form of whitespace _must_ precede a description because a summary must be there
              (?! @\pL ) # The description may not start with an @
              (
                [^\n]+
                (?: \n+
                  (?! [ \t]* @\pL ) # End description when an @ is found as first character on a new line
                  [^\n]+            # Include anything else
                )*
              )
            )?

            # 4. Extract the tags (anything that follows)
            (\s+ [\s\S]*)? # everything that follows
            /ux', $comment, $matches ); array_shift($matches); while (count($matches) < 4) { $matches[] = ''; } return $matches; } protected function parseTags($tags) { $result = array(); $tags = trim($tags); if ('' !== $tags) { if ('@' !== $tags[0]) { throw new \LogicException( 'A tag block started with text instead of an actual tag,' . ' this makes the tag block invalid: ' . $tags ); } foreach (explode("\n", $tags) as $tag_line) { if (isset($tag_line[0]) && ($tag_line[0] === '@')) { $result[] = $tag_line; } else { $result[count($result) - 1] .= "\n" . $tag_line; } } foreach ($result as $key => $tag_line) { $result[$key] = Tag::createInstance(trim($tag_line), $this); } } $this->tags = $result; } public function getText() { $short = $this->getShortDescription(); $long = $this->getLongDescription()->getContents(); if ($long) { return "{$short}\n\n{$long}"; } else { return $short; } } public function setText($comment) { list(,$short, $long) = $this->splitDocBlock($comment); $this->short_description = $short; $this->long_description = new DocBlock\Description($long, $this); return $this; } public function getShortDescription() { return $this->short_description; } public function getLongDescription() { return $this->long_description; } public function isTemplateStart() { return $this->isTemplateStart; } public function isTemplateEnd() { return $this->isTemplateEnd; } public function getContext() { return $this->context; } public function getLocation() { return $this->location; } public function getTags() { return $this->tags; } public function getTagsByName($name) { $result = array(); foreach ($this->getTags() as $tag) { if ($tag->getName() != $name) { continue; } $result[] = $tag; } return $result; } public function hasTag($name) { foreach ($this->getTags() as $tag) { if ($tag->getName() == $name) { return true; } } return false; } public function appendTag(Tag $tag) { if (null === $tag->getDocBlock()) { $tag->setDocBlock($this); } if ($tag->getDocBlock() === $this) { $this->tags[] = $tag; } else { throw new \LogicException( 'This tag belongs to a different DocBlock object.' ); } return $tag; } public function deleteTag(Tag $tag) { if (($key = array_search($tag, $this->tags)) !== false) { unset($this->tags[$key]); return true; } return false; } public static function export() { throw new \Exception('Not yet implemented'); } public function __toString() { return 'Not yet implemented'; } } 