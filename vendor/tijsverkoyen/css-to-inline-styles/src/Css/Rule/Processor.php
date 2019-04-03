<?php
 namespace TijsVerkoyen\CssToInlineStyles\Css\Rule; use Symfony\Component\CssSelector\Node\Specificity; use \TijsVerkoyen\CssToInlineStyles\Css\Property\Processor as PropertyProcessor; class Processor { public function splitIntoSeparateRules($rulesString) { $rulesString = $this->cleanup($rulesString); return (array) explode('}', $rulesString); } private function cleanup($string) { $string = str_replace(array("\r", "\n"), '', $string); $string = str_replace(array("\t"), ' ', $string); $string = str_replace('"', '\'', $string); $string = preg_replace('|/\*.*?\*/|', '', $string); $string = preg_replace('/\s\s+/', ' ', $string); $string = trim($string); $string = rtrim($string, '}'); return $string; } public function convertToObjects($rule, $originalOrder) { $rule = $this->cleanup($rule); $chunks = explode('{', $rule); if (!isset($chunks[1])) { return array(); } $propertiesProcessor = new PropertyProcessor(); $rules = array(); $selectors = (array) explode(',', trim($chunks[0])); $properties = $propertiesProcessor->splitIntoSeparateProperties($chunks[1]); foreach ($selectors as $selector) { $selector = trim($selector); $specificity = $this->calculateSpecificityBasedOnASelector($selector); $rules[] = new Rule( $selector, $propertiesProcessor->convertArrayToObjects($properties, $specificity), $specificity, $originalOrder ); } return $rules; } public function calculateSpecificityBasedOnASelector($selector) { $idSelectorsPattern = "  \#"; $classAttributesPseudoClassesSelectorsPattern = "  (\.[\w]+)                     # classes
                        |
                        \[(\w+)                       # attributes
                        |
                        (\:(                          # pseudo classes
                          link|visited|active
                          |hover|focus
                          |lang
                          |target
                          |enabled|disabled|checked|indeterminate
                          |root
                          |nth-child|nth-last-child|nth-of-type|nth-last-of-type
                          |first-child|last-child|first-of-type|last-of-type
                          |only-child|only-of-type
                          |empty|contains
                        ))"; $typePseudoElementsSelectorPattern = "  ((^|[\s\+\>\~]+)[\w]+       # elements
                        |
                        \:{1,2}(                    # pseudo-elements
                          after|before
                          |first-letter|first-line
                          |selection
                        )
                      )"; return new Specificity( preg_match_all("/{$idSelectorsPattern}/ix", $selector, $matches), preg_match_all("/{$classAttributesPseudoClassesSelectorsPattern}/ix", $selector, $matches), preg_match_all("/{$typePseudoElementsSelectorPattern}/ix", $selector, $matches) ); } public function convertArrayToObjects(array $rules, array $objects = array()) { $order = 1; foreach ($rules as $rule) { $objects = array_merge($objects, $this->convertToObjects($rule, $order)); $order++; } return $objects; } public static function sortOnSpecificity(Rule $e1, Rule $e2) { $e1Specificity = $e1->getSpecificity(); $value = $e1Specificity->compareTo($e2->getSpecificity()); if ($value === 0) { $value = $e1->getOrder() - $e2->getOrder(); } return $value; } } 