<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class AdminUserRepository extends EntityRepository implements UserLoaderInterface
{

    /**
     * @param string $username
     *
     * @return null|AdminUser
     */
    public function loadUserByUsername($username)
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq('username', $username))
            ->andWhere($criteria->expr()->gt('admin', 0))
            ->andWhere($criteria->expr()->eq('active', true));

        return $this->matching($criteria)->first() ?: null;
    }

}
