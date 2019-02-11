<?php

namespace Oc\Translation;

class TranslationStruct
{
    /**
     * @var int
     */
    public $identifier;

    /**
     * @var string
     */
    public $sourceString;

    /**
     * @var string
     */
    public $comment;

    /**
     * @var string
     */
    public $de;

    /**
     * @var string
     */
    public $fr;

    /**
     * @var string
     */
    public $nl;

    /**
     * @var string
     */
    public $es;

    /**
     * @var string
     */
    public $pl;

    /**
     * @var string
     */
    public $it;

    /**
     * @var string
     */
    public $ru;

    public function fromCsvArray(array $data): self
    {
        $this->identifier = (int) $data['Identifier'];
        $this->sourceString = $data['SourceString'];
        $this->comment = $data['Comment'];
        $this->de = $data['DE'];
        $this->fr = $data['FR'];
        $this->nl = $data['NL'];
        $this->es = $data['ES'];
        $this->pl = $data['PL'];
        $this->it = $data['IT'];
        $this->ru = $data['RU'];

        return $this;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
