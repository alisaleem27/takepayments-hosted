<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

use BackedEnum;
use DateTimeImmutable;
use JsonSerializable;
use ReflectionClass;
use ReflectionProperty;

class Request implements JsonSerializable
{
    #[Mandatory]
    public Action $action = Action::Sale;

    #[Mandatory]
    public Type $type = Type::Ecommerce;

    #[Mandatory]
    public int $countryCode = 826;

    #[Mandatory]
    public int $currencyCode = 826;

    #[Mandatory]
    public string $merchantID;

    #[Mandatory]
    public ?string $signature = null;

    public ?string $merchantPwd;

    public ?PaymentMethod $paymentMethod;

    public ?string $remoteAddress;

    public ?int $captureDelay;

    public ?DateTimeImmutable $orderDate;

    public ?string $callbackURL;

    public ?string $customerPostCode;

    public ?string $customerAddress;

    public function __construct(
        #[Mandatory]
        public int $amount,
        #[Mandatory]
        public string $orderRef,
        #[Mandatory]
        public string $transactionUnique,
        #[Mandatory]
        public string $redirectURL
    ) {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            if (!$property->getAttributes(Mandatory::class)) {
                $property->setValue($this, null);
            }
        }
    }

    public function orderDate(?DateTimeImmutable $orderDate): static
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    public function captureDelay(?int $captureDelay): static
    {
        $this->captureDelay = $captureDelay;

        return $this;
    }

    public function remoteAddress(?string $remoteAddress): static
    {
        $this->remoteAddress = $remoteAddress;

        return $this;
    }

    public function callbackURL(?string $callbackURL): static
    {
        $this->callbackURL = $callbackURL;

        return $this;
    }

    public function customerPostCode(?string $customerPostCode): static
    {
        $this->customerPostCode = $customerPostCode;

        return $this;
    }

    public function customerAddress(?string $customerAddress): static
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function sign(string $secret): void
    {
        $data = $this->toArray();
        unset($data['signature']);
        $ret = http_build_query($data, '', '&');
        $ret = preg_replace('/%0D%0A|%0A%0D|%0D/i', '%0A', $ret);
        $this->signature = hash('SHA512', $ret.$secret);
    }

    public function toArray(): array
    {
        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        $data = [];
        foreach ($properties as $property) {
            $value = $property->getValue($this);
            $value = match (true) {
                $value instanceof BackedEnum        => $value->value,
                $value instanceof DateTimeImmutable => $value->format('Y-m-d H:i:s'),
                is_bool($value)                     => $value ? 'Y' : 'N',
                default                             => $value,
            };

            if ($property->getAttributes(Mandatory::class) || !is_null($value)) {
                $data[$property->getName()] = $value;
            }
        }

        ksort($data);

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
