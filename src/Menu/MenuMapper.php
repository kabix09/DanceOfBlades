<?php
declare(strict_types=1);

namespace App\Menu;

final class MenuMapper
{
     private const MAPPER = [
         'app_sign_in' => 'app_login',
         'app_profile' => 'app_user_profile',
         'app_world_maps' => 'app_list_map',
         'app_tasks_tournaments' => 'app_tournaments_list',
         'app_tasks_raids' => 'app_raids_list',
         'app_items_weapons' => 'app_items_weapon_list',
         'app_items_outfits' => 'app_items_outfit_list',
         'app_items_grimuars' => 'app_items_grimuar_list',
         'app_items_potions' => 'app_items_potion_list',
         'app_items_others' => 'app_items_other_list',
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