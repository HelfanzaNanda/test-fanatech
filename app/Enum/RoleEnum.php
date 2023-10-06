<?php
namespace App\Enum;



enum RoleEnum : string {
    case SUPERADMIN = 'SUPERADMIN';
    case SALES = 'SALES';
    case PURCHASE = 'PURCHASE';
    case MANAGER = 'MANAGER';




    // private function color(): string
    // {
    //     return match($this)
    //     {
    //         self::DRAFT => 'grey',
    //         self::PUBLISHED => 'green',
    //         self::ARCHIVED => 'red',
    //     };
    // }
}
