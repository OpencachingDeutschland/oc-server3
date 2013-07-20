<?phpclass Validator_Regex
{
  private $regex;

  public function __construct($regex)
  {
    $this->regex = $regex;
  }

  public function isValid($value)
  {
    return mb_ereg_match($this->regex, $value);
  }
}
?>