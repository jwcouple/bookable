<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookableControllerTest extends WebTestCase
{

    public function testSettings()
    {
        $client = static::createClient();
        $client->request('GET', '/settings');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Settings');
        // Add more assertions based on the expected behavior of the settings route
    }

    public function testBook()
    {
        $client = static::createClient();
        $client->request('GET', '/book/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Book Title');
        // Add more assertions based on the expected behavior of the book route
    }

    public function testVote()
    {
        $client = static::createClient();
        $client->request('POST', '/book/1/vote');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        // Add more assertions based on the expected behavior of the vote route
    }

    public function testFollow()
    {
        $client = static::createClient();
        $client->request('POST', '/book/1/follow');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        // Add more assertions based on the expected behavior of the follow route
    }

    public function testWelcome()
    {
        $client = static::createClient();
        $client->request('GET', '/welcome');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Welcome!');
        // Add more assertions based on the expected behavior of the welcome route
    }

    public function testHome()
    {
        $client = static::createClient();
        $client->request('GET', '/home');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Home');
        // Add more assertions based on the expected behavior of the home route
    }

    public function testProfile()
    {
        $client = static::createClient();
        $client->request('GET', '/profile/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Profile');
        // Add more assertions based on the expected behavior of the profile route
    }

    public function testAbout()
    {
        $client = static::createClient();
        $client->request('GET', '/about');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'About');
        // Add more assertions based on the expected behavior of the about route
    }

    public function testBrowsing()
    {
        $client = static::createClient();
        $client->request('GET', '/browsing/book-title');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.title', 'Browsing');
        // Add more assertions based on the expected behavior of the browsing route
    }
}
