<?php
/** Author: Dilshod Khudoyarov */

/** Telegram: @Dishkan2000 */

namespace zetsoft\service\league;
use Faker\Factory;
use zetsoft\system\kernels\ZFrame;

class FakerService extends ZFrame
{
    public function test_case() {
        $this->generate_fake_data_infoTest();
    }

    public function generate_fake_data_infoTest() {
        $result = $this->generate_fake_data_info();
        vd($result);
    }
    public function generate_fake_data_info() {
// use the factory to create a Faker\Generator instance
        $faker = Factory::create();
// generate data by accessing properties
        echo $faker->name;
        // 'Lucy Cechtelar';
        echo $faker->address;
        // "426 Jordy Lodge
        // Cartwrightshire, SC 88120-6700"
        echo $faker->text;
        // Dolores sit sint laboriosam dolorem culpa et autem. Beatae nam sunt fugit
    }

    public function generate_numbers() {
        $faker = Factory::create();

        echo $faker->randomDigit;
        echo $faker->randomDigitNot(5); // it works good!
        echo $faker->randomDigitNotNull;
        echo $faker->randomNumber($nbDigits = NULL, $strict = false) ;
        echo $faker->randomFloat($nbMaxDecimals = NULL, $min = 0, $max = NULL) ;
        echo $faker->numberBetween($min = 1000, $max = 9000);
        echo $faker->randomLetter ;
    }

    public function generate_words() {
        $faker = Factory::create();

        echo $faker->word;
       // echo $faker->words($nb = 3, $asText = false);
        echo $faker->sentence($nbWords = 6, $variableNbWords = true);
       // echo $faker->sentences($nb = 3, $asText = false);
        echo $faker->paragraph($nbSentences = 3, $variableNbSentences = true);
       // echo $faker->paragraphs($nb = 3, $asText = false);
    }

    public function generate_address() {
        $faker = Factory::create();

        echo $faker->cityPrefix;          // 'Lake'
        echo $faker->secondaryAddress;          // 'Suite 961'
        echo $faker->state;          // 'NewMexico'
        echo $faker->stateAbbr;          // 'OH'
        echo $faker->citySuffix;          // 'borough'
        echo $faker->streetSuffix;          // 'Keys'
        echo $faker->buildingNumber;          // '484'
    }

    public function generate_phone_number() {
        $faker = Factory::create();

        echo $faker->phoneNumber;
        echo $faker->tollFreePhoneNumber;
        echo $faker->e164PhoneNumber;
    }

}