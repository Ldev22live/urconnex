<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Twig\Environment as TwigEnvironment;

class ChatController extends BaseController
{
    /**
     * @Route("/chat", name="app_index_chat")
     */
    public function index(): Response
    {
        $users = $this->em->getRepository(Users::class)->findAll();

        return $this->render('chat/index.html.twig', [
            'users' => $users,
            'uid' => $this->uid,
        ]);
    }

    /**
     * @Route("/private/{receiverId}", name="app_private_chat")
     * @param int $receiverId
     * @return Response
     */
    public function private(MessagesRepository $messagesRepository, int $receiverId): Response
    {
        $users = $this->em->getRepository(Users::class)->findAll();
        $uid = $this->uid;

        $receiver = $this->em->getRepository(Users::class)->find($receiverId);
        $userMessages = $messagesRepository->findBySender($uid, $receiver->getId());

        $rname = $receiver->getFirstname() . " " . $receiver->getLastname();
        $ravatar = $receiver->getAvatar();
        $reccontroller = 'chat';
        $seotag = "Chat with " . $rname;

        return $this->render('chat/private.html.twig', [
            'users' => $users,
            'uid' => $uid,
            'rid' => $receiverId,
            'messages' => $userMessages,
            'rname' => $rname,
            'ravatar' => $ravatar,
            'reccontroller' => $reccontroller,
            'seotag' => $seotag
        ]);
    }

    /**
     * @Route("/chat/save", methods={"POST"})
     */
    public function save(Request $request): JsonResponse
    {
        try {
            $chatMessage = new Messages();
            $chatMessage->setCreated(new \DateTime());
            $chatMessage->setUpdate(new \DateTime());
            $message = $request->request->get('message');
            if ($message) {
                $chatMessage->setMessage($message);
            }
            if (!$request->request->has('rid')) {
                $chatMessage->setSenderId($request->request->get('uid'));
                $chatMessage->setReceiverId($request->request->get('rid'));
            } else {
                $chatMessage->setSenderId($this->getUser()->getId());
                $chatMessage->setReceiverId($request->request->get('rid'));
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($chatMessage);
            $entityManager->flush();

            return $this->json(['success']);
        } catch (\Exception $ex) {
            return $this->json(['failure']);
        }
    }
}
