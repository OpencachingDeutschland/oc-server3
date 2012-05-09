<?php

class ChildWp_Type
{
  private $id;
  private $name;
  private $image;

  public function __construct($id, $name, $image = false)
  {
    $this->id = $id;
    $this->name = $name;
    $this->image = $image;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getName()
  {
    return $this->name;
  }

  public function getImage()
  {
    return $this->image;
  }
}

?>
