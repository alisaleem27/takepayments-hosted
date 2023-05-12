# TakePayments (Hosted)

#### Render a payment link button
```php
use AliSaleem\TakePaymentsHosted\Action;
use AliSaleem\TakePaymentsHosted\Gateway;
use AliSaleem\TakePaymentsHosted\Transaction;
use AliSaleem\TakePaymentsHosted\Request;

$gateway = new Gateway('<MERCHANT-ID>', '<MERCHANT-SECRET>');

$transaction = new Transaction(
    amount: 1000,
    orderRef: 'my-order-ref',
    transactionUnique: 'my-unique-transaction-id',
    redirectURL: 'https://myredirect.url/path'  
);

echo $gateway->hostedRequest(Request::fromTransaction($transaction));
```
