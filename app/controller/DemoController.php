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
        $v = ViewFactory::createInstance("Demo");
        $v->setData($m->toArray());
        $v->display($reponse);
    }

    /**
     * 接收主页
     */
    public function index2($request, $reponse)
    {
        $m = ModelFactory::createInstance("Demo");
        $m->load(1);
        $v = ModelFactory::createInstance("Demo");
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