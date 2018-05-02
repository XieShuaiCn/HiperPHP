<!DOCTYPE html>
<html>
<head>
    <title><?=$this->data["title"]?></title>
</head>
<body>
<?php include "E:\www\PHP\HiperPHP/cache/tpl_tmp/c7b8169ee447ae1c4951b1036544bee6.php"; ?>
<?=$this->data["content"]?><br/>
<?php if ($this->data['title']=='test') { ?>
<p>当前是默认首页。</p>
<?php }?>
<?php for ($row=1; $row<=9; ++$row){ ?>
<?=$row?>
<?php if ($row==1) { ?>
：我是老大。
<?php }?>
<br/>
<?php }?>
</body>
</html>