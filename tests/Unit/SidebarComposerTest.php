<?php

namespace Tests\Unit;

use App\View\Composers\SidebarComposer;
use Tests\TestCase;

class SidebarComposerTest extends TestCase
{
    protected function makeComposer(): SidebarComposer
    {
        return new class extends SidebarComposer
        {
            public function visibleMenu(array $items, $user): array
            {
                return $this->filterMenuItems($items, $user);
            }
        };
    }

    /** @test */
    public function parent_menu_is_visible_when_any_child_is_visible(): void
    {
        $user = new class
        {
            public function can($permission): bool
            {
                return $permission === 'index requests';
            }
        };

        $menu = [
            [
                'type' => 'submenu',
                'label' => 'Parent',
                'permissions' => ['index mokalafs', 'index requests'],
                'children' => [
                    [
                        'type' => 'link',
                        'route' => 'requests.index',
                        'label' => 'Visible Child',
                        'permissions' => ['index requests'],
                    ],
                    [
                        'type' => 'link',
                        'route' => 'mokalafs.index',
                        'label' => 'Hidden Child',
                        'permissions' => ['index mokalafs'],
                    ],
                ],
            ],
        ];

        $visible = $this->makeComposer()->visibleMenu($menu, $user);

        $this->assertCount(1, $visible);
        $this->assertSame('Parent', $visible[0]['label']);
        $this->assertCount(1, $visible[0]['children']);
        $this->assertSame('Visible Child', $visible[0]['children'][0]['label']);
    }

    /** @test */
    public function leaf_item_with_multiple_permissions_still_requires_all_by_default(): void
    {
        $user = new class
        {
            public function can($permission): bool
            {
                return $permission === 'index requests';
            }
        };

        $menu = [
            [
                'type' => 'link',
                'route' => 'requests.index',
                'label' => 'Requests',
                'permissions' => ['index requests', 'update requests'],
            ],
        ];

        $visible = $this->makeComposer()->visibleMenu($menu, $user);

        $this->assertCount(0, $visible);
    }

    /** @test */
    public function item_can_opt_in_to_any_permission_matching(): void
    {
        $user = new class
        {
            public function can($permission): bool
            {
                return $permission === 'index requests';
            }
        };

        $menu = [
            [
                'type' => 'link',
                'route' => 'requests.index',
                'label' => 'Requests',
                'permissions' => ['index requests', 'update requests'],
                'permission_match' => 'any',
            ],
        ];

        $visible = $this->makeComposer()->visibleMenu($menu, $user);

        $this->assertCount(1, $visible);
        $this->assertSame('Requests', $visible[0]['label']);
    }

    /** @test */
    public function parent_can_be_configured_to_require_its_own_visibility_rules(): void
    {
        $user = new class
        {
            public function can($permission): bool
            {
                return $permission === 'index requests';
            }
        };

        $menu = [
            [
                'type' => 'submenu',
                'label' => 'Strict Parent',
                'permissions' => ['index mokalafs'],
                'show_when_any_child_visible' => false,
                'children' => [
                    [
                        'type' => 'link',
                        'route' => 'requests.index',
                        'label' => 'Visible Child',
                        'permissions' => ['index requests'],
                    ],
                ],
            ],
        ];

        $visible = $this->makeComposer()->visibleMenu($menu, $user);

        $this->assertCount(0, $visible);
    }
}
