<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\MediaRepository;

class HomeController extends AbstractController
{
    private $session;
    private $mediaRepository;

    public function __construct(SessionInterface $session, RequestStack $requestStack, MediaRepository $mediaRepository)
    {
        $this->session = $requestStack->getSession();
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * @Route("/home/login", name="app_home_login")
     */
    public function login(Request $request): Response
    {
        $error = '';
        $email = '';
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $error = 'Invalid email or password';
            } elseif ($user->getPassword() !== sha1($password)) {
                $error = 'Invalid email or password';
            } else {
                $this->session->set('uid', $user->getId());

                return $this->redirectToRoute('app_dashboard');
            }
        }

        return $this->render('home/login.html.twig', [
            'error' => $error,
            'email' => $email,
        ]);
    }

    /**
        * @Route("/home/jsonlogin", name="app_home_login")
     */
    public function jsonlogin(Request $request): Response
    {
        $error = '';
        $email = '';
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $user = $this->getDoctrine()->getRepository(Users::class)->findOneBy(['email' => $email]);

            if (!$user) {
                $error = 'Invalid email or password';
            } elseif ($user->getPassword() !== sha1($password)) {
                $error = 'Invalid email or password';
            } else {
                $this->session->set('uid', $user->getId());

                // Return a JSON response upon successful login
                $response = [
                    'success' => true,
                    'message' => 'Login successful',
                    'redirect' => $this->generateUrl('app_dashboard'), // Provide the route name
                ];

                return new JsonResponse($response);
            }
        }

        // Return an error message as a JSON response if applicable
        if (!empty($error)) {
            $response = [
                'success' => false,
                'message' => $error,
            ];

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        // Return a JSON response for initial rendering
        $response = [
            'success' => true,
            'message' => 'Render login form',
            'email' => $email,
        ];

        return new JsonResponse($response);
    }

    /**
        * @Route("/home/fetchobjects", name="app_home_login")
     */
    public function fetchobjects(): JsonResponse
    {
        $mediaObjects = $this->mediaRepository->findByObjects('unity_model');
        return $this->json($mediaObjects);
    }

    /**
 * @Route("/home/storelogs", name="app_store_logs")
 */
public function storelogs(Request $request): JsonResponse
{
    $msg = $request->query->get('msg');
    
    $newuser = new Logger();
    $newuser->errmessage = $msg;
    $newuser->created = date('Y-m-d');            
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newuser);
                $entityManager->flush();

    return $this->json($mediaObjects);
}
    /**
     * @Route("/home/signup", name="app_home_signup")
     */
    public function signup(Request $request): Response
    {
        $exists = $request->query->get('exists');
        if ($exists !== null) {
            $vars['exists'] = 1;
        }

        $emailFromFront = $request->query->get('email');
        $passFromFront = $request->query->get('passowrd');
        $vars['email'] = $emailFromFront;
        $vars['pass'] = $passFromFront;

        if ($request->isMethod('POST')) {
            $checkuser = $this->getDoctrine()->getRepository(Users::class)->findOneByEmail($request->request->get('email'));
            if (!$checkuser) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $lookup = "https://extreme-ip-lookup.com/json/".$ip."?key=k9efMu0Pi0rkCdJKFNWE";
                $json = file_get_contents($lookup);
                $lookupArray = json_decode($json, true);
                $ipName = $lookupArray['ipName'];
                $checkCarrier = $this->getDoctrine()->getRepository(Carriers::class)->findOneByCarrierUrl($ipName);
                if (!$checkCarrier) {
                    $carrier = new Carriers();
                    $carrier->setCarrierName($lookupArray['asnOrg']);
                    $carrier->setCarrierUrl($ipName);
                    $carrier->setCreated(new \DateTime());
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($carrier);
                    $entityManager->flush();
                }

                $newuser = new Users();
                $newuser->setFirstName($request->request->get('firstname'));
                $newuser->setLastName($request->request->get('lastname'));
                $newuser->setEmail($request->request->get('email'));
                $newuser->setUsername($request->request->get('username'));
                $newuser->setPassword(sha1($request->request->get('password')));
                $newuser->setCarrier($ipName);
                $newuser->setDob(new \DateTime($request->request->get('dob')));
                $newuser->setGender($request->request->get('gender'));
                $newuser->setCity($request->request->get('city'));
                $newuser->setRegionStateProvince($request->request->get('region'));
                $newuser->setCountry($request->request->get('country'));
                $newuser->setCellphone($request->request->get('cellphone'));
                $newuser->setRegistered(new \DateTime());
                $newuser->setLastLogin(new \DateTime());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newuser);
                $entityManager->flush();

                $transport = (new Swift_SmtpTransport());
                //start the session for the new user
                $this->get('session')->set('uid', $newuser->getId());
                return $this->redirectToRoute('dashboard');
            } else {
                return $this->redirectToRoute('signup', ['exists' => 1]);
            }
        }

        return $this->render('home/signup.html.twig', $vars);
    }

    /**
     * @Route("/home/session", name="app_home_session")
     */
    public function session(): Response
    {
        return $this->render('session/session_expired.html.twig');
    }
}
