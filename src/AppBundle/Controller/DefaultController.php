<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\model\DefaultModel;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Page;
use AppBundle\Entity\Task;

class DefaultController extends Controller
{

     /**
     * @Route("/wypisztekst/{parametr1}/{parametr2}", name="default", defaults={"parametr2": "domyslny"})
     */
    public function defaultAction($parametr1, $parametr2)
    {
    	$model = new DefaultModel;
    	$wynik = $model->tekstHelper($parametr1, $parametr2);

        return $this->render('katalog/layout.html.twig', array(
        	'tekst'=>$wynik));
	}

	

    /**
     * @Route("/page")
     */
    public function nowaPage(){
    $page=new Page;

    $page->setPoleContent('pole strony<h1>Wielkie pole</h1>');
    $page->setTitle('Tytuł');
    $now=new\Datetime();
    $page->setCreationDate($now);
    $page->setUpdateDate($now);
    $page->setViewsCount();
 

    $dm=$this->getDoctrine()->getManager();
    $dm->persist($page);
    $dm->flush();

    return $this->render('katalog/widok.html.twig', array('page' =>$page));
    }

    /**
     * @Route("/indexa", name="homepage")
     */
    public function indexAction()
    {

        $dm=$this->getDoctrine()->getRepository('AppBundle:Page')->findAll();
        return $this->render('katalog/widokIndex.html.twig', array('page'=>$dm));
    }

    /**
     * @Route("/createa")
     */
    public function createAction(){
    $page=new Page;

    $page->setPoleContent('pole strony<h1>Wielkie pole</h1>');
    $page->setTitle('Tytuł');
    $now=new\Datetime();
    $page->setCreationDate($now);
    $page->setUpdateDate($now);
    $page->setViewsCount();
 

    $dm=$this->getDoctrine()->getManager();
    $dm->persist($page);
    $dm->flush();

    return $this->render('katalog/widokCreate.html.twig', array('page' => $page));
    }
    /**
     * @Route("/updatea/{id}")
     */
    public function updateAction($id){

    $page=$this->getDoctrine()->getRepository('AppBundle:Page')->find($id);
    $stary=$page->getTitle();
    $page->setTitle($stary.'!!');
    $this->getDoctrine()->getManager()->flush();
    return $this->render('katalog/widokUpdate.html.twig', array('page'=>$page));
    }

    /**
     * @Route("/removea/{id}")
     */
    public function removeAction($id){
    $page=$this->getDoctrine()->getRepository('AppBundle:Page')->find($id); 
    $dm=$this->getDoctrine()->getManager();
    $dm->remove($page);
    $dm->flush();


    return $this->render('katalog/widokRemove.html.twig');
    }
    /**
     * @Route("/showa/{id}")
     */
    public function showAction($id){
    $dm=$this->getDoctrine()->getRepository('AppBundle:Page')->find($id);
    $counter=$dm->getViewsCount();
    $counter++;
    $dm->setViewsCount($counter);
    $this->getDoctrine()->getManager()->flush();

    return $this->render('katalog/widokShow.html.twig', array('page'=>$dm));
    }


    /**
     * @Route("/task_new")
     */
    public function newAction(Request $request)
    {
        // create a task and give it some dummy data for this example
        $task = new Task();
  

        $form = $this->createFormBuilder($task)
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->add('save', 'submit')
            ->add('saveAndAdd', 'submit')
            ->getForm();

        if($form->isValid()){
        $nextAction = $form->get('saveAndAdd')->isClicked()
        ? 'task_new'
        : 'task_success';
        return $this->redirect($this->generateUrl($nextAction));
        }    
        
        return $this->render('AppBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

	}