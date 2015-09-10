<?php

/**
 * Specific UserWeb for this System
 */
class UserWeb extends BUserWeb {

    /**
     * @return User get current level
     */
    public function level() {
        return $this->user()->level();
    }

    /**
     * to checks whether the User is Moderator or not
     * @return boolean true if user is Moderator, false otherwise
     */
    public function isModerator() {
        $user = $this->loadUser();
        return $user && $user->moderator;
    }

    /**
     * to checks whether the User is Common User or not
     * @return boolean true if user is Common User, false otherwise
     */
    public function isUser() {
        $user = $this->loadUser();
        return $user && !UserWeb::instance()->isGuest;
    }


}
