<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class RequestWorkflowException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function render(Request $request)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => $this->getMessage() ?: 'لم يتم إعداد مسار (سير عمل) لهذا النوع من الطلبات',
                'error' => 'workflow_not_configured'
            ], 400);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $this->getMessage() ?: 'لم يتم إعداد مسار (سير عمل) لهذا النوع من الطلبات');
    }

    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        \Log::error('Request Workflow Exception', [
            'message' => $this->getMessage(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
