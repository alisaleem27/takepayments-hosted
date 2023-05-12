<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

enum Type: int
{
    case Ecommerce = 1;
    case MailOrder = 2;
    case ContinuousAuthority = 9;
}
