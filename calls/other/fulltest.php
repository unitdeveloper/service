<?php
namespace zetsoft\service\calls;
use zetsoft\models\App\eyuf\db2\CallsCdr;
use zetsoft\service\ALL\Asteriskk;
use zetsoft\system\actives\ZActiveRecord;
use \zetsoft\system\kernels\ZFrame;

class fulltest extends ZFrame
{
    public function full(){
        $usernumber = $_POST['number'] ?? '701';
        $total= CallsCdr::find()->count();

        $limit = 20;

        // How many pages will there be
        $pages = ceil($total / $limit);

        // What page are we currently on?
        $page = min($pages, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array(
            'options' => array(
                'default'   => 1,
                'min_range' => 1,
            ),
        )));
        $offset = ($page - 1)  * $limit;

        // Some information to display to the user
        $start = $offset + 1;
        $end = min(($offset + $limit), $total);

        // The "back" link
        $prevlink = ($page > 1) ? '<li class="page-item"><a href="?page=1" title="First page" class="page-link">&laquo;</a></li><li class="page-item"><a href="?page=' . ($page - 1) . '" title="Previous page" class="page-link">&lsaquo;</a></li> ' : '<li class="page-item"><span class="page-link">&laquo;</span></li> <li class="page-item"><span class="page-link">&lsaquo;</span></li>';

        // The "forward" link
        $nextlink = ($page < $pages) ? '<li class="page-item"><a href="?page=' . ($page + 1) . '" title="Next page" class="page-link">&rsaquo;</a></li> <li class="page-item"><a href="?page=' . $pages . '" title="Last page" class="page-link">&raquo;</a></li>' : '<li class="page-item"><span class="page-link">&rsaquo;</span></li> <li class="page-item"><span class="page-link">&raquo;</span></li>';

        // Display the paging information
        $stmt = CallsCdr::findBySql("SELECT * FROM cdr GROUP BY cdr.calldate LIMIT  :limit  OFFSET :offset",
            [':limit'=>$limit,':offset'=>$offset])->all();
        $query = CallsCdr::findBySql("SELECT * FROM cdr WHERE src=:src",[':src'=>$usernumber]);


        return $this->viewRender('view',compact('query','usernumber','stmt','nextlink','prevlink','start','end'));
    }
    public function getFullPath($filename){
        // exploder year month day
        $filename = bname($filename);
        $arrayExplode = explode('-', $filename);
        $time = strtotime($arrayExplode[3]);
        $date = date('Y-m-d', $time);
        $pathExplode = explode('-', $date);
        $pathExplode[0];
        $pathExplode[1];
        $pathExplode[2];
        $structure = 'audio/' . $pathExplode[0] . '/' . $pathExplode[1] . '/' . $pathExplode[2] . '/' . $filename;
        return $structure;
    }

}
