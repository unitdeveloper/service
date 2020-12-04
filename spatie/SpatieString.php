<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\spatie;


use Cake\Utility\Inflector;
use Patchwork\Utf8;
use Underscore\Types\Strings;
use zetsoft\system\kernels\ZFrame;

class SpatieString extends ZFrame
{
    #region Vars
    public  $string;

    #endregion


    #region Cores

    public function init()
    {
        parent::init();
    }

    public function test()
    {
        /*test between*/
        $this->string = 'first welcom uzbekistan last';

        $between = $this->between($this->string, 'first', 'last');
        vd($between);

        /*test toUpper*/
        $this->string = 'salom toupper';
        $toUpper = $this->toUpper($this->string);
        vd($toUpper);

        /*test toLower*/
        $this->string = 'SALOM TOLOWER';
        $toLower = $this->toLower($this->string);
        vd($toLower);

        /*test tease*/
        $this->string = 'Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is 
                         advertised as the most popular gun in American crime. Do you believe that shit? It
                         actually says that in the little book that comes with it: the most popular gun in
                         American crime.';
        $tease = $this->tease($this->string, 10);
        vd($tease);

        /*test replaceFirst*/
        $this->string = 'SALOM TOLOWER salom';
        $replaceFirst = $this->replaceFirst($this->string, 'SA', 'Ta');
        vd($replaceFirst);


        /*test replaceLast*/
        $this->string = 'SALOM TOLOWER salom';
        $replaceLast = $this->replaceLast($this->string, 'sa', 'Ta');
        vd($replaceLast);

        /*test prefix*/
        $this->string = 'SALOM';
        $prefix = $this->prefix($this->string, 'world ');
        vd($prefix);

        /*test suffix*/
        $this->string = 'SALOM';
        $suffix = $this->suffix($this->string, ' world');
        vd($suffix);

        /*test concat*/
        $this->string = 'concat';
        $concat = $this->concat($this->string, ' world');
        vd($concat);

        /*test concat*/
        $this->string = 'possessive';
        $possessive = $this->possessive($this->string);
        vd($possessive);

        /*test segment*/
        $this->string = 'segment1 segment2 segment3 segment4';
        $segment = $this->segment($this->string, ' ', 'firstSegment');
        vd($segment);
        /*
         argument position = int(0,1,2 ...) /  firstSegment  / lastSegment
        */

        /*test pop*/
        $this->string = 'pop1 pop2 pop3 pop4';
        $pop = $this->pop($this->string, ' ');
        vd($pop);

        /*test contains*/
        $this->string = 'contains1 contains1 contains3 contains';
        $contains = $this->contains($this->string, 'contains35');
        vd($contains);

        /*test accord*/
        
        $accord = $this->accord(2, 'Create a string from a number.', 'one');
        vd($accord);

        /*test random*/

        $random = $this->random(10);
        vd($random);


        /*test quickRandom*/
        $quickRandom = $this->quickRandom(5);
        vd($quickRandom);

        /*test randomStrings*/
        $randomStrings = $this->randomStrings(3,1);
        vd($randomStrings);

       /* test endsWith*/

       $endsWith = $this->endsWith('hello world', ['welcome our world', 'world']);
        vd($endsWith);

        /* test isIp*/

        $isIp = $this->isIp('120.123.12.1');
        vd($isIp);

        /* test isEmail */

        $isEmail = $this->isEmail('test@mail.ru');
        vd($isEmail);

        /* test isUrl */

        $isUrl = $this->isUrl('https://github.com/Anahkiasen/underscore-php/blob/master/src/Methods/StringsMethods.php');
        vd($isUrl);

        /* test startWith*/

        $startWith = $this->startsWith('hello world', ['welcome our world', 'hello world']);
        vd($startWith);

       /* test find*/

       $find = $this->find('welcome uzbekistan', 'turk');
       vd($find);

      /* test slice*/

      $slice = $this->slice('hello world', 'wor');
      vd($slice);

      /*test baseClass*/

      $baseClass = $this->baseClass('hello owrld');
      vd($baseClass);

      /*test prepend*/

      $prepend = $this->prepend('birinchi', 'ikkinchi ');
      vd($prepend);

        /*test append*/

        $append = $this->append('birinchi', ' ikkinchi');
        vd($append);

        /*test limit*/
        $this->string = 'Now that there is the Tec-9, a crappy spray gun from South Miami. This gun is 
                         advertised as the most popular gun in American crime. Do you believe that shit? It
                         actually says that in the little book that comes with it: the most popular gun in
                         American crime.';

        $limit = $this->limit($this->string);
        vd($limit);

        /*test remove*/

        $remove = $this->remove($this->string, 'Tec-9') ;
        vd($remove);

        /*test toogle*/
        $this->string = 'Now that there is the Tec-9, a crappy spray gun from South Miami.';
        $toogle = $this->toggle($this->string, 'Tec-9', 'Miami', true) ;
        vd($toogle);

       /* test slug*/
       $slug = $this->slug('webuser hello', '*');
       vd($slug);

       /*test explode*/

       $explode = $this->explode($this->string, ' ', 10);
       vd($explode);

       /*test title*/
       $title = $action->title = Azl . 'hello world';
       vd($title);

        $case = $this->toCamelCase('uzbBigCountry');
        vd($case);


        
        
    }

    #endregion



    #region Provided methods

    public function between(string $string, string $start, string $end)
    {
        return string($string)->between($start, $end);
    }

    public function toUpper(string $string)
    {
        return string($string)->toUpper();
    }

    public function toLower(string $string)
    {
        return string($string)->toLower();
    }

    public function tease(string $string, int $length)
    {
        return string($string)->tease($length);
    }

    public function replaceFirst(string $string, $search, $replace)
    {
        return string($string)->replaceFirst($search, $replace);
    }

    public function replaceLast(string $string, $search, $replace)
    {
        return string($string)->replaceLast($search, $replace);
    }

    public function prefix(string $string, $prefix)
    {
        return string($string)->prefix($prefix);
    }

    public function suffix(string $string, $suffix)
    {
        return string($string)->suffix($suffix);
    }

    public function concat(string $string, $concat)
    {
        return string($string)->concat($concat);
    }

    public function possessive(string $string)
    {
        return string($string)->possessive();
    }

    public function segment(string $string, $delimiter, $position)
    {

        if (is_int($position))
        {
            return string($string)->segment($delimiter, $position);
        }
        else if($position === 'firstSegment')
        {
            return string($string)->firstSegment($delimiter);
        }
        else if($position === 'lastSegment')
        {
            return string($string)->lastSegment($delimiter);
        }

    }

    public function pop(string $string, $delimiter)
    {
        return string($string)->pop($delimiter);
    }

    public function contains(string $string, $search)
    {
        return string($string)->contains($search);
    }
    #endregion

    #region   Integration with underscore.php

    /**
     * Create a string from a number.
     *
     * @param int    $count A number
     * @param string $many  If many
     * @param string $one   If one
     * @param string $zero  If one
     *
     * @return string A string
     */
    public function accord($count, $many, $one, $zero = null)
    {
        if ($count === 1) {
            $output = $one;
        } else {
            if ($count === 0 and !empty($zero)) {
                $output = $zero;
            } else {
                $output = $many;
            }
        }

        return sprintf($output, $count);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @throws \RuntimeException
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public function random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false) {
                throw new RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $length);
        }

        return static::quickRandom($length);
    }

    /**
     * Generate a "random" alpha-numeric string.
     *
     * Should not be considered sufficient for cryptography, etc.
     *
     * @param int $length
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public function quickRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    /**
     * Generates a random suite of words.
     *
     * @param int $words  The number of words
     * @param int $length The length of each word
     *
     * @return string
     */
    public function randomStrings($words, $length = 10)
    {
        return Strings::from('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ->shuffle()
            ->split($length)
            ->slice(0, $words)
            ->implode(' ')
            ->obtain();
    }


    ////////////////////////////////////////////////////////////////////
    ////////////////////////////// ANALYZE /////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     *
     * @author Taylor Otwell
     */
    public function endsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ((string) $needle === substr($haystack, -strlen($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a string is an IP.
     *
     * @return bool
     */
    public function isIp($string)
    {
        return filter_var($string, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Check if a string is an email.
     *
     * @return bool
     */
    public function isEmail($string)
    {
        return filter_var($string, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if a string is an url.
     *
     * @return bool
     */
    public function isUrl($string)
    {
        return filter_var($string, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     *
     * @author Taylor Otwell
     */
    public function startsWith($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    ////////////////////////////////////////////////////////////////////
    ///////////////////////////// FETCH FROM ///////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Find one or more needles in one or more haystacks.
     *
     * @param array|string $string        The haystack(s) to search in
     * @param array|string $needle        The needle(s) to search for
     * @param bool         $caseSensitive Whether the function is case sensitive or not
     * @param bool         $absolute      Whether all needle need to be found or whether one is enough
     *
     * @return bool Found or not
     */
    public function find($string, $needle, $caseSensitive = false, $absolute = false)
    {
        // If several needles
        if (is_array($needle) or is_array($string)) {
            $sliceFrom = $string;
            $sliceTo = $needle;

            if (is_array($needle)) {
                $sliceFrom = $needle;
                $sliceTo = $string;
            }

            $found = 0;
            foreach ($sliceFrom as $need) {
                if (static::find($sliceTo, $need, $absolute, $caseSensitive)) {
                    ++$found;
                }
            }

            return ($absolute) ? count($sliceFrom) === $found : $found > 0;
        }

        // If not case sensitive
        if (!$caseSensitive) {
            $string = strtolower($string);
            $needle = strtolower($needle);
        }

        // If string found
        $pos = strpos($string, $needle);

        return !($pos === false);
    }

    /**
     * Slice a string with another string.
     */
    public function slice($string, $slice)
    {
        $sliceTo = static::sliceTo($string, $slice);
        $sliceFrom = static::sliceFrom($string, $slice);

        return [$sliceTo, $sliceFrom];
    }

    /**
     * Slice a string from a certain point.
     */
    public static function sliceFrom($string, $slice)
    {
        $slice = strpos($string, $slice);

        return substr($string, $slice);
    }
    /**
     * Slice a string up to a certain point.
     */
    public static function sliceTo($string, $slice)
    {
        $slice = strpos($string, $slice);

        return substr($string, 0, $slice);
    }

    /**
     * Get the base class in a namespace.
     *
     * @param string $string
     *
     * @return string
     */
    public function baseClass($string)
    {
        $string = static::replace($string, '\\', '/');

        return bname($string);
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////////// ALTER //////////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Prepend a string with another.
     *
     * @param string $string The string
     * @param string $with   What to prepend with
     *
     * @return string
     */
    public function prepend($string, $with)
    {
        return $with.$string;
    }

    /**
     * Append a string to another.
     *
     * @param string $string The string
     * @param string $with   What to append with
     *
     * @return string
     */
    public function append($string, $with)
    {
        return $string.$with;
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int    $limit
     * @param string $end
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public function limit($value, $limit = 100, $end = '...')
    {
        if (mb_strlen($value) <= $limit) {
            return $value;
        }

        return rtrim(mb_substr($value, 0, $limit, 'UTF-8')).$end;
    }

    /**
     * Remove part of a string.
     */
    public function remove($string, $remove)
    {
        if (is_array($remove)) {
            $string = preg_replace('#('.implode('|', $remove).')#', null, $string);
        }

        // Trim and return
        return trim(str_replace($remove, null, $string));
    }

    /**
     * Correct arguments order for str_replace.
     */
    public static function replace($string, $replace, $with)
    {
        return str_replace($replace, $with, $string);
    }


    /**
     * Toggles a string between two states.
     *
     * @param string $string The string to toggle
     * @param string $first  First value
     * @param string $second Second value
     * @param bool   $loose  Whether a string neither matching 1 or 2 should be changed
     *
     * @return string The toggled string
     */
    public function toggle($string, $first, $second, $loose = false)
    {
        // If the string given match none of the other two, and we're in strict mode, return it
        if (!$loose and !in_array($string, [$first, $second], true)) {
            return $string;
        }

        return $string === $first ? $second : $first;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string $title
     * @param string $separator
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    protected function slug($title, $separator = '-')
    {
        $title = Utf8::toAscii($title);

        // Convert all dashes/underscores into separator
        $flip = $separator === '-' ? '_' : '-';

        $title = preg_replace('!['.preg_quote($flip).']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^'.preg_quote($separator).'\pL\pN\s]+!u', '', mb_strtolower($title));

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('!['.preg_quote($separator).'\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Slugifies a string.
     */
    public static function slugify($string, $separator = '-')
    {
        $string = str_replace('_', ' ', $string);

        return slug($string, $separator);
    }

    /**
     * Explode a string into an array.
     */
    public function explode($string, $with, $limit = null)
    {
        if (!$limit) {
            return explode($with, $string);
        }

        return explode($with, $string, $limit);
    }

    /**
     * Lowercase a string.
     *
     * @param string $string
     *
     * @return string
     */
    public function lower($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }

    /**
     * Get the plural form of an English word.
     *
     * @param string $value
     * @param int    $count
     *
     * @return string
     */
    public function plural($value)
    {
        return Inflector::pluralize($value);
    }

    /**
     * Get the singular form of an English word.
     *
     * @param string $value
     *
     * @return string
     */
    public function singular($value)
    {
        return Inflector::singularize($value);
    }

    /**
     * Uppercase a string.
     *
     * @param string $string
     *
     * @return string
     */
    public function upper($string)
    {
        return mb_strtoupper($string, 'UTF-8');
    }

    /**
     * Convert a string to title case.
     *
     * @param string $string
     *
     * @return string
     */
    public function title($string)
    {
        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * Limit the number of words in a string.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     *
     * @return string
     *
     * @author Taylor Otwell
     */
    public function words($value, $words = 100, $end = '...')
    {
        preg_match('/^\s*+(?:\S++\s*+){1,'.$words.'}/u', $value, $matches);

        if (!isset($matches[0]) || strlen($value) === strlen($matches[0])) {
            return $value;
        }

        return rtrim($matches[0]).$end;
    }

    ////////////////////////////////////////////////////////////////////
    /////////////////////////// CASE SWITCHERS /////////////////////////
    ////////////////////////////////////////////////////////////////////

    /**
     * Convert a string to PascalCase.
     *
     * @param string $string
     *
     * @return string
     */
    public function toPascalCase($string)
    {
        return Inflector::classify($string);
    }

    /**
     * Convert a string to snake_case.
     *
     * @param string $string
     *
     * @return string
     */
    public function toSnakeCase($string)
    {
        return preg_replace_callback('/([A-Z])/', function ($match) {
            return '_'.strtolower($match[1]);
        }, $string);
    }

    /**
     * Convert a string to camelCase.
     *
     * @param string $string
     *
     * @return string
     */
    public function toCamelCase($string)
    {
        return Inflector::camelize($string);
    }

    #endregion
}
