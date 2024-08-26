<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Entity\Request;

class PeopleController extends BaseController
{
    /**
     * @Route("/people", name="app_people")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(Users::class)->findAll();
        $requests = $this->em->getRepository(Request::class)->findAll();

        if(isset($_GET['message'])){
            $sent = true;
            $fid = $_GET['friendid'];
            $uid = $this->getUser()->getId();
        } else {
            $sent = false;
            $fid = null;
            $uid = null;
        }

        return $this->render('people/index.html.twig', [
            'users' => $users,
            'requests' => $requests,
            'sent' => $sent,
            'fid' => $fid,
            'uid' => $uid,
        ]);
    }
    /**
     * @Route("/people/sendrequest", name="send_request")
     */

    public function sendrequestAction(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        if(isset($_GET['friendid'])){
            $friendId = $_GET['friendid'];
            $request = new Request();
            $request->setUid($this->getUser()->getId());
            $request->setFriendId($friendId);
            $request->setRequestStatus("requested");
            $request->setCreated(new \DateTime());
            $request->setUpdated(new \DateTime());
            $entityManager->persist($request);
            $entityManager->flush();
            if ($request->getId() == null) {
                $this->addFlash('error', 'Failed to send friend request.');
            } else {
                $this->addFlash('success', 'Friend request sent successfully!');
            }
        }

        return $this->redirectToRoute('people_index', [
            'message' => 'sent',
            'friendid' => $friendId
        ]);
    }
}
