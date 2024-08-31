<?php 

namespace Ninja;

use ErrorException;
use Throwable;

class ApiErrorHandler {

    public static function handleException (Throwable $exception) {

        //TODO: send a more generic error message for a production enviroment and also log the error

        http_response_code(500);

        echo json_encode([

            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine()

        ]);

    }

    public static function handleError (int $errorNumber, string $errorMessage, string $errorFile, int $errorLine) {

        throw new ErrorException($errorMessage, 0, $errorNumber,  $errorFile, $errorLine);

    }

}