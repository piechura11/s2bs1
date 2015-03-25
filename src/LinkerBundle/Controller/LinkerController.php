<?php

namespace LinkerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use LinkerBundle\Entity\Link;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
class LinkerController extends Controller
{
    /**
     * @Route("/index")
     */
    public function indexAction(Request $request )
    {
        $link = new Link();
        $form = $this -> createFormBuilder($link)
        ->add('longLink', 'textarea', array('required' => 'NotBlank', 'label'=>'Wpisz link do skrócenia'))
        ->add('shortLink', 'text', array('required'=>false, 'label'=>'Nazwa krótkiego Url'))
        ->add('description', 'textarea', array('required'=>false, 'label'=>'Opis'))
        ->add('modyficator', 'choice', array( 'label'=>'Typ', 'data'=>0, 'choices' => array(
        	0=>'Publiczny',
        	1=>'Prywatny',
        	2=>'Chroniony')))
        ->add('password', 'password', array('required'=>false, 'label'=>'Hasło'))
        ->getForm();
      


        $form -> handleRequest($request);
        if($form->isValid()){
        	//sprawdzenie czy haslo zostalo ustawione przy trybie chronionym
        	if($link->getModyficator()==2 && $link->getPassword()==null){
        		$form->get('password')->addError(new FormError('Podaj hasło'));
        		return $this->render('LinkerBundle:Default:index.html.twig', array('form'=>$form->createView()));	
        	}
        	//sprawdzenie unikalności shortLinku
        	if($link->getShortLink()){
        		$dm=$this->getDoctrine()->getManager();
        		$sprawdzanyLink=$dm->getRepository('LinkerBundle:Link')->findByShortLink($link->getShortLink());
        		if($sprawdzanyLink){
        			$form->get('shortLink')->addError(new FormError('Rządana nazwa już istnieje w bazie danych, wybierz inną 
        				lub zostanie ona wygenerowana'));
        			return $this->render('LinkerBundle:Default:index.html.twig', array('form'=>$form->createView()));
        		}}
        	else{
        		//generuj i ustaw
        		$link->setShortLink('generuje');
        	}

        	$dm = $this->getDoctrine()->getManager();
        	$dm ->persist($link);
        	$dm ->flush();

        	return $this->redirectToRoute('show', array('link'=>$link->getShortLink()));
        }

        return $this->render('LinkerBundle:Default:index.html.twig', array('form'=>$form->createView()));
    }
    /**
     * @Route("/show/{link}", name="show")
     */
    public function showAction($link){
        $linker=new Link();
        $dm=$this->getDoctrine()->getManager();
        $linker=$dm->getRepository('LinkerBundle:Link')->findOneByShortLink($link);
       
		return $this->render('LinkerBundle:Default:show.html.twig', array('link'=>$linker->getShortLink()));
    }

    /**
     * @Route("/showLong/{shortLink}")
     */
    public function showLongAction(Request $request, $shortLink){
    	$link = new Link();
    	$dm=$this->getDoctrine()->getManager();
    	$link=$dm->getRepository('LinkerBundle:Link')->findOneByShortLink($shortLink);

        $shortLink=$link->getShortLink();
        //jeśli tryb chroniony podaj haslo
        if($link->getModyficator()==2){
            $protected = new Link();
            $form = $this->createFormBuilder($protected)
            ->add('password', 'password', array('required'=>true))
            ->getForm();

            $form->handleRequest($request);
            if($form->isValid()){
                //sprawdza poprawność hasla
                if($link->getPassword()==$protected->getPassword()){

                    return $this->redirect($link->getLongLink());   
                }
                else{
                    $form->get('password')->addError(new FormError('Podaj hasło aby przejść do strony'));
                    return $this->render('LinkerBundle:Default:protected.html.twig', array('form'=>$form->createView(), 'shortLink'=>$shortLink
                ));                    
                }

            }
            return $this->render('LinkerBundle:Default:protected.html.twig', array('form'=>$form->createView(), 'shortLink'=>$shortLink
                ));
        }

    	return $this->redirect($link->getLongLink());
    	
    }

}
