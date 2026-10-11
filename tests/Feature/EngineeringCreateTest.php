<?php

namespace Tests\Feature;

use Tests\TestCase;

class EngineeringCreateTest extends TestCase
{
    /** @test */
    public function engineering_create_route_exists()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('engineering.create'));
    }

    /** @test */
    public function engineering_store_route_exists()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('engineering.store'));
    }

    /** @test */
    public function engineering_index_route_exists()
    {
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('engineering.index'));
    }

    /** @test */
    public function engineering_routes_are_protected_by_auth()
    {
        $this->get('/engineering/create')->assertRedirect('/login');
        $this->post('/engineering')->assertRedirect('/login');
        $this->get('/engineering')->assertRedirect('/login');
    }
}
