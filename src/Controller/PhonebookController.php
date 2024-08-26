<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Phonebook;
use App\Entity\Users;
use DateTime;
use App\Entity\Messagelog;
use App\Entity\Carriers;


class PhonebookController extends BaseController
{
    /**
     * @Route("/phonebook", name="app_phonebook")
     * @param $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $address = $this->em->getRepository(Phonebook::class)->findBy(['uid' => $this->uid]);

        return $this->render('phonebook/index.html.twig', [
            'contacts' => $address,
            'seotag' => 'Phonebook Book',
            'success' => (bool)$request->query->get('success'),
        ]);
    }

    /**
     * @Route("/phonebook/save", name="save_phonebook_contact", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function save(Request $request)
    {
        if ($request->isMethod('POST')) {
            $addressbook = new Phonebook();
            $addressbook->setUid($this->uid);
            $addressbook->setFirstname($request->request->get('firstname'));
            $addressbook->setLastname($request->request->get('lastname'));
            $addressbook->setEmail($request->request->get('email'));
            $addressbook->setCellphone($request->request->get('cellphone'));
            $dob = DateTime::createFromFormat('d/m/Y', $request->request->get('dob'));

            if ($dob !== false) {
                $addressbook->setDob($dob);
            } else {
                $this->addFlash('error', 'Invalid date format. Please enter a valid date in the format YYYY-MM-DD.');
                return $this->redirectToRoute('phonebook_add');
            }
            $addressbook->setGender($request->request->get('gender'));
            $addressbook->setRsp($request->request->get('region'));
            $addressbook->setCity($request->request->get('city'));
            $addressbook->setCountry($request->request->get('country'));
            $addressbook->setZip($request->request->get('zip'));
            $addressbook->setCarrier($request->request->get('carrier'));
            $addressbook->setCreated(new DateTime());
            $addressbook->setUpdated(new DateTime());

            $user = $this->em->getRepository(Users::class)->findOneByEmail($request->request->get('email'));
            if ($user) {
                $addressbook->setMemberid($user->getId());
            } else {
                $addressbook->setMemberid(-1);
            }

            $entityManager = $this->em;
            $entityManager->persist($addressbook);
            $entityManager->flush();

            $this->addFlash('success', 'Contact saved successfully.');
            return $this->redirectToRoute('app_phonebook');
        }

        return $this->render('phonebook/save.html.twig');
    }

    /**
     * @Route("/phonebook/add", name="phonebook_add")
     */
    public function add(Request $request): Response
    {
        $seotag = "Address Book";
        $carriers = $this->em->getRepository(Carriers::class)->findAll();

        $success = $request->query->get('success', false);

        return $this->render('phonebook/add.html.twig', [
            'seotag' => $seotag,
            'carriers' => $carriers,
            'success' => $success,
        ]);
    }

    /**
     * @Route("/phonebook/sendmms/{sid}", name="send_mms")
     */
    public function sendmms(Request $request, int $sid): Response
    {
        $entityManager = $this->em;

        if ($request->isMethod('GET')) {
            //$sid = $request->query->get('sid');
            $media = $this->em->getRepository(Media::class)->findByFiletype('video');
            $phonebook = $this->em->getRepository(Phonebook::class)->findOneById($sid);
            return $this->render('phonebook/mms.html.twig',
                [
                    'sid' => $sid,
                    'firstname' => $phonebook->getFirstname(),
                    'lastname' => $phonebook->getLastname(),
                    'address' => $phonebook->getCarrier(),
                    'number' => $phonebook->getCellphone(),
                    'mediafiles' => $media,
                ]);
        }

        $message = $request->request->get('message');
        $address = $request->request->get('address');
        $number = $request->request->get('number');
        $attached = $request->request->get('media');
        $sid = $request->request->get('sid');

        $carrierRepository = $entityManager->getRepository(Carriers::class);
        $carrier = $carrierRepository->findOneBy(['carrierUrl' => $address]);

        $select = $carrier->getCarrierMms();
        $fullmsg = $attached.'-sent-from-'.$number.'@'.$select;
        $messagelog = new Messagelog();
        $messagelog->setUid($this->uid);
        $messagelog->setIdto($sid);
        $messagelog->setMessage($fullmsg);
        $messagelog->setCreated(new \DateTime());

        $entityManager->persist($messagelog);
        $entityManager->flush();

        $from = 'noreply@urconnex.com';
        $to = $number . '@' . $select;
        $subject = 'Urconnex Message:\n\n'.$message;
        $headers = 'From:' . $from;
        mail($to, $subject, $attached, $headers);

        return $this->redirectToRoute('app_phonebook', ['success' => 'yes']);
    }

    /**
     * @Route("/phonebook/sendsms/{sid}", name="phonebook_sendsms")
     */
    public function sendsms(Request $request, int $sid): Response
    {
        if ($request->isMethod('GET')) {
            $phonebook = $this->em->getRepository(Phonebook::class)->findOneById($sid);
            return $this->render('phonebook/sms.html.twig',
                [
                    'sid' => $sid,
                    'firstname' => $phonebook->getFirstname(),
                    'lastname' => $phonebook->getLastname(),
                    'address' => $phonebook->getCellphone().'@'.$phonebook->getCarrier(),
                ]);
        }

        $entityManager = $this->em;
        $message = $request->request->get('message');
        $address = $request->request->get('address');
        $sid = $request->request->get('sid');

        $messageLog = new Messagelog();
        $messageLog->setUid($this->uid);
        $messageLog->setIdto($sid);
        $messageLog->setMessage($message);
        $messageLog->setCreated(new \DateTime());

        $entityManager->persist($messageLog);
        $entityManager->flush();

        $from = 'noreply@urconnex.com';
        $to = $address;
        $subject = 'Urconnex Message';
        $headers = 'From:' . $from;
        mail($to, $subject, $message, $headers);

        return $this->redirectToRoute('app_phonebook', ['success' => 'yes']);
    }

    public function loadMedia(string $filename): Response
    {
        $base = "/var/www/html/urconnex/public/";
        //step 1 fetch the file from the database
        $file = Media::findOneBy(['filename' => $filename]);
        $media = Media::findByFiletype();
        $device = $this->wurfl->findDeviceProperties();
        $response = new Response();

        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($this->renderView('phonebook/load_media.html.twig', [
            'media' => $media,
            'seotag' => "Watching " . $filename,
            'uid' => $this->uid,
            'width' => $device['reswidth'],
            'height' => $device['resheight']
        ]));

        if ($device['reswidth'] < 500 || $device['resheight'] < 500) {
            $gp = MediaManager::convertTo3gp($base . $file->filepath, $device['reswidth'], $device['resheight']);
            var_dump($gp);
            $streamlink = "http://urconnex.com/media/files/" . $device['reswidth'] . $filename . ".3gp";
            $stream = "http://urconnex.com/media/files/" . $filename . ".mp4";
            $response->headers->set('Content-Type', 'video/mp4');
            $response->setContent(file_get_contents($base . $file->filepath));
        } else {
            //build versions for desktop player
            $webm = MediaManager::convertToWebm($base . $file->filepath, $device['reswidth'], $device['resheight']);
            $response->headers->set('Content-Type', 'video/webm');
            $response->setContent(file_get_contents($webm));
            $response->setStatusCode(Response::HTTP_OK);
        }

        return $response;
    }
}
