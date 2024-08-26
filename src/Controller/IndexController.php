<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Media;
use App\Repository\MediaRepository;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(MediaRepository $mediaRepository): Response
    {
        $media = $mediaRepository->findByFiletype('video');

        return $this->render('index/index.html.twig', [
            'media' => $media,
        ]);
    }
}