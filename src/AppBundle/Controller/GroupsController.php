<?php
/**
 * Created by PhpStorm.
 * Group: Andrey
 * Date: 7/17/2017
 * Time: 6:43 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Group;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Form\Group as GroupForm;
use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GroupsController extends Controller
{
    use ControllerTrait;
    /**
     * @Rest\Get("/api/groups/")
     */
    public function getGroupsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Group::class)
            ->findAll();
        return $this->view($data);
    }

    /**
     * @Rest\Get("/api/groups/{groupId}/")
     * @Rest\QueryParam(name="groupId", requirements="\d+", nullable=false, description="Group id")
     * @param int $groupId
     * @return \FOS\RestBundle\View\View
     */
    public function getGroupAction($groupId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Group::class)
            ->findBy(['id' => $groupId]);
        return $this->view($data[0]);
    }
    /**
     * @Rest\Post("/api/groups/")
     */
    public function newGroupsAction(Request $request)
    {
        $user = new Group();
        $form = $this->createForm(GroupForm::class, $user);

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
     * @Rest\Patch("/api/groups/{groupId}/")
     * @Rest\QueryParam(name="groupId", requirements="\d+", nullable=false, description="Group id")
     * @param int $groupId
     * @return \FOS\RestBundle\View\View
     */
    public function editGroupsAction($groupId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = $em->getRepository(Group::class)
            ->findBy(['id' => $groupId]);

        if (!$data) {
            throw new NotFoundHttpException('Group not found');
        }

        $form = $this->createForm(GroupForm::class, $data[0]);

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