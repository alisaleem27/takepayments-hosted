<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

enum Action: string
{
    case PreAuth = 'PREAUTH';
    case Verify = 'VERIFY';
    case Sale = 'SALE';
    case Refund = 'REFUND';
    case RefundSale = 'REFUND_SALE';
}
