<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends BaseController
{
    /**
     * @Route("/profile", name="app_profile_index")
     */
    public function index(Request $request)
    {
        $profile = $this->em->getRepository(Users::class)->find($this->uid);

        if ($request->isMethod('POST')) {
            $entityManager = $this->getDoctrine()->getManager();

            $profile->setFirstname($request->request->get('firstname'));
            $profile->setLastname($request->request->get('lastname'));
            $profile->setCellphone($request->request->get('cellphone'));
            $profile->setDob(new \DateTime($request->request->get('dob')));
            $profile->setRegionStateProvince($request->request->get('region'));
            $profile->setCity($request->request->get('city'));
            $profile->setCountry($request->request->get('country'));

            if ($request->files->has('propic')) {
                $avatarFile = $request->files->get('propic');
                $avatarFileName = $this->uid.'avatar.jpg';
                $avatarFilePath = '/var/www/html/uronimo/public/profilepic/'.$avatarFileName;
                $avatarFile->move('/var/www/html/uronimo/public/profilepic/', $avatarFileName);
                $profile->setAvatar('/profilepic/'.$avatarFileName);
            }

            $entityManager->persist($profile);
            $entityManager->flush();

            $this->addFlash('success', 'Profile successfully updated.');
            return $this->redirectToRoute('profile_index');
        }

        $data = [
            'seotag' => 'Edit Profile',
            'firstname' => $profile->getFirstname(),
            'lastname' => $profile->getLastname(),
            'email' => $profile->getEmail(),
            'cellphone' => $profile->getCellphone(),
            'dob' => $profile->getDob()->format('Y-m-d'),
            'city' => $profile->getCity(),
            'region' => $profile->getRegionStateProvince(),
            'country' => $profile->getCountry(),
        ];

        if ($request->query->has('message')) {
            $data['message'] = 'Profile successfully updated.';
        }

        return $this->render('profile/index.html.twig', $data);
    }
}
