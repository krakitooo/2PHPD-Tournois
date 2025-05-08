<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserGettersAndSetters(): void
    {
        $user = new User();

        $user->setUsername('testuser');
        $user->setFirstName('Jean');
        $user->setLastName('Dupont');
        $user->setEmailAddress('test@example.com');
        $user->setPassword('hashedpassword');
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setStatus('actif');

        $this->assertEquals('testuser', $user->getUsername());
        $this->assertEquals('Jean', $user->getFirstName());
        $this->assertEquals('Dupont', $user->getLastName());
        $this->assertEquals('test@example.com', $user->getEmailAddress());
        $this->assertEquals('hashedpassword', $user->getPassword());
        $this->assertEquals(['ROLE_USER', 'ROLE_ADMIN'], $user->getRoles());
        $this->assertEquals('actif', $user->getStatus());
    }

    public function testRolesAlwaysContainUserRole(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $roles = $user->getRoles();
        $this->assertContains('ROLE_USER', $roles, 'ROLE_USER doit être présent');
        $this->assertContains('ROLE_ADMIN', $roles);
    }
}
