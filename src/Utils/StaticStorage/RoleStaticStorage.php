<?php


namespace App\Utils\StaticStorage;


class RoleStaticStorage
{
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * @return array
     */
    public static function getRoles(): array
    {
        return [
            self::ROLE_USER => 'User',
            self::ROLE_ADMIN => 'Admin'
        ];
    }
}