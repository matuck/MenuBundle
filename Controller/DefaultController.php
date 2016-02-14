<?php

namespace matuck\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use matuck\MenuBundle\Entity\MenuEntry;
use matuck\MenuBundle\Form\MenuEntryType;
use matuck\MenuBundle\Form\MenuType;
use matuck\MenuBundle\Repository\MenuEntryRepository;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="matuckmenu")
     */
    public function Menu()
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        /* @var MenuEntry $topmenu */
        $rootMenus = $repo->getRootNodes('title', 'ASC');
        return $this->render('matuckMenuBundle:Default:index.html.twig', array('menus' => $rootMenus));
    }

    /**
     * @Route("/create", name="matuckmenurootcreate")
     */
    public function MenuCreate(Request $request)
    {
        $menu = new MenuEntry();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('matuckmenu');
        }
        return $this->render('matuckMenuBundle:Default:createRoot.html.twig', array('form' => $form->createView()));
    }
    /**
     * @Route("/menu", name="matuckmenushow")
     * @param $slug
     */
    public function ManageMenu()
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        /* @var MenuEntry $topmenu */
        $topmenu = $repo->findOneByTitle("Main Menu");

        $menu = $repo->childrenHierarchy($topmenu, false);
        return $this->render('matuckMenuBundle:Default:index.html.twig', array('menu' => $topmenu));
    }

    /**
     * @Route("/menu/add/{id}", name="matuckmenuadd")
     * @param $id
     */
    public function MenuAdd(Request $request, $id = null)
    {
        $parent = null;
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');

        if($id != -1)
        {
            $parent = $repo->findOneById($id);
        }
        $menuEntry = new MenuEntry();
        $form = $this->createForm(new MenuEntryType(), $menuEntry);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            if($parent)
            {
                $menuEntry->setParent($parent);
            }
            $em->persist($menuEntry);
            $em->flush();
            return $this->redirectToRoute('matuckmenu');
        }
        return $this->render('matuckMenuBundle:Default:createMenu.html.twig', array('parentmenu' => $parent, 'menuForm' => $form->createView()));

    }

    /**
     * @Route("/menu/edit/{id}", name="matuckmenuedit")
     * @param $id
     */
    public function MenuEdit(Request $request, $id)
    {
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $form = $this->createForm(new MenuEntryType(), $entry);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entry);
            $em->flush();
            return $this->redirectToRoute('matuckmenu');
        }
        return $this->render('matuckMenuBundle:Default:editMenu.html.twig', array('menu' => $entry, 'menuForm' => $form->createView()));
    }
    /**
     * @Route("/menu/delete/{id}", name="matuckmenudelete")
     * @param $id of menu to delete
     */
    public function MenuDelete($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $menu = $repo->findOneById($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($menu);
        $repo->recover();
        $em->flush();
        return $this->redirectToRoute('matuckmenu');
    }

    /**
     * * @Route("/menu/up/{id}", name="menuup")
     * @param $id of the menu
     */
    public function MenuUp($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $repo->moveUp($entry);
        return $this->redirectToRoute('matuckmenu');
    }

    /**
     * @Route("/menu/down/{id}", name="menudown")
     * @param $id of the menu
     */
    public function MenuDown($id)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $entry = $repo->findOneById($id);
        $repo->moveDown($entry);
        return $this->redirectToRoute('matuckmenu');
    }
}
