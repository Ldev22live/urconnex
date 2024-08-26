<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Cerebro\Cerebro;
use App\Entity\Media;

class PlayController extends AbstractController
{
    private $device;
    private $filename;
    private $file;
    protected $session;
    protected $registry;
    protected $em;
    protected $path;

    public function __construct(SessionInterface $session, ManagerRegistry $registry)
    {
        $this->session = $session;
        $this->registry = $registry;
        $this->em = $registry->getManager();
        $this->path = "/var/www/html/urconnex/public";
        $cerebro = new Cerebro();
        $this->device = $cerebro->findDeviceProperties();
    }
    /**
     * @Route("/play/index/{filename}", name="app_play_index")
     */
    public function index(Request $request, string $filename): Response
    {
        // Fetch the media from the database
        $media = $this->em->getRepository(Media::class)->findByViews(1);
        
        if ($this->device['reswidth'] < 500 || $this->device['resheight'] < 500) {
            return $this->render('play/index.html.twig', [
                'mediafiles' => $media,
                'filename' => $filename,
            ]);
        }else{
            return $this->render('play/index.html.twig', [
                'mediafiles' => $media,
                'filename' => $filename,
                'desktop' => 1
            ]);
        }
        // Render the template with the media passed as a variable
        
    }

    /**
     * @Route("/play/process", name="app_play_process")
     */
    public function process(Request $request, $filename): JsonResponse
    {
        //$this->em = $registry->getManager();
        //$filename = $request->query->get('filename');
        $file = $this->em->getRepository(Media::class)->findOneBy(['filename' => $filename]);
        //$gp = MediaManager::convertTo3gp($this->path, $this->device['reswidth'], $this->device['resheight']);
        //echo $this->path;
        //var_dump(get_class_methods($file));
        
        
        if (!$file) {
            return new JsonResponse(['error' => 'File not found'], 404);
        }
        
        if ($this->device['reswidth'] < 500 || $this->device['resheight'] < 500) {
            $gp = "https://urconnex.com/media/files/".$this->getMobileBuild($this->path.$file->getFilepath(), $file->getFilename());
            $streamlink = "https://urconnex.com/media/files/".$this->device['reswidth'].$filename.".3gp";
            $stream = "https://urconnex.com/".$file->getFilepath();
            $data = [
                'stream' => $stream,
                'mp4' => $stream,
                'gp' => $gp,
            ];
            
        }else{
            $f = $this->getDesktopBuild($this->path.$file->getFilepath(), $file->getFilename());
            $webm = "https://urconnex.com/media/files/".$file->getFilename().".webm";
            $poster = $file->getThumbnail();
            $data = [
                'webm' => $webm,
                'poster' => $poster,
                'desktop' => 1
            ];
        }
        $seotag = 'Watching ' . $file->getFilename();
        
        // Return the links
        

        return new JsonResponse($data);
    }

    private function getMobileBuild($newpath, $filename) {
        $desktop = 0;
        $newfile = $this->path."/media/files/".$this->device['reswidth'].$filename.".3gp";
        if($this->device['reswidth'] > 1000){
            $cmd = "/usr/bin/ffmpeg -i ".$newpath." -r 20 -vb 400k -acodec aac -strict experimental -ac 1 -ar 8000 -ab 24k -s 1408x1152 ".$newfile;
        }
        if($this->device['reswidth'] < 800 && $this->device['reswidth'] > 600){
            $cmd = "/usr/bin/ffmpeg -i ".$newpath." -r 20 -vb 400k -acodec aac -strict experimental -ac 1 -ar 8000 -ab 24k -s 704x576 ".$newfile;
        }
        if($this->device['reswidth'] < 600 && $this->device['reswidth'] > 300){
            $cmd = "/usr/bin/ffmpeg -i ".$newpath." -r 20 -vb 400k -acodec aac -strict experimental -ac 1 -ar 8000 -ab 24k -s 352x288 ".$newfile;
        }
        if($this->device['reswidth'] < 250 && $this->device['reswidth'] > 150){
            $cmd = "/usr/bin/ffmpeg -i ".$newpath." -r 20 -vb 400k -acodec aac -strict experimental -ac 1 -ar 8000 -ab 24k -s 176x144 ".$newfile;
        }
        if($this->device['reswidth'] < 130 && $this->device['reswidth'] > 90){
            $cmd = "/usr/bin/ffmpeg -i ".$newpath." -r 20 -vb 400k -acodec aac -strict experimental -ac 1 -ar 8000 -ab 24k -s 128x96 ".$newfile;
        }
        shell_exec($cmd);
        if(file_exists($newfile)){
            return $this->device['reswidth'].$filename.".3gp";
        }else{
            return "101";
        }
    }

    private function getDesktopBuild($newpath, $filename){
        //build versions for desktop player
        $newfile = $this->path."/media/files/".$filename.".webm";
        $cmd = "/usr/bin/ffmpeg -i ".$newpath." -c:v libvpx-vp9 -crf 30 -b:v 0 -b:a 128k -c:a libopus -s ".$this->device['reswidth']."x".$this->device['resheight']." ".$newfile;
        shell_exec($cmd);
        if(file_exists($newfile)){
            return $filename.".webm";
        }else{
            return "102";
        }
    }
}
