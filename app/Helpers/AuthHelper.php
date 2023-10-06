<?php
namespace App\Helpers;

class AuthHelper {
    public static function getRole()
    {
        $user = auth()->user();
        if ($user) {
            return $user->getRoleNames()[0];
        }
        return null;
    }
}
