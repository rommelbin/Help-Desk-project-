<?php

namespace App\Http\Middleware;

use App\Exceptions\ExceptionDistribution;
use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Response;

class IsLoggedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $registration = false;
            $methods = [
                'login',
                'send',
                'check',
                'register'
            ];
            if (in_array($request->params['method'], $methods)) {
                if ($request->params['method'] === 'register') {
                    $registration = true;
                } else {
                    return $next($request);
                }
            }
       if (!$request->header('Authorization')) {
            return ['data' => 'Unauthorized', 'status' => Response::HTTP_UNAUTHORIZED];
        }
        $token = substr($request->header('Authorization'), 7);


            $credentials = JWT::decode($token, env('JWT_SECRET'), ['HS256']);
            if ($registration) {
                return ['data' => ['id' => $credentials->sub], 'status' => Response::HTTP_OK];
            }
            $user = User::findOrFail($credentials->sub);
//            $user = User::where('role', '=', 'Администратор')->first();
            auth()->setUser($user);
            return $next($request);

        } catch (\Exception $exception) {
            return ExceptionDistribution::defineException($exception);
        }

    }
}
