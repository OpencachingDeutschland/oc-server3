<?php

namespace Oc\Security\Voter;

use Oc\Entity\UserEntity;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 *
 */
class UserVoter extends Voter
{
    /**
     * @var AccessDecisionManagerInterface
     */
    private $accessDecisionManager;

    public function __construct(AccessDecisionManagerInterface $accessDecisionManager)
    {
        $this->accessDecisionManager = $accessDecisionManager;
    }

    /**
     * @param $attribute
     * @param $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['CAN_VIEW'])
            && ($subject instanceof UserEntity || $subject === UserEntity::class);
    }

    /**
     * @param $attribute
     * @param $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token) : bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $grantingRoles = [
            'ROLE_SUPER_ADMIN',
            'ROLE_ADMIN',
            'ROLE_SUPPORT_TRAINEE',
            'ROLE_SUPPORT',
            'ROLE_SUPPORT_MAINTAIN',
            'ROLE_SUPPORT_HEAD',
            'ROLE_SOCIAL_TRAINEE',
            'ROLE_SOCIAL',
            'ROLE_SOCIAL_HEAD',
            'ROLE_DEVELOPER_CONTRIBUTOR',
            'ROLE_DEVELOPER_CORE',
            'ROLE_DEVELOPER_HEAD',
        ];

        foreach ($grantingRoles as $grantingRole) {
            if ($this->accessDecisionManager->decide($token, [$grantingRole])) {
                return true;
            }
        }

        switch ($attribute) {
            case 'CAN_VIEW':
                // logic to determine if the user can EDIT
                // return true or false
                break;
        }

        return false;
    }
}
