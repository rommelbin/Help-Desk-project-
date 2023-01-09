<?php
declare(strict_types=1);
namespace App\Http\Middleware;

use Closure;

class GetParamsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $params = $request->route()[2];
        $request->params = $params;

        $modelClass = "App\\Models\\" . ucfirst($request->params['model']);

        if (!class_exists($modelClass))
            return response('wrong model', 404);

        $request->params['modelClass'] = $modelClass;
        return $next($request);
    }
}
