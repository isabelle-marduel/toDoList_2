<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Task;
use App\Form\TaskType;
use App\Entity\ToDoList;

/**
 * Class TaskController
 * @package App\Controller
 *
 * @Route("/")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Task::class);
        $lm = $em->getRepository(ToDoList::class);
        $tasks = $repository->findBy([], ['id' => 'desc']);
        $tasks2 = [];
        foreach ($tasks as $task) {
            $list = $lm->findOneBy(['id' => $task->getList()]);
            if(!$list){
                $task->listName = '';
                $task->listId = '';
                $tasks2[] = $task;
                continue;
            }
            $task->listName = $list->getName();
            $task->listId = $list->getId();
            $tasks2[] = $task;
        }

        return $this->render(
            'task/index.html.twig',
            [
                'tasks' => $tasks2
            ]
        );
    }


    /**
     * {id} est optionnel et doit être un nombre
     * @Route("/edition/{id}", defaults={"id": null}, requirements={"id": "\d+"})
     */
    public function edit(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($id)){ // création
            $task = new Task();
        } else { // modification

            $task = $em->find(Task::class, $id);

//            404 si l'id reçu dans l'url n'est pas en bdd
            if (is_null($task)){
                throw new NotFoundHttpException();
            }
        }

//        création du formulaire lié à la tâche
        $form = $this->createForm(TaskType::class, $task);
//        le formulaire analyse la requête HTTP et traite le formulaire s'il a été soumis
        $form->handleRequest($request);

//        si le formulaire a été envoyé et validé
        if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($task);
                $em->flush();

//                redirection vers la liste
                return $this->redirectToRoute('app_task_index');
            }

        return $this->render(
            'task/edit.html.twig',
            [
//                passage du formulaire au template
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/suppression/{id}")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Task::class);
        $task = $repository->find($id);

//        si l'id existe en bdd
        if (!is_null($task)) {
            $em->remove($task);
            $em->flush();

            return $this->redirectToRoute(
                'app_task_index'
            );
        }
    }

    /**
     * @Route("/{id}/inProgress")
     */
    public function inProgress(Request $request, $id)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Task::class);
        $task       = $repository->find($id);

        if ($task) {
            $task->setStatus('en cours');

            $em->flush();
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }

    /**
     * @Route("/{id}/completed")
     */
    public function completed($id)
    {
        $em         = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(Task::class);
        $task       = $repository->find($id);

        if ($task) {
            $task->setStatus('terminée');

            $em->flush();
        }

        return $this->redirectToRoute('app_task_index');
    }
}