<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Laravel 13');
    }

    public function test_health_check_returns_a_successful_response(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_environment_file_is_not_accessible(): void
    {
        $this->get('/.env')->assertNotFound();
    }
}
