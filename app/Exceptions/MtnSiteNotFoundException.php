<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

class MtnSiteNotFoundException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool
     */
    public function report()
    {
        return false; // Don't report to logs
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error' => 'MTN site not found'
        ], Response::HTTP_NOT_FOUND);
    }
}
