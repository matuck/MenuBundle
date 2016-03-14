<?php

namespace matuck\MenuBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use matuck\MenuBundle\Entity\MenuEntry;
use matuck\MenuBundle\Form\MenuEntryType;
use matuck\MenuBundle\Form\MenuType;
use matuck\MenuBundle\Repository\MenuEntryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * Class AdminController
 * Handles the admin functions for the bundle
 *
 * {@inheritdoc}
 * @package matuck\MenuBundle\Controller
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="matuckmenu")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function Menu()
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        /* @var MenuEntry $topmenu */
        $rootMenus = $repo->getRootNodes('title', 'ASC');
        return $this->render('matuckMenuBundle:Admin:index.html.twig', array('menus' => $rootMenus));
    }

    /**
     * @Route("/create", name="matuckmenurootcreate")
     * @param Request $request http request object
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function MenuCreate(Request $request)
    {
        $menu = new MenuEntry();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('matuckmenu');
        }
        return $this->render('matuckMenuBundle:Admin:createRoot.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/mangage/{menu}", requirements={"menu" = "\d+"},name="matuckmenumanage")
     * @param Request $request the http request object
     * @param MenuEntry $menu the menu hierarchy to manage
     * @return \Symfony\Component\HttpFoundation\Response
     * @ParamConverter("menu", class="matuckMenuBundle:MenuEntry", options={"menu" = "id"})
     */
    public function ManageMenu(Request $request, MenuEntry $menu)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        /* @var MenuEntry $topmenu */
        //$menu = $repo->findOneById($id);
        $form = $this->createFormBuilder()->add("order", TextareaType::class)->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $data = $form->getData();
            $order = json_decode($data['order']);
            $repo->setTreeOrder($menu, $order);
            $menu = $repo->findOneById($menu->getId());
        }
        return $this->render('matuckMenuBundle:Admin:manage.html.twig', array('menu' => $menu, 'form' => $form->createView()));
    }

    /**
     * @Route("/add/{parent}", requirements={"parent" = "\d+"}, name="matuckmenuadd")
     * @param Request $request the http request object
     * @param MenuEntry $parent the parent of the menu to add
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @ParamConverter("parent", class="matuckMenuBundle:MenuEntry", options={"parent" = "id"})
     */
    public function MenuAdd(Request $request, MenuEntry $parent = null)
    {
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $menuEntry = new MenuEntry();
        $form = $this->createForm(MenuEntryType::class, $menuEntry);
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
            return $this->redirectToRoute('matuckmenumanage', array('menu' => $parent->getRoot()->getId()));
        }
        return $this->render('matuckMenuBundle:Admin:createMenu.html.twig', array('parentmenu' => $parent, 'menuForm' => $form->createView()));

    }

    /**
     * @Route("/edit/{menu}", requirements={"menu" = "\d+"}, name="matuckmenuedit")
     * @param Request $request the http request object
     * @param MenuEntry $menu the menu to edit
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @ParamConverter("menu", class="matuckMenuBundle:MenuEntry", options={"menu" = "id"})
     */
    public function MenuEdit(Request $request, MenuEntry $menu)
    {
        $form = $this->createForm(MenuEntryType::class, $menu);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($menu);
            $em->flush();
            return $this->redirectToRoute('matuckmenumanage', array('menu' => $menu->getRoot()->getId()));
        }
        return $this->render('matuckMenuBundle:Admin:editMenu.html.twig', array('menu' => $menu, 'menuForm' => $form->createView()));
    }

    /**
     * @Route("/delete/{menu}", requirements={"menu" = "\d+"}, name="matuckmenudelete")
     * @param MenuEntry $menu this is the menu to get rid of.
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("menu", class="matuckMenuBundle:MenuEntry", options={"menu" = "id"})
     */
    public function MenuDelete(MenuEntry $menu)
    {
        $root = $menu->getRoot()->getId();
        /* @var MenuEntryRepository $repo */
        $repo = $this->getDoctrine()->getRepository('matuckMenuBundle:MenuEntry');
        $repo->removeFromTree($menu);
        $em = $this->getDoctrine()->getManager();
        $repo->recover();
        $em->flush();
        return $this->redirectToRoute('matuckmenumanage', array('menu' => $root));
    }
}
