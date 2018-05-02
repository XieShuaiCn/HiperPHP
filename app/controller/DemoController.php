<?php
/**
 * Created by PhpStorm.
 * User: xieshuai
 * Date: 18-4-15
 * Time: 下午9:36
 */

namespace App\Controller;

use Core\Base\Controller;
use Core\Module\BaseFunc\Template;
use Core\Module\ModelFactory;
use Core\Module\ViewFactory;

/**
 * Class DemoController
 * @package App\Controller
 */
class DemoController extends Controller
{
    protected $_class_name = 'DemoController';

    public function handle($func, $request, $response)
    {
        // TODO: Implement handle() method.
        if ($func != null && method_exists($this, $func)) {
            $this->$func($request, $response);
        } else {
            $this->index($request, $response);
        }
    }

    /**
     * 接收主页
     */
    public function index($request, $reponse)
    {
        $m = ModelFactory::createInstance("Demo");
        $m->load(1);
        $d = $m->toArray();
        $t = new \Core\Module\Template('index.tpl');
        $t->assign('title', 'test');
        $t->assign('content', '这个页面使用Template生成输出。');
        $t->display();
    }

    /**
     * 接收主页
     */
    public function index2($request, $reponse)
    {
        $m = ModelFactory::createInstance("Demo");
        $m->load(1);
        $v = ViewFactory::createInstance("Demo");
        $v->setData($m->toArray());
        $v->setData($request->getRouterArgs(1), 'arg_name');
        $v->display($reponse);
    }


    /**
     * 接收主页
     */
    public function index3($request, $reponse)
    {
        $m = ModelFactory::createInstance("Demo");
        $m->load(1);
        $v = ViewFactory::createInstance("Demo");
        $v->setData($m->toArray());
        $v->setData($request->getRouterArgs(1), 'arg_id');
        $v->setData($request->getRouterArgs(2), 'arg_name');
        $v->display($reponse);
    }
}