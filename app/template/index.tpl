<!DOCTYPE html>
<html>
<head>
    <title><{title}></title>
</head>
<body>
<[include header.tpl]>
<{content}><br/>
<[if var.title == 'test']>
<p>当前是默认首页。</p>
<[endif]>
<[for 1 to 9]>
<[row]>
<[if row == 1]>
：我是老大。
<[endif]>
<br/>
<[endfor]>
</body>
</html>