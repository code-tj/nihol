<?php
// constants
define('BDIR',str_replace('\\','/',realpath(dirname(__FILE__)))); // absolute base dir path for app
define('CDIR','/home/nihol2/core'); // abs core dir path
define('APPDIR',BDIR.'/app'); // path to application directory
define('APPATH','./app'); // here path means url not dir
define('APPNAME','test'); // application codename
define('APPREFIX',APPNAME.'_'); // prefix for session vars
define('UIPATH','./ui');
define('XMODE',0); // maintenance
define('XDEBUG',false);
// database settings
$conf['db_server']='localhost';
$conf['db_port']='';
$conf['db_charset']='utf8';
$conf['db_name']=''.APPNAME;
$conf['db_user']='test';
$conf['db_pass']='test';
// user interface
$conf['tpl']=UIPATH.'/tpl/test'; // template dir
