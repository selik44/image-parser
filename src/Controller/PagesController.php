<?php

namespace App\Controller;

use App\Repository\ParsePagesRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{

    /**
     * @var ParsePagesRepository
     */
    protected $parseRepository;

    /**
     * ParseService constructor.
     * @param ParsePagesRepository $parseRepository
     */
    public function __construct(ParsePagesRepository $parseRepository)
    {
        $this->parseRepository = $parseRepository;
    }

    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        $pages = $this->parseRepository->findBy([], ['count_images' => 'DESC']);
        return $this->render('pages/index.html.twig', [
            'pages' => $pages
        ]);
    }
}