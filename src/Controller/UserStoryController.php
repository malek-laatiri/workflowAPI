<?php

namespace App\Controller;

use App\Entity\Label;
use App\Entity\Status;
use App\Entity\UserStory;
use App\Form\LabelAssignType;
use App\Form\ProgressBarType;
use App\Form\PutIsComfirmedType;
use App\Form\PutIsVerifiedType;
use App\Form\UserStoryType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/secured/UserStory",name="userstory_")
 */
class UserStoryController extends FOSRestController
{

    /**
     * Lists userstories by backlog.
     * @Rest\Get("/userStoryList/{backlogId}",name="userStoryListByProject")
     * @Rest\View()
     * @param int $backlogId
     * @return JsonResponse|Response
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getUserStoriesByBacklog(int $backlogId)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);
        $allPriority = $repository->findBy(['backlog' => $backlogId]);
        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($allPriority, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * Create UserStory.
     * @Rest\Post("/UserStoryCreate")
     * @param Request $request
     * @return Response
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function postUserStory(Request $request)
    {
        $data = json_decode(
            $request->getContent(),
            true
        );

        $form = $this->createForm(UserStoryType::class, new UserStory());

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

        return new JsonResponse(['status' => 'ok'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\FormInterface|Response
     * @throws \Exception
     * @Rest\View()
     * @Rest\Patch("/UerStoryUpdate/{id}")
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putUserStory(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userstory = $repository->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserStoryType::class, $userstory);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'data' => $userstory], JsonResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @Rest\View()
     * @Rest\Get("/userStoryShow/{id}")
     * @return Response
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function getOneUserStory(Request $request, int $id)
    {

        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userStory = $repository->find($id);
        if (empty($userStory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = $serializer = SerializerBuilder::create()->build()->serialize($userStory, 'json');
        $response = new Response($data);
        return $response;
    }

    /**
     * @param int $id
     * @return View|Response
     * @Rest\Delete("/userStoryDelete/{id}")
     * @Rest\View()
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function deleteUserStory(int $id)
    {
        $userstory = $this->getDoctrine()->getRepository(UserStory::class)->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($userstory);
        $em->flush();

        return new JsonResponse(['status' => 'ok'], JsonResponse::HTTP_OK);
    }

    /**
     * @param int $userStoryId
     * @param int $oldStatusId
     * @param int $newStatusId
     * @return View|Response
     * @Rest\Patch("/switchStoryStatus/{userStoryId}/{newStatusId}")
     * @Rest\View()
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function changeStatus(int $userStoryId, int $newStatusId)
    {
        $userstory = $this->getDoctrine()->getRepository(UserStory::class)->find($userStoryId);
        $status = $this->getDoctrine()->getRepository(Status::class)->find($newStatusId);
        $userstory->setStatus($status);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($userstory);
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok'], JsonResponse::HTTP_OK);


    }


    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\FormInterface|Response
     * @throws \Exception
     * @Rest\View()
     * @Rest\Patch("/PutProgress/{id}")
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putProgress(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userstory = $repository->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(ProgressBarType::class, $userstory);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'data' => $userstory], JsonResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\FormInterface|Response
     * @throws \Exception
     * @Rest\View()
     * @Rest\Patch("/UerStoryLabel/{id}")
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putLabel(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userstory = $repository->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $label = new Label();
        $form = $this->createForm(LabelAssignType::class, $userstory);

        $form->submit($data);
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'data' => $userstory], JsonResponse::HTTP_OK);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\FormInterface|Response
     * @throws \Exception
     * @Rest\View()
     * @Rest\Patch("/putIsVerified/{id}")
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putIsVerified(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userstory = $repository->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(PutIsVerifiedType::class, $userstory);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'data' => $userstory], JsonResponse::HTTP_OK);
    }
    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\Form\FormInterface|Response
     * @throws \Exception
     * @Rest\View()
     * @Rest\Patch("/PutIsComfirmed/{id}")
     *  * @SWG\Response(
     *     response=401,
     *     description="JWT Token not found / Invalid JWT Token / unauthorized",
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Expedition not Found",
     * )
     * @SWG\Parameter(name="Authorization", in="header", required=true, type="string", default="Bearer accessToken", description="Authorization")
     * @Security(name="Bearer")
     */
    public function putIsComfirmed(Request $request, int $id)
    {
        $repository = $this->getDoctrine()->getRepository(UserStory::class);

        $userstory = $repository->find($id);
        if (empty($userstory)) {
            return new JsonResponse(['status' => 'Expedition not Found'], JsonResponse::HTTP_NOT_FOUND);
        }
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(PutIsComfirmedType::class, $userstory);

        $form->submit($data);

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->persist($form->getData());
        $entityManager->flush();
        return new JsonResponse(['status' => 'ok', 'data' => $userstory], JsonResponse::HTTP_OK);
    }
}
