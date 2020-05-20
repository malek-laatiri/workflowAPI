<?php

namespace App\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;

use App\Entity\History;
use App\Form\HistoryType;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class HistoryController
 * @package App\Controller
 * @Route("/secured/history",name="history_")
 */
class HistoryController extends FOSRestController
{
    /**
     * Create new History.
     * @Rest\Post("/historyCreate")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="post new history"
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
    public function postHistory(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $history = new History();
        $form = $this->createForm(HistoryType::class, $history);
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
