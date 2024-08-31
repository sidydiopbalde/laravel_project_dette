<?php
// app/Enums/RoleEnum.php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'ADMIN';
    case BOUTIQUIER = 'Boutiquier';
    case CLIENT = 'Client';
}
