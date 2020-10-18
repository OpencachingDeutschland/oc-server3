<?php

namespace Oc\Entity;

use Oc\Repository\AbstractEntity;

class SecurityRolesEntity extends AbstractEntity
{
	/** @var int */
	public $id;

	/** @var string */
	public $role;


	/**
	 * @return bool
	 */
	public function isNew(): bool
	{
		return $this->id === null;
	}
}
