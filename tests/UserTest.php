<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_be_created()
    {
        // Arrange
        $userData = ['name' => 'John Doe', 'email' => 'john@example.com'];

        // Act
        $response = $this->post('/users', $userData);

        // Assert
        $response->assertStatus(200);
        $this->assertDatabaseHas('users', $userData);
    }
}
