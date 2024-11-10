<?php

namespace App\Enums;

enum ProductIngredientStatus :  string
{
    case Reserved = 'reserved';
    case Used = 'used';
    case Spoiled = 'spoiled';
    case Reverted = 'reverted';
}
