<?php

declare(strict_types=1);

namespace AliSaleem\TakePaymentsHosted;

readonly class Gateway
{
    public function __construct(
        private string $hostedUrl,
        private string $merchantId,
        private string $merchantSecret,
        private ?string $merchantPwd = null
    ) {
    }

    public function hostedRequest(Request $request, array $options = []): string
    {
        $request->merchantID = $this->merchantId;
        $request->merchantPwd = $this->merchantPwd;
        $request->signature = $this->sign($request->toArray());

        return sprintf(
            '<form method="post" action="%s" %s>'.PHP_EOL.'%s'.PHP_EOL.'%s'.PHP_EOL.'</form>',
            htmlentities($this->hostedUrl, ENT_COMPAT, 'UTF-8'),
            $options['formAttrs'] ?? '',
            $this->getInputElements($request),
            $this->getSubmitElement($options)
        );
    }

    public function sign(array $data): string
    {
        unset($data['signature']);
        ksort($data);
        $ret = preg_replace('/%0D%0A|%0A%0D|%0D/i', '%0A', http_build_query($data, '', '&'));

        return hash('SHA512', $ret.$this->merchantSecret);
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
