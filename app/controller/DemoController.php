<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午9:36
 */

namespace App\Controller;

use Core\Base\Controller;
use Core\Module\ModelFactory;
use Core\Module\ViewFactory;

/**
 * Class DemoController
 * @package App\Controller
 */
class DemoController extends Controller
{
    protected $_class_name = 'DemoController';

    /**
     * 接收主页
     */
    public function index()
    {
        $f = new ModelFactory();
        $m = $f->createInstance("Demo");
        $m->load(1);
        $f2 = new ViewFactory();
        $v = $f2->createInstance("Demo");
        $v->setData($m->toArray());
        $v->display();
    }
}