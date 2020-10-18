<?php

namespace App\Security;

use App\Entity\Pin;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PinVoter extends Voter {

    // these strings are just invented: you can use anything
    const DELETE = 'delete';
    const EDIT = 'edit';

    protected function supports(string $attribute, $subject) {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::DELETE, self::EDIT])) {
            return false;
        }

        // only vote on `Post` objects
        if (!$subject instanceof Pin) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token) {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        /** @var Pin $pin */
        $pin = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canDelete($pin, $user);
            case self::EDIT:
                return $this->canEdit($pin, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }
    
     private function canDelete(Pin $pin, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($pin, $user)) {
            return true;
        }

        // the Pin object could have, for example, a method `isPrivate()`
        return !$pin->isPrivate();
    }

    private function canEdit(pin $pin, User $user)
    {
        // this assumes that the Pin object has a `getOwner()` method
        return $user === $pin->getUser();
    }

}
