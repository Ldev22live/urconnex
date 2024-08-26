<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Media;
use DateTime;

class VideosController extends BaseController
{
    /**
     * @Route("/videos", name="app_videos_index")
     */
    public function index(Request $request): Response
    {
        // Handle the form submission
        if ($request->getMethod() === 'POST') {
            try {
                //upload media file to the server
                if ($request->files->has('video')) {
                    $name = "";
                    if ($request->request->has('newname')) {
                        $name = $request->request->get('newname');
                    } else {
                        $name = $request->files->get('video')->getClientOriginalName();
                    }

                    //var_dump($name);

                    //convert media file to defacto mp4 format
                    $newfile = MediaManager::convertToMp4($request->files->get('video')->getPathname(), $this->path, $name);
                    //var_dump($newfile);
                    //create thumbnail for media file
                    $thumbnail = MediaManager::createThumbnail($newfile, $this->path);
                    //die();
                    $check = $this->em->getRepository(Media::class)->findOneBy(['filename' => basename($newfile)]);
                    if (!$check) {
                        $media = new Media();
                        $media->setUid($this->uid);
                        $media->setFilename(basename($newfile));
                        $media->setFilepath($newfile);
                        $media->setThumbnail($thumbnail);
                        $media->setDateUploaded(new \DateTime());
                        $media->setFiletype('video');
                        $media->setSharedId('');
                        $media->setFileUpdated(new \DateTime());
                        $media->setViews(0);
                        $media->setLikes(0);
                        $entityManager = $this->em;
                        $entityManager->persist($media);
                        $entityManager->flush();
                        $this->addFlash('success', 'Video uploaded successfully!');
                    }
                }
            } catch (\Exception $ex) {
                $this->addFlash('error', $ex->getMessage());
            }

            return $this->redirectToRoute('app_videos');
        }
        // Get videos from the database
        $media = $this->em->getRepository(Media::class)->findByFiletype('video');

        return $this->render('videos/index.html.twig', [
            'mediafiles' => $media,
        ]);
    }

    /**
     * @Route("/saveyoutube", name="app_videos_save_youtube", methods={"POST"})
     */
    public function saveYoutube(Request $request): Response
    {
        try {
            $filename = str_replace(" ", "-", $request->request->get('youname'));
            $url = $request->request->get('url');
            $destination = $this->path."/media/files/".$filename.".mp4";
            $result = $this->getFromYoutube($destination, $url);
            $dateUploaded = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));

            if (file_exists($result)) {
                $thumbnail = $this->createThumbnail($result, $this->path);
                $media = new Media();
                $media->setUid($this->uid);
                $media->setFilename(basename($filename));
                $media->setFilepath("/media/files/".$filename.".mp4");
                $media->setThumbnail($thumbnail);
                $media->setDateUploaded($dateUploaded);
                $media->setFiletype("video");
                $media->setSharedId("");
                $media->setFileUpdated($dateUploaded);
                $media->setViews(0);
                $media->setLikes(0);
                $entityManager = $this->em;
                $entityManager->persist($media);
                $entityManager->flush();
                $this->addFlash('success', 'Video saved successfully.');
            } else {
                $this->addFlash('error', 'Video not found or could not be saved.');
            }
        } catch (Exception $ex) {
            $this->addFlash('error', $ex->getMessage());
        } catch (PDOException $ex) {
            $this->addFlash('error', $ex->getMessage());
        }

        return $this->redirectToRoute('app_videos_index');
    }

    private function getFromYoutube($destination, $url){
        $cmd = "/usr/local/bin/yt-dlp -o '".$destination."' -f mp4 ".$url;
        shell_exec($cmd);
        //var_dump(file_exists($destination));

        if(file_exists($destination)){
            return $destination;
        }else{
            return "Blocked";
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
}
