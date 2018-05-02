<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/5/1
 * Time: 16:24
 */

namespace Core\Module;

/**
 * 模板类
 * Class Template
 * @package Core\Module
 */
class Template
{
    /**
     * @var array 模板配置
     */
    protected $config = [
        'tpl_dir' => APP_ROOT . '/template',
        'tpl_begin' => '<',
        'tpl_end' => '>',
    ];
    /**
     * @var string 模板起始结束标记
     */
    protected $tpl_begin = '<';
    protected $tpl_end = '>';
    /**
     * @var array 模板变量的数值
     */
    protected $data = [];
    /**
     * @var \Core\Base\Cache 系统缓存
     */
    protected $cache = null;
    /**
     * @var string 模板文件名
     */
    protected $tpl = null;
    /**
     * @var string 模板名加密后的ID
     */
    protected $tpl_id = null;
    /**
     * @var string 模板在缓存中的键名
     */
    protected $tpl_cache_name = null;
    /**
     * @var string 模板的预处理生成文件
     */
    protected $tpl_tmp_file = null;
    /**
     * @var array 模板中嵌套的文件信息
     */
    protected $tpl_include_files = [];

    /**
     * Template constructor.
     * @param $tpl_file
     */
    public function __construct($tpl_file)
    {
        //读取配置
        if (is_file(APP_ROOT . '/config/template.conf.php')) {
            $this->config = array_merge($this->config, include CONFIG_ROOT . '/template.config.php');
        }
        //加载常用配置
        $this->tpl_begin = $this->config['tpl_begin'];
        $this->tpl_end = $this->config['tpl_end'];
        //模板文件路径
        $this->tpl = $this->config['tpl_dir'] . '/' . $tpl_file;
        $this->tpl_id = md5($tpl_file);
        $this->tpl_cache_name = 'tpl_' . $this->tpl_id;
        if (!is_dir(CACHE_ROOT . '/tpl_tmp/')) {
            mkdir(CACHE_ROOT . '/tpl_tmp/');
        }
        $this->tpl_tmp_file = CACHE_ROOT . '/tpl_tmp/' . $this->tpl_id . ".php";
        $this->cache = CacheFactory::getInstance("core");
    }

    /**
     * 判断是否被缓存，判断缓存有效性
     * @return bool
     */
    protected function isCached()
    {
        //检测文件是否存在
        if (!is_file($this->tpl_tmp_file)) {
            return false;
        }
        //验证修改日期
        $t1 = filemtime($this->tpl_tmp_file);
        $t2 = filemtime($this->tpl);
        if ($t2 > $t1) {//模板文件比缓存修改日期新则为无效
            return false;
        }
        //是否有缓存记录
        if (!$this->cache->existKey($this->tpl_cache_name)) {
            return false;
        }
        return true;
    }

    /**
     * 给模板变量赋值
     * @param string|array $name
     * @param null string $value
     */
    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } elseif ($value !== null) {
            $this->data[$name] = $value;
        }
    }

    /**
     * 获取模板的临时文件
     * @return null|string
     */
    public function getTmpFile()
    {
        return $this->tpl_tmp_file;
    }

    /**
     * 预编译模板，将模板渲染成php文件
     * @return bool
     */
    public function render()
    {
        //检测是否有缓存
        $cached = $this->isCached();
        if (!$cached) {
            $tpl_content = file_get_contents($this->tpl);
            //分析模板嵌套的文件
            $reg_ret = preg_match_all(
                "/" . $this->tpl_begin . '\[include ([\w\W]+?)\]' . $this->tpl_end . "/i",
                $tpl_content, $reg_matches);
            if ($reg_ret != false) {
                //记录嵌套模板信息
                for ($i = 1; $i < count($reg_matches); ++$i) {
                    $this->tpl_include_files [] = ['file_tpl' => $reg_matches[$i][0]];
                }
                //将模板嵌套的分析结果存入缓存
                $this->cache->setHashValue($this->tpl_cache_name, 'include_files', $this->tpl_include_files);
            }
        } else {
            //读取模板嵌套结果
            $v = $this->cache->getHashValue($this->tpl_cache_name, 'include_files');
            if ($v !== false) {
                $this->tpl_include_files = array_merge($this->tpl_include_files, $v);
            }
        }
        //迭代render子模板
        foreach ($this->tpl_include_files as &$f) {
            $t = new Template($f['file_tpl']);
            $t->assign($this->data);
            $t->render();
            $f['file_tmp'] = $t->getTmpFile();
        }
        //开始预编译模板
        if (!$cached) {
            //编译模板变量
            $tpl_content = preg_replace(
                '/' . $this->tpl_begin . '\[var.(\w+?)\]' . $this->tpl_end . '/',
                '<?=$this->data["\1"]?>', $tpl_content);
            //编译模板变量的简单写法
            $tpl_content = preg_replace(
                '/' . $this->tpl_begin . '\{(\w+?)\}' . $this->tpl_end . '/',
                '<?=$this->data["\1"]?>', $tpl_content);
            //编译模板嵌套
            foreach ($this->tpl_include_files as $f) {
                $tpl_content = str_replace(
                    $this->tpl_begin . "[include " . $f['file_tpl'] . "]" . $this->tpl_end,
                    '<?php include "' . $f['file_tmp'] . '"; ?>', $tpl_content);
            }
            //编译逻辑控制语句
            $tpl_content = preg_replace_callback(
                '/' . $this->tpl_begin . '\[(\w+?)((?:\s+[\w\W]+?)*)\]' . $this->tpl_end . '/',
                function ($matches) {
                    $matches[1] = strtolower($matches[1]);
                    $rpl_value = '';
                    switch ($matches[1]) {
                        case 'for':
                            if (!isset($matches[2])) {//没有参数时为死循环
                                $rpl_value = '<?php for ($row=0; ; ++$row) { ?>';
                            } //解析for范围
                            elseif (preg_match('/\s*(var\.)?(\w+)\s*to\s*(var\.)?(\w+)\s*/', $matches[2], $sub_matches)) {
                                //var_dump($sub_matches);
                                $rpl_value = '<?php '
                                    . 'for ($row=' . (empty(trim($sub_matches[1])) ? $sub_matches[2] : '$this->data[\'' . $sub_matches[2] . '\']') . '; '
                                    . '$row<=' . (empty(trim($sub_matches[3])) ? $sub_matches[4] : '$this->data[\'' . $sub_matches[4] . '\']') . '; '
                                    . '++$row){ ?>';
                            } //解析为foreach遍历
                            elseif (preg_match('/\s*var.(\w+)\s*/', $matches[2], $sub_matches)) {
                                $rpl_value = '<?php foreach ($this->data[\'' . $sub_matches[1] . '\'] as $row) {?>';
                            }
                            break;
                        case 'if':
                            if (!isset($matches[2])) {
                                $rpl_value = '<?php if(false){ ?>';
                            } elseif (preg_match('/\s*(var\.)?(\w+)\s*(([\w\W]+?)\s*(var\.)?(\'?"?\w+\'?"?)\s*)?/', $matches[2], $sub_matches)) {
                                //var_dump($sub_matches);
                                //单变量if
                                if (!isset($sub_matches[3]) || empty(trim($sub_matches[3]))) {
                                    $rpl_value = '<?php if ('
                                        . (empty(trim($sub_matches[1])) ?//判断是否为变量
                                            ($sub_matches[2] == 'row' ? '$row' : $sub_matches[2]) ://判断是否为row
                                            '$this->data[\'' . $sub_matches[2] . '\']')
                                        . ') { ?>';
                                } //条件if
                                else {
                                    $rpl_value = '<?php if ('
                                        . (empty(trim($sub_matches[1])) ?
                                            ($sub_matches[2] == 'row' ? '$row' : $sub_matches[2]) ://判断是否为row
                                            '$this->data[\'' . $sub_matches[2] . '\']')
                                        . $sub_matches[4]
                                        . (empty(trim($sub_matches[5])) ?
                                            ($sub_matches[6] == 'row' ? '$row' : $sub_matches[6]) ://判断是否为row
                                            '$this->data[\'' . $sub_matches[6] . '\']')
                                        . ') { ?>';
                                }
                            }
                            break;
                        case 'row':
                            $rpl_value = '<?=$row?>';
                            break;
                        case 'endfor':
                        case 'endif':
                            $rpl_value = '<?php }?>';
                            break;
                        case 'break':
                            $rpl_value = '<?php break;?>';
                            break;
                        case 'continue':
                            $rpl_value = '<?php continue;?>';
                            break;
                        default:
                            break;
                    }
                    return $rpl_value;
                }, $tpl_content);
            file_put_contents($this->tpl_tmp_file, $tpl_content);
        }
        return true;
    }

    /**
     * 显示模板页面
     */
    public function display()
    {
        $this->render();
        try {
            include $this->tpl_tmp_file;
        } catch (\Exception $e) {

        }
    }
}