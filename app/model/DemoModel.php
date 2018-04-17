<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午9:36
 */

namespace App\Model;


use Core\Base\Model;
use Core\Module\DaoFactory;

/**
 * Class DemoModel
 * @package App\Model
 */
class DemoModel extends Model
{
    protected $_class_name = "DemoModel";

    /**
     * DemoModel constructor.
     */
    public function __construct()
    {
        $f = new DaoFactory();
        $this->dao = $d = $f->getInstance("Demo");
    }

}