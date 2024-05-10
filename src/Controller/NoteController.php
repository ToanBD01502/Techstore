<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Note;
use App\Form\NoteFormType;

class NoteController extends AbstractController
{
    #[Route('/note', name: 'app_note')]
    public function index(Request $req): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteFormType::class, $note);
        $form->handleRequest($req);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = $data->getMessage();
            $created = $data->getCreated()->format('Y-m-d H:i:s');
            return $this->redirectToRoute("app_success", [
                'message' => $message,
                'created' => $created, ]);
        }

        return $this->render('note/index.html.twig', [
            'note_form' => $form->createView(),
        ]);
    }
}
