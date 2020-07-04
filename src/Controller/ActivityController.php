<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Form\ActivityType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ActivityController
 * @package App\Controller
 * @Route("/secured/activity",name="activity_")
 */
class ActivityController extends AbstractController
{
    /**
     * lists all the activities
     * @return Response
     * @Rest\Get("/activities")
     * @SWG\Response(
     *     response=200,
     *     description="get all activities",
     *     @SWG\Schema(
     *   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * )
     *     )
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
     * @SWG\Tag(name="Activities")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllActivities()
    {
        $repository = $this->getDoctrine()->getRepository(Activity::class);
        $allProjects = $repository->findall();
        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allProjects, null, [AbstractNormalizer::ATTRIBUTES => ['id','name'],
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
     * Create Activity.
     * @Rest\Post("/activityCreate")
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @SWG\Response(
     *     response=200,
     *     description="add new activity",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Activities")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postActivity(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
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

        return new JsonResponse(
            [
                'status' => 'ok',
            ],
            JsonResponse::HTTP_CREATED
        );
    }

    /**
     * update an activity
     * @param Request $request
     * @Rest\View()
     * @Rest\Patch("/activityUpdate/{id}")
     * @return \Symfony\Component\Form\FormInterface|Response
     * @SWG\Response(
     *     response=200,
     *     description="update an activity",
     *
     *
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Activities")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putActivity(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(Activity::class);

        $activity = $repository->find($id);
        if (empty($activity)) {
            return new JsonResponse(
                [
                    'status' => 'Expedition not Found',
                ],
                JsonResponse::HTTP_NOT_FOUND
            );
        }
        $data = json_decode(
            $request->getContent(),
            true
        );
        $form = $this->createForm(ActivityType::class, $activity);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok',], JsonResponse::HTTP_OK);
    }

    /**
     * get one activity
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/activityShow/{id}")
     * @return Response
     * @SWG\Response(
     *     response=200,
     *     description="get one activity",
     *             @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),

     * )
     *     )
     *
     *
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )

     * @SWG\Tag(name="Activities")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOneActivity(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(Activity::class);

        $activity = $repository->find($id);
        if (empty($activity)) {
            return new JsonResponse(['status' => 'Expedition not Found'], Response::HTTP_NOT_FOUND);
        }
        $data = SerializerBuilder::create()->build()->serialize($activity, 'json');

        $response = new Response($data);
        return $response;
    }

    /**
     * delete an activity
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/activityDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="activity deleted",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Activities")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteActivity(int $id)
    {
        $activity = $this->getDoctrine()->getRepository(Activity::class)->find($id);
        if (empty($activity)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($activity);
        $em->flush();

        return new JsonResponse(['status' => 'activity deleted'],
            JsonResponse::HTTP_OK
        );
    }
}
