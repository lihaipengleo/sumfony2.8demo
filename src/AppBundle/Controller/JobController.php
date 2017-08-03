<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use AppBundle\Entity\Job;
use AppBundle\Form\JobType;
use Doctrine\Common\Util\Debug;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{

    /**
     * Lists all Job entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

//        $entities = $em->getRepository('AppBundle:Job')->findAll();
        $query = $em->createQuery(
            'SELECT j FROM AppBundle:Job j WHERE j.expires_at > :date'
        )->setParameter('date', date('Y-m-d H:i:s', time()));
//        $entities = $query->getResult();
//        $entities = $em->getRepository('AppBundle:Job')->getActiveJobs();
//
//        return $this->render('AppBundle:Job:index.html.twig', array(
//            'entities' => $entities,
//        ));

//        $categories = $em->getRepository('AppBundle:Category')->getWithJobs();

//        foreach($categories as $category) {
//            $category->setActiveJobs($em->getRepository('AppBundle:Job')->getActiveJobs($category->getId(),$this->container->getParameter('max_jobs_on_homepage')));
//            $category->setMoreJobs($em->getRepository('AppBundle:Job')->countActiveJobs($category->getId()) - $this->container->getParameter('max_jobs_on_homepage'));
//        }
//       Debug::dump($categories);exit;
//        return $this->render('AppBundle:Job:index.html.twig', array(
//            'categories' => $categories
//        ));

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('AppBundle:Job:index.html.twig', array(
            'pagination' => $pagination
        ));
    }

    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT j FROM AppBundle:Job j WHERE j.expires_at > :date'
        )->setParameter('date', date('Y-m-d H:i:s', time()));
//        $query=  $em->getRepository('AppBundle:Job')->getQuery();
//        var_dump(111);exit;
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('AppBundle:Job:list1.html.twig', array('pagination' => $pagination));
    }
    /**
     * Creates a new Job entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new Job();
        $form = $this->createCreateForm($entity);
        $form->bind($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

//            $entity->file->move(__DIR__.'/../../../../web/uploads/jobs', $entity->file->getClientOriginalName());
//            $entity->setLogo($entity->file->getClientOriginalName());
//            Debug::dump($entity);EXIT;
            $em->persist($entity);
            $em->flush();

//            return $this->redirect($this->generateUrl('ibw_job_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('ibw_job_show', array(
                'company' => $entity->getCompanySlug(),
                'location' => $entity->getLocationSlug(),
                'id' => $entity->getId(),
                'position' => $entity->getPositionSlug()
            )));

        }

        return $this->render('AppBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a Job entity.
     *
     * @param Job $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ibw_job_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Job entity.
     *
     */
    public function newAction()
    {
        $entity = new Job();
        $entity->setType('part-time');
        $form   = $this->createCreateForm($entity);

        return $this->render('AppBundle:Job:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Job')->getActiveJob($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AppBundle:Job:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    public function previewAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($entity->getId());

        return $this->render('AppBundle:Job:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
    public function editAction($token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($token);

        return $this->render('AppBundle:Job:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a Job entity.
    *
    * @param Job $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Job $entity)
    {
        $form = $this->createForm(new JobType(), $entity, array(
            'action' => $this->generateUrl('ibw_job_update', array('token' => $entity->getToken())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Job entity.
     *
     */
    public function updateAction(Request $request, $token)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Job')->findOneByToken($token);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($token);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ibw_job_edit', array('token' => $token)));
        }

        return $this->render('AppBundle:Job:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction(Request $request, $token)
    {
        $form = $this->createDeleteForm($token);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Job')->findOneByToken($token);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Job entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('ibw_job'));
    }

    /**
     * Creates a form to delete a Job entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createDeleteForm($id)
//    {
//        return $this->createFormBuilder()
//            ->setAction($this->generateUrl('ibw_job_delete', array('id' => $id)))
//            ->setMethod('DELETE')
//            ->add('submit', 'submit', array('label' => 'Delete'))
//            ->getForm()
//        ;
//    }

    private function createDeleteForm($token)
    {
        return $this->createFormBuilder(array('token' => $token))
            ->add('token', 'hidden')
            ->getForm()
            ;
    }
}
