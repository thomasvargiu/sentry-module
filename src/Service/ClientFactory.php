<?php

namespace Facile\SentryModule\Service;

use Facile\SentryModule\Options\ClientOptions;
use Interop\Container\ContainerInterface;

class ClientFactory extends AbstractFactory
{
    /**
     * @param ContainerInterface $container
     * @return Client
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws \Interop\Container\Exception\ContainerException
     */
    public function __invoke(ContainerInterface $container)
    {
        /** @var array $optionsArray */
        $optionsArray = $container->get('config')['facile']['sentry']['client'][$this->name];
        
        $options = new ClientOptions($optionsArray);

        $ravenClient = new \Raven_Client($options->getDsn(), $options->getOptions());

        $client = new Client($ravenClient, $options);

        $errorHandlerListener = $container->get($options->getErrorHandlerListener());
        if ($errorHandlerListener instanceof ClientAwareInterface) {
            $errorHandlerListener->setClient($client);
        }
        $client->setErrorHandlerListener($errorHandlerListener);
        
        return $client;
    }
}
