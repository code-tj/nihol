<?php
namespace mvc\v;

class groups_v extends \view
{

  public function main($model)
  {
    $groups = $model->get_groups();
    $groups_number=count($groups);
    $result='<h4>Groups: <span class="badge">'.$groups_number."</span></h4>\n";
    if(count($groups_number)>0)
    {
      $result.='<table class="table table-bordered table-hover" style="width:auto;">
    <thead>
    <tr class="active">
    <th>#</th><th>Group</th><th>Actions</th>
    </tr>
    </thead>
    <tbody>
    ';
      $num=0;
      foreach ($groups as $gid => $group) {
        $num++;
        $result.='<tr><td>'.$num.'.</td><td>'.$group['gp-group'].'</td>
    <td class="text-center"><div class="btn-group btn-group-xs">
    <!--
    <a href="#edit" class="btn btn-default" rel="'.$group['gp-gid'].'">Edit</a>
    <a href="#delete" class="btn btn-default" rel="'.$group['gp-gid'].'">Delete</a>
    -->
    </div></td>
    </tr>'."\n";
      }

      $result.='
    </tbody>
    </table>';

    } else {
      $result.='<strong class="text-danger">No records found. </strong>';
    }

    return $result;
  }

}
