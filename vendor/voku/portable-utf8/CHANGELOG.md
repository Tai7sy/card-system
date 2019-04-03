# Changelog

### 5.2.9 (2018-10-01)
- fix binary check for UTF16 / UTF32

### 5.2.8 (2018-09-29)
- "composer.json" -> remove extra alias
- UTF8::substr_replace() -> optimize performance
- UTF8::clean() -> add tests with "\00"
- update "UTF8::get_file_type()"
- fix fallback for "UTF8::encode()"

### 5.2.7 (2018-09-15)
- simplify "UTF8::encode()"

### 5.2.6 (2018-09-15)
- use more vanilla php fallbacks
- new encoding-from / -to parameter for "UTF8::encode()"
- optimize "mbstring_func_overload" fallbacks

### 5.2.5 (2018-09-11)
- more fixes for "mbstring_func_overload"

### 5.2.4 (2018-09-11)
- optimize performance for "UTF8::remove_bom()"
- optimize performance for "UTF8::is_binary()"
- fix tests with "mbstring_func_overload"

### 5.2.3 (2018-09-07)
- fix some breaking changes from "strict_types=1"

### 5.2.2 (2018-09-06)
- use "UTF8::encode()" internal ...

### 5.2.1 (2018-08-06)
- add more php-warnings
- optimize native php fallback
- fix tests without "mbstring"-ext
- UTF8::strlen() can return "false", if "mbstring" is not installed
    
### 5.2.0 (2018-08-05)
- use phpstan (+ fixed many code smells)
- added more tests

### 5.1.0 (2018-08-03)
- merge methods from "Stringy" into "UTF8"
- added many new tests

### 5.0.6 (2018-05-02)
- fix "UTF8::to_ascii()"
- update encoding list for "UTF8::str_detect_encoding()"
- use root namespaces for php functions


### 5.0.5 (2018-02-14)
- update -> "require-dev" -> "phpunit"


### 5.0.4 (2018-01-07)
- performance optimizing
  -> use "UTF8::normalize_encoding()" if needed
  -> use "CP850" encoding only if needed
  -> don't use "UTF8::html_encode()" in a foreach-loop


### 5.0.3 (2018-01-02)
- fix tests without "finfo" (e.g. appveyor - windows)
- optimize "UTF8::str_detect_encoding()"
  -> return "false" if we detect binary data, but not for UTF-16 / UTF-32


### 5.0.2 (2018-01-02)
- optimize "UTF8::is_binary()" v2
- edit "UTF8::clean()" -> do not remote diamond question mark by default
  -> fix for e.g. UTF8::file_get_contents() + auto encoding detection


### 5.0.1 (2018-01-01)
- optimize "UTF8::is_binary()" + new tests


### 5.0.0 (2017-12-10)
- "Fixed symfony/polyfill dependencies"

-> this is a breaking change, because "symfony/polyfill" contains more dependencies as we use now

before:
    "symfony/polyfill-apcu": "~1.0",
    "symfony/polyfill-php54": "~1.0",
    "symfony/polyfill-php55": "~1.0",
    "symfony/polyfill-php56": "~1.0",
    "symfony/polyfill-php70": "~1.0",
    "symfony/polyfill-php71": "~1.0",
    "symfony/polyfill-php72": "~1.0",
    "symfony/polyfill-iconv": "~1.0",
    "symfony/polyfill-intl-grapheme": "~1.0",
    "symfony/polyfill-intl-icu": "~1.0",
    "symfony/polyfill-intl-normalizer": "~1.0",
    "symfony/polyfill-mbstring": "~1.0",
    "symfony/polyfill-util": "~1.0",
    "symfony/polyfill-xml": "~1.0"
        
after:
    "symfony/polyfill-php72": "~1.0",
    "symfony/polyfill-iconv": "~1.0",
    "symfony/polyfill-intl-grapheme": "~1.0",
    "symfony/polyfill-intl-normalizer": "~1.0",
    "symfony/polyfill-mbstring": "~1.0"


### 4.0.1 (2017-11-13)
- update php-unit to 6.x


### 4.0.0 (2017-11-13)
- "php": ">=7.0"
  * drop support for PHP < 7.0
  * use "strict_types"
  * "UTF8::number_format()" -> removed deprecated method 
  * "UTF8::normalize_encoding()" -> change $fallback from bool to empty string
