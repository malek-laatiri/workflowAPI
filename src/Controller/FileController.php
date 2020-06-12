<?php

namespace App\Controller;

use App\Entity\Files;
use App\Form\FilesType;
use App\Form\FileType;
use App\Repository\CommentsRepository;
use App\Repository\FilesRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;


/**
 * Class FileController
 * @package App\Controller
 * @Route("/secured/files",name="files_")
 */
class FileController extends FOSRestController
{
    /**
     * Upload new file.
     * @Rest\Post("/uploadFile")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="File uploaded"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Files")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postFile(Request $request,CommentsRepository $commentsRepository)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $file = new Files();
        $form = $this->createForm(FilesType::class, $file);
        $file->setImageType($request->request->get('imageType'));
        $file->setImageSize($request->request->get('imageSize'));
        $file->setImageFile($request->files->get('upload')['imageFile']);
        $file->setComments($commentsRepository->find($request->request->get('comment')));


        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($file);
        $entityManager->flush();

        return new JsonResponse(
            [
                'status' => 'ok'],
            JsonResponse::HTTP_CREATED
        );
    }
    /**
     * Upload new file.
     * @Rest\Get("/download/{fileId}")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @SWG\Response(
     *     response=200,
     *     description="File uploaded"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Files")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function DownloadFile(DownloadHandler $downloadHandler,int $fileId,FilesRepository $filesRepository)
    {
        return $downloadHandler->downloadObject($filesRepository->findOneBy(['id'=>$fileId]), $fileField = 'imageFile', $objectClass = null, $fileName = null, $forceDownload = false);


    }
}
