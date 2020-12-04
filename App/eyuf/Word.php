<?php


namespace zetsoft\service\App\eyuf;


use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\ALL\CoreAction;
use zetsoft\models\eyuf\EyufScholar;

use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */
class Word extends ZFrame
{
    public $phpword;
    public $project_name;
    public $section;

    public function cores($model)
    {

        $User = User::findOne($model->core_user_id);
        $coreCompany = $model->getCoreCompany();

        $data = [
            'birthdate' => $model->birthdate,
            'nation' => 'uzbek',
            'program' => empty($model->program) ? '-' : $model->program,
            'degree' => '-',
            'speciality' => $model->speciality,
            'languages' => empty($User) ? '-' : $User->lang,
            'is_deputy' => '-',
            'address' => empty($model->address) ? '-' : $model->address,
            'edu_end' => $model->edu_end,
            'military_cert' => '-',
            'nominations' => '-',
            'community' => '-',
            'position' => '-',
            'edu_degree' => '-',
            'photo' => empty($User) ? '-' : $User->userPhoto(),
            'job' => empty($coreCompany) ? '-' : $model->getCoreCompany()->name,
            'job_start' => '-',
            'job_end' => '-',
        ];

        // Load the template processor
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(Root . '/binary/words/CV_updated.docx');

        $templateProcessor->setValues($data);

        $file = Root . $data['photo'];

        if (file_exists($file))
            $templateProcessor->setImageValue('photo',
                [
                    'path' => $file,
                    'width' => 130,
                    'height' => 180,
                    'ratio' => false,
                ]);

        $templateProcessor->saveAs(Root . "/execut/web/eyuf/resume/" . $model->id . ".docx");

    }

    /**
     *
     * Function  writeWord
     * @param EyufScholar $scholar
     * @return  array
     */
    public function cv($scholar)
    {

        $photo = $scholar->userPhoto();
        $return = [
            'name' => $scholar->name,
            'title' => $scholar->title,
            'birthdate' => $scholar->birthdate,
            'address' => $scholar->address,
            'position' => $scholar->position,
            'program' => $scholar->program,
            'user' => $scholar->core_user_id,
            'edu_start' => $scholar->edu_start,
            'edu_place' => $scholar->edu_place,
            'edu_end' => $scholar->edu_end,
            'speciality' => $scholar->speciality,
            'photo' => normalizer_normalize($photo),
        ];

        return $return;


    }

    public function test()
    {
        //$word = Az::$app->App->eyuf->word->writeWord();

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $word = [
            'name'      => 'Person Name',
            'title'     => 'test data 1',
            'birthdate' => 'test data 2',
            'address'   => 'test data 3',
            'position'  => 'test data 4',
            'program'   => 'test data 5',
            'user'      => 'test_doc',
            'edu_start' => 'test data 7',
            'edu_place' => 'test data 8',
            'edu_end'   => 'test data 9',
            'speciality'=> 'test data 0',
            'photo'     => 'D:\Download\pics\fish.jpg',
        ];

        $templateProcessor = new TemplateProcessor(Root . '/binary/words/2007_777.docx');
        $templateProcessor->setValue('title',       $word['title']);
        $templateProcessor->setValue('birthdate',   $word['birthdate']);
        $templateProcessor->setValue('address',     $word['address']);
        $templateProcessor->setValue('position',    $word['position']);
        $templateProcessor->setValue('program',     $word['program']);
        $templateProcessor->setValue('edu_start',   $word['edu_start']);
        $templateProcessor->setValue('edu_place',   $word['edu_place']);
        $templateProcessor->setValue('edu_end',     $word['edu_end']);
        $templateProcessor->setValue('speciality',  $word['speciality']);
        Az::debug(is_file($word['photo']));
        $templateProcessor->setImageValue('photo', array(
            'path' => Root . '/binary/words/fish.jpg',
            //'path' => $word['photo'],
            'width' => 40, //px
            'height' => 60, //px
            'ratio' => false,
            //'size' => array(102, 40) //px
        ));

        $el = $templateProcessor->save();

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($el);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $objWriter->save(Root . '/binary/words/cv/' . $word['user'] . '.docx');
        shell_exec(Root . '/binary/words/cv/' . $word['user'] . '.docx');

    }

}

