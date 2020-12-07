<?php
//use function DI\autowire;
use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/config.php');
$containerBuilder->useAutowiring(true);

try {
    $container = $containerBuilder->build();
} catch (Exception $e){
    if(ENV_DEV){
        echo $e->getMessage();
        exit('Błąd aplikacji, przepraszamy');
    }else{
        exit('Błąd aplikacji, przepraszamy');
    }

}
return $container;
