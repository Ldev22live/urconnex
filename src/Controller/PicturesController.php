<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PicturesController extends BaseController
{
    /**
     * @Route("/pictures", name="app_pictures")
     */
    public function index(): Response
    {
        $media = $this->em->getRepository(Media::class)->findByFiletype('image');
        return $this->render('songs/index.html.twig', [
            'mediafiles' => $media,
        ]);
    }

    /**
     * @Route("/pictures/save", name="pictures_save", methods={"POST"})
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
                $newfile = $this->convertToPng($name, $this->getParameter('kernel.project_dir'));

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

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('audio_index', ['message' => 'success']);
            }else{
                return $this->redirectToRoute('audio_index', ['message' => 'Error occurred']);
            }
        } catch (Exception $ex) {
            var_dump($ex->getMessage());
            return $this->redirectToRoute('audio_index', ['message' => 'Error occurred']);
        } catch(PDOException $ex){
            var_dump($ex->getMessage());
            return $this->redirectToRoute('audio_index', ['message' => 'DB error occured']);
        }
    }
    private function convertToPng($filename, $path){
        $newname = str_replace(" ", "-", $filename);
        if(strstr($filename, "png")){
            copy($path."/media/uploads/".$filename, $path."/media/files/".$newname);
            if(file_exists($path."/media/files/".$newname)){
                return "/media/files/".$newname;
            }
        }else{
            $nameOnly = preg_replace('/\\.[^.\\s]{3,4}$/', '', $newname);
            if(file_exists($path."/media/files/".$nameOnly.".mp4")){
                return "/media/files/".$nameOnly.".png";
            }else{
                $cmd = "/usr/bin/convert ".$path."/media/uploads/".$filename." -resize 1280x1024\! ".$path."/media/files/".$nameOnly.".png";
                shell_exec($cmd);
                return "/media/files/".$nameOnly.".png";
            }
        }
    }
}
