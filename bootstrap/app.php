<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Configuration\Exceptions;
use App\Http\Middleware\EnsureUserIsVerified;
use App\Http\Middleware\SetLocaleFromHeader;


$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // هنا تقدر تضيف Middleware global لو حبيت
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// ✅ تسجيل alias للميدلوير custom
$app->router->aliasMiddleware('verified.user', EnsureUserIsVerified::class);
$app->router->aliasMiddleware('set.locale', SetLocaleFromHeader::class);

return $app;
