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
use Twig\Environment as TwigEnvironment;

class InsideplayController extends BaseController
{
    /**
     * @Route("/play/insideplay/{filename}", name="app_insideplay_index")
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
            return $this->render('insideplay/index.html.twig', [
                'mediafiles' => $media,
                'filename' => $filename,
                'desktop' => 1
            ]);
        }
        // Render the template with the media passed as a variable

    }

    /**
     * @Route("/insideplay/convert/{filename}", name="app_insideplay_convert")
     */
    public function convert(Request $request, string $filename): Response
    {
        if($request->isMethod('GET')){
            $media = $this->em->getRepository(Media::class)->findOneBy(['filename' => $filename]);
            $video = $this->convertToMp3($filename, $this->path);
            //var_dump($video);
            //die();
            $thumbnail = "/media/images/audio.png";
            $check = $this->em->getRepository(Media::class)->findOneBy(['filename' => basename($video)]);
            if(!$check){
                $media = new Media();
                $media->setUid($this->uid);
                $media->setFilename(basename($video));
                $media->setFilepath($video);
                $media->setThumbnail($thumbnail);
                $media->setDateUploaded(new \DateTime());
                $media->setFiletype("audio");
                $media->setSharedId("");
                $media->setFileUpdated(new \DateTime());
                $media->setViews(0);
                $media->setLikes(0);

                $entityManager = $this->em;
                $entityManager->persist($media);
                $entityManager->flush();

                $this->addFlash('success', 'Media added successfully!');
                return $this->redirectToRoute('app_songs');
            }else{
                $this->addFlash('error', 'Media already exists!');
                return $this->redirectToRoute('app_songs');
            }
        }
    }

    /**
     * @Route("/insideplay/process", name="app_insideplay_process")
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

    /**
     * @Route("/insideplay/capture/{{filename}}", name="app_insideplay_capture")
     */
    public function capture(Request $request, string $filename): Response{
        if($request->isMethod('GET')){

            return $this->render('insideplay/capture.html.twig', [
                'name' => $filename,
            ]);
        }

        $video = $request->request->get('name');
        $time = $request->request->get('capture');
        //var_dump($video);

        $thumbnail = $this->makeSnapshot($video, $this->path, $time);
        $check = $this->em->getRepository(Media::class)->findOneBy(['filename' => basename($thumbnail)]);
        if(!$check){
            $media = new Media();
            $media->setUid($this->uid);
            $media->setFilename(basename($video));
            $media->setFilepath($thumbnail);
            $media->setThumbnail($thumbnail);
            $media->setDateUploaded(new \DateTime());
            $media->setFiletype("image");
            $media->setSharedId("");
            $media->setFileUpdated(new \DateTime());
            $media->setViews(0);
            $media->setLikes(0);

            $entityManager = $this->em->getManager();
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Media added successfully!');
            return $this->redirectToRoute('pictures_index');
        }else{
            $this->addFlash('error', 'Media already exists!');
            return $this->redirectToRoute('pictures_index');
        }
    }

    /**
     * @Route("/insideplay/trim/{{filename}}", name="app_insideplay_trim")
     */
    public function trim(Request $request, string $filename): Response{
        if($request->isMethod('GET')){

            return $this->render('insideplay/trim.html.twig', [
                'name' => $filename,
            ]);
        }

        $video = $request->request->get('name');
        $time = $request->request->get('capture');
        $start = $request->request->get('start');
        $end = $request->request->get('end');
        $truename = str_replace("/media/files/", "", $video);
        //var_dump($video);
        $trimmedVideo = $this->trimVideo($truename, $this->path, $start, $end);
        $thumbnail = $this->makeSnapshot($trimmedVideo, $this->path, $time);
        $check = $this->em->getRepository(Media::class)->findOneBy(['filename' => basename($thumbnail)]);
        if(!$check){
            $media = new Media();
            $media->setUid($this->uid);
            $media->setFilename(basename($video));
            $media->setFilepath($trimmedVideo);
            $media->setThumbnail($thumbnail);
            $media->setDateUploaded(new \DateTime());
            $media->setFiletype("clip");
            $media->setSharedId("");
            $media->setFileUpdated(new \DateTime());
            $media->setViews(0);
            $media->setLikes(0);

            $entityManager = $this->em->getManager();
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Media added successfully!');
            return $this->redirectToRoute('app_videos_index');
        }else{
            $this->addFlash('error', 'Media already exists!');
            return $this->redirectToRoute('app_videos_index');
        }
    }

    /**
     * @Route("/insideplay/makevideo/", name="app_insideplay_makevideo")
     */
    public function makevideo(Request $request): Response{
        if($request->isMethod('GET')){
            $media = $this->em->getRepository(Media::class)->findByFiletype('audio');
            $images = $this->em->getRepository(Media::class)->findByFiletype('image');

            return $this->render('insideplay/makevideo.html.twig', [
                'audiofiles' => $media,
                'imagesfiles' => $images,
                'build' => 1
            ]);
        }

        $images = $request->request->get('images');
        $song = $request->request->get('song');
        $frame = 30;
        $imgArr = $this->prepImages($this->uid, $images);
        //var_dump($imgArr);
        $video = $this->createVideo($this->uid, $song, $frame, date('Y-H:i:s'));

        $thumbnail = $this->createThumbnail($video, $this->path);
        $check = $this->em->getRepository(Media::class)->findOneBy(['filename' => basename($video)]);
        if(!$check){
            $media = new Media();
            $media->setUid($this->uid);
            $media->setFilename(basename($video));
            $media->setFilepath($video);
            $media->setThumbnail($thumbnail);
            $media->setDateUploaded(new \DateTime());
            $media->setFiletype("video");
            $media->setSharedId("");
            $media->setFileUpdated(new \DateTime());
            $media->setViews(0);
            $media->setLikes(0);

            $entityManager = $this->em;
            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Media added successfully!');
            return $this->redirectToRoute('app_videos_index');
        }else{
            $this->addFlash('error', 'Media already exists!');
            return $this->redirectToRoute('app_videos_index');
        }
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

    private function createThumbnail($filename, $path){
        $nameOnly = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($filename));
        //echo basename($filename);
        $cmd = "/usr/bin/ffmpeg -i ".$path."/media/files/".$nameOnly.".mp4 -ss 00:00:10.000 -frames:v 1 -s 512x288 ".$path."/media/thumbnails/".$nameOnly.".png";
        shell_exec($cmd);
        if(file_exists($path."/media/thumbnails/".$nameOnly.".png")){
            return "/media/thumbnails/".$nameOnly.".png";
        }
    }

    private function prepImages($uid, $images){
        $picArr = array();
        $cmd = "mkdir /var/www/html/urconnex/public/media/files/".$uid;
        shell_exec($cmd);
        if(is_array($images)){
            for($i = 0; $i < count($images); $i++){
                $cmd = "/usr/bin/convert /var/www/html/urconnex/public/media/thumbnails/".$images[$i]." -resize 1280x1024! /var/www/html/urconnex/public/media/files/".$uid."/image00".$i.".png";
                shell_exec($cmd);
                array_push($picArr, "/var/www/html/urconnex/public/media/files/".$uid."/image00".$i.".png");
            }
        }

        return $picArr;
    }

    private function createVideo($uid, $song, $frame, $i){
        $cmd = "/usr/bin/ffmpeg  -framerate 1/5 -i /var/www/html/urconnex/public/media/files/".$uid."/image%03d.png -i /var/www/html/urconnex/public/media/files/".$song." -vcodec libx264 -crf 25 -acodec copy -vf fps=25 -pix_fmt yuv420p /var/www/html/urconnex/public/media/files/".$uid."made".$i.".mp4";
        shell_exec($cmd);


        if(file_exists("/var/www/html/urconnex/public/media/files/".$uid."made".$i.".mp4")){
            return "/media/files/".$uid."made".$i.".mp4";
        }
    }

    private function convertToMp3($filename, $path){
        if(file_exists($path."/media/files/".$filename.".mp3")){
            return "/media/files/".$filename.".mp3";
        }else{
            $cmd = "/usr/bin/ffmpeg -i /var/www/html/urconnex/public/media/files/".$filename.".mp4 -acodec libmp3lame /var/www/html/urconnex/public/media/files/".$filename.".mp3";
            shell_exec($cmd);
            return "/media/files/".$filename.".mp3";
        }
    }

    private function makeSnapshot($filename, $path, $time){
        $nameOnly = preg_replace('/\\.[^.\\s]{3,4}$/', '', basename($filename));
        //echo basename($filename);
        $cmd = "/usr/bin/ffmpeg -i ".$path." -ss ".$time." -frames:v 1 -s 512x288 /var/www/html/urconnex/public/media/thumbnails/".$nameOnly.".png";
        shell_exec($cmd);
        if(file_exists("/var/www/html/urconnex/public/media/thumbnails/".$nameOnly.".png")){
            return "/media/thumbnails/".$nameOnly.".png";
        }
    }

    private function trimVideo($filename, $path, $start, $duration){
        $newname = $start."clip".basename($filename);

        if(file_exists("/var/www/html/urconnex/public/media/files/".$newname)){
            return "/media/files/".$newname;
        }else{
            $cmd = "/usr/bin/ffmpeg -i ".$path." -ss ".$start." -to ".$duration." -c copy /var/www/html/urconnex/public/media/files/".$newname;
            shell_exec($cmd);
            return "/media/files/".$newname;
        }
    }
}
