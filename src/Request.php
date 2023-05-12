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
    public int $amount;

    #[Mandatory]
    public string $orderRef;

    #[Mandatory]
    public string $transactionUnique;

    #[Mandatory]
    public string $redirectURL;

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

    public ?string $cardNumber;

    public ?string $cardCVV;

    public ?string $cardExpiryMonth;

    public ?string $cardExpiryYear;

    public ?string $cardExpiryDate;

    public ?string $customerName;

    public ?string $customerAddress;

    public ?string $customerTown;

    public ?string $customerCounty;

    public ?string $customerPostcode;

    public ?string $customerCountryCode;

    public ?string $customerEmail;

    public ?string $customerPhone;

    public ?string $receiverDateOfBirth;

    public ?bool $cardCVVMandatory;

    public ?bool $customerNameMandatory;

    public ?bool $customerFullNameMandatory;

    public ?bool $customerAddressMandatory;

    public ?bool $customerTownMandatory;

    public ?bool $customerCountyMandatory;

    public ?bool $customerPostcodeMandatory;

    public ?bool $customerCountryCodeMandatory;

    public ?bool $customerEmailMandatory;

    public ?bool $customerPhoneMandatory;

    public ?bool $receiverDateOfBirthMandatory;

    public ?string $formAmountEditable;

    public ?string $formResponsive;

    public ?string $formAllowCancel;

    public ?string $allowedPaymentMethods;

    public function __construct(
        int $amount,
        string $orderRef,
        string $transactionUnique,
        string $redirectURL
    ) {
        $this->amount = $amount;
        $this->orderRef = $orderRef;
        $this->transactionUnique = $transactionUnique;
        $this->redirectURL = $redirectURL;

        $reflection = new ReflectionClass($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            if (!$property->getAttributes(Mandatory::class)) {
                $property->setValue($this, null);
            }
        }
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

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
