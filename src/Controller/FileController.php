<?php

namespace App\Controller;

use App\Entity\Files;
use App\Form\FilesType;
use App\Form\FileType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


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
     * @SWG\Tag(name="History")
     * * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postFile(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $history = new Files();
        $form = $this->createForm(FilesType::class, $history);
        $form->submit($data);
        if (!$form->isValid()) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                    'errors' => (string)$form->getErrors(true, false),
                    'form' => $form
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }


        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_CREATED
        );
    }
}
