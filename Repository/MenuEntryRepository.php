<?php

namespace matuck\MenuBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use matuck\MenuBundle\Entity\MenuEntry;

/**
 * MenuEntryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 * {@inheritdoc}
 *
 */
class MenuEntryRepository extends NestedTreeRepository
{
    /**
     * @param MenuEntry $root
     * @param $order array of objects with with attributes id and children.  The children is an array of objects that look just olike parent
     * @return boolean
     */
    public function setTreeOrder(MenuEntry $root, $order)
    {
        $em = $this->getEntityManager();
        $previous = null;
        foreach($order as $currorder)
        {
            /** @var MenuEntry $entry */
            $entry = $this->findOneById($currorder->id);
            $entry->setParent($root);
            if($previous == null)
            {
                $this->persistAsFirstChildOf($entry, $root);
            }
            else
            {
                $this->persistAsNextSiblingOf($entry, $previous);
            }
            $em->flush();
            $previous = $entry;
            if(property_exists($currorder, 'children') && (count($currorder->children) > 0))
            {
                $this->setTreeOrder($entry, $currorder->children);
            }
        }
    }
}
