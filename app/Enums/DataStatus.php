<?php

namespace App\Enums;

enum DataStatus: string
{
    case Placeholder = 'placeholder';
    case Estimated = 'estimated';
    case Measured = 'measured';
    case Verified = 'verified';
}
