<?php
// src/AppBundle/Controller/RegistrationController.php
namespace AppBundle\Controller;

use AppBundle\Form\UserLoginType;
use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Twig_Environment;
use Twig_Loader_Filesystem;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        $errors = $this->get('validator')->validate($user);
        if ($form->isSubmitted() && $form->isValid()) {


            $file = $user->getAvatar();
            $fileName = $this->get('app.ava_uploader')->upload($file);

            $user->setAvatar($fileName);


            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);


            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

    public function checkUser(){
        $tmp = unserialize($this->get('session')->get('auth'));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($tmp);

        return $user -> getUsername();
    }
    /**
     * @Route("/login", name="user_login")
     */
    public function loginAction(Request $request)
    {
        // 1) build the form
        $form = $this->createForm(UserLoginType::class);
        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $user = $repository->findBy(array('email'=>$form['email']->getData()));
//            $user = $repository->find(1);
            $passwordEncode = $this->get('security.password_encoder')
                ->encodePassword($user[0], $form['password']->getData());
            if($user[0]->getPassword() == $passwordEncode ){
                $this->get('session')->set('auth',serialize($user[0] -> getId()));
//                $twig->addGlobal('var', 'var');

//                    die($this->get('twig'));
                    return $this->redirectToRoute('homepage');
            }else{

            }
//            var_dump($user[0]);
            return $this->render(
                'registration/login.html.twig',
                array('form' => $form->createView())
            );

        }

        return $this->render(
            'registration/login.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/logout", name="user_logout")
     */
    public function logoutAction()
    {
        $this->get('session')->remove('auth');
        return $this->redirectToRoute('homepage');

    }

    /**
     * @Route("/profile", name="view_profile")
     */
    public function ViewProfileAction()
    {
        $tmp = unserialize($this->get('session')->get('auth'));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($tmp);

        return $this->render('registration/view.html.twig',array(
            'user' => $user
        ));

    }

    /**
     * @Route("/editprofile", name="edit_profile")
     */
    public function EditProfileAction()
    {
        $tmp = unserialize($this->get('session')->get('auth'));

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($tmp);


        $user -> setUsername($user->getUserName());
        $user -> setEmail($user->getEmail());
        $user -> setEmail($user->getEmail());



//        $form = $this -> createFormBuilder($topic)
//            ->add('title', TextType::class)
//            ->add('content', TextareaType::class)
//            ->add('create', SubmitType::class)
//            ->getForm();
//        $form -> handleRequest($request);
//        if($form->isSubmitted()&&$form->isValid()) {
//            $title = $form['title']->getData();
//            $content = $form['content']->getData();
//
//            $em = $this->getDoctrine()->getManager();
//            $topic = $em->getRepository('AppBundle:Topic')->find($id);
//
//            $topic->setTitle($title);
//            $topic->setContent($content);
//            $topic->setDate($now);
//            $topic->setAuthor("me");
//
//            $em->flush();
//
//            $this->addFlash(
//                'notice',
//                'Topic Redacted'
//            );
//
//            return $this->redirectToRoute('topic_list');
//        }

        return $this->render('topic/edit.html.twig',array(
            'form' => $form->createView()));

        return $this->render('topic/view.html.twig',array(
            'user' => $user
        ));

    }
}