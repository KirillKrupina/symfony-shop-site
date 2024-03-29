<?php


namespace App\Utils\Generator;


class PasswordGenerator
{
    /**
     * @param int $length
     * @return string
     */
    public static function generatePassword($length = 8): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!-.[]?*()';
        $password = '';
        $characterListLength = mb_strlen($characters, '8bit') - 1;

        for ($i = 1; $i <= $length; ++$i) {
            try {
                $password .= $characters[random_int(0, $characterListLength)];
            } catch (\Exception $e) {
            }
        }

        return $password;
    }
}