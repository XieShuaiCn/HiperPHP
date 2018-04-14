# HiperPHP
一款高性能PHP框架。A high performance PHP framework.

网站目录结构：
━┳━admin                    网站后台管理目录
  ┣━classes                 框架核心类
  ┃  ┣━base.func.php        通用基本函数
  ┃  ┣━db_mysql.class.php   数据库操作类，mysql扩展
  ┃  ┗━db_mysqli.class.php  数据库操作类，mysqli扩展
  ┣━config                  网站配置文件
  ┃  ┣━common.php           网站公共文件
  ┃  ┣━functions.php        配置相关的函数
  ┃  ┣━db.condif.php        数据库配置
  ┃  ┗━site.config.php      站点配置
  ┣━model                   网站的模型和逻辑处理
  ┣━template                页面模板文件夹
  ┃  ┣━index.tpl            首页模板
  ┃  ┗━readme.html          模板的说明文档
  ┣━index.php               首页入口
  ┣━favicon.ico             网站logo
  ┗━.gitignore              git忽略清单