[![Build Status](https://travis-ci.org/voku/portable-utf8.svg?branch=master)](https://travis-ci.org/voku/portable-utf8)
[![Build status](https://ci.appveyor.com/api/projects/status/gnejjnk7qplr7f5t/branch/master?svg=true)](https://ci.appveyor.com/project/voku/portable-utf8/branch/master)
[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8.svg?type=shield)](https://app.fossa.io/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8?ref=badge_shield)
[![Coverage Status](https://coveralls.io/repos/voku/portable-utf8/badge.svg?branch=master&service=github)](https://coveralls.io/github/voku/portable-utf8?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/voku/portable-utf8/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/voku/portable-utf8/?branch=master)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/997c9bb10d1c4791967bdf2e42013e8e)](https://www.codacy.com/app/voku/portable-utf8)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/be5bf087-366c-463e-ac9f-c184db6347ba/mini.png)](https://insight.sensiolabs.com/projects/be5bf087-366c-463e-ac9f-c184db6347ba)
[![Latest Stable Version](https://poser.pugx.org/voku/portable-utf8/v/stable)](https://packagist.org/packages/voku/portable-utf8) 
[![Total Downloads](https://poser.pugx.org/voku/portable-utf8/downloads)](https://packagist.org/packages/voku/portable-utf8) 
[![Latest Unstable Version](https://poser.pugx.org/voku/portable-utf8/v/unstable)](https://packagist.org/packages/voku/portable-utf8)
[![License](https://poser.pugx.org/voku/portable-utf8/license)](https://packagist.org/packages/voku/portable-utf8)

# Portable UTF-8

## Description

It is written in PHP (PHP 7+) and can work without "mbstring", "iconv" or any other extra encoding php-extension on your server. 

The benefit of Portable UTF-8 is that it is easy to use, easy to bundle. This library will also 
auto-detect your server environment and will use the installed php-extensions if they are available, 
so you will have the best possible performance.

As fallback we will use Symfony Polyfills, if needed. (https://github.com/symfony/polyfill)

The project based on ...
+ Hamid Sarfraz's work (http://pageconfig.com/attachments/portable-utf8.php) 
+ Nicolas Grekas's work (https://github.com/tchwork/utf8) 
+ Behat's work (https://github.com/Behat/Transliterator) 
+ Sebastián Grignoli's work (https://github.com/neitanod/forceutf8) 
+ Ivan Enderlin's work (https://github.com/hoaproject/Ustring)
+ and many cherry-picks from "github"-gists and "Stack Overflow"-snippets ...

## Index

* [Alternative](#alternative)
* [Install](#install-portable-utf-8-via-composer-require)
* [Why Portable UTF-8?](#why-portable-utf-8)
* [Requirements and Recommendations](#requirements-and-recommendations)
* [Warning](#warning)
* [Usage](#usage)
* [Class methods](#class-methods)
    * [access](#accessstring-str-int-pos)
    * [add_bom_to_string](#add_bom_to_stringstring-str)
    * [binary_to_str](#binary_to_strmixed-bin)
    * [bom](#bom)
    * [chr](#chrint-code_point--string)
    * [chr_map](#chr_mapstringarray-callback-string-str--array)
    * [chr_size_list](#chr_size_liststring-str--array)
    * [chr_to_decimal](#chr_to_decimalstring-chr--int)
    * [chr_to_hex](#chr_to_hexstring-chr-string-pfix--u)
    * [chunk_split](#chunk_splitstring-body-int-chunklen--76-string-end--rn--string)
    * [clean](#cleanstring-str-bool-remove_bom--false-bool-normalize_whitespace--false-bool-normalize_msword--false-bool-keep_non_breaking_space--false--string)
    * [cleanup](#cleanupstring-str--string)
    * [codepoints](#codepointsmixed-arg-bool-u_style--false--array)
    * [count_chars](#count_charsstring-str-bool-cleanutf8--false--array)
    * [encode](#encodestring-encoding-string-str-bool-force--true--string)
    * [file_get_contents](#file_get_contentsstring-filename-intnull-flags--null-resourcenull-context--null-intnull-offset--null-intnull-maxlen--null-int-timeout--10-bool-converttoutf8--true--string) 
    * [file_has_bom](#file_has_bomstring-file_path--bool)
    * [filter](#filtermixed-var-int-normalization_form--4-string-leading_combining----mixed)
    * [filter_input](#filter_inputint-type-string-var-int-filter--filter_default-nullarray-option--null--string) 
    * [filter_input_array](#filter_input_arrayint-type-mixed-definition--null-bool-add_empty--true--mixed) 
    * [filter_var](#filter_varstring-var-int-filter--filter_default-array-option--null--string) 
    * [filter_var_array](#filter_var_arrayarray-data-mixed-definition--null-bool-add_empty--true--mixed)
    * [fits_inside](#fits_insidestring-str-int-box_size--bool)
    * [fix_simple_utf8](#fix_simple_utf8string-str--string)
    * [fix_utf8](#fix_utf8stringstring-str--mixed) 
    * [getCharDirection](#getchardirectionstring-char--string-rtl-or-ltr)
    * [hex_to_int](#hex_to_intstring-str--intfalse)
    * [html_encode](#html_encodestring-str-bool-keepasciichars--false-string-encoding--utf-8--string)
    * [html_entity_decode](#html_entity_decodestring-str-int-flags--null-string-encoding--utf-8--string) 
    * [htmlentities](#htmlentitiesstring-str-int-flags--ent_compat-string-encoding--utf-8-bool-double_encode--true--string) 
    * [htmlspecialchars](#htmlspecialcharsstring-str-int-flags--ent_compat-string-encoding--utf-8-bool-double_encode--true--string) 
    * [int_to_hex](#int_to_hexint-int-string-pfix--u--str)
    * [is_ascii](#is_asciistring-str--bool) 
    * [is_base64](#is_base64string-str--bool) 
    * [is_binary](#is_binarymixed-input--bool)
    * [is_binary_file](#is_binary_filestring-file--bool) 
    * [is_bom](#is_bomstring-str--bool)
    * [is_json](#is_jsonstring-str--bool)
    * [is_html](#is_htmlstring-str--bool)
    * [is_utf16](#is_utf16string-str--intfalse) 
    * [is_utf32](#is_utf32string-str--intfalse) 
    * [is_utf8](#is_utf8string-str-bool-strict--false--bool)
    * [json_decode](#json_decodestring-json-bool-assoc--false-int-depth--512-int-options--0--mixed) 
    * [json_encode](#json_encodemixed-value-int-options--0-int-depth--512--string)
    * [lcfirst](#lcfirststring-str--string)
    * [max](#maxmixed-arg--string) 
    * [max_chr_width](#max_chr_widthstring-str--int)
    * [min](#minmixed-arg--string)
    * [normalize_encoding](#normalize_encodingstring-encoding--string)
    * [normalize_msword](#normalize_mswordstring-str--string)
    * [normalize_whitespace](#normalize_whitespacestring-str-bool-keepnonbreakingspace--false-bool-keepbidiunicodecontrols--false--string)
    * [ord](#ordstring-chr--int) 
    * [parse_str](#parse_strstring-str-result--bool)
    * [range](#rangemixed-var1-mixed-var2--array)
    * [remove_bom](#remove_bomstring-str--string)
    * [remove_duplicates](#remove_duplicatesstring-str-stringarray-what-----string)
    * [remove_invisible_characters](#remove_invisible_charactersstring-str-bool-url_encoded--true-string-replacement----string) 
    * [replace_diamond_question_mark](#replace_diamond_question_markstring-str-string-unknown----string) 
    * [trim](#trimstring-str---string-chars--inf--string) 
    * [rtrim](#rtrimstring-str---string-chars--inf--string) 
    * [ltrim](#ltrimstring-str-string-chars--inf--string) 
    * [single_chr_html_encode](#single_chr_html_encodestring-char-bool-keepasciichars--false--string) 
    * [split](#splitstring-str-int-length--1-bool-cleanutf8--false--array) 
    * [str_detect_encoding](#str_detect_encodingstring-str--string) 
    * [str_ireplace](#str_ireplacemixed-search-mixed-replace-mixed-subject-int-count--null--mixed) 
    * [str_limit_after_word](#str_limit_after_wordstring-str-int-length--100-stirng-straddon----string) 
    * [str_pad](#str_padstring-str-int-pad_length-string-pad_string----int-pad_type--str_pad_right--string) 
    * [str_repeat](#str_repeatstring-str-int-multiplier--string) 
    * [str_shuffle](#str_shufflestring-str--string) 
    * [str_sort](#str_sortstring-str-bool-unique--false-bool-desc--false--string) 
    * [str_split](#str_splitstring-str-int-len--1--array) 
    * [str_to_binary](#str_to_binarystring-str--string) 
    * [str_word_count](#str_word_countstring-str-int-format--0-string-charlist----string) 
    * [strcmp](#strcmpstring-str1-string-str2--int) 
    * [strnatcmp](#strnatcmpstring-str1-string-str2--int) 
    * [strcasecmp](#strcasecmpstring-str1-string-str2--int) 
    * [strnatcasecmp](#strnatcasecmpstring-str1-string-str2--int) 
    * [strncasecmp](#strncasecmpstring-str1-string-str2-int-len--int)
    * [strncmp](#strncmpstring-str1-string-str2-int-len--int) 
    * [string](#stringstring-str1-string-str2--int) 
    * [string_has_bom](#string_has_bomstring-str--bool) 
    * [strip_tags](#strip_tagsstring-str-stingnull-allowable_tags--null--string) 
    * [strlen](#strlenstring-str-string-encoding--utf-8-bool-cleanutf8--false--int) 
    * [strwidth](#strwidthstring-str-string-encoding--utf-8-bool-cleanutf8--false--int) 
    * [strpbrk](#strpbrkstring-haystack-string-char_list--string) 
    * [strpos](#strposstring-haystack-string-char_list--intfalse) 
    * [stripos](#striposstr-needle-before_needle--false--intfalse) 
    * [strrpos](#strrposstring-haystack-string-needle-int-offset--0-bool-cleanutf8--false--stringfalse) 
    * [strripos](#strriposstring-haystack-string-needle-int-offset--0-bool-cleanutf8--false--stringfalse) 
    * [strrchr](#strrchrstring-haystack-string-needle-bool-part--false-string-encoding--stringfalse) 
    * [strrichr](#strrichrstring-haystack-string-needle-bool-part--false-string-encoding--stringfalse) 
    * [strrev](#strrevstring-str--string)
    * [strspn](#strspnstring-str-string-mask-int-offset--0-int-length--2147483647--string) 
    * [strstr](#strstrstring-str-string-needle-bool-before_needle--false--string) 
    * [stristr](#stristrstring-str-string-needle-bool-before_needle--false--string)
    * [strtocasefold](#strtocasefoldstring-str-bool-full--true--string) 
    * [strtolower](#strtolowerstring-str-string-encoding--utf-8--string) 
    * [strtoupper](#strtoupperstring-str-string-encoding--utf-8--string) 
    * [strtr](#strtrstring-str-stringarray-from-stringarray-to--inf--string) 
    * [substr](#substrstring-str-int-start--0-int-length--null-string-encoding--utf-8-bool-cleanutf8--false--string) 
    * [substr_compare](#substr_comparestring-main_str-string-str-int-offset-int-length--2147483647-bool-case_insensitivity--false--int) 
    * [substr_count](#substr_countstring-haystack-string-needle-int-offset--0-int-length--null-string-encoding--utf-8--int) 
    * [substr_replace](#substr_replacestringstring-str-stringstring-replacement-intint-start-intint-length--null--stringarray) 
    * [swapCase](#swapcasestring-str-string-string-encoding--utf-8--string)
    * [to_ascii](#to_asciistring-str-string-unknown---bool-strict--string) 
    * [to_utf8](#to_utf8stringstring-str--stringstring) 
    * [to_iso8859](#to_iso8859stringstring-str--stringstring) 
    * [ucfirst](#ucfirststring-str--string) 
    * [ucwords](#ucwordsstring-str--string)
    * [urldecode](#urldecodestring-str--string)
    * [utf8_decode](#utf8_decodestring-str--string)
    * [utf8_encode](#utf8_encodestring-str--string) 
    * [words_limit](#words_limitstring-str-int-words--100-string-straddon----string) 
    * [wordwrap](#wordwrapstring-str-int-width--75-string-break--n-bool-cut--false--string) 
* [Unit Test](#unit-test)
* [License and Copyright](#license-and-copyright)

## Alternative

If you like a more Object Oriented Way to edit strings, then you can take a look at [voku/Stringy](https://github.com/voku/Stringy), it's a fork of "danielstjules/Stringy" but it used the "Portable UTF-8"-Class and some extra methodes. 

```php
// Standard library
strtoupper('fòôbàř');       // 'FòôBàř'
strlen('fòôbàř');           // 10

// mbstring 
// WARNING: if you don't use a polyfill like "Portable UTF-8", you need to install the php-extension "mbstring" on your server
mb_strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
mb_strlen('fòôbàř');        // '6'

// Portable UTF-8
use voku\helper\UTF8;
UTF8::strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
UTF8::strlen('fòôbàř');        // '6'

// voku/Stringy
use Stringy\Stringy as S;
$stringy = S::create('fòôbàř');
$stringy->toUpperCase();    // 'FÒÔBÀŘ'
$stringy->length();         // '6'
```


## Install "Portable UTF-8" via "composer require"
```shell
composer require voku/portable-utf8
```


##  Why Portable UTF-8?[]()
PHP 5 and earlier versions have no native Unicode support. To bridge the gap, there exist several extensions like "mbstring", "iconv" and "intl".

The problem with "mbstring" and others is that most of the time you cannot ensure presence of a specific one on a server. If you rely on one of these, your application is no more portable. This problem gets even severe for open source applications that have to run on different servers with different configurations. Considering these, I decided to write a library:

## Requirements and Recommendations

*   No extensions are required to run this library. Portable UTF-8 only needs PCRE library that is available by default since PHP 4.2.0 and cannot be disabled since PHP 5.3.0. "\u" modifier support in PCRE for UTF-8 handling is not a must.
*   PHP 5.3 is the minimum requirement, and all later versions are fine with Portable UTF-8.
*   PHP 7.0 is the minimum requirement since version 4.0 of Portable UTF-8, otherwise composer will install an older version
*   To speed up string handling, it is recommended that you have "mbstring" or "iconv" available on your server, as well as the latest version of PCRE library
*   Although Portable UTF-8 is easy to use; moving from native API to Portable UTF-8 may not be straight-forward for everyone. It is highly recommended that you do not update your scripts to include Portable UTF-8 or replace or change anything before you first know the reason and consequences. Most of the time, some native function may be all what you need.
*   There is also a shim for "mbstring", "iconv" and "intl", so you can use it also on shared webspace. 

## Warning

By default this library requires that you using "UTF-8"-encoding on your server and it will force "UTF-8" by "bootstrap.php".
If you need to disable this behavior you can define "PORTABLE_UTF8__DISABLE_AUTO_FILTER".

```php
define('PORTABLE_UTF8__DISABLE_AUTO_FILTER', 1);
```

## Usage

Example 1: UTF8::cleanup()
```php
  echo UTF8::cleanup('�DÃ¼sseldorf�');
  
  // will output
  // Düsseldorf
```

Example 2: UTF8::strlen()
```php
  $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

  echo strlen($string) . "\n<br />";
  echo UTF8::strlen($string) . "\n<br />";

  // will output:
  // 70
  // 67

  $string_test1 = strip_tags($string);
  $string_test2 = UTF8::strip_tags($string);

  echo strlen($string_test1) . "\n<br />";
  echo UTF8::strlen($string_test2) . "\n<br />";

  // will output:
  // 53
  // 50
```

Example 3: UTF8::fix_utf8()
```php

  echo UTF8::fix_utf8('DÃ¼sseldorf');
  echo UTF8::fix_utf8('Ã¤');
  
  // will output:
  // Düsseldorf
  // ä
```

# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API.


## Class methods

##### access(string $str, int $pos)

Return the character at the specified position: $str[1] like functionality.

```php
UTF8::access('fòô', 1); // 'ò'
```

##### add_bom_to_string(string $str)

Prepends UTF-8 BOM character to the string and returns the whole string.

If BOM already existed there, the Input string is returned.

```php
UTF8::add_bom_to_string('fòô'); // "\xEF\xBB\xBF" . 'fòô'
```

##### binary_to_str(mixed $bin)

Convert binary into an string.

opposite: UTF8::str_to_binary()

```php
UTF8::binary_to_str('11110000100111111001100010000011'); // '😃'
```

##### bom()

Returns the UTF-8 Byte Order Mark Character.

```php
UTF8::bom(); // "\xEF\xBB\xBF"
```

##### chr(int $code_point) : string

Generates a UTF-8 encoded character from the given code point.

opposite: UTF8::ord()

```php
UTF8::chr(0x2603); // '☃'
```

##### chr_map(string|array $callback, string $str) : array

Applies callback to all characters of a string.

```php
UTF8::chr_map(['voku\helper\UTF8', 'strtolower'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']
```

##### chr_size_list(string $str) : array

Generates a UTF-8 encoded character from the given code point.

 1 byte => U+0000  - U+007F
 2 byte => U+0080  - U+07FF
 3 byte => U+0800  - U+FFFF
 4 byte => U+10000 - U+10FFFF

```php
UTF8::chr_size_list('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]
```

##### chr_to_decimal(string $chr) : int

Get a decimal code representation of a specific character.

opposite: UTF8::decimal_to_chr()

alias: UTF8::chr_to_int()

```php
UTF8::chr_to_decimal('§'); // 0xa7
```

##### chr_to_hex(string $chr, string $pfix = 'U+')

Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.

```php
UTF8::chr_to_hex('§'); // U+00a7
```

##### chunk_split(string $body, int $chunklen = 76, string $end = "\r\n") : string

Splits a string into smaller chunks and multiple lines, using the specified line ending character.

```php
UTF8::chunk_split('ABC-ÖÄÜ-中文空白-κόσμε', 3); // "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε"
```

##### clean(string $str, bool $remove_bom = false, bool $normalize_whitespace = false, bool $normalize_msword = false, bool $keep_non_breaking_space = false, bool $replace_diamond_question_mark = false, bool $remove_invisible_characters = true) : string

Accepts a string and removes all non-UTF-8 characters from it + extras if needed.

```php
UTF8::clean("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - DÃ¼sseldorf'
```

##### cleanup(string $str) : string

Clean-up a and show only printable UTF-8 chars at the end + fix UTF-8 encoding.

```php
UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - Düsseldorf'
```

##### codepoints(mixed $arg, bool $u_style = false) : array

Accepts a string and returns an array of Unicode code points.

opposite: UTF8::string()

```php
UTF8::codepoints('κöñ'); // array(954, 246, 241)
// ... OR ...
UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
```

##### count_chars(string $str, bool $cleanUtf8 = false) : array

Returns count of characters used in a string.

```php
UTF8::count_chars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)
```

##### decimal_to_chr(mixed $int) : string

Converts a int-value into an UTF-8 character.

opposite: UTF8::chr_to_decimal()

alias: UTF8::int_to_chr()

```php
UTF8::decimal_to_chr(931); // 'Σ'
```

##### encode(string $encoding, string $str, bool $autodetectFromEncoding = true, string $fromEncoding = '') : string

Encode a string with a new charset-encoding.

INFO:  The different to "UTF8::utf8_encode()" is that this function, try to fix also broken / double encoding,
       so you can call this function also on a UTF-8 String and you don't mess the string.

```php
UTF8::encode('ISO-8859-1', '-ABC-中文空白-'); // '-ABC-????-'
//
UTF8::encode('UTF-8', '-ABC-中文空白-'); // '-ABC-中文空白-'
//
UTF8::encode('HTML', '-ABC-中文空白-'); // '-ABC-&#20013;&#25991;&#31354;&#30333;-'
//
UTF8::encode('BASE64', '-ABC-中文空白-'); // 'LUFCQy3kuK3mlofnqbrnmb0t'
```

##### file_get_contents(string $filename, int|null $flags = null, resource|null $context = null, int|null $offset = null, int|null $maxlen = null, int $timeout = 10, bool $convertToUtf8 = true) : string

Reads entire file into a string.

WARNING: do not use UTF-8 Option ($convertToUtf8) for binary-files (e.g.: images) !!!

```php
UTF8::file_get_contents('utf16le.txt'); // ...
```

##### file_has_bom(string $file_path) : bool

Checks if a file starts with BOM (Byte Order Mark) character.

```php
UTF8::file_has_bom('utf8_with_bom.txt'); // true
```

##### filter(mixed $var, int $normalization_form = 4, string $leading_combining = '◌') : mixed

Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter(array("\xE9", 'à', 'a')); // array('é', 'à', 'a')
```

##### filter_input(int $type, string $var, int $filter = FILTER_DEFAULT, null|array $option = null) : string

"filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input(INPUT_GET, 'foo', FILTER_SANITIZE_STRING)); // 'bar'
```

##### filter_input_array(int $type, mixed $definition = null, bool $add_empty = true) : mixed

"filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
// _GET['foo'] = 'bar';
UTF8::filter_input_array(INPUT_GET, array('foo' => 'FILTER_SANITIZE_STRING')); // array('bar')
```

##### filter_var(string $var, int $filter = FILTER_DEFAULT, array $option = null) : string

"filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
UTF8::filter_var('-ABC-中文空白-', FILTER_VALIDATE_URL); // false
```

##### filter_var_array(array $data, mixed $definition = null, bool $add_empty = true) : mixed

"filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

```php
$filters = [ 
  'name'  => ['filter'  => FILTER_CALLBACK, 'options' => ['voku\helper\UTF8', 'ucwords']],
  'age'   => ['filter'  => FILTER_VALIDATE_INT, 'options' => ['min_range' => 1, 'max_range' => 120]],
  'email' => FILTER_VALIDATE_EMAIL,
];

$data = [
  'name' => 'κόσμε', 
  'age' => '18', 
  'email' => 'foo@bar.de'
];

UTF8::filter_var_array($data, $filters, true); // ['name' => 'Κόσμε', 'age' => 18, 'email' => 'foo@bar.de']
```

##### fits_inside(string $str, int $box_size) : bool

Check if the number of unicode characters are not more than the specified integer.

```php
UTF8::fits_inside('κόσμε', 6); // false
```

##### fix_simple_utf8(string $str) : string

Try to fix simple broken UTF-8 strings.

INFO: Take a look at "UTF8::fix_utf8()" if you need a more advanced fix for broken UTF-8 strings.

```php
UTF8::fix_simple_utf8('DÃ¼sseldorf'); // 'Düsseldorf'
```

##### fix_utf8(string|string[] $str) : mixed

Fix a double (or multiple) encoded UTF8 string.

```php
UTF8::fix_utf8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration'); // 'Fédération'
```

##### getCharDirection(string $char) : string ('RTL' or 'LTR')

Get character of a specific character.

```php
UTF8::getCharDirection('ا'); // 'RTL'
```

##### hex_to_chr(string $hexdec) : string|false

Converts a hexadecimal-value into an UTF-8 character.

opposite: UTF8::chr_to_hex()

```php
UTF8::hex_to_chr('U+00a7'); // '§'
```

##### hex_to_int(string $hexdec) : int|false

Converts hexadecimal U+xxxx code point representation to integer.

opposite: UTF8::int_to_hex()

```php
UTF8::hex_to_int('U+00f1'); // 241
```

##### html_encode(string $str, bool $keepAsciiChars = false, string $encoding = 'UTF-8') : string

Converts a UTF-8 string to a series of HTML numbered entities.

opposite: UTF8::html_decode()

```php
UTF8::html_encode('中文空白'); // '&#20013;&#25991;&#31354;&#30333;'
```

##### html_entity_decode(string $str, int $flags = null, string $encoding = 'UTF-8') : string

UTF-8 version of html_entity_decode()

The reason we are not using html_entity_decode() by itself is because
while it is not technically correct to leave out the semicolon
at the end of an entity most browsers will still interpret the entity
correctly. html_entity_decode() does not convert entities without
semicolons, so we are left with our own little solution here. Bummer.

Convert all HTML entities to their applicable characters

opposite: UTF8::html_encode()

alias: UTF8::html_decode()

```php
UTF8::html_entity_decode('&#20013;&#25991;&#31354;&#30333;'); // '中文空白' 
```

##### htmlentities(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert all applicable characters to HTML entities: UTF-8 version of htmlentities()

```php
UTF8::htmlentities('<白-öäü>'); // '&lt;&#30333;-&ouml;&auml;&uuml;&gt;'
```

##### htmlspecialchars(string $str, int $flags = ENT_COMPAT, string $encoding = 'UTF-8', bool $double_encode = true) : string

Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()

INFO: Take a look at "UTF8::htmlentities()"

```php
UTF8::htmlspecialchars('<白-öäü>'); // '&lt;白-öäü&gt;'
```

##### int_to_hex(int $int, string $pfix = 'U+') : str

Converts Integer to hexadecimal U+xxxx code point representation.

opposite: UTF8::hex_to_int()

```php
UTF8::int_to_hex(241); // 'U+00f1'
```

##### is_ascii(string $str) : bool

Checks if a string is 7 bit ASCII.

alias: UTF8::isAscii()

```php
UTF8::is_ascii('白'); // false
```

##### is_base64(string $str) : bool

Returns true if the string is base64 encoded, false otherwise.

alias: UTF8::isBase64()

```php
UTF8::is_base64('4KSu4KWL4KSo4KS/4KSa'); // true
```

##### is_binary(mixed $input, bool $strict = false) : bool

Check if the input is binary... (is look like a hack).

alias: UTF8::isBinary()

```php
UTF8::is_binary(01); // true
```

##### is_binary_file(string $file) : bool

Check if the file is binary.

```php
UTF8::is_binary('./utf32.txt'); // true
```

##### is_bom(string $str) : bool

Checks if the given string is equal to any "Byte Order Mark".

WARNING: Use "UTF8::string_has_bom()" if you will check BOM in a string.

alias: UTF8::isBom()

```php
UTF8::is_bom("\xef\xbb\xbf"); // true
```

##### is_json(string $str) : bool

Try to check if "$str" is an json-string.

alias: UTF8::isJson()

```php
UTF8::is_json('{"array":[1,"¥","ä"]}'); // true
```

##### is_html(string $str) : bool

Check if the string contains any html-tags <lall>.

alias: UTF8::isHtml()

```php
UTF8::is_html('<b>lall</b>'); // true
```

##### is_utf16(string $str) : int|false

Check if the string is UTF-16: This function will return  false if is't not UTF-16, 1 for UTF-16LE, 2 for UTF-16BE.

alias: UTF8::isUtf16()

```php
UTF8::is_utf16(file_get_contents('utf-16-le.txt')); // 1
UTF8::is_utf16(file_get_contents('utf-16-be.txt')); // 2
UTF8::is_utf16(file_get_contents('utf-8.txt')); // false
```

##### is_utf32(string $str) : int|false

Check if the string is UTF-32: This function will return  false if is't not UTF-32, 1 for UTF-32LE, 2 for UTF-32BE.

alias: UTF8::isUtf16()

```php
UTF8::is_utf32(file_get_contents('utf-32-le.txt')); // 1
UTF8::is_utf32(file_get_contents('utf-32-be.txt')); // 2
UTF8::is_utf32(file_get_contents('utf-8.txt')); // false
```

##### is_utf8(string $str, bool $strict = false) : bool

Checks whether the passed string contains only byte sequences that appear valid UTF-8 characters.

alias: UTF8::isUtf8()

```php
UTF8::is_utf8('Iñtërnâtiônàlizætiøn'); // true
UTF8::is_utf8("Iñtërnâtiônàlizætiøn\xA0\xA1"); // false
```

##### json_decode(string $json, bool $assoc = false, int $depth = 512, int $options = 0) : mixed

Decodes a JSON string.

```php
UTF8::json_decode('[1,"\u00a5","\u00e4"]'); // array(1, '¥', 'ä')
```

##### json_encode(mixed $value, int $options = 0, int $depth = 512) : string

Returns the JSON representation of a value.

```php
UTF8::json_enocde(array(1, '¥', 'ä')); // '[1,"\u00a5","\u00e4"]'
```

##### lcfirst(string $str) : string

Makes string's first char lowercase.

```php
UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'); // ñTËRNÂTIÔNÀLIZÆTIØN 
```

##### max(mixed $arg) : string

Returns the UTF-8 character with the maximum code point in the given data.

```php
UTF8::max('abc-äöü-中文空白'); // 'ø'
```

##### max_chr_width(string $str) : int

Calculates and returns the maximum number of bytes taken by any
UTF-8 encoded character in the given string.

```php
UTF8::max_chr_width('Intërnâtiônàlizætiøn'); // 2
```

##### min(mixed $arg) : string

Returns the UTF-8 character with the minimum code point in the given data.

```php
UTF8::min('abc-äöü-中文空白'); // '-'
```

##### normalize_encoding(string $encoding) : string

Normalize the encoding-"name" input.

```php
UTF8::normalize_encoding('UTF8'); // 'UTF-8'
```

##### normalize_msword(string $str) : string

Normalize some MS Word special characters.

```php
UTF8::normalize_msword('„Abcdef…”'); // '"Abcdef..."'
```

##### normalize_whitespace(string $str, bool $keepNonBreakingSpace = false, bool $keepBidiUnicodeControls = false) : string

Normalize the whitespace.

```php
UTF8::normalize_whitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true); // "abc-\xc2\xa0-öäü- -"
```

##### ord(string $chr) : int

Calculates Unicode code point of the given UTF-8 encoded character.

opposite: UTF8::chr()

```php
UTF8::ord('☃'); // 0x2603
```

##### parse_str(string $str, &$result, bool $cleanUtf8 = false) : bool

Parses the string into an array (into the the second parameter).

WARNING: Instead of "parse_str()" this method do not (re-)placing variables in the current scope,
          if the second parameter is not set!

```php
UTF8::parse_str('Iñtërnâtiônéàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ', $array);
echo $array['Iñtërnâtiônéàlizætiøn']; // '測試'
```

##### range(mixed $var1, mixed $var2) : array

Create an array containing a range of UTF-8 characters.

```php
UTF8::range('κ', 'ζ'); // array('κ', 'ι', 'θ', 'η', 'ζ',)
```

##### remove_bom(string $str) : string

Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.

```php
UTF8::remove_bom("\xEF\xBB\xBFΜπορώ να"); // 'Μπορώ να'
```

##### remove_duplicates(string $str, string|array $what = ' ') : string

Removes duplicate occurrences of a string in another string.

```php
UTF8::remove_duplicates('öäü-κόσμεκόσμε-äöü', 'κόσμε'); // 'öäü-κόσμε-äöü'
```

##### remove_invisible_characters(string $str, bool $url_encoded = true, string $replacement = '') : string

Remove invisible characters from a string.

```php
UTF8::remove_invisible_characters("κόσ\0με"); // 'κόσμε'
```

##### replace_diamond_question_mark(string $str, string $replacementChar = '', bool $processInvalidUtf8 = true) : string

Replace the diamond question mark (�) and invalid-UTF8 chars with the replacement.

```php
UTF8::replace_diamond_question_mark('中文空白�', ''); // '中文空白'
```

##### trim(string $str = '', string $chars = INF) : string

Strip whitespace or other characters from beginning or end of a UTF-8 string.

```php
UTF8::rtrim('   -ABC-中文空白-  '); // '-ABC-中文空白-'
```

##### rtrim(string $str = '', string $chars = INF) : string

Strip whitespace or other characters from end of a UTF-8 string.

```php
UTF8::rtrim('-ABC-中文空白-  '); // '-ABC-中文空白-'
```

##### ltrim(string $str, string $chars = INF) : string

Strip whitespace or other characters from beginning of a UTF-8 string.

```php
UTF8::ltrim('　中文空白　 '); // '中文空白　 '
```

##### single_chr_html_encode(string $char, bool $keepAsciiChars = false) : string

Converts a UTF-8 character to HTML Numbered Entity like "&#123;".

```php
UTF8::single_chr_html_encode('κ'); // '&#954;'
```

##### split(string $str, int $length = 1, bool $cleanUtf8 = false) : array

Convert a string to an array of Unicode characters.

```php
UTF8::split('中文空白'); // array('中', '文', '空', '白')
```

##### str_detect_encoding(string $str) : string

Optimized "\mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.

```php
UTF8::str_detect_encoding('中文空白'); // 'UTF-8'
UTF8::str_detect_encoding('Abc'); // 'ASCII'
```

##### str_ends_with(string $haystack, string $needle) : bool

Check if the string ends with the given substring.

```php
UTF8::str_ends_with('BeginMiddleΚόσμε', 'Κόσμε'); // true
UTF8::str_ends_with('BeginMiddleΚόσμε', 'κόσμε'); // false
```

##### str_iends_with(string $haystack, string $needle) : bool

Check if the string ends with the given substring, case insensitive.

```php
UTF8::str_iends_with('BeginMiddleΚόσμε', 'Κόσμε'); // true
UTF8::str_iends_with('BeginMiddleΚόσμε', 'κόσμε'); // true
```

##### str_ireplace(mixed $search, mixed $replace, mixed $subject, int &$count = null) : mixed

Case-insensitive and UTF-8 safe version of <function>str_replace</function>.

```php
UTF8::str_ireplace('lIzÆ', 'lise', array('Iñtërnâtiônàlizætiøn')); // array('Iñtërnâtiônàlisetiøn')
```

##### str_limit_after_word(string $str, int $length = 100, stirng $strAddOn = '...') : string

Limit the number of characters in a string, but also after the next word.

```php
UTF8::str_limit_after_word('fòô bàř fòô', 8, ''); // 'fòô bàř'
```

##### str_pad(string $str, int $pad_length, string $pad_string = ' ', int $pad_type = STR_PAD_RIGHT) : string

Pad a UTF-8 string to given length with another string.

```php
UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH); // '___中文空白___'
```

##### str_repeat(string $str, int $multiplier) : string

Repeat a string.

```php
UTF8::str_repeat("°~\xf0\x90\x28\xbc", 2); // '°~ð(¼°~ð(¼'
```

##### str_shuffle(string $str) : string

Shuffles all the characters in the string.

```php
UTF8::str_shuffle('fòô bàř fòô'); // 'àòôřb ffòô '
```

##### str_sort(string $str, bool $unique = false, bool $desc = false) : string

Sort all characters according to code points.

```php
UTF8::str_sort('  -ABC-中文空白-  '); // '    ---ABC中文白空'
```

##### str_split(string $str, int $len = 1) : array

Split a string into an array.

```php
UTF8::split('déjà', 2); // array('dé', 'jà')
```

##### str_starts_with(string $haystack, string $needle) : bool

Check if the string starts with the given substring.

```php
UTF8::str_starts_with('ΚόσμεMiddleEnd', 'Κόσμε'); // true
UTF8::str_starts_with('ΚόσμεMiddleEnd', 'κόσμε'); // false
```

##### str_istarts_with(string $haystack, string $needle) : bool

Check if the string starts with the given substring, case insensitive.

```php
UTF8::str_istarts_with('ΚόσμεMiddleEnd', 'Κόσμε'); // true
UTF8::str_iistarts_with('ΚόσμεMiddleEnd', 'κόσμε'); // true
```

##### str_to_binary(string $str) : string

Get a binary representation of a specific string.

opposite: UTF8::binary_to_str()

```php
UTF8::str_to_binary('😃'); // '11110000100111111001100010000011'
```

##### str_word_count(string $str, int $format = 0, string $charlist = '') : string

Get a binary representation of a specific string.

```php
// format: 0 -> return only word count (int)
//
UTF8::str_word_count('中文空白 öäü abc#c'); // 4
UTF8::str_word_count('中文空白 öäü abc#c', 0, '#'); // 3

// format: 1 -> return words (array) 
//
UTF8::str_word_count('中文空白 öäü abc#c', 1); // array('中文空白', 'öäü', 'abc', 'c')
UTF8::str_word_count('中文空白 öäü abc#c', 1, '#'); // array('中文空白', 'öäü', 'abc#c')

// format: 2 -> return words with offset (array) 
//
UTF8::str_word_count('中文空白 öäü ab#c', 2); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc', 13 => 'c')
UTF8::str_word_count('中文空白 öäü ab#c', 2, '#'); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc#c')
```

##### strcmp(string $str1, string $str2) : int

Case-insensitive string comparison: < 0 if str1 is less than str2; 
                                    > 0 if str1 is greater than str2, 
                                    0 if they are equal.

```php
UTF8::strcmp("iñtërnâtiôn\nàlizætiøn", "iñtërnâtiôn\nàlizætiøn"); // 0
```

##### strnatcmp(string $str1, string $str2) : int

Case sensitive string comparisons using a "natural order" algorithm: < 0 if str1 is less than str2; 
                                                                     > 0 if str1 is greater than str2, 
                                                                     0 if they are equal.

INFO: natural order version of UTF8::strcmp()

```php
UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // -1
UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1

UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!')); // -1
```

##### strcasecmp(string $str1, string $str2) : int

Case-insensitive string comparison: < 0 if str1 is less than str2; 
                                    > 0 if str1 is greater than str2, 
                                    0 if they are equal.

INFO: Case-insensitive version of UTF8::strcmp()

```php
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn", "Iñtërnâtiôn\nàlizætiøn"); // 0
```

##### strnatcasecmp(string $str1, string $str2) : int

Case insensitive string comparisons using a "natural order" algorithm: < 0 if str1 is less than str2; 
                                                                       > 0 if str1 is greater than str2, 
                                                                       0 if they are equal.

INFO: natural order version of UTF8::strcasecmp()

```php
UTF8::strnatcasecmp('2', '10Hello WORLD 中文空白!'); // -1
UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1
    
UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
```

##### strncasecmp(string $str1, string $str2, int $len) : int

Case-insensitive string comparison of the first n characters.: 
    < 0 if str1 is less than str2; 
    > 0 if str1 is greater than str2, 
    0 if they are equal.

INFO: Case-insensitive version of UTF8::strncmp()

```php
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn321", "iñtërnâtiôn\nàlizætiøn123", 5); // 0
```

##### strncmp(string $str1, string $str2, int $len) : int

Case-sensitive string comparison of the first n characters.: 
    < 0 if str1 is less than str2; 
    > 0 if str1 is greater than str2, 
    0 if they are equal.

```php
UTF8::strncmp("Iñtërnâtiôn\nàlizætiøn321", "Iñtërnâtiôn\nàlizætiøn123", 5); // 0
```

##### string(string $str1, string $str2) : int

Create a UTF-8 string from code points.

opposite: UTF8::codepoints()

```php
UTF8::string(array(246, 228, 252)); // 'öäü'
```

##### string_has_bom(string $str) : bool

Checks if string starts with "BOM" (Byte Order Mark Character) character.

alias: UTF8::hasBom()

```php
UTF8::string_has_bom("\xef\xbb\xbf foobar"); // true
```

##### strip_tags(string $str, sting|null $allowable_tags = null, bool $cleanUtf8 = false) : string

Strip HTML and PHP tags from a string + clean invalid UTF-8.

```php
UTF8::strip_tags("<span>κόσμε\xa0\xa1</span>"); // 'κόσμε'
```

##### strip_whitespace(string $str)

Strip all whitespace characters. This includes tabs and newline characters, 
as well as multibyte whitespace such as the thin space and ideographic space.

```php
UTF8::strip_whitespace('   Ο     συγγραφέας  '); // 'Οσυγγραφέας'
```

##### strlen(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int

Get the string length, not the byte-length!

```php
UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn")); // 20
```

##### strwidth(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int

Return the width of a string.

```php
UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn")); // 21
```

##### strpbrk(string $haystack, string $char_list) : string

Search a string for any of a set of characters.

```php
UTF8::strpbrk('-中文空白-', '白'); // '白-'
```

##### strpos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int|false

Find position of first occurrence of string in a string.

```php
UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8
```

##### stripos(string $str, string $needle, int $offset = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int|false

Finds position of first occurrence of a string within another, case insensitive.

```php
UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8
```

##### strrpos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string|false

Find position of last occurrence of a string in a string.

```php
UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13
```

##### strripos(string $haystack, string $needle, int $offset = 0, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string|false

Find position of last occurrence of a case-insensitive string.

```php
UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13
```

##### strrchr(string $haystack, string $needle, bool $part = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string|false

Finds the last occurrence of a character in a string within another.

```php
UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε'); // 'κόσμε-äöü'
```

##### strrichr(string $haystack, string $needle, bool $part = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string|false

Finds the last occurrence of a character in a string within another, case insensitive.

```php
UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε'); // 'Aκόσμεκόσμε-äöü'
```

##### strrev(string $str) : string

Reverses characters order in the string.

```php
UTF8::strrev('κ-öäü'); // 'üäö-κ'
```

##### strspn(string $str, string $mask, int $offset = 0, int $length = 2147483647) : string

Finds the length of the initial segment of a string consisting entirely of characters contained within a given mask.

```php
UTF8::strspn('iñtërnâtiônàlizætiøn', 'itñ'); // '3'
```

##### strstr(string $str, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Returns part of haystack string from the first occurrence of needle to the end of haystack.

alias: UTF8::strchr()

```php
$str = 'iñtërnâtiônàlizætiøn';
$search = 'nât';

UTF8::strstr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::strstr($str, $search, true)); // 'iñtër'
```

##### stristr(string $str, string $needle, bool $before_needle = false, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Returns all of haystack starting from and including the first occurrence of needle to the end.

alias: UTF8::strichr()

```php
$str = 'iñtërnâtiônàlizætiøn';
$search = 'NÂT';

UTF8::stristr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::stristr($str, $search, true)); // 'iñtër'
```

##### strtocasefold(string $str, bool $full = true) : string

Unicode transformation for case-less matching.

```php
UTF8::strtocasefold('ǰ◌̱'); // 'ǰ◌̱'
```

##### strtolower(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Make a string lowercase.

```php
UTF8::strtolower('DÉJÀ Σσς Iıİi'); // 'déjà σσς iıii'
```

##### strtoupper(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Make a string uppercase.

```php
UTF8::strtoupper('Déjà Σσς Iıİi'); // 'DÉJÀ ΣΣΣ IIİI'
```

##### strtr(string $str, string|array $from, string|array $to = INF) : string

Translate characters or replace sub-strings.

```php
$arr = array(
    'Hello'   => '○●◎',
    '中文空白' => 'earth',
);
UTF8::strtr('Hello 中文空白', $arr); // '○●◎ earth'
```

##### str_to_words(string $str, string $charlist = '') : array

Convert a string (phrase, sentence, ...) into an array of words.

```php
UTF8::str_to_words('中文空白 oöäü#s', '#') // array('', '中文空白', ' ', 'oöäü#s', '')
```

##### substr(string $str, int $start = 0, int $length = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Get part of a string.

```php
UTF8::substr('中文空白', 1, 2); // '文空'
```

##### substr_compare(string $main_str, string $str, int $offset, int $length = 2147483647, bool $case_insensitivity = false) : int

Binary safe comparison of two strings from an offset, up to length characters.

```php
UTF8::substr_compare("○●◎\r", '●◎', 0, 2); // -1
UTF8::substr_compare("○●◎\r", '◎●', 1, 2); // 1
UTF8::substr_compare("○●◎\r", '●◎', 1, 2); // 0
```

##### substr_count(string $haystack, string $needle, int $offset = 0, int $length = null, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : int|false

Count the number of substring occurrences.

```php
UTF8::substr_count('中文空白', '文空', 1, 2); // 1
```

##### substr_left(string $haystack, string $needle) : string

Removes an prefix ($needle) from start of the string ($haystack).

```php
UTF8::substr_left('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
UTF8::substr_left('ΚόσμεMiddleEnd', 'κόσμε'); // 'ΚόσμεMiddleEnd'
```

##### substr_ileft(string $haystack, string $needle) : string

Removes an prefix ($needle) from start of the string ($haystack), case insensitive.

```php
UTF8::substr_ileft('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
UTF8::substr_ileft('ΚόσμεMiddleEnd', 'κόσμε'); // 'MiddleEnd'
```

##### substr_right(string $haystack, string $needle) : string

Removes an suffix ($needle) from end of the string ($haystack).

```php
UTF8::substr_right('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
UTF8::substr_right('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddleΚόσμε'
```

##### substr_iright(string $haystack, string $needle) : string

Removes an suffix ($needle) from end of the string ($haystack), case insensitive.

```php
UTF8::substr_iright('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
UTF8::substr_iright('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddle'
```

##### substr_replace(string|string[] $str, string|string[] $replacement, int|int[] $start, int|int[] $length = null) : string|array

Replace text within a portion of a string.

```php
UTF8::substr_replace(array('Iñtërnâtiônàlizætiøn', 'foo'), 'æ', 1); // array('Iæñtërnâtiônàlizætiøn', 'fæoo')
```

##### swapCase(string $str, string string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Returns a case swapped version of the string.

```php
UTF8::swapCase('déJÀ σσς iıII'); // 'DÉjà ΣΣΣ IIii'
```

##### to_ascii(string $str, string $unknown = '?', bool $strict) : string

Convert a string into ASCII.

alias: UTF8::toAscii()
alias: UTF8::str_transliterate()

```php
UTF8::to_ascii('déjà σσς iıii'); // 'deja sss iiii'
```

##### to_utf8(string|string[] $str, bool $decodeHtmlEntityToUtf8 = false) : string|string[]

This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.

* It decode UTF-8 codepoints and unicode escape sequences.
* It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859-1.
* WARNING: It does not remove invalid UTF-8 characters, so you maybe need to use "UTF8::clean()" for this case.

alias: UTF8::toUtf8()

```php
UTF8::to_utf8("\u0063\u0061\u0074"); // 'cat'
```

##### to_iso8859(string|string[] $str) : string|string[]

Convert a string into "ISO-8859"-encoding (Latin-1).

alias: UTF8::toIso8859()
alias: UTF8::to_latin1()
alias: UTF8::toLatin1()

```php
UTF8::to_utf8(UTF8::to_latin1('  -ABC-中文空白-  ')); // '  -ABC-????-  ' 
```

##### ucfirst(string $str, string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Makes string's first char uppercase.

alias: UTF8::ucword()

```php
UTF8::ucfirst('ñtërnâtiônàlizætiøn'); // 'Ñtërnâtiônàlizætiøn'
```

##### ucwords(string $str, array $exceptions = array(), string $charlist = '', string $encoding = 'UTF-8', bool $cleanUtf8 = false) : string

Uppercase for all words in the string.

```php
UTF8::ucwords('iñt ërn âTi ônà liz æti øn'); // 'Iñt Ërn ÂTi Ônà Liz Æti Øn'
```

##### rawurldecode(string $str) : string

Multi decode html entity & fix urlencoded-win1252-chars.

```php
UTF8::urldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest+test'
```

##### urldecode(string $str) : string

Multi decode html entity & fix urlencoded-win1252-chars.

```php
UTF8::urldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest test'
```

##### utf8_decode(string $str) : string

Decodes an UTF-8 string to ISO-8859-1.

```php
UTF8::encode('UTF-8', UTF8::utf8_decode('-ABC-中文空白-')); // '-ABC-????-'
```

##### utf8_encode(string $str) : string

Encodes an ISO-8859-1 string to UTF-8.

```php
UTF8::utf8_decode(UTF8::utf8_encode('-ABC-中文空白-')); // '-ABC-中文空白-'
```

##### words_limit(string $str, int $words = 100, string $strAddOn = '...') : string

Limit the number of words in a string.

```php
UTF8::words_limit('fòô bàř fòô', 2, ''); // 'fòô bàř'
```

##### wordwrap(string $str, int $width = 75, string $break = "\n", bool $cut = false) : string

Wraps a string to a given number of characters

```php
UTF8::wordwrap('Iñtërnâtiônàlizætiøn', 2, '<br>', true)); // 'Iñ<br>të<br>rn<br>ât<br>iô<br>nà<br>li<br>zæ<br>ti<br>øn'
```


## Unit Test

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit
```

### License and Copyright

"Portable UTF8" is free software; you can redistribute it and/or modify it under
the terms of the (at your option):
- [Apache License v2.0](http://apache.org/licenses/LICENSE-2.0.txt), or
- [GNU General Public License v2.0](http://gnu.org/licenses/gpl-2.0.txt).

Unicode handling requires tedious work to be implemented and maintained on the
long run. As such, contributions such as unit tests, bug reports, comments or
patches licensed under both licenses are really welcomed.


[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fvoku%2Fportable-utf8?ref=badge_large)
