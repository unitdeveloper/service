<?php
/*
* Author: Madaminov Shaykhnazar
*
*/

namespace zetsoft\service\spatie;

use Spatie\Regex\Regex;

class RegExcp
{
    #region Vars
    public $pattern;
    public $subject;
    public $id;
    public $replacement;
    public $limit;
    public static $pattern_arr = [];
    public static $subject_arr = [];
    public static $replacement_arr = [];


    #endregion
    #region Cores

    public function init(){
        return $this->test();
    }
    public function run(){

    }
    public function test(){
//        vd(Regex::match('/kc/', 'dsabkcjnjkssxfvkdsd'));
    }
    #endregion
    #region Service

    /**
     * @param string $pattern
     * @param string $subject
     * @uses  Regex::match('/abc/', 'abc')->hasMatch(); // true
     * @uses  Regex::match('/def/', 'abc')->hasMatch(); // false
     *
     * @return \Spatie\Regex\MatchResult bool
     */
    public function matchHasMatch(){
        return Regex::match($this->pattern, $this->subject)->hasMatch();
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @uses  Regex::match('/abc/', 'abc')->result(); // 'abc'
     * @uses  Regex::match('/def/', 'abc')->result(); // null
     *
     * @return \Spatie\Regex\MatchResult bool
     */
    public function matchResult(){
        return Regex::match($this->pattern, $this->subject)->result();
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @param integer $id
     * @uses  Regex::match('/a(b)c/', 'abc')->group(1); // 'b'
     * @uses  Regex::match('/a(b)c/', 'abc')->group(2); // `RegexFailed` exception
     *
     * @return \Spatie\Regex\MatchResult string
     */
    public function matchGroup(){
        return Regex::match($this->pattern, $this->subject)->group($this->id);
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @uses  Regex::matchAll('/abc/', 'abc')->hasMatch(); // true
     * @uses  Regex::matchAll('/abc/', 'abcabc')->hasMatch(); // true
     * @uses  Regex::matchAll('/def/', 'abc')->hasMatch(); // false
     *
     * @return \Spatie\Regex\MatchAllResult bool
     */
    public function matchAllhasMatch(){
        return Regex::matchAll($this->pattern, $this->subject)->hasMatch();
    }

    /**
     * @param string $pattern
     * @param string $subject
     * @uses  $results = Regex::matchAll('/ab([a-z])/', 'abcabd')->results();
     * @uses    $results[0]->result(); // 'abc'
     * @uses    $results[0]->group(1); // 'c'
     * @uses    $results[1]->result(); // 'abd'
     * @uses    $results[1]->group(1); // 'd'
     * @return \Spatie\Regex\MatchAllResult array
     */
    public function matchAllResults(){
        $results = Regex::matchAll('/ab([a-z])/', 'abcabd')->results();
//        foreach ($results as $result) {
//            $result->result();
//            $result->group($this->id);
//        }
        return $results;
    }

    /**
     * @param string|array $pattern
     * @param string|array|callable $replacement
     * @param string|array $subject
     * @param int $limit
     * @uses  Regex::replace('/a/', 'b', 'abc')->result(); // 'bbc'
     * @return \Spatie\Regex\ReplaceResult mixed
     */
    public function replaceResult(){
        return Regex::replace( $this->pattern_arr, $this->replacement_arr, $this->subject_arr, $this->limit = -1)->result();
    }

//Regex::replace('/a/', function (MatchResult $matchResult) {
//    return str_repeat($matchResult->result(), 2);
//}, 'abc')->result(); // 'aabc'


    #endregion



}