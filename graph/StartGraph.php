<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    11.06.2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\service\graph;

use GraphQL\Error\Error;
use GraphQL\Error\FormattedError;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use yii\caching\TagDependency;
use yii\helpers\FileHelper;
use zetsoft\models\page\PageAction;
use zetsoft\models\page\PageBlocks;
use zetsoft\models\page\PageBlocksType;
use zetsoft\models\page\PageControl;
use zetsoft\models\page\PageModule;
use zetsoft\system\actives\ZActiveRecord;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\kernels\ZView;
use zetsoft\system\module\Models;

class StartGraph extends ZFrame
{
    public function run()
    {
        try {
            $input = $this->httpPost();
            $query = $input['query'];

            // Создание схемы
            try {
                $schema = new Schema([
                    'query' => Types::query(),
                    'mutation' => Types::mutation()
                ]);

                $variables = isset($input['variables']) ?$input['variables']: null;
                // Выполнение запроса
                
                $result = GraphQL::executeQuery($schema, $query, null, null, $variables)
                    ->toArray();
            } catch(\Exception $e) {
                $result = [
                    'status' => 500,
                    'errors' => [FormattedError::createFromException($e)]
                ];
            }

        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }
        
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($result);
    }
}
