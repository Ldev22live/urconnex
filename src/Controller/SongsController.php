<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Media;

class SongsController extends BaseController
{
    /**
     * @Route("/songs", name="app_songs")
     */
    public function index(): Response
    {
        $media = $this->em->getRepository(Media::class)->findByFiletype('audio');
        return $this->render('songs/index.html.twig', [
            'mediafiles' => $media,
        ]);
    }

    /**
     * @Route("/songs/save", name="songs_save", methods={"POST"})
     */
    public function save(Request $request): Response
    {
        //upload media file to the server
        try{
            if($request->files->count() > 0){
                $name = "";
                if($request->request->has('newname')){
                    $name = $request->request->get('newname');
                }else{
                    $name = $request->files->get('video')->getClientOriginalName();
                }

                $destination = $this->getParameter('kernel.project_dir').'/public/media/uploads/'.$name;
                $request->files->get('video')->move($this->getParameter('kernel.project_dir').'/public/media/uploads/', $name);

                //convert media file to defacto mp4 format
                $newfile = $this->convertToMp3($name, $this->getParameter('kernel.project_dir'));

                //create thumbnail for media file
                $thumbnail = "/media/images/audio.png";

                $media = new Media();
                $media->setUid($this->getUser());
                $media->setFilename(basename($newfile));
                $media->setFilepath($newfile);
                $media->setThumbnail($thumbnail);
                $media->setDateUploaded(new \DateTime());
                $media->setFileType('audio');
                $media->setSharedId('');
                $media->setFileUpdated(new \DateTime());
                $media->setViews(0);
                $media->setLikes(0);

                $entityManager = $this->em->getManager();
                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('audio_index', ['message' => 'success']);
            }
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
        } catch(PDOException $ex){
            var_dump($ex->getMessage());
        }
    }

    private function convertToMp3($filename, $path)
    {
        $newname = str_replace(" ", "-", $filename);
        if (strstr($filename, "mp3")) {
            copy($path."/media/uploads/".$filename, $path."/media/files/".$newname);
            if (file_exists($path."/media/files/".$newname)) {
                return "/media/files/".$newname;
            }
        } else {
            $nameOnly = preg_replace('/\\.[^.\\s]{3,4}$/', '', $newname);
            if (file_exists($path."/media/files/".$nameOnly.".mp4")) {
                return "/media/files/".$nameOnly.".mp3";
            } else {
                $cmd = "/usr/bin/ffmpeg -i /var/www/html/urconnex/public/".$path." -acodec libmp3lame /var/www/html/urconnex/public".$nameOnly.".mp3";
                shell_exec($cmd);
                return $nameOnly.".mp3";
            }
        }
    }

}
