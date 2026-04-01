<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoadsForAnonymousUser(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form.loginForm');
        $this->assertSelectorExists('input[name="_username"]');
        $this->assertSelectorExists('input[name="_password"]');
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginRedirectsIfAlreadyAuthenticated(): void
    {
        $client = static::createClient();

        // Crée un utilisateur en base
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = (new User())->setEmail('test@test.com');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));
        $user->setLastname('Test');
        $user->setFirstname('User');
        $em->persist($user);
        $em->flush();

        // Simule la connexion
        $client->loginUser($user);
        $client->request('GET', '/login');

        $this->assertResponseRedirects();
    }

    public function testLogoutWorks(): void
    {
        $client = static::createClient();

        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get('security.user_password_hasher');

        $user = (new User())->setEmail('test@test.com');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));
        $user->setLastname('Test');
        $user->setFirstname('User');
        $em->persist($user);
        $em->flush();

        $client->loginUser($user);
        $client->request('GET', '/logout');

        $this->assertResponseRedirects();
    }
}