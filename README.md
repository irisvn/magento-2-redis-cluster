This custom module allows Magento 2 to use Redis Cluster with `nrk/predis` and the `phpredis` PHP extension.

The class is an almost exact copy of the Predis Session Handler class adapted to Magento 2, with the deployment configuration as dependency and several small refactors to support a better naming key strategy.

```php
'session' => [
        'save' => 'redisCluster',
        'redisCluster' => [
            'protocol' => 'tcp',
            'host' => 'localhost',
            'port' => '6379',
            'database' => '0',
            'password' => '',
            'keyPrefix' => 'session'
        ]
    ],
```
