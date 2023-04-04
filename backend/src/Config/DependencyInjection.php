<?php declare(strict_types=1);

namespace Neuro3\Presenza\Config;

use DI;
use Env\Env;
use Neuro3\Model\DbAdapter;
use Neuro3\Model\DbInterface;
use Neuro3\Security\DbManager;
use Neuro3\Presenza\App;
use Neuro3\Presenza\Model\ToDoDataMapper;
use Neuro3\Security\AuthenticationService;
use Middlewares\BasicAuthentication;
use Middlewares\ContentType;
//use Odan\Session\PhpSession;
//use Odan\Session\SessionInterface;
//use Odan\Session\SessionMiddleware;
use League\Route\Router;
use Psr\Container\ContainerInterface as Container;
use Psr\Log\LoggerInterface;

class DependencyInjection
{
    public function __invoke(): Container
    {
        $builder = new DI\ContainerBuilder();

        $builder->addDefinitions(
            [
                LoggerInterface::class => DI\factory('\Neuro3\Presenza\logger'),
                Router::class => DI\factory(RouterFactory::class),
                AuthenticationService::class => DI\factory(AuthenticationFactory::class),
                // AuthenticationServiceInterface::class => function (Container $container) {
                //     return new AuthenticationService($container->get(AuthenticationAdapter::class));
                // },
                //DbManager::class => DI\create()->constructor(),
                DbAdapter::class => DI\autowire()->lazy(),
                ContentType::class => DI\create()
                    ->constructor(
                        array_filter(
                            ContentType::getDefaultFormats(),
                            function ($key) {
                                return $key === 'json';
                            },
                            ARRAY_FILTER_USE_KEY
                        )
                    )
                    ->method('useDefault', false),
                // SessionInterface::class => function (Container $container) {
                //     $session = new PhpSession();
                //     return $session;
                // },
                // SessionMiddleware::class => function (Container $container) {
                //     return new SessionMiddleware($container->get(SessionInterface::class));
                // },
            ]
        );

        if (Env::get('CACHE')) {
            $builder->enableCompilation(App::CACHE_DIR);
        }

        return $builder->build();
    }
}
