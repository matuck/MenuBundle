<?php

namespace matuck\MenuBundle\Twig;

use Doctrine\ORM\EntityManager;
use matuck\MenuBundle\Entity\MenuEntry;
use matuck\MenuBundle\Repository\MenuEntryRepository;
use Symfony\Component\Security\Http\AccessMapInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MenuExtension
 * Twig extention to get the menu entries
 * {@inheritdoc}
 * @package matuck\MenuBundle\Twig
 */
class MenuExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var MenuEntryRepository
     */
    private $menurepo;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $sec;

    /**
     * @var AccessMapInterface
     */
    private $accessMap;

    public function __construct(EntityManager $em, AccessMapInterface $accessmap, AuthorizationCheckerInterface $sec)
    {
        $this->em = $em;
        $this->menurepo = $em->getRepository('matuckMenuBundle:MenuEntry');
        $this->sec = $sec;
        $this->accessMap = $accessmap;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getName()
    {
        return 'matuck_menu';
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('matuck_menu_render', array($this, 'render'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    /**
     * Renders a menu with the specified renderer.
     *
     * @param ItemInterface|string|array $menu
     * @param array                      $options
     * @param string                     $renderer
     *
     * @return string
     */
    public function render(\Twig_Environment $env, $menu, $template = 'matuckMenuBundle::bootstrapmenu.html.twig', array $options = array())
    {
        $rootmenu = $this->menurepo->findOneBy(array('title' => $menu, 'lvl' => 0));

        $rootmenu->setChildren($this->cleanMenu($rootmenu->getChildren()));
        return $env->render($template,array('menus' => $rootmenu->getChildren(), 'top' => true));
    }

    private function CleanMenu($menus)
    {
        foreach ($menus as $key => &$menu)
        {
            if (!$this->checkAccessCallback($menu->getUrl()))
            {
                unset($menus[$key]);
            }
            else if ($menu->hasChildren())
            {
                $menu->setChildren($this->CleanMenu($menu->getChildren()));
            }
        }

        return $menus;
    }

    private function checkAccessCallback($url)
    {
        $retval = false;
        $request = Request::create($url);
        list($roles, $channels) = $this->accessMap->getPatterns($request);
        if(count($roles) == 0)
        {
            $retval = true;
        }
        else
        {
            foreach($roles as $role)
            {
                if($this->sec->isGranted($role))
                {
                    $retval = true;
                    break;
                }
            }
        }

        return $retval;
    }
}