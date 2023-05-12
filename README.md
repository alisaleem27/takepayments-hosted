# TakePayments (Hosted)

#### Render a payment link button

```php
use AliSaleem\TakePaymentsHosted\Gateway;
use AliSaleem\TakePaymentsHosted\Request;

$gateway = new Gateway('<MERCHANT-ID>', '<MERCHANT-SECRET>');

$request = new Request(
    amount           : 1000,
    orderRef         : 'my-order-ref',
    transactionUnique: 'my-unique-transaction-id',
    redirectURL      : 'https://myredirect.url/path',
);

$request
    ->customerAddress('1 Street')
    ->customerPostCode('AB1 2CD');

echo $gateway->hostedRequest($request, [
    'formAttrs'  => 'class="my-form-class"',
    'submitText' => 'Reserve',
]);
```
