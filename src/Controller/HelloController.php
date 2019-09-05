<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\HelloType;
use Aws\Sns\SnsClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HelloController extends AbstractController
{
    private $aws;
    private $messageBus;
    private $cache;

    public function __construct(SnsClient $aws, MessageBusInterface $messageBus, CacheInterface $cacheApcu)
    {
        $this->aws = $aws;
        $this->messageBus = $messageBus;
        $this->cache = $cacheApcu;
    }

    /**
     * @Route("/{name}", name="hello_world")
     */
    public function world(string $name = null)
    {
        // Crete some objects
        $request = Psr17FactoryDiscovery::findRequestFactory()->createRequest('GET', '/foo');
        $response = Psr17FactoryDiscovery::findResponseFactory()->createResponse(200);

        // Fetch something from disk
        $cacheKey = 'string'.uniqid();
        $result = $this->cache->get($cacheKey, function (ItemInterface $item) {
            $item->expiresAfter(100);

            return 'foobar';
        });

        $this->cache->delete($cacheKey);

        // Forms are good to have
        $form = $this->createForm(HelloType::class);

        return $this->render('helloWorld.html.twig', [
            'form' => $form->createView(),
            'request' => $request,
            'response' => $response,
        ]);
    }
}