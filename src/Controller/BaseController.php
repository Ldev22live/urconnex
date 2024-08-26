<?php

namespace App\Controller;

use App\Entity\Users;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\RequestStack;
use Cerebro\Cerebro;

class BaseController extends AbstractController
{
    protected $session;
    protected $registry;
    protected $twig;
    protected $path;
    protected $em;
    protected $uid;
    protected $device;
    public function __construct(RequestStack $requestStack, ManagerRegistry $registry, TwigEnvironment $twig)
    {
        $this->session = $requestStack->getSession();
        $this->registry = $registry;
        $this->twig = $twig;

        if (!$this->session->has('uid')) {
            header('Location: https://urconnex.com/home/session');
            //return new RedirectResponse($url);
            die();
            throw new \Exception('Session has expired');

        }
        $this->uid = $this->session->get('uid');
        $this->initialize();
    }

    protected function initialize()
    {
        try{
            //var_dump($this->uid);
            //die();
            $cerebro = new Cerebro();
            $this->device = $cerebro->findDeviceProperties();
            $this->em = $this->registry->getManager();
            $user = $this->em->getRepository(Users::class)->findOneBy(['id' => $this->uid]);
            if (!$user) {
                return $this->redirectToRoute('app_home_session');
            }

            if (!$this->session->has('uid')) {
                return $this->redirectToRoute('app_home_session');
            }
            $this->twig->addGlobal('user', $user);
            $this->twig->addGlobal('uid', $this->uid);

            if($user->getAvatar()){
                $this->twig->addGlobal('avatar', $user->getAvatar());
                $this->twig->addGlobal('fullname', $user->getFirstname().' '.$user->getLastname());
            }else{
                return $this->redirectToRoute('app_home_sessionExpired');
            }

            $this->path = "/var/www/html/urconnex/public";
        }catch (\Exception $e){
            print $e->getMessage();
        }
    }
}
