<?php

namespace ImageEditorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ImageEditorBundle\Entity\Image;
use Symfony\Component\HttpFoundation\Request;
/**
* @Route("/img")
*/
class DefaultController extends Controller
{
    /**
     * @Route("/main", name="main")
     */
    public function mainAction(Request $request)
    {
	    //formularz wrzucenia zdjęcia, 
	    $image = new Image();
	    $image->setName()->setAddDate()->setPath();
	    $form = $this->createFormBuilder($image)
	    ->add('file', 'file', array('required'=>true, 'label'  => 'Dodaj obrazek:'))
	    ->getForm();
	    $form->handleRequest($request);

	 	 if ($this->getRequest()->getMethod() === 'POST') {
	        if ($form->isValid()) {
	            $em = $this->getDoctrine()->getEntityManager();
	            $image->upload();
	            $em->persist($image);
	            $em->flush();
	            $session=$this->getRequest()->getSession();
	            $session->set('obrazek', $image->getPath());
	            $this->redirect($this->generateUrl('menu'));
	        }
	    }
	        return $this->render('ImageEditorBundle:Default:index.html.twig', array('form'=>$form->createView()));

    }
    /**
     * @Route("/menu",name="menu")
     */
    public function menuAction()
    {
    	//wybór dostępnych opcji edycji, pogrupowane w kategorie dla każdej grupy
    	//wraz z suwakami, pokrętłami, zakresami   
    	$session=$this->getRequest()->getSession();


    return $this->render('ImageEditorBundle:Default:menu.html.twig', array('image'=>$session->get('obrazek')));
    }

    /**
     * @Route("/menu/brigness", name="brigness")
     */
    public function brignessAction($prefix, $uploads, $brigness)
    {
    //rozjaśnienie 
    $image=imagecreatefromjpeg($prefix);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, $brigness);
    imagejpeg($image, $uploads, 100);
    return $this->redirect($this->generateUrl('base'));    
    }
    /**
     * @Route("/menu/contrast", name="contrast")
     */
    public function contrastAction($prefix, $uploads, $contrast)
    {
    //contrast 
    $image=imagecreatefromjpeg($prefix);
    imagefilter($image, IMG_FILTER_CONTRAST, $contrast);
    imagejpeg($image, $uploads, 100);
    return $this->redirect($this->generateUrl('base'));
    }
    /**
     * @Route("/menu/base", name="base")
     */
    public function baseAction(Request $request)
    {
    header('Content-Type: image/jpeg');
    $session=$this->getRequest()->getSession();
    $img=$session->get('obrazek');
    $prefix = 'http://localhost/web/uploads/obrazki/'.$img;
    $uploads = __DIR__."/../../../../web/uploads/obrazki/".$img;

    $request  = $this->getRequest();
	$brigness = $request->request->get('level');
	$contrast = $request->request->get('contrast');
	$negate = $request->request->get('negate');
	$gray = $request->request->get('gray');

	$colorize=array(0, 70, 0);
	$this->contrastAction($prefix, $uploads, $contrast);
	$this->brignessAction($prefix, $uploads, $brigness);
	$this->negateAction($prefix, $uploads, $negate);
	$this->grayAction($prefix, $uploads, $gray);
	$this->colorizeAction($prefix, $uploads, $colorize);
    return $this->render('ImageEditorBundle:Default:baseAction.html.twig', array('image'=>$img));
    }
    /**
     * @Route("/menu/size", name="size")
     */
    public function sizeAction(Request $request)
    {
    	//funkcjie: przytnij, dostosuj wymiary, powiększ, zmiejsz
    header('Content-Type: image/jpeg');
    $session=$this->getRequest()->getSession();

    return $this->render('ImageEditorBundle:Default:sizeAction.html.twig', array('image'=>$img));
    }
    /**
     * @Route("/menu/negate", name="negate")
     */
    public function negateAction($prefix, $uploads, $negate=false)
    {
    //negatyw
    if($negate == true){
    $image=imagecreatefromjpeg($prefix);
    imagefilter($image, IMG_FILTER_NEGATE);
    imagejpeg($image, $uploads, 100);
    }
    return $this->redirect($this->generateUrl('base'));
    }
    /**
     * @Route("/menu/gray", name="gray")
     */
    public function grayAction($prefix, $uploads, $gray=false)
    {
    //negatyw
    if($gray == true){
    $image=imagecreatefromjpeg($prefix);
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagejpeg($image, $uploads, 100);
    }
    return $this->redirect($this->generateUrl('base'));
    }
    /**
     * @Route("/menu/colorize", name="colorize")
     */
    public function colorizeAction($prefix, $uploads, $colorize)
    {
    $image=imagecreatefromjpeg($prefix);
    imagefilter($image, IMG_FILTER_COLORIZE, $colorize[0], $colorize[1], $colorize[2]);
    imagejpeg($image, $uploads, 100);

    return $this->redirect($this->generateUrl('base'));

    }
}
