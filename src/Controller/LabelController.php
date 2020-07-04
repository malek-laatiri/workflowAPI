<?php

namespace App\Controller;

use App\Entity\Label;
use App\Form\LabelType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\View;
use JMS\Serializer\SerializationContext;
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
 * Class LabelController
 * @package App\Controller
 * @Route("/secured/label",name="label_")
 */
class LabelController extends AbstractController
{
    /**
     * get all the labels
     * @param int $projectId
     * @return Response
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     * @Rest\Get("/labelsList/{projectId}")
     * @SWG\Response(
     *     response=200,
     *     description="get all labels",
     *     @SWG\Schema(
     *                   type="object",
     * @SWG\Property(property="id",type="integer",description="ID"),
     * @SWG\Property(property="name",type="string",description="name"),
     * @SWG\Property(property="color",type="string",description="color")
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
     * @SWG\Tag(name="Labels")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getAllLabels(int $projectId)
    {
        $repository = $this->getDoctrine()->getRepository(Label::class);
        $allProjects = $repository->findBy(["project"=>$projectId]);

        $serializer = new Serializer([new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter())]);

        $data = $serializer->normalize($allProjects, null, [AbstractNormalizer::ATTRIBUTES => ['id','name','color'],
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
     * Create new label.
     * @Rest\Post("/labelCreate")
     * @param Request $request
     * @return \FOS\RestBundle\View\View|JsonResponse
     * @SWG\Response(
     *     response=200,
     *     description="add new label"
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Tag(name="Labels")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postLabels(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );
        $label = new Label();
        $form = $this->createForm(LabelType::class, $label);
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


    /**
     * delete a label
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/labelDelete/{id}")
     * @Rest\View()
     * @SWG\Response(
     *     response=200,
     *     description="ok",
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
     * @SWG\Tag(name="Labels")
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteProject(int $id)
    {
        $label = $this->getDoctrine()->getRepository(Label::class)->find($id);
        if (empty($label)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($label);
        $em->flush();

        return new JsonResponse(['status' => 'ok'],
            JsonResponse::HTTP_OK
        );
    }

}
