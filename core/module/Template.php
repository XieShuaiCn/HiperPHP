<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2018/5/1
 * Time: 16:24
 */

namespace Core\Module;


class Template
{
    protected $config = [
        'tpl_dir' => APP_ROOT . '/template',
        'tpl_begin' => '<',
        'tpl_end' => '>',
    ];
    protected $tpl_begin = '<';
    protected $tpl_end = '>';
    protected $data = [];
    protected $cache = null;
    protected $tpl = null;
    protected $tpl_id = null;
    protected $tpl_cache_name = null;
    protected $tpl_tmp_file = null;
    protected $tpl_include_files = [];

    /**
     * Template constructor.
     * @param $tpl_file
     */
    public function __construct($tpl_file)
    {
        //读取配置
        if (is_file(CONFIG_ROOT . '/template.conf.php')) {
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

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } elseif ($value !== null) {
            $this->data[$name] = $value;
        }
    }

    public function getTmpFile()
    {
        return $this->tpl_tmp_file;
    }

    public function render()
    {
        //检测是否有缓存
        $cached = $this->isCached();
        if (!$cached) {
            $tpl_content = file_get_contents($this->tpl);
            //分析模板嵌套的文件
            $reg_ret = preg_match_all(
                "/" . $this->tpl_begin . "\\[include ([\w\W]+?)\\]" . $this->tpl_end . "/i",
                $tpl_content, $reg_matches);
            if ($reg_ret != false) {
                //记录嵌套模板信息
                for ($i = 1; $i < count($reg_matches); ++$i) {
                    $this->tpl_include_files []= ['file_tpl' => $reg_matches[$i][0]];
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
            $tpl_content = preg_replace(
                '/' . $this->tpl_begin . '{(\w+?)}' . $this->tpl_end . '/',
                '<?=$this->data["\1"]?>', $tpl_content);
            foreach ($this->tpl_include_files as $f) {
                $tpl_content = str_replace($this->tpl_begin . "[include " . $f['file_tpl'] . "]" . $this->tpl_end,
                    '<?php include "' . $f['file_tmp'] . '"; ?>', $tpl_content);
            }
            file_put_contents($this->tpl_tmp_file, $tpl_content);
        }
        return true;
    }

    public function display()
    {
        $this->render();
        include $this->tpl_tmp_file;
    }
}