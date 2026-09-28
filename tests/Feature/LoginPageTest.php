<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_returns_a_successful_response(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertViewIs('home');
    }

    public function test_forgot_password_page_returns_a_successful_response(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertViewIs('home');
    }

    public function test_registration_page_returns_a_successful_response(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertViewIs('home');
    }
}
