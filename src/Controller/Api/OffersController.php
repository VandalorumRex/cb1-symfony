<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/api/offers', name: 'app_api_offers')]
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
}
