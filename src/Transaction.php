<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

readonly class Transaction
{
    public function __construct(
        public int $amount,
        public string $orderRef,
        public string $transactionUnique,
        public string $redirectURL,
        public Action $action = Action::SALE,
        public bool $formResponsive = true,
        public int $type = 1,
        public int $countryCode = 826,
        public int $currencyCode = 826
    ) {
    }

    public function toArray(): array
    {
        return [
            'action'            => $this->action,
            'amount'            => $this->amount,
            'orderRef'          => $this->orderRef,
            'transactionUnique' => $this->transactionUnique,
            'redirectURL'       => $this->redirectURL,
            'formResponsive'    => $this->formResponsive,
            'type'              => $this->type,
            'countryCode'       => $this->countryCode,
            'currencyCode'      => $this->currencyCode,
        ];
    }
}
