<?php
namespace mvc\m;

class groups_m extends \model
{

  public function get_groups()
  {
    $groups=array();
    $db=\my::module('db');
    $groups=$db->get('SELECT * FROM `n-groups` ORDER BY `gp-sort`;');
    return $groups;
  }

}
