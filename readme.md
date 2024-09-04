## Intialization

index.php

```php 
ErrorExtension::startup();
```

or as extension

```yaml
extensions:
	error: WebChemistry\ErrorPresenter\DI\ErrorExtension
```

## Nette configuration

```neon
application:
	errorPresenter:
		4xx: Error:Error4xx
		5xx: Error:Error5xx
```

5xxPresenter

```php
use WebChemistry\ErrorPresenter\Error5xxPresenter as ParentError5xxPresenter;

final class Error5xxPresenter extends ParentError5xxPresenter
{

}
```
