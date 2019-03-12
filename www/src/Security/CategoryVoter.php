<?php

namespace App\Security;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CategoryVoter extends Voter
{
    const EDIT = 'edit';
    const isUser = 'isUser';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, [self::isUser, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof Category) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /**
         * @var Category
         */
        $category = $subject;

        switch ($attribute) {

            case self::isUser:
                if ($this->security->isGranted('ROLE_USER')) {
                    return true;
                }

                break;

            case self::EDIT:
                return $this->canEdit($category, $user);

                break;
        }

        throw new \LogicException('This code should not be reached');
    }

    public function canEdit(Category $category, User $user){
        return $category->getUser()->getId() == $user->getId();
    }
}