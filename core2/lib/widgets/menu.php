<?php
namespace CORE\WIDGETS;
class MENU {

function __construct(){
    \UI::init()->p('menu<br>');
}

public static function get_menu_from_db(){
    $DB=\DB::init();
    $menu=array();
    if($DB->ok()){
        $sql="SELECT * FROM `n-menu-items` LEFT OUTER JOIN `n-menu` ON `mi-menu`=`menu-id` 
        ORDER BY `mi-menu`,`mi-sort`;";
        $sth=$DB->dbh->prepare($sql);
        $sth->execute();
        $DB->query_count();
        if($sth->rowCount()>0){
            \CORE::msg('Loading menu from DB','debug');
            while($r=$sth->fetch()){
                $menu[$r['menu-name']][]=$r['mi-html'];
            }
        }
    }
    ///\UI::init()->p(print_r($menu,true));
    return $menu;
}


}