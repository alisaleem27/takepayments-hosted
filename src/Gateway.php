<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

class Gateway
{
    const RC_SUCCESS = 0;
    const RC_DO_NOT_HONOR = 5;
    const RC_NO_REASON_TO_DECLINE = 85;
    const RC_3DS_AUTHENTICATION_REQUIRED = 0x1010A;
    static protected $hostedUrl = 'https://gw1.tponlinepayments.com/paymentform/';

    public function __construct(
        private readonly string $merchantId,
        private readonly string $merchantSecret,
        private ?string $merchantPwd = null,
        protected ?string $proxyUrl = null,
        protected bool $debug = true
    ) {
    }

    public function hostedRequest(Request $request, array $options = []): string
    {
        $request->merchantID = $this->merchantId;
        $request->merchantPwd = $this->merchantPwd;
        $request->sign($this->merchantSecret);

        return sprintf(
            '<form method="post" action="%s" %s>%s %s</form>',
            htmlentities(static::$hostedUrl, ENT_COMPAT, 'UTF-8'),
            $options['formAttrs'] ?? '',
            $this->getInputElements($request),
            $this->getSubmitElement($options)
        );
    }

    protected function getSubmitElement(array $options): string
    {
        if (isset($options['submitImage'])) {
            return sprintf(
                '<input alt="Pay Now" type="image" src="%s" %s/>',
                htmlentities($options['submitImage'], ENT_COMPAT, 'UTF-8'),
                $options['submitAttrs'] ?? ''
            );
        }

        if (isset($options['submitHtml'])) {
            return sprintf(
                '<button type="submit" %s>%s</button>',
                $options['submitAttrs'] ?? '',
                $options['submitHtml']
            );
        }

        return sprintf(
            '<input type="submit" value="%s" %s/>',
            isset($options['submitText'])
                ? htmlentities($options['submitText'], ENT_COMPAT, 'UTF-8')
                : 'Pay Now',
            $options['submitAttrs'] ?? ''
        );
    }

    protected function getInputElements(Request $request): string
    {
        $elements = [];
        foreach ($request->toArray() as $key => $value) {
            $elements[] = $this->fieldToHtml($key, $value);
        }

        return implode("\n", $elements);
    }

    protected function fieldToHtml(string $name, $value): string
    {
        $element = '';
        if (is_array($value)) {
            foreach ($value as $n => $v) {
                $element .= $this->fieldToHtml("{$name}[{$n}]", $v);
            }
        } elseif ($value != '') {
            $value = preg_replace_callback(
                '/[\x00-\x1f]/',
                fn ($matches) => '&#'.ord($matches[0]).';',
                htmlentities((string)$value, ENT_COMPAT, 'UTF-8', true)
            );

            $element = sprintf('<input type="hidden" name="%s" value="%s"/>', $name, $value);
        }

        return $element;
    }
}
