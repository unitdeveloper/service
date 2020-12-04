<?php


namespace zetsoft\service\App\eyuf;


use PhpOffice\PhpWord\TemplateProcessor;
use zetsoft\models\page\PageAction;
use zetsoft\models\App\eyuf\EyufScholar;
use zetsoft\models\user\User;
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

        $User = User::findOne($model->user_id);
        $coreCompany = $model->getUserCompany();

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
            'job' => empty($coreCompany) ? '-' : $model->getUserCompany()->name,
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

        $templateProcessor->saveAs(Root . "/exweb/eyuf/resume/" . $model->id . ".docx");

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
            'user' => $scholar->user_id,
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
            'title'     => 'the best title ever',
            'birthdate' => '29-02-2021',
            'address'   => 'edge of the lake',
            'position'  => '1:1',
            'program'   => 'Ms',
            'user'      => 'Cosmo',
            'edu_start' => 'today',
            'edu_place' => 'here',
            'edu_end'   => 'tomorrow',
            'speciality'=> 'engineer',
            'photo'     => 'D:\Download\pics\forest.jpg',
        ];
                                              
        //$templateProcessor = new TemplateProcessor(Root . '/binary/words/2007_777.docx');
        $templateProcessor = new TemplateProcessor(Root . '/binary/words/2007_ad2.docx');
        $templateProcessor->setValues($word);

        Az::debug(is_file($word['photo']), 'isFile?');

        $templateProcessor->setImageValue('photo', array(
            //'path' => 'D:\Download\pics\fish.jpg',
            //'path' => 'D:\Download\pics\environment-forest-grass-leaves-142497.jpg',
            'path' => 'D:\Download\nature1.png',
            //'path' => $word['photo'],
            'width' => 640, //px
            //'borderStyle' => 'solid',
            //'height' => 240, //px
            //'ratio' => false,
            //'size' => array(102, 40) //px
        ));

        $el = $templateProcessor->save();

        $phpWord = \PhpOffice\PhpWord\IOFactory::load($el);

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $objWriter->save(Root . '/exweb/eyuf/cv/' . $word['user'] . '_' . date('H-i-s') . '.docx');

        // shell_exec(Root . '/binary/words/cv/' . $word['user'] . '.docx');

    }

}

