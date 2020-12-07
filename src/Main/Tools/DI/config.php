<?php

//use function DI\create;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

return [
    // Bind an interface to an implementation
    // ArticleRepository::class => create(InMemoryArticleRepository::class),
    // Request::class => create(Request::class),
    // Configure Twig
    Environment::class => function () {
        $loader = new FilesystemLoader(BASE . '/App/views');
        return new Environment($loader);
    },
    Request::class => function () {
        return Request::createFromGlobals();
    },
    Client::class => function () {
        return new Client(['base_uri' => 'http://gorest.co.in/']);
    }
];
