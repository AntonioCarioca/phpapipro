<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\JwtService;

class JwtServiceTest extends TestCase
{
    public function testGenerateToken(): void
    {
        $_ENV['JWT_SECRET'] = '9f2a0f5d8c7e4b9a1d3f6c8e2a7b5d1f';

        $_ENV['JWT_EXPIRE'] = 3600;

        $user = [
            'id' => 1,
            'email' => 'admin@email.com',
            'role' => 'admin'
        ];

        $token = JwtService::generate($user);

        $this->assertNotEmpty($token);
    }

    public function testValidateToken(): void
    {
        $_ENV['JWT_SECRET'] = '9f2a0f5d8c7e4b9a1d3f6c8e2a7b5d1f';

        $_ENV['JWT_EXPIRE'] = 3600;

        $user = [
            'id' => 1,
            'email' => 'admin@email.com',
            'role' => 'admin'
        ];

        $token = JwtService::generate($user);

        $decoded = JwtService::validate($token);

        $this->assertEquals(
            'admin@email.com',
            $decoded->user->email
        );
    }
}
