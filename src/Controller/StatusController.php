<?php

namespace App\Controller;

use App\Entity\Status;
use App\Form\StatusType;
use App\Service\EmailService;
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
use Symfony\Component\Validator\Constraints\Json;


/**
 * Class StatusController
 * @package App\Controller
 * @Route("/secured/status",name="status_")
 */
class StatusController extends FOSRestController
{
    /**
     * Lists all Status.
     * @Rest\Get("/StatusList/{projectId}")
     * @Rest\View()
     * @return JsonResponse|Response
     * @SWG\Response(
     *     response=200,
     *     description="get all status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllStatus(int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $allPriority = $repository->findBy(["project" => $projectId]);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allPriority, 'json');
        $response = new Response($data);
        return $response;
    }


    /**
     * Lists all Status.
     * @Rest\Get("/StatusListThree/{projectId}")
     * @Rest\View()
     * @param int $projectId
     * @return JsonResponse|Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @SWG\Response(
     *     response=200,
     *     description="get all status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllStatusThree(int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $allPriority = $repository->findBy(["project" => $projectId]);
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
     * Lists all Status.
     * @Rest\Get("/StatusListPrime/{projectId}")
     * @Rest\View()
     * @param int $projectId
     * @return JsonResponse|Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @SWG\Response(
     *     response=200,
     *     description="get all status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllStatusPrime(int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $allPriority = $repository->findBy(["project" => $projectId]);

        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allPriority, null, [AbstractNormalizer::ATTRIBUTES => ['id','name',
            'userStories'=>['id','subject','content'
                ,'priority'=>['id','name'],
                'status'=>['id','name'],
                'estimatedTime','dueDate','tags'
                ,'comments'=>['id','content','writtenAt','writtenBy'=>['id','username'],'files']
                ,'activity'=>['id','name'],
                'histories'=>['id','modifiedAt','status'=>['name']],
                'asignedTo'=>['id','username','email','roles'],
                'isComfirmed','isVerified','label'=>['name','color'],'dueDate',
                'progress'

            ],
            'role'],
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
     * Lists all Status.
     * @Rest\Get("/StatusListSecond/{projectId}")
     * @Rest\View()
     * @param int $projectId
     * @return JsonResponse|Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @SWG\Response(
     *     response=200,
     *     description="get all status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllStatusSecond(int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $allPriority = $repository->findBy(["project" => $projectId]);

        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allPriority, null, [AbstractNormalizer::ATTRIBUTES => ['id','name','role'],
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
     * Create Status.
     * @Rest\Post("/statusCreate")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @SWG\Response(
     *     response=200,
     *     description="post new status",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postStatus(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $form = $this->createForm(StatusType::class, new Status());

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

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * delete a status
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/StatusDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="delete status",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteStatus(int $id)
    {
        $status = $this->getDoctrine()->getRepository(Status::class)->find($id);
        if (empty($status)) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($status);
        $em->flush();

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_OK
        );
    }


    /**
     * update a status
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/statusUpdate/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update a status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putStatus(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);

        $status = $repository->find($id);
        if (empty($status)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(StatusType::class, $status);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'object' => $form->getData()], JsonResponse::HTTP_OK);
    }

    /**
     * Lists all Status.
     * @Rest\Get("/RoleByStatus/{statusId}")
     * @Rest\View()
     * @return JsonResponse|Response
     * @SWG\Response(
     *     response=200,
     *     description="get all status"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Status")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getRoleByStatus(int $statusId, EmailService $emailService)
    {
        $repository = $this->getDoctrine()->getRepository(Status::class);
        $allPriority = $repository->findOneBy(["id" => $statusId]);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allPriority, 'json');
        foreach (json_decode($data)->project->_team as &$value) {

            if (in_array(json_decode($data)->role, $value->roles)) {
                $emailService->sendEmail($value->email,$this->render('email.html.twig', ['title' => "New Notification", 'content' => " you have new notification "]));

            }
        }
        return new JsonResponse(['status' => 'Done'], JsonResponse::HTTP_OK);
    }
}
