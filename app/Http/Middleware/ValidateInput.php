<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ValidateInput
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$rules)
    {
        if ($request->method() === 'GET') {
            return $next($request);
        }

        $rules = $this->parseRules($rules);
        
        if (empty($rules)) {
            return $next($request);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        // Sanitize input
        $this->sanitizeInput($request);

        return $next($request);
    }

    /**
     * Parse rules from middleware parameters.
     */
    protected function parseRules(array $rules): array
    {
        $parsed = [];
        
        foreach ($rules as $rule) {
            if (str_contains($rule, ':')) {
                [$field, $ruleString] = explode(':', $rule, 2);
                $parsed[$field] = explode('|', $ruleString);
            }
        }

        return $parsed;
    }

    /**
     * Sanitize input data.
     */
    protected function sanitizeInput(Request $request): void
    {
        $input = $request->all();

        foreach ($input as $key => $value) {
            if (is_string($value)) {
                // Remove HTML tags and special characters
                $sanitized = strip_tags($value);
                $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
                $sanitized = trim($sanitized);
                
                $request->merge([$key => $sanitized]);
            }
        }
    }
}
