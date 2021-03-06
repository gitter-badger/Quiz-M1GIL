<?php

namespace GIL\QueazyBundle\Controller\Visitor;

use GIL\QueazyBundle\Controller\AppController;
use Symfony\Component\HttpFoundation\Request;

class ViewQuizController extends AppController
{
    public function viewAction($quiz_id) {
        $em = $this->getDoctrine()->getManager();

        $quiz = null;
        $quiz = $em->getRepository('GILQueazyBundle:Quiz')->find($quiz_id);
        $questions = $em->getRepository('GILQueazyBundle:Question')->findBy(
            array('quiz' => $quiz)
        );

        return $this->render('GILQueazyBundle:Visitor:viewQuiz.html.twig', array(
            'quiz' => $quiz,
            'questions' => $questions
        ));
    }

    /**
     * @param $quiz_id
     * @param $question_id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewQuestionAction($quiz_id, $question_id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        $quiz = $em->getRepository('GILQueazyBundle:Quiz')->find($quiz_id);
        $question = $em->getRepository('GILQueazyBundle:Question')->find($question_id);

        // Gestion des bonnes (ou mauvaises) réponses avec des cookies (miam ! :p)
        if ($request->isMethod('POST')) {
            $data = $request->request->get('reponse');

            $cookie_data = unserialize($_COOKIE[$quiz_id]);
            $key = array_keys($cookie_data)[count(array_keys($cookie_data)) - 1];
            $cookie_data[$key] = $data;
            $cookie_data[$question_id] = false;

            setcookie($quiz_id, serialize($cookie_data), time() + 86400, "/");
        } else {
            $cookie_name = $quiz_id;
            $cookie_value[$question_id] = false;
            setcookie($cookie_name, serialize($cookie_value), time() + 86400, "/"); // 86400 = 1 day
        }

        $reponses = $em->getRepository('GILQueazyBundle:Reponse')->findBy(
            array('question' => $question)
        );

        $q_suivante = null;
        $prochaine = false;
        $listQuestions = null;
        $listQuestions = $em->getRepository('GILQueazyBundle:Question')->findBy(
            array('quiz' => $quiz)
        );
        // On cherche la prochaine question dans la liste de question restante
        foreach($listQuestions as $q) {
            if ($prochaine) {
                $q_suivante = $q;
                $prochaine = false;
            }
            if ($q->getId() == $question_id) {
                $prochaine = true;
            }
        }

        return $this->render('GILQueazyBundle:Visitor:viewQuizQuestion.html.twig', array(
            'quiz' => $quiz,
            'question' => $question,
            'reponses' => $reponses,
            'q_suivante' => $q_suivante
        ));
    }

    public function resultatAction($quiz_id, Request $request) {
        $em = $this->getDoctrine()->getManager();

        // Gestion de la dernière réponse avec des cookies (miam ! :p)
        if ($request->isMethod('POST')) {
            $data = $request->request->get('reponse');

            $cookie_data = unserialize($_COOKIE[$quiz_id]);
            $key = array_keys($cookie_data)[count(array_keys($cookie_data)) - 1];
            $cookie_data[$key] = $data;
            setcookie($quiz_id, serialize($cookie_data), time() + 86400, "/");
        }

        // On récupère l'ensemble des réponses de l'utilisateur !
        $choix_reponse = null;
        $nbReponseCorrecte = 0;
        foreach($cookie_data as $key => $value) {
            $choix_reponse[$key] = $em->getRepository('GILQueazyBundle:Reponse')->find(intval($value));

            if ($choix_reponse[$key]->getCorrecte()) {
                $nbReponseCorrecte++;
            }
        }

        $quiz = $em->getRepository('GILQueazyBundle:Quiz')->find($quiz_id);
        $questions = $em->getRepository('GILQueazyBundle:Question')->findBy(
            array('quiz' => $quiz)
        );
        $reponses = null;
        foreach($questions as $question) {
            $tmp =  $em->getRepository('GILQueazyBundle:Reponse')->findBy(
                array('question' => $question)
            );
            foreach ($tmp as $reponse) {
                if ($reponse->getCorrecte()) {
                    $reponses[$question->getId()] = $reponse; // tableau de bonnes réponses
                }
            }
        }

        return $this->render('GILQueazyBundle:Visitor:resultat.html.twig', array(
            'quiz' => $quiz,
            'questions' => $questions,
            'reponses' => $reponses,
            'choixReponses' => $choix_reponse,
            'nbReponseCorrecte' => $nbReponseCorrecte
        ));
    }
}
