<?php
// constants
define('BDIR',str_replace('\\','/',realpath(dirname(__FILE__)))); // absolute base dir path for app
define('CDIR','/home/iw7/web/nihol2/core'); // abs core dir path
define('APPDIR',BDIR.'/app'); // path to application directory
define('APPATH','./app'); // here path means url not dir
define('APPNAME','bweb2'); // application codename
define('APPREFIX',APPNAME.'_'); // prefix for session vars
define('UIPATH','./ui');
define('XMODE',0); // maintenance
define('XDEBUG',false);
// database settings
$conf['db_server']='localhost';
$conf['db_port']='';
$conf['db_charset']='utf8';
$conf['db_name']=''.APPNAME;
$conf['db_user']='bweb2';
$conf['db_pass']='Test2014';
// user interface
$conf['tpl']=UIPATH.'/tpl/bweb'; // template dir
// additional app settings
$conf['breports']='/home/iw7/web/breports';

// modules registry, 1 - core, 2 - app
$xmods=array(
'user'=>1,
'groups'=>1,
'page'=>1,
'tb'=>2,
'br'=>2,
);