<?php

namespace App\View\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;

class BreadcrumbComposer
{
    /**
     * Bind breadcrumb items to the view.
     * Builds: Home (link) + current page (label from config or derived from route name).
     */
    public function compose(View $view): void
    {
        $view->with('breadcrumbItems', $this->buildBreadcrumbItems());
    }

    /**
     * @return array<int, array{label: string, url: string|null}>
     */
    protected function buildBreadcrumbItems(): array
    {
        if (!Route::current()) {
            return [];
        }

        $items = [];
        $items[] = [
            'label' => __('لوحة التحكم الرئيسية'),
            'url'   => route('home.index'),
        ];

        $routeName = Route::currentRouteName();
        if ($routeName && $routeName !== 'home.index') {
            $label = config("breadcrumbs.{$routeName}");
            if ($label !== null) {
                $label = __($label);
            } else {
                $label = $this->deriveLabelFromRouteName($routeName);
            }
            $items[] = [
                'label' => $label,
                'url'   => null,
            ];
        }

        return $items;
    }

    protected function deriveLabelFromRouteName(string $name): string
    {
        $parts = explode('.', $name);
        $last = end($parts);
        if ($last === 'index' && count($parts) > 1) {
            array_pop($parts);
            $last = end($parts);
        }
        $key = str_replace(['-', '_'], ' ', $last);
        $translated = __($key);
        return $translated !== $key ? $translated : ucfirst($key);
    }
}
