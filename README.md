ceceply/action
==============
A [Laravel](https://laravel.com) package for generate action classes using [Artisan Command](https://laravel.com/docs/artisan).

Installation
------------

> **Requires [PHP 8.1+](https://php.net/releases/)**

Require using [Composer](https://getcomposer.org).

```bash
composer require ceceply/action --dev
```

Put `Ceceply\Action\Providers\ActionServiceProvider::class` into list of service providers in `config/app.php`.
```php
'providers' => ServiceProvider::defaultProviders()->merge([
    /*
     * Package Service Providers...
     */
    Ceceply\Action\Providers\ActionServiceProvider::class,

    /*
     * Application Service Providers...
     */
])->toArray(),
```

Action Class
-----
In this package, an Action Class is a simple class that has only one task. Example, we have an Action class named `CreatePayment`. This class will has only one task, which is to create payments.

Also in this package, by default an Action class will implement interface. That interface will be automatically generated, before the action class generated. Example, if the Action class is named `CreatePayment`, the interface that class implements will be named `CreatesPayments`. You can customize the interface name later.

Run the following command to generate a new Action Class named `CreatePayment`.

```bash
php artisan make:action CreatePayment
```

If that command executed, an Action Class with interface implemented by the class generated.

```php
// app/Actions/Payment/Contracts/CreatesPayments.php

<?php

namespace App\Actions\Payment\Contracts;

use App\Models\Payment;

interface CreatesPayments
{
	public function create(array $inputs);
}
```

```php
// app/Actions/Payment/CreatePayment.php

<?php

namespace App\Actions\Payment;

use App\Actions\Payment\Contracts\CreatesPayments;
use App\Models\Payment;

class CreatePayment implements CreatesPayments
{
	public function create(array $inputs)
	{
		// TODO: create
	}
}
```

Action Method
-------------

You can customize the method name when writing the command.

> If the method name is `create` or `update`, method will automatically have an array parameter named `inputs`. 

```bash
php artisan make:action Payment/CreatePayment --action=handle
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -ahandle
```

Output.
```php
// app/Actions/Payment/Contracts/CreatesPayments.php

interface CreatesPayments
{
	public function handle();
}


// app/Actions/Payment/CreatePayment.php

class CreatePayment implements CreatesPayments
{
	public function handle()
	{
		// TODO: handle
	}
}
```

Model
-----
You can add a model to your Action Class. The model will be placed in first parameter of the action method.

```bash
php artisan make:action Payment/CreatePayment --model=Payment
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -mPayment
```

Output.

```php
// app/Actions/Payment/Contracts/CreatesPayments.php
<?php

namespace App\Actions\Payment\Contracts;

use App\Models\Payment;

interface CreatesPayments
{
	public function create(Payment $payment, array $inputs);
}
```

```php
// app/Actions/Payment/CreatePayment.php
<?php

namespace App\Actions\Payment;

use App\Actions\Payment\Contracts\CreatesPayments;
use App\Models\Payment;

class CreatePayment implements CreatesPayments
{
	public function create(Payment $payment, array $inputs)
	{
		// TODO: create
	}
}
```

Guess Model
-----------

Instead of defining model manually, you can add `--guess-model` option when writing the command.

```bash
php artisan make:action Payment/CreatePayment --guess-model
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -g
```

Last word of the class name will be considered as the model name. If the last word is plural, it will be changed to singular first.

Custom Interface
----------------

You can customize interface name when writing the command.

```bash
php artisan make:action Payment/CreatePayment --interface=Payment/Contracts/CreatePaymentContracts
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -iPayment/Contracts/CreatePaymentContracts
```

Without Interface
-----------------

You can create an Action Class without implementing an interface.

```bash
php artisan make:action Payment/CreatePayment --without-interface
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -w
```

Force
-----

Create the class even if the class already exists.

```bash
php artisan make:action Payment/CreatePayment --force
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -f
```

Force Both
----------

Create the interface and the class even if the class and the interface already exists.

```bash
php artisan make:action Payment/CreatePayment --force-both
```

Or shorter.

```bash
php artisan make:action Payment/CreatePayment -F
```

Create Action Interface
-----------------------

If you want to create the interface only, you can do that by using the `make:iaction` command. So only the interface generated.

```bash
php artisan make:iaction Payment/CreatesPayments
```

Even if you create the interface only, you can still customize action method name, add model, guess model and force create interface.

```bash
php artisan make:iaction Payment/CreatesPayments -ahandle
```

```bash
php artisan make:iaction Payment/CreatesPayments -mPayment
```

```bash
php artisan make:iaction Payment/CreatesPayments -g
```

```bash
php artisan make:iaction Payment/CreatesPayments -f
```

License
-------

This package is an open-sourced software licensed under the [MIT license](LICENSE.md).
