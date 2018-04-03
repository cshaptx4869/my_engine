<?php
    require_once 'engine/MyTpl.php';
    $mytpl = new MyTpl();
    // 开启缓存
    $mytpl->caching = true;
    $mytpl->cache_lifetime = 3;
    $mytpl->assign('name','tom');
    $mytpl->assign('now',time());
    $mytpl->display('user.html');