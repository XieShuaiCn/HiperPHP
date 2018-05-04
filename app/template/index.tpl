<!DOCTYPE html>
<html>
<head>
    <title><{title}></title>
</head>
<body>
<[include header.tpl]>
<[include header.tpl]>
<{content}><br/>
<[if var.title == test]>
    <p>当前是默认首页。</p>
<[endif]>
<[for row in 1 to 9]>
    <[var.row]>
    <[if var.row == 1]>
        ：我是老大。
    <[elseif var.row == 2]>
        ：我是老二。
    <[else]>
        ：我们是小弟。
    <[endif]>
    <br/>
<[endfor]>
</body>
</html>