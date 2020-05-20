<?php

namespace App\Controller;

use App\Entity\ProgressHistory;
use App\Form\ProgressHistoryType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * @package App\Controller
 * @Route("/secured/ProgressHistory",name="progressHistory_")
 */
class ProgressHistoryController extends FOSRestController
{
    /**
     * Create new ProgressHistory.
     * @Rest\Post("/ProgressHistoryCreate")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="post new ProgressHistory"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="ProgressHistory")
     * * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postHistory(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $progressHistory = new ProgressHistory();
        $form = $this->createForm(ProgressHistoryType::class, $progressHistory);
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
