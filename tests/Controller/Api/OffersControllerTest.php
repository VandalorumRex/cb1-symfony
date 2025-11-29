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
}
