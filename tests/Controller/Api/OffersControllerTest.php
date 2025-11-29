<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OffersControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $response = static::createClient()->request('GET', '/api/offers');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        //$this->assertJsonContains(['@id' => '/']);
    }
    
    public function testStore(): void
    {
        $response = static::createClient()->xmlHttpRequest('POST', '/api/offers', ['name' => 'Fabien']);;

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        //$this->assertJsonContains(['@id' => '/']);
    }
}
