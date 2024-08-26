<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DashboardController extends BaseController
{
    /**
     * @Route("/dashboard", name="app_dashboard_index")
     */
    public function index(Request $request, MediaRepository $mediaRepository): Response
    {
        //$loggeduser = $this->getDoctrine()->getRepository(Users::class)->find($this->session->get('user_id'));
        $seotag = "Dashboard";
        $success = $request->query->get('message');
        $successd = $request->query->get('messaged');
        $media = $mediaRepository->findByFiletype('video');

        return $this->render('dashboard/index.html.twig', [
            'seotag' => $seotag,
            'success' => $success,
            'successd' => $successd,
            'media' => $media,
        ]);
    }

}
