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

    public function toDatabaseArray(): array
    {
        return [
            'id' => (int) $this->id,
            'email' => $this->email,
            'status' => $this->status,
            'created' => $this->created,
        ];
    }

    public function fromDatabaseArray(array $data): self
    {
        $this->id = (int) $data['id'];
        $this->email = $data['email'];
        $this->status = $data['status'];
        $this->created = $data['created'];

        return $this;
    }

    public function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $value;
        }
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
