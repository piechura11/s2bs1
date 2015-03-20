<?php

namespace Dodatki\KategorieBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Dodatki\KategorieBundle\Entity\Category;


class DefaultController extends Controller
{
    
    /**
     * @Route("/kategorie")
     */
    public function indexAction()
    {
    	$kategorie = new Category();
    	$kategorie=$this->getDoctrine()->getRepository('DodatkiKategorieBundle:Category')
    	->findByParent(null);



        return $this->render('Default\kategorie.html.twig', array('kategorie'=>$kategorie));
    }

    /**
     * @Route("/kategorie/{id}")
     */
    public function kategoriaAction($id)
    {
        $kategorie = new Category();
        $kategorie=$this->getDoctrine()->getRepository('DodatkiKategorieBundle:Category')
        ->findOneByName($id);
       
        $dzieci=$kategorie->getChildren();


        return $this->render('Default\kategorie.html.twig', array('kategorie'=>$dzieci));
    }
    /**
     * @Route("/kategorie/{kategoriaI}/{kategoriaII}")
     */
    public function kategoriaIAction($kategoriaI, $kategoriaII)
    {
        $kategorie = new Category();
        $kategorie=$this->getDoctrine()->getRepository('DodatkiKategorieBundle:Category')
        ->findOneByName($kategoriaII);
       
        $dzieci=$kategorie->getChildren();


        return $this->render('Default\kategorie.html.twig', array('kategorie'=>$dzieci));
    }
    /**
     * @Route("/dodajkat")
     */
    public function dodajAction()
    {	$dm=$this->getDoctrine()->getManager();
    	$kategorie = new Category;
    	$kategorie->setName('Sztuka');
    	$dm->persist($kategorie);
    

    	$parent = new Category();
    	$parent->setName('renesans');
    	$parent->setParent($kategorie);
    	$dm->persist($parent);
    	

    	$kategoria = new Category();
    	$kategoria->setName('Da Vinci');
    	$kategoria->setParent($parent);

    	$dm->persist($kategoria);
    	$dm->flush();


/*
    	// ...

    	$kategoria = //...

    	$rodzic = $kategoria->getParent();
    	$dzieci = $kategoria->getChildren();






*/

        return render('Default\index.html.twig', array('name'=>'nazwa'));
    }

}
