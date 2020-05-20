<?php

namespace App\Controller;

use App\Entity\Backlog;
use App\Form\BacklogType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class BacklogController
 * @package App\Controller
 * @Route("/secured/Backlog",name="backlog_")
 */
class BacklogController extends FOSRestController
{
    /**
     * Lists  Backlogs By project
     * @Rest\Get("/BacklogList/{projectId}",name="BacklogList")
     * @Rest\View()
     * @return JsonResponse|Response
     * @SWG\Response(
     *     response=200,
     *     description="Lists all Backlogs",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Backlog")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllBacklogs(Request $request, int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Backlog::class);
        $allBacklog = $repository->findBy(["project" => $projectId]);
        $serializer = SerializerBuilder::create()->build();

        $data = $serializer->serialize($allBacklog, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * Create Backlog.
     * @Rest\Post("/backlogCreate")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @SWG\Response(
     *     response=200,
     *     description="post new backlog",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Backlog")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postBacklog(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $form = $this->createForm(BacklogType::class, new Backlog());

        $form->submit($data);
        if (!$form->isValid()) {

            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                    'errors' => (string)$form->getErrors(true, false)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_CREATED);
    }

    /**
     * update a backlog
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/backlogUpdate/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update a backlog"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Backlog")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putBacklog(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Backlog::class);

        $backlog = $repository->find($id);
        if (empty($backlog)) {
            return new JsonResponse(['status' => 'Expedition not Found',], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(BacklogType::class, $backlog);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }

    /**
     * get one backlog
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/backlogShow/{id}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="get one backlog"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Backlog")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOneBacklog(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(Backlog::class);

        $backlog = $repository->find($id);
        if (empty($backlog)) {
            return $this->handleView($this->view(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND));
        }
        $data = SerializerBuilder::create()->build()->serialize($backlog, 'json');

        $response = new Response($data);
        return $response;
    }

    /**
     * delete a backlog
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/BacklogDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="delete a backlog"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Backlog")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteBacklog(int $id)
    {
        $Priority = $this->getDoctrine()->getRepository(Backlog::class)->find($id);
        if (empty($Priority)) {
            return new JsonResponse(['status' => 'Expedition not Found',], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($Priority);
        $em->flush();

        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);

    }
}
