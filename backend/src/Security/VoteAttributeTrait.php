<?php

namespace App\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

trait VoteAttributeTrait
{
    /** @var array $permissions */
    protected $permissions;
    /** @var AccessDecisionManagerInterface $decisionManager */
    protected $decisionManager;

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        foreach ($this->permissions as $method => $attributes) {
            if (in_array($attribute, $attributes) && $this->$method($subject, $token)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function isAdmin($subject, TokenInterface $token): bool
    {
        return $this->decisionManager->decide($token, ['ROLE_ADMIN']);
    }

    /**
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function isModerator($subject, TokenInterface $token): bool
    {
        return $this->decisionManager->decide($token, ['ROLE_MODERATOR']);
    }
}
