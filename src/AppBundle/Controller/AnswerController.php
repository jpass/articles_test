<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Answer;
use AppBundle\Form\AnswerType;

/**
 * Answer controller.
 */
class AnswerController extends FOSRestController implements ClassResourceInterface
{
    private function createNew($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $em->getRepository('AppBundle:Article')->findOneBySlug($slug);

        if (!$article) {
            throw $this->createNotFoundException('Unable to find Article.');
        }

        $entity = new Answer();
        $entity->setArticle($article);

        return $entity;
    }

    /**
     * Lists all Answer entities.
     * @View(templateVar="entities")
     */
    public function cgetAction($slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Article')->findOneBySlug($slug);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Article.');
        }

        return $entity->getAnswers();
    }

    /**
     * Creates a new Answer entity.
     *
     * @View()
     */
    public function postAction(Request $request, $slug)
    {
        $entity = $this->createNew($slug);
        $form = $this->createCreateForm($entity, $slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('get_article_answer', array(
                'slug' => $slug,
                'id' => $entity->getId()
            )));
        }

        return $form->createView();
    }

    /**
     * Creates a form to create a Answer entity.
     *
     * @param Answer $entity The entity
     * @param string $slug Article slug
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Answer $entity, $slug)
    {
        $form = $this->createForm(new AnswerType(), $entity, array(
            'action' => $this->generateUrl('post_article_answer', array('slug' => $slug)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Answer entity.
     * @View()
     */
    public function newAction($slug)
    {
        $entity = $this->createNew($slug);
        $form   = $this->createCreateForm($entity, $slug);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Answer entity.
     * @View(templateVar="entity")
     */
    public function getAction($slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Answer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Answer.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId(), $slug);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView()
        );
    }

    /**
     * Displays a form to edit an existing Answer entity.
     * @View(templateVar="entity")
     */
    public function editAction($slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Answer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Answer entity.');
        }

        $editForm = $this->createEditForm($entity, $slug);
        $deleteForm = $this->createDeleteForm($entity->getId(), $slug);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Answer entity.
    *
    * @param Answer $entity The entity
    * @param string $slug Article slug
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Answer $entity, $slug)
    {
        $form = $this->createForm(new AnswerType(), $entity, array(
            'action' => $this->generateUrl('put_article_answer', array(
                'slug' => $slug,
                'id' => $entity->getId()
            )),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Answer entity.
     * @View(templateVar="entity")
     */
    public function putAction(Request $request, $slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Answer')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Answer entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity, $slug);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('edit_article_answer', array(
                'slug' => $slug,
                'id' => $entity->getId()
            )));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Answer entity.
     * @View()
     */
    public function deleteAction(Request $request, $slug, $id)
    {
        $form = $this->createDeleteForm($id, $slug);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Answer')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Answer entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('get_article_answers'));
    }

    /**
     * Creates a form to delete a Answer entity by id.
     *
     * @param mixed $id The entity id
     * @param string $slug Article slug
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id, $slug)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('delete_article_answer', array(
                'slug' => $slug, 'id' => $id
            )))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
