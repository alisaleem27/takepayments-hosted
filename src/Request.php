<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

use JsonSerializable;

class Request implements JsonSerializable
{
    public string $merchantID;
    public ?string $merchantPwd;
    public string $action;
    public int $type;
    public int $countryCode;
    public int $currencyCode;
    public int $amount;
    public string $orderRef;
    public string $formResponsive;
    public string $transactionUnique;
    public string $redirectURL;
    public ?string $signature = null;

    public static function fromArray(array $options): static
    {
        $instance = new static();
        foreach ($options as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }

    public static function fromTransaction(Transaction $transaction): static
    {
        return static::fromArray($transaction->toArray());
    }

    public function sign(string $secret): void
    {
        $data = $this->toArray();
        ksort($data);
        $ret = http_build_query($data, '', '&');
        $ret = preg_replace('/%0D%0A|%0A%0D|%0D/i', '%0A', $ret);
        $this->signature = hash('SHA512', $ret.$secret);
    }

    public function toArray(): array
    {
        return array_merge(
            [
                'merchantID'        => $this->merchantID,
                'merchantPwd'       => $this->merchantPwd,
                'action'            => $this->action,
                'type'              => $this->type,
                'countryCode'       => $this->countryCode,
                'currencyCode'      => $this->currencyCode,
                'amount'            => $this->amount,
                'orderRef'          => $this->orderRef,
                'formResponsive'    => $this->formResponsive,
                'transactionUnique' => $this->transactionUnique,
                'redirectURL'       => $this->redirectURL,
            ],
            array_filter([
                'signature' => $this->signature,
            ])
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
