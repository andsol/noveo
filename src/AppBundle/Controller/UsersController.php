<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 7/17/2017
 * Time: 6:43 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersController extends Controller
{
    use ControllerTrait;
    /**
     * @Rest\Get("/api/users/")
     */
    public function getUsersAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(User::class)
            ->findAll();
        return $this->view($data);
    }

    /**
     * @Rest\Get("/api/users/{userId}/")
     * @Rest\QueryParam(name="userId", requirements="\d+", nullable=false, description="User id", strict=true)
     * @Rest\QueryParam (name="state", requirements="[01]", nullable=true, description="Group", strict=true)
     * @param int $userId
     * @return \FOS\RestBundle\View\View
     */
    public function getUserAction($userId, Request $request)
    {

        $findBy = array_merge($request->query->all(),['id' => $userId]);

        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(User::class)
            ->findOneBy($findBy);

        if (!$data) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->view($data);
    }
    /**
     * @Rest\Post("/api/users/")
     */
    public function newUsersAction(Request $request)
    {
        $user = new User;
        $form = $this->createForm(\AppBundle\Form\User::class, $user);

        $data = $request->request->all();
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->view($user);
        } else {
            return $this->view($form);
        }
    }
    /**
     * @Rest\Patch("/api/users/{userId}/")
     * @Rest\QueryParam(name="userId", requirements="\d+", nullable=false, description="User id")
     * @param int $userId
     * @return \FOS\RestBundle\View\View
     */
    public function editUserAction($userId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(User::class)
            ->findOneBy(['id' => $userId]);

        if (!$data) {
            throw new NotFoundHttpException('User not found');
        }

        $form = $this->createForm(\AppBundle\Form\User::class, $data);

        $data = $request->request->all();
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->view($user);
        } else {
            return $this->view($form);
        }
    }
}