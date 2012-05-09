<?php 
  // 
  // bench.inc.php 
  // 
  class Cbench 
  { 
    var $start; 
    var $stop; 

    function CBench() 
    { 
      $this->start = 0; 
      $this->stop = 0; 
    } 
    function getmicrotime() 
    { 
        list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
    } 
    function start() 
    { 
       $this->start = $this->getmicrotime(); 
    } 

    function stop() 
    { 
       $this->stop = $this->getmicrotime(); 
    } 

    function diff() 
    { 
       $result = $this->stop - $this->start; 
       return $result; 
    } 
    function runTime() 
    { 
       $result = $this->getmicrotime() - $this->start; 
       return $result; 
    } 
  } 
?>