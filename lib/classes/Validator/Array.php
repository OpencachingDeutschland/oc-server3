<?php

class Validator_Array
{
  private $values;

  public function __construct($values)
  {
    $this->values = $values;
  }

  public function isValid($value)
  {
    return in_array($value, $this->values);
  }
}

?>
