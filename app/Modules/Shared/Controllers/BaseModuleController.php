<?php

namespace App\Modules\Shared\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class BaseModuleController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Module name
     */
    protected string $moduleName;

    /**
     * Service class for the module
     */
    protected $service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->moduleName = $this->getModuleName();
        $this->middleware(['auth', 'permission:access ' . strtolower($this->moduleName)]);
    }

    /**
     * Get module name from class
     */
    protected function getModuleName(): string
    {
        $className = class_basename(static::class);
        return str_replace('Controller', '', $className);
    }

    /**
     * Get service instance
     */
    protected function getService()
    {
        if (!$this->service) {
            $serviceClass = "App\\Modules\\{$this->moduleName}\\Services\\{$this->moduleName}Service";
            $this->service = app($serviceClass);
        }
        return $this->service;
    }

    /**
     * Standard index response
     */
    protected function indexResponse($data, $view = null)
    {
        $view = $view ?: strtolower($this->moduleName) . '.index';
        return view($view, $data);
    }

    /**
     * Standard show response
     */
    protected function showResponse($data, $view = null)
    {
        $view = $view ?: strtolower($this->moduleName) . '.show';
        return view($view, $data);
    }

    /**
     * Standard create response
     */
    protected function createResponse($data, $view = null)
    {
        $view = $view ?: strtolower($this->moduleName) . '.create';
        return view($view, $data);
    }

    /**
     * Standard edit response
     */
    protected function editResponse($data, $view = null)
    {
        $view = $view ?: strtolower($this->moduleName) . '.edit';
        return view($view, $data);
    }

    /**
     * Success response with message
     */
    protected function successResponse($message = 'Operation completed successfully')
    {
        return redirect()
            ->back()
            ->with('success', $message);
    }

    /**
     * Error response with message
     */
    protected function errorResponse($message = 'Operation failed')
    {
        return redirect()
            ->back()
            ->with('error', $message);
    }

    /**
     * Redirect to index with message
     */
    protected function redirectToIndex($message = null, $type = 'success')
    {
        $route = strtolower($this->moduleName) . '.index';
        return redirect()
            ->route($route)
            ->with($type, $message ?: 'Operation completed successfully');
    }

    /**
     * Validate request using module rules
     */
    protected function validateModuleRequest($request, array $rules = null)
    {
        $rules = $rules ?: $this->getValidationRules();
        return $request->validate($rules);
    }

    /**
     * Get validation rules for the module
     */
    protected function getValidationRules(): array
    {
        return [];
    }

    /**
     * Get module-specific permissions
     */
    protected function getModulePermissions(): array
    {
        return [
            'view' => 'view ' . strtolower($this->moduleName),
            'create' => 'create ' . strtolower($this->moduleName),
            'update' => 'update ' . strtolower($this->moduleName),
            'delete' => 'delete ' . strtolower($this->moduleName),
        ];
    }

    /**
     * Check if user has module permission
     */
    protected function hasModulePermission($permission): bool
    {
        $permissions = $this->getModulePermissions();
        return auth()->user()->hasPermissionTo($permissions[$permission] ?? '');
    }
}
