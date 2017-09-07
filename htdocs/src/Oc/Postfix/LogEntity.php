<?php

namespace Oc\Postfix;

class LogEntity
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var \DateTimeInterface
     */
    public $created;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $email;

    /**
     * @return array
     */
    public function toDatabaseArray()
    {
        return [
            'id' => (int) $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'created' => $this->created,
        ];
    }

    /**
     * @param array $data
     * @return $this
     */
    public function fromDatabaseArray(array $data)
    {
        $this->id = (int) $data['id'];
        $this->email = $data['email'];
        $this->status = $data['status'];
        $this->created = $data['created'];

        return $this;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
