<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryTest extends WebTestCase
{
    public function testCategoryIndex(): void
    {
        $client = self::createClient();
        $client->request('GET', '/category');
        $this->assertResponseStatusCodeSame(302);
    }

    public function testCategoryIndexRedirectToLogin(): void
    {
        $client = self::createClient();
        $client->request('GET', '/category');
        $this->assertResponseRedirects('/login');
    }

    public function testCategoryIndexSuccessLogin(): void
    {
        $client = self::createClient();
        $userRepo = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepo->findOneByEmail('test@gmail.com');
        $client->loginUser($testUser);
        $client->request('GET', '/category');
        $this->assertResponseStatusCodeSame(301);
    }
}
