<?php

namespace App\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class OffersControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        static::createClient()->request('GET', '/api/offers');
        $this->assertResponseIsSuccessful();
        //$this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        //$this->assertJsonContains(['@id' => '/']);
    }
    
    public function testStore(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest('POST', '/api/offers', ['name' => 'Fabien']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $content='{
            "type": "продажа",
            "propertyType": "жилая",
            "category": "дача",
            "garageType": "гараж",
            "lotNumber": "1",
            "cadastralNumber": "16:00:111222:789",
            "url": "https://example.com",
            "creationDate": "",
            "location": {
              "country": "Россия",
              "region": "Республика Татарстан",
              "district": "Зеленодольский",
              "localityName": "Осиново",
              "subLocalityName": "Радужный",
              "address": "Садовая 9",
              "apartment": ""
            }
          }';
        $client->request('POST', '/api/offers', content: $content);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
    
    public function testUpdate(): void
    {
        $client = static::createClient();
        $client->xmlHttpRequest('PUT', '/api/offers/1', ['name' => 'Fabien']);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        
        $content='{
            "type": "продажа",
            "propertyType": "жилая",
            "category": "дача",
            "garageType": "гараж",
            "lotNumber": "1",
            "cadastralNumber": "16:00:111222:789",
            "url": "https://example.com",
            "creationDate": "",
            "location": {
              "country": "Россия",
              "region": "Республика Татарстан",
              "district": "Зеленодольский",
              "localityName": "Осиново",
              "subLocalityName": "Радужный",
              "address": "Садовая 9",
              "apartment": ""
            }
          }';
        $client->request('PUT', '/api/offers/2', content: $content);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
