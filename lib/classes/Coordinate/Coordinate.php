<?php

class Coordinate_Coordinate
{
  const epsilon = 8.3333e-6;

  private $latitude;
  private $longitude;

  public function __construct($latitude, $longitude)
  {
    if (abs($latitude) > 90 || abs($longitude) > 180)
      throw new InvalidArgumentException();

    $this->latitude = $latitude;
    $this->longitude = $longitude;
  }

  static public function fromHemDegMin($latHem, $latDeg, $latMin, $lonHem, $lonDeg, $lonMin)
  {
    $latitude = self::hemDegMinToFloat($latHem, $latDeg, $latMin);
    $longitude = self::hemDegMinToFloat($lonHem, $lonDeg, $lonMin);

    return new Coordinate_Coordinate($latitude, $longitude);
  }

  static private function hemDegMinToFloat($hem, $deg, $min)
  {
    if ($deg < 0 || round($deg) != $deg || $min < 0 || $min >= 60)
      throw new InvalidArgumentException();

    $retval =  $deg + $min / 60;

    return $hem ? $retval : -$retval;
  }

  public function latitude()
  {
    return $this->latitude;
  }

  public function longitude()
  {
    return $this->longitude;
  }

  public function latHem()
  {
    return $this->latitude >= 0;
  }

  public function lonHem()
  {
    return $this->longitude >= 0;
  }

  public function latDeg()
  {
    return self::getDeg($this->latitude);
  }

  public function lonDeg()
  {
    return self::getDeg($this->longitude);
  }

  static private function getDeg($value)
  {
    return floor(abs($value) + self::epsilon);
  }

  public function latMin()
  {
    return self::getMin($this->latitude);
  }

  public function lonMin()
  {
    return self::getMin($this->longitude);
  }

  static private function getMin($value)
  {
    return abs(abs($value) - self::getDeg($value)) * 60;
  }
}
?>
