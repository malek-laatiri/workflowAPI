<?php

namespace App\Controller;

use App\Entity\Priority;
use App\Form\PriorityType;
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
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class PriorityController
 * @package App\Controller
 * @Route("/secured/priority",name="priority_")
 */
class PriorityController extends FOSRestController
{

    /**
     * Lists all Priority.
     * @Rest\Get("/priorityList",name="priorityList")
     * @Rest\View()
     * @return JsonResponse|Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @SWG\Response(
     *     response=200,
     *     description="Lists all Priority"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Priority")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllPriority()
    {
        $repository = $this->getDoctrine()->getRepository(Priority::class);
        $allPriority = $repository->findall();
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allPriority, null, [AbstractNormalizer::ATTRIBUTES => ['id','name'],
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }]);

        return new JsonResponse(
            [
                'status' => 'ok',
                'data'=>$data
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * Create Priority.
     * @Rest\Post("/priorityCreate")
     * @param Request $request
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="add new priority",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     *
     * @SWG\Tag(name="Priority")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postPriority(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $priority = new Priority();
        $form = $this->createForm(PriorityType::class, $priority);

        $form->submit($data);
        if (!$form->isValid()) {

            return new JsonResponse(
                [
                    'status' => 'no',
                    'errors' => (string)$form->getErrors(true, false)
                ],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();

        return new JsonResponse(['status' => 'ok', 'ID' => json_encode($form->getData()->getId(), true), 'name' => json_encode($form->getData()->getName(), true)], JsonResponse::HTTP_CREATED);
    }

    /**
     * update a priority
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/priorityUpdate/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update priority",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * )

     * @SWG\Tag(name="Priority")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putPriority(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Priority::class);

        $priority = $repository->find($id);
        if (empty($priority)) {
            return new JsonResponse(['status' => 'Expedition not Found',], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(PriorityType::class, $priority);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'object' => $form->getData()], JsonResponse::HTTP_OK);
    }

    /**
     * get one priority
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/priorityShow/{id}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="get one priority"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Priority")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOnePriority(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(Priority::class);
        $priority = $repository->find($id);
        if (empty($priority)) {
            return $this->handleView($this->view(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND));
        }
        $data = SerializerBuilder::create()->build()->serialize($priority, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * delete a priority
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/priorityDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="delete a priority",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(
     *     name="id",
     *     in="query",
     *     type="integer",
     *     description="priority id"
     * )
     * @SWG\Tag(name="Priority")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deletePriority(int $id)
    {
        $Priority = $this->getDoctrine()->getRepository(Priority::class)->find($id);
        if (empty($Priority)) {
            return new JsonResponse(['status' => 'Expedition not Found',], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($Priority);
        $em->flush();

        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }
}
