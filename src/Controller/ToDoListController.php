<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\ToDoList;
use App\Form\ToDoListType;

/**
 * Class ToDoListController
 * @package App\Controller
 *
 * @Route("/list", name="list")
 */
class ToDoListController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ToDoList::class);
        $lists = $repository->findBy([], ['id' => 'desc']);

        return $this->render(
            'list/index.html.twig',
            [
                'lists' => $lists
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
            $list = new ToDoList();
        } else { // modification

            $list = $em->find(ToDoList::class, $id);

//            404 si l'id reçu dans l'url n'est pas en bdd
            if (is_null($list)){
                throw new NotFoundHttpException();
            }
        }

//        création du formulaire lié à la tâche
        $form = $this->createForm(ToDoListType::class, $list);
//        le formulaire analyse la requête HTTP et traite le formulaire s'il a été soumis
        $form->handleRequest($request);

//        si le formulaire a été envoyé et validé
        if ($form->isSubmitted() && $form->isValid()) {
                $em->persist($list);
                $em->flush();

//                redirection vers la liste
                return $this->redirectToRoute('listapp_todolist_index');
            }

        return $this->render(
            'list/edit.html.twig',
            [
//                passage du formulaire au template
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/suppression_liste/{id}")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository(ToDoList::class);
        $list = $repository->find($id);

        if (!is_null($list)) {
            $em->remove($list);
            $em->flush();

            return $this->redirectToRoute('listapp_todolist_index');
        }
    }
}