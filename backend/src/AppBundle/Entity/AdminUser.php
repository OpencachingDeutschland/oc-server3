<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\AttributeOverride;
use Doctrine\ORM\Mapping\AttributeOverrides;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Mirsch\Bundle\AdminBundle\Entity\AdminUser as BaseAdminUser;

/**
 * AdminUser
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"username"}), @ORM\UniqueConstraint(name="email", columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\AdminUserRepository")
 * @AttributeOverrides({
 *      @AttributeOverride(name="id",
 *          column=@Column(
 *              name = "user_id", type = "integer"
 *          )
 *      ),
 *      @AttributeOverride(name="password",
 *          column=@Column(
 *              name = "admin_password", type="string", length=60, nullable=true
 *          )
 *      ),
 *      @AttributeOverride(name="active",
 *          column=@Column(
 *              name = "is_active_flag", type="boolean", nullable=false
 *          )
 *      ),
 *      @AttributeOverride(name="locale",
 *          column=@Column(
 *              name = "language", type="string", length=2, nullable=true
 *          )
 *      )
 * })
 *
 */
class AdminUser extends BaseAdminUser
{

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ManyToMany(targetEntity="Mirsch\Bundle\AdminBundle\Model\AdminGroupInterface", inversedBy="users")
     * @JoinTable(name="admin_users_groups",
     *     joinColumns={@JoinColumn(name="user_id", referencedColumnName="user_id")},
     *     inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id", unique=true)}
     * )
     */
    protected $groups;

    /**
     * @var bool
     *
     * @ORM\Column(name="admin", type="boolean", nullable=false)
     */
    protected $admin = 0;

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->admin > 0 ? true : false;
    }

    /**
     * @param bool $admin
     *
     * @return AdminUser
     */
    public function setIsAdmin($admin)
    {
        $this->admin = $admin ? 1 : 0;

        return $this;
    }

}
