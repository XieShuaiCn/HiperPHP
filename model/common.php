<?php
/**
 * Created by PhpStorm.
 * User: XIESHUAI
 * Date: 2017/9/23
 * Time: 12:32
 */

include_once dirname(dirname(__FILE__)) . "/config/site.config.php";

//用户当前ID
define("USER_CURRENT_ID", -1);

//用户权限
//0001 0001 0000 1111 1111 1111 1111 1111
define("USER_PERMISSION_ADDUSER", 1);
define("USER_PERMISSION_DELUSER", 2);
define("USER_PERMISSION_MODIFYUSER", 4);
define("USER_PERMISSION_LISTUSER", 8);
define("USER_PERMISSION_ADDROLE", 0x10);
define("USER_PERMISSION_DELROLE", 0x20);
define("USER_PERMISSION_MODIFYROLE", 0x40);
define("USER_PERMISSION_LISTROLE", 0x80);
define("USER_PERMISSION_ADDIMAGE", 0x100);
define("USER_PERMISSION_DELIMAGE", 0x200);
define("USER_PERMISSION_MODIFYIMAGE", 0x400);
define("USER_PERMISSION_LISTIMAGE", 0x800);
define("USER_PERMISSION_ADDPAPER", 0x1000);
define("USER_PERMISSION_DELPAPER", 0x2000);
define("USER_PERMISSION_MODIFYPAPER", 0x4000);
define("USER_PERMISSION_LISTPAPER", 0x8000);
define("USER_PERMISSION_UPLOADFILE", 0x10000);
define("USER_PERMISSION_DELETEFILE", 0x20000);
define("USER_PERMISSION_MODIFYFILE", 0x40000);
define("USER_PERMISSION_LISTFILE", 0x80000);
define("USER_PERMISSION_WEBCONF", 0x1000000);
define("USER_PERMISSION_DEVELOPER", 0x10000000);

//错误号
define("ERROR_SUCCESS", 0);
define("ERROR_PERMISSION_DENY", 1);

$_error_message = array(
    "操作成功。",
    "当前用户没有权限。"
);


$last_error = 0;