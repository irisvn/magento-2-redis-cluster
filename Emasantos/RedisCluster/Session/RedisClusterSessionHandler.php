<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Emasantos\RedisCluster\Session;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Session\SaveHandler\Native;
use Predis\Client as PredisClient;
use Predis\ClientInterface;

class RedisClusterSessionHandler extends Native
{
    /** @var PredisClient */
    protected $client;

    /** @var int */
    protected $ttl;

    /** @var string */
    private $keyPrefix;

    public function __construct(
        DeploymentConfig $deploymentConfig
    )
    {
        /** @var string $protocol */
        $protocol = $deploymentConfig->get('session/redisCluster/protocol');

        /** @var string $host */
        $host = $deploymentConfig->get('session/redisCluster/host');

        $this->keyPrefix = $deploymentConfig->get('session/redisCluster/keyPrefix');

        /** @var int $port */
        $port = $deploymentConfig->get('session/redisCluster/port');

        /** @var string $connectionUrl */
        $connectionUrl = sprintf(
            '%s://%s:%s',
            $protocol,
            $host,
            $port
        );

        $this->client = new PredisClient(
            [
                $connectionUrl
            ],
            [
                'cluster' => 'redis'
            ]
        );

        $this->ttl = ini_get('session.gc_maxlifetime');
    }

    /**
     * Registers this instance as the current session handler.
     */
    public function register()
    {
        if (PHP_VERSION_ID >= 50400) {
            session_set_save_handler($this, true);
        } else {
            session_set_save_handler(
                array($this, 'open'),
                array($this, 'close'),
                array($this, 'read'),
                array($this, 'write'),
                array($this, 'destroy'),
                array($this, 'gc')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($save_path, $session_id)
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($session_id)
    {
        if ($data = $this->client->get($this->getSessionKey($session_id))) {
            return $data;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data)
    {
        $this->client->setex($this->getSessionKey($session_id), $this->ttl, $session_data);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($session_id)
    {
        $this->client->del($this->getSessionKey($session_id));

        return true;
    }

    /**
     * Returns the underlying client instance.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the session max lifetime value.
     *
     * @return int
     */
    public function getMaxLifeTime()
    {
        return $this->ttl;
    }

    /**
     * @param string $sessionId
     * @return string
     */
    private function getSessionKey($sessionId)
    {
        return $this->keyPrefix . ':' . $sessionId;
    }
}
