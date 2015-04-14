<?php

namespace LinkerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;#fixme: not in use
use LinkerBundle\Entity\Link;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormError;
use LinkerBundle\Form\LinkForm;
use LinkerBundle\Service\Helper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
#fixme: TU brakuje spacji
class LinkerController extends Controller
{
    /**
     * #fixme: Definitywnie trzeba skrócić tą akcję
     * @Route("/index")
     */
    public function indexAction(Request $request )
    {   #todo -pobieranie ostatnich 10 linkerów, - wyswietlanie statystyk(-ilość linkerów w bd, licznik odwiedzin)
        #todo -przeglądarka linków, wyszukiwarka
        #todo -mailing przy podanym adresie, link do edycji, dodawania hała, zmieniania hasła, opisu
        #todo -dodać czas dodania do bazy danych
        $link = new Link();
        #fixme: nie dawać spacji wokół '->'
        $form = $this -> createForm(new LinkForm(), $link);
        $form -> handleRequest($request);
        $helper = $this->get('linker_helper');
        if($form->isValid())
        {
            $flag = true;
            #fixme: Widzę tu tabulacje
        	//sprawdzenie czy haslo zostalo ustawione przy trybie chronionym
           #fixme: To da się rozwiązać za pomocą walidacji
           #        poczytaj sobie o tworzeniu walidatorów. To dodatkowy temat,
           #        ale wiele się nauczysz
        	if($link->getModyficator()==2 && $link->getPassword()==null)
            {
        		$form->get('password')->addError(new FormError('Podaj hasło'));
                $flag = false;
        	}
        	//sprawdzenie unikalności shortLinku
        	if($link->getShortLink())
            {
        		if(!$helper->unique($link->getShortLink()))
                {

        			$form->get('shortLink')->addError(new FormError('Rządana nazwa już istnieje w bazie danych, wybierz inną
        				lub zostanie ona wygenerowana'));
                    $flag = false;
        		}
            }
             //jeśli trafił na błędy renderuje formularz z wiadomościami
             if($flag == false)
             {
                 return $this->render('LinkerBundle:Default:index.html.twig', array('form'=>$form->createView()));
             }
            //generowanie shortLinku
           if(!$link->getShortLink())
            {
                //sprawdzenie unikalności, jeśli nie jest, generuje
                #fixme: sprawdź, czy nie da się tego zrobić za pomocą doctrina
                #       na poziomie bazy danych
                $i = false;
                while($i == false)
                {
                    $randomString = $helper->randGenerator();
                    if($helper->unique($randomString))
                    {
                    $link->setShortLink($randomString);
                    $i = true;
                    }
                }
            }
                #fixme: to powinno leżeć w encji. Można dać do kontructora,
                #       ale można też użyć eventów doctrinowych
               $link->setAddDate();
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
     * @Route("/long/{shortLink}")
     */
    public function showLongAction(Request $request, $shortLink){
    	$link = new Link();
    	$dm=$this->getDoctrine()->getManager();
    	$link=$dm->getRepository('LinkerBundle:Link')->findOneByShortLink($shortLink);

        $shortLink=$link->getShortLink();
        //jeśli tryb chroniony podaj haslo
        if($link->getModyficator() == 2){
            $protected = new Link();
            $form = $this->createFormBuilder($protected)
            ->add('password', 'password', array('required'=>true))
            ->getForm();

            $form->handleRequest($request);
            if($form->isValid()){
                //sprawdza poprawność hasla
                if($link->getPassword() == $protected->getPassword()){

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
    /**
     * @Route("/kontakt")
     */
    public function mailMe()
    {
        $mailer = $this->get('mailer');
        $message =$mailer->createMessage()
        #fixme: wcięcia. Plus te dane powinny być pomierane z pliki parameters.yml
        ->setFrom('piechura11@gmail.com')
        ->setTo('piechura11@gmail.com')
        ->setSubject('Ważna sprawa')
        ->setBody('treść', 'text/html');

        $mailer->send($message);
        ////poprawić szablon inaczej nie wysysła

        return $this->render('LinkerBundle:Default:index.html.twig', array('form'=>'form'));
    }
    /**
     * @Route("/baza")
     */
    public function bazaAction()
    {
        //wyswietla wszytkie wpisy publiczne wraz z opisem i skrótem oraz czasem dodania
        // tabela -skrócony link, data dodania, opis
        $qb = $this->getDoctrine()->getManager()->getRepository('LinkerBundle:Link')
        ->createQueryBuilder('u');
        $qb->select( 'u.shortLink', 'u.addDate', 'u.longLink')
        #fixme: wcięcia
        ->where('u.modyficator = 0')
        ->orderBy('u.addDate', 'ASC');
        $dane = $qb->getQuery()->getResult();

        #fixme: zwracasz dane/wyniki wyszukiwania, a w twigu będą dostępne jako
        #       zmienna qb. Trochę agresywne. Ja bym się pokusił o zmienną results
        #       lub nawet links, shortUrl, lub jeszcze czegoś innego.
        return $this->render('LinkerBundle:Default:baza.html.twig', array('qb'=>$dane));
    }


    //zrobić wyszykiwarke


}
