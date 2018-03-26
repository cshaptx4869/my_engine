<?php
    require_once 'MyTpl.php';
    $mytpl = new MyTpl();
    $mytpl->assign('name','tom');
    $mytpl->display('user.html');