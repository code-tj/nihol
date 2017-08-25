<?php
// app->data (output data store, buffer)
class appdata
{
  private $blocks=array(
    'meta'=>'',
    'link'=>'',
    'title'=>'',
    'js'=>'',
    'main'=>''
  );
  private $breadcrumb=array();

  public function set($content='',$block='main')
  {
    if(isset($this->blocks[$block])) {
      $this->blocks[$block].=$content;
    } else {
      $this->blocks[$block]=$content;
    }
  }
  public function get($block='main')
  {
    $content='';
    if(isset($this->blocks[$block])) $content=$this->blocks[$block];
    return $content;
  }
  public function get_blocks()
  {
    return $this->blocks;
  }
  public function breadcrumb($title,$link='')
  {
    $this->breadcrumb[]=array('link'=>$link,'title'=>$title);
  }
  public function get_breadcrumb()
  {
    return $this->breadcrumb;
  }
}
