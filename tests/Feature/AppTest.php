<?php

namespace Tests\Feature;

use Tests\TestCase;

class AppTest extends TestCase
{
    public function test_home_page_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_register_page_loads()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }
}
