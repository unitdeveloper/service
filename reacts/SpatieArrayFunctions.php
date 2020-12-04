<?php


namespace zetsoft\service\reacts;


use \Spatie;
use zetsoft\system\kernels\ZFrame;

class SpatieArrayFunctions extends ZFrame
{
//    private $array;
//    private $numReq;
//
//    public function run($array, $numReq){
//        //echo 'success';
//        $this->array = $array;
//        $this->numReq = $numReq;
//        $spatie  = new Spatie( $array, $numReq = 1);
//        function array_rand_value(array $array, $numReq = 1)
//        {
//            if (!count($array)) {
//                return null;
//            }
//
//            $keys = array_rand($array, $numReq);
//
//            if ($numReq === 1) {
//                return $array[$keys];
//            }
//
//            return array_intersect_key($array, array_flip($keys));
//        }
//    }

    protected $testArray = [
        'one' => 'a',
        'two' => 'b',
        'three' => 'c',
    ];

    /**
     * @test
     */
    public function it_can_handle_an_empty_array()
    {
        $this->assertNull(Spatie\array_rand_value([]));
    }

    /**
     * @test
     */
    public function it_can_get_a_random_value()
    {
        $testArrayValues = array_values($this->testArray);
        $randomArrayValue = Spatie\array_rand_value($this->testArray);

        $this->assertContains($randomArrayValue, $testArrayValues);
    }

    /**
     * @test
     */
    public function it_can_get_multiple_random_values()
    {
        $testArrayValues = array_values($this->testArray);
        $randomArrayValues = Spatie\array_rand_value($this->testArray, 2);

        $this->assertCount(2, $randomArrayValues);

        foreach ($randomArrayValues as $randomArrayValue) {
            $this->assertContains($randomArrayValue, $testArrayValues);
        }
    }
}
