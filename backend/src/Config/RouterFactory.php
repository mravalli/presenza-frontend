<?php declare(strict_types=1);

namespace Neuro3\Presenza\Config;

use Assert\Assert;
use League\Route\RouteGroup;
use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;
use Middlewares\ContentType;
use Middlewares\JsonPayload;
use Neuro3\Presenza\Http\OptionsHandler;
use Neuro3\Presenza\Http\RouteHandler;
use Neuro3\Security\AuthMiddleware;
use Psr\Container\ContainerInterface as Container;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class RouterFactory
{
    public function __invoke(Container $container): LeagueRouter
    {
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($container);

        $strategy->addDefaultResponseHeader('Access-Control-Allow-Origin', '*');

        $router   = new LeagueRouter();
        $router->setStrategy($strategy);

        $authMiddleware = $container->get(AuthMiddleware::class);
        $contentNegotiationMW = $container->get(ContentType::class);

        Assert::thatAll([
            $authMiddleware,
            $contentNegotiationMW
        ])->isInstanceOf(Middleware::class);

        $router->middleware($contentNegotiationMW);
        $router->middleware(new JsonPayload());
        // $router->middleware($authMiddleware);

        $router->map('GET', '/', RouteHandler\Home::class)->middleware($authMiddleware);
        $router->map('OPTIONS', '/', OptionsHandler::class);
        $router->map('GET', '/days', [RouteHandler\Home::class, 'getdays'])->middleware($authMiddleware);
        $router->map('GET', '/export', RouteHandler\Export::class)->middleware($authMiddleware);
        $router->map('OPTIONS', '/modday', OptionsHandler::class);
        $router->map('POST', '/modday', [RouteHandler\Home::class, 'setday'])->middleware($authMiddleware);

        $router->map('GET', '/auth/add', [RouteHandler\Authentication::class, 'add']);
        $router->map('POST', '/auth/login', [RouteHandler\Authentication::class, 'login']);
        $router->map('GET', '/auth/logout', [RouteHandler\Authentication::class, 'logout'])->middleware($authMiddleware);
        $router->map('POST', '/auth/refresh', [RouteHandler\Authentication::class, 'refresh']);

        $router->map('GET', '/users', RouteHandler\User\Lists::class)->middleware($authMiddleware);
        $router->group('/user', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('POST', '/', RouteHandler\User\Create::class);
            $route->map('GET', '/{id}', RouteHandler\User\Read::class);
            $route->map('PATCH', '/{id}', RouteHandler\User\Update::class);
        })->middleware($authMiddleware);

        $router->map('OPTIONS', '/auth/login', OptionsHandler::class);
        $router->map('OPTIONS', '/auth/logout', OptionsHandler::class);

        $router->group('/employees', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('GET', '/', RouteHandler\Employee\Lists::class);
            $route->map('POST', '/', RouteHandler\Employee\Create::class); //->middleware($authMiddleware);
            $route->map('GET', '/{id}', RouteHandler\Employee\Read::class);
            $route->map('PATCH', '/{id}', RouteHandler\Employee\Update::class); //->middleware($authMiddleware);
            $route->map('DELETE', '/{id}', RouteHandler\Employee\Delete::class); //->middleware($authMiddleware);
            // $route->map('OPTIONS', '/', OptionsHandler::class);
            // $route->map('OPTIONS', '/{id}', OptionsHandler::class);
            
            $route->map('POST', '/{id}/engagement', [RouteHandler\Employee\Engagement::class, 'add']);
            $route->map('PATCH', '/{id}/engagement/{eid}', [RouteHandler\Employee\Engagement::class, 'update']);
            $route->map('DELETE', '/{id}/engagement/{eid}', [RouteHandler\Employee\Engagement::class, 'delete']);
            // $route->map('OPTIONS', '/{id}/engagement', OptionsHandler::class);
            // $route->map('OPTIONS', '/{id}/engagement/{eid}', OptionsHandler::class);
        })->middleware($authMiddleware);

        $router->group('/offices', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('GET', '/', RouteHandler\Office\Lists::class);
            $route->map('POST', '/', RouteHandler\Office\Create::class); //->middleware($authMiddleware);
            $route->map('GET', '/{id}', RouteHandler\Office\Read::class);
            $route->map('PATCH', '/{id}', RouteHandler\Office\Update::class); //->middleware($authMiddleware);
            $route->map('DELETE', '/{id}', RouteHandler\Office\Delete::class); //->middleware($authMiddleware);

            // $route->map('OPTIONS', '/', OptionsHandler::class);
            // $route->map('OPTIONS', '/{id}', OptionsHandler::class);
        })->middleware($authMiddleware);

        $router->group('/hoursweek', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('GET', '/', RouteHandler\HoursWeek\Lists::class);
            $route->map('POST', '/', RouteHandler\HoursWeek\Create::class); //->middleware($authMiddleware);
            $route->map('GET', '/{id}', RouteHandler\HoursWeek\Read::class);
            $route->map('PATCH', '/{id}', RouteHandler\HoursWeek\Update::class); //->middleware($authMiddleware);
            $route->map('DELETE', '/{id}', RouteHandler\HoursWeek\Delete::class); //->middleware($authMiddleware);

            // $route->map('OPTIONS', '/', OptionsHandler::class);
            // $route->map('OPTIONS', '/{id}', OptionsHandler::class);
        })->middleware($authMiddleware);

        $router->group('/justification', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('GET', '/', RouteHandler\Justification\Lists::class);
            $route->map('POST', '/', RouteHandler\Justification\Create::class); //->middleware($authMiddleware);
            $route->map('GET', '/{id}', RouteHandler\Justification\Read::class);
            $route->map('PATCH', '/{id}', RouteHandler\Justification\Update::class); //->middleware($authMiddleware);
            $route->map('DELETE', '/{id}', RouteHandler\Justification\Delete::class); //->middleware($authMiddleware);

            // $route->map('OPTIONS', '/', OptionsHandler::class);
            // $route->map('OPTIONS', '/{id}', OptionsHandler::class);
        })->middleware($authMiddleware);

        $router->map('GET', '/week/format/{type}', RouteHandler\Week::class)->middleware($authMiddleware);
        // $router->map('OPTIONS', '/week/format/{type}', OptionsHandler::class)->middleware($authMiddleware);

        $router->group('/settings', function (RouteGroup $route) use ($authMiddleware): void {
            $route->map('GET', '/', RouteHandler\Company\Read::class);
            $route->map('PATCH', '/', RouteHandler\Company\Update::class);
            
            // $route->map('OPTIONS', '/', OptionsHandler::class);
        })->middleware($authMiddleware);

        return $router;
    }
}
