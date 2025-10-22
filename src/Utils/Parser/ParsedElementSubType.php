<?php
namespace Marcosiino\Pipeflow\Utils\Parser;

enum ParsedElementSubType
{
    case plain;
    case indexed;
    case array;
}