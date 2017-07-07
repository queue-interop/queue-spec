# Queue Interoperability

This package could come in handy for everyone who wants to build an implementation and make sure it meets its requirements.
The spec class is nothing special but a Phpunit test case. It has to be extended and the abstract methods are implemented.  
After you can use them as tests for your transport. Some of the specs require an interaction with a real broker. 

## Example

Here's an example of the spec for Gearman connection factory class:

```php
<?php

namespace Enqueue\Gearman\Tests\Spec;

use Enqueue\Gearman\GearmanConnectionFactory;
use Interop\Queue\Spec\PsrConnectionFactorySpec;

class GearmanConnectionFactoryTest extends PsrConnectionFactorySpec
{
    protected function createConnectionFactory()
    {
        return new GearmanConnectionFactory();
    }
}
```

## License

[MIT license](LICENSE)