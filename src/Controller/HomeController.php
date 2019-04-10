<?php

namespace App\Controller;

use Psr\SimpleCache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Tecnogo\MeliSdk\Client;
use Tecnogo\MeliSdk\Entity\Category\Category;
use Tecnogo\MeliSdk\Exception\ContainerException;
use Tecnogo\MeliSdk\Exception\MissingConfigurationException;
use Tecnogo\MeliSdk\Request\Exception\RequestException;

class HomeController extends AbstractController
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index()
    {
        return $this->render('home.html.twig', [
            'categories' => $this->getCategories(),
            'selected_category_id' => null
        ]);
    }

    public function category($category)
    {
        return $this->render('category.html.twig', [
            'categories' => $this->getCategories(),
            'selected_category_id' => $category,
            'selected_category_data' => $this->getCategoryData($category)
        ]);
    }

    private function getCategories()
    {
        return $this->client
            ->category('MLA1182')
            ->children()
            ->map(function (Category $category) {
                return [
                    'id' => $category->id(),
                    'name' => $category->name()
                ];
            });
    }

    private function getCategoryData($id)
    {
        $category = $this->client->category($id);

        try {
            $data = [
                'name' => $category->name(),
                'attributes' => $category->attributes()->toArray(),
                'error' => null
            ];
        } catch (\Exception $e) {
            $data = [
                'name' => null,
                'attributes' => [],
                'error' => $e->getMessage()
            ];
        }

        return $data;
    }
}