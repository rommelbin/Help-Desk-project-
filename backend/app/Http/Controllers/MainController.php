<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ExceptionDistribution;
use Illuminate\Http\Request;

class MainController extends Controller
{
    /**
     * @param Request $request
     * @param string $model
     * @param string $method
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(
        Request $request,
        string  $model,
        string  $method,
        ?int    $id = null
    )
    {
        try {
            $modelClass = $request->params['modelClass'];
            $method .= 'Model';
            $res = $modelClass::$method($id, $request->all());
            return response()->json($res, $res['status'] ?? 200);

        } catch (\Exception $exception) {
            return ExceptionDistribution::defineException($exception);
        }
    }

    public function checkCentrifugo()
    {
        $client = new \phpcent\Client("http://centrifugo:8000/api");
        $client->setApiKey("api_key");
        $client->publish("123", ["message" => "Hello World"]);
        return response('Всё ок пока что', 200);
    }
}
