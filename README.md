# HiperPHP
一款高性能PHP框架。A high performance PHP framework.

## 网站目录结构

```
━┳━app                            应用程序目录
  ┃  ┣━model                      模型文件夹
  ┃  ┣━template                   页面模板文件夹
  ┃  ┃  ┣━index.tpl              首页模板
  ┃  ┃  ┗━readme.html            模板的说明文档
  ┃  ┗━common.php                 网站公共文件
  ┣━cache                          系统缓存目录
  ┣━config                         网站配置文件
  ┃  ┣━db.config.php              数据库配置
  ┃  ┗━site.config.php            站点配置
  ┣━core                           网站的模型和逻辑处理
  ┃  ┣━lib                        存放核心操作库
  ┃  ┃  ┣━cacheLocal.class.php   本独缓存类
  ┃  ┃  ┣━db_mysql.class.php     数据库操作类，mysql
  ┃  ┃  ┣━db_mysqlExt.class.php  数据库扩展类，mysql
  ┃  ┃  ┣━db_mysqli.class.php    数据库操作类，mysqli
  ┃  ┃  ┗━page.class.php         分页类
  ┃  ┣━module                     存放核心模块功能
  ┃  ┃  ┣━base.func.php          基础函数文件
  ┃  ┃  ┗━DbFactory.class.php    数据库工厂类
  ┃  ┗━HiperPHP.php               HiperPHP的核心类，页面访问由此启动
  ┣━web                            网站目录
  ┗━.gitignore                     git忽略清单
```