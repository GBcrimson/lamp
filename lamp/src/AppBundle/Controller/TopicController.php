<?php
// src/AppBundle/Controller/RegistrationController.php
namespace AppBundle\Controller;

use AppBundle\Entity\Tag;
use AppBundle\Entity\Topic;
use AppBundle\Form\UserLoginType;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TopicController extends Controller
{
    /**
     * @Route("/topics", name="topic_list")
     */
    public function listAction()
    {
        $topics = $this->getDoctrine()
            ->getRepository('AppBundle:Topic')
            ->findAll();
        return $this->render('topic/index.html.twig',array(
            'topics' => $topics
        ));
    }

    /**
     * @Route("/topic/create", name="topic_creation")
     */
    public function createAction(Request $request)
    {
        $topic = new Topic;
        $form = $this -> createFormBuilder($topic)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
//            ->add('tag')
            ->add('tag', TextareaType::class, array('required' => false, 'mapped' => false))

            ->add('create', SubmitType::class)
            ->getForm();
        $form -> handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){

            $tags = explode(" ", $form['tag']->getData());
            $now = new\DateTime('now');

            $tmp = unserialize($this->get('session')->get('auth'));
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle:User')->find($tmp);

            $topic -> setDate($now);
            $topic -> setAuthor($user);
            foreach ($tags as $tag){
                $t = new Tag();
                $t->setName($tag);
                $topic -> addTag($t);
            }

            $em = $this -> getDoctrine() -> getManager();
            $em->persist($topic);
            $em->flush();

            $this -> addFlash(
                'notice',
                'Topic Added'
            );

            return $this->redirectToRoute('topic_list');
        }


        return $this->render('topic/create.html.twig', array(
            'form'=>$form->createView()
            ));
    }
    /**
     * @Route("/topic/edit/{id}", name="topic_edit")
     */
    public function editAction($id, Request $request)
    {
        $topic = $this->getDoctrine()
            ->getRepository('AppBundle:Topic')
            ->find($id);

        $now = new\DateTime('now');

        $topic -> setTitle($topic->getTitle());
        $topic -> setContent($topic->getContent());
        $topic -> setDate($now);
        $topic -> setAuthor("me");


        $form = $this -> createFormBuilder($topic)
            ->add('title', TextType::class)
            ->add('content', TextareaType::class)
            ->add('create', SubmitType::class)
            ->getForm();
        $form -> handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()) {
            $title = $form['title']->getData();
            $content = $form['content']->getData();

            $em = $this->getDoctrine()->getManager();
            $topic = $em->getRepository('AppBundle:Topic')->find($id);

            $topic->setTitle($title);
            $topic->setContent($content);
            $topic->setDate($now);
            $topic->setAuthor("me");

            $em->flush();

            $this->addFlash(
                'notice',
                'Topic Redacted'
            );

            return $this->redirectToRoute('topic_list');
        }

        return $this->render('topic/edit.html.twig',array(
            'form' => $form->createView()));

    }

    /**
     * @Route("/topic/view/{id}", name="topic_view")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction($id)
    {
        $topic = $this->getDoctrine()
            ->getRepository('AppBundle:Topic')
            ->find($id);
        return $this->render('topic/view.html.twig',array(
            'topic' => $topic
        ));
    }

    /**
     * @Route("/topic/delete/{id}", name="topic_delete")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $topic = $em->getRepository('AppBundle:Topic')->find($id);

        $em ->remove($topic);
        $em -> flush();

        $this->addFlash(
            'notice',
            'Topic Deleted'
        );
        return $this->redirectToRoute('topic_list');

    }

    /**
     * @Route("/topic/like/{id}", name="topic_like")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function likeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $topic = $em->getRepository('AppBundle:Topic')->find($id);
        $tmp = unserialize($this->get('session')->get('auth'));
        $user = $em->getRepository('AppBundle:User')->find($tmp);


        $topic -> addLike($user);
//        return $this->redirectToRoute('topic_list');
        return $this->redirectToRoute('topic_view', array('id' => $id));
    }

}
