This custom module allows Magento 2 to use Redis Cluster with [nrk/predis](https://packagist.org/packages/predis/predis) and the `phpredis` PHP extension.

The class is an almost exact copy of the Predis Session Handler class adapted to Magento 2, with the deployment configuration as dependency and several small refactors to support a better naming key strategy.

# Configuration

1. Enable the module:

   . `magento module:enable Emasantos_RedisCluster`
   . `magento setup:upgrade`
   . `magento cache:flush`

2. Edit your `env.php` file and add your Redis Cluster connection configuration: 

```php
'session' => [
        'save' => 'redisCluster',
        'redisCluster' => [
            'protocol' => 'tcp',
            'host' => 'localhost',
            'port' => '6379',
            'keyPrefix' => 'session'
        ]
    ],
```
