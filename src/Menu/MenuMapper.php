<?php
declare(strict_types=1);

namespace App\Menu;

final class MenuMapper
{
     private const MAPPER = [
         'app_sign_in' => 'app_login',
         'app_profile' => 'app_user_profile'
     ];

     public function __construct() {}

    /**
     * It's mach label link to system route
     *
     * @param string $route
     * @return string
     */
     public function mapRoute(string $route): string
     {
         if(array_key_exists($route, self::MAPPER)) {
             return self::MAPPER[$route];
         }

         return $route;
     }
}