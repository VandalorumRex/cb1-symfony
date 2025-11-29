<?php

namespace App\Controller\Api;

use App\Lib\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OffersController extends AbstractController
{
    private string $path;

    public function __construct()
    {
        //parent::__construct();
        $this->path = filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../xml/offers.xml';
    }

    #[Route('/api/offers', name: 'get_api_offers',methods: ['GET'])]
    public function index(): JsonResponse
    {
        $superResponse = ['message' => 'Данные не найдены'];
        $code = Response::HTTP_NOT_FOUND;
        if (file_exists($this->path)) {
            $xmlString = (string)file_get_contents($this->path);
            $xml = new \SimpleXMLElement($xmlString);
            $code = Response::HTTP_OK;
            $superResponse = [];
            foreach ($xml as $offer) {
                $response = ['internalId' => (string)$offer[0]->attributes()->{'internal-id'}[0]];
                foreach ($offer[0] as $field => $value) {
                    $isObject = count($value[0]) > 1;
                    // camel-case => camelCase
                    $feld = $field;//Inflector::variable($field, '-');
                    $response[$feld] =  $isObject ? $value[0] : (string)$value[0];
                    if (!$isObject) {
                        $response[$feld] =  (string)$value[0];
                    } else {
                        $response[$feld] = [];
                        foreach ($value[0] as $subField => $subValue) {
                            //$response[$feld][Inflector::variable($subField, '-')] = (string)$subValue[0];
                            $response[$feld][$subField] = (string)$subValue[0];
                        }
                    }
                }
                array_push($superResponse, $response);
            }
        }
        return $this->json($superResponse, $code);
    }
    
    #[Route('/api/offers', name: 'post_api_offers',methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        /** @var array<string, string|array<string, string>> $offer */
        $offer = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
            return new JsonResponse(['message' => 'Invalid JSON provided'], Response::HTTP_BAD_REQUEST);
        }
        //return $this->json($offer);
        if (!file_exists(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../xml')) {
            mkdir(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . '/../xml');
        }
        if (!file_exists($this->path)) {
            $xmlString = '<?xml version="1.0" encoding="UTF-8"?><offers></offers>';
        } else {
            $xmlString = (string)file_get_contents($this->path);
        }
        $offers = new \SimpleXMLElement($xmlString);
        $child = $offers->addChild('offer');
        foreach ($offer as $field => $item) {
            if (is_array($item)) {
                $onyq = $child->addChild($field);
                //print_r($item);
                foreach ($item as $subField => $subItem) {
                    // Превращаем camelCase в camel-case
                    //$onyq->addChild(Inflector::dasherize($subField), $subItem);
                    //print_r($subItem);
                    $onyq->addChild($subField, $subItem);
                }
            } else {
                if ($field === 'creationDate' && !$item) {
                    $item = date('c');
                }
                // Превращаем camelCase в camel-case согласно
                // https://yandex.ru/support/realty/ru/requirements/requirements-sale-housing#in_common
                //$child->addChild(Inflector::dasherize($field), $item);
                $child->addChild($field, $item);
            }
        }
        $child->addAttribute('internal-id', Utils::GUIDv4());

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML((string)$offers->asXML());
        $xmlPretty = $dom->saveXML();
        file_put_contents($this->path, $xmlPretty);
        //return response()->json(['code' => HttpCode::CREATED, 'message' => 'Принято']);
        return $this->json(['message' => 'Принято'], Response::HTTP_CREATED);
    }
    
    #[Route('/api/offers/{guid}', name: 'get_api_offer',methods: ['GET'])]
    public function view(string $guid): JsonResponse
    {
        if (!file_exists($this->path)) {
            $response = ['message' => 'Данные не найдены'];
            $code = Response::HTTP_NOT_FOUND;
        } else {
            $xmlString = (string)file_get_contents($this->path);
            $xml = new \SimpleXMLElement($xmlString);
            $response = ['message' => 'Оффер на найден'];
            $code = Response::HTTP_NOT_FOUND;
            $offer = $xml->xpath("//offer[@internal-id='" . $guid . "']");
            if ($offer) {
                $response = ['internalId' => $guid];
                $code = Response::HTTP_OK;
                foreach ($offer[0] as $field => $value) {
                    $isObject = count($value[0]) > 1;
                    // camel-case => camelCase
                    $feld = $field;//Inflector::variable($field, '-');
                    $response[$feld] =  $isObject ? $value[0] : (string)$value[0];
                    if (!$isObject) {
                        $response[$feld] =  (string)$value[0];
                    } else {
                        $response[$feld] = [];
                        foreach ($value[0] as $subField => $subValue) {
                            //$response[$feld][Inflector::variable($subField, '-')] = (string)$subValue[0];
                            $response[$feld][$subField] = (string)$subValue[0];
                        }
                    }
                }
                //print_r($response);
            }
        }
        return $this->json($response, $code);
    }
}
