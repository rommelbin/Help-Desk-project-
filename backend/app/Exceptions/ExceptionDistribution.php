<?php
declare(strict_types=1);

namespace App\Exceptions;

use Doctrine\Instantiator\Exception\UnexpectedValueException;
use Firebase\JWT\ExpiredException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpFoundation\Response;

class ExceptionDistribution
{
    public static function defineException(\Exception $exception): \Illuminate\Http\JsonResponse
    {
        $exception_class = get_class($exception);
        switch ($exception_class) {
            case ValidationException::class:
                return response()->json(['errors' => $exception->getValidationError(), 'status' => 422], 200);
            case ExpiredException::class:
                return response()->json('ExpiredToken', 401);
            case UnexpectedValueException::class:
                return response()->json('Wrong number of segments', Response::HTTP_UNAUTHORIZED);
            case FilterException::class:
                return response()->json($exception->getMessage(), $exception->getCode());
            case AuthorizationException::class:
            case CodeException::class:
                return response()->json(['errors' => $exception->getMessage(), 'status' => $exception->getCode()], 200);
            default:
                (new SendExceptionsService())->sendException($exception);
                return response()->json([], 500);
        }
    }
}
