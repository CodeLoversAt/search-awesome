<?php

namespace SearchAwesome\CoreBundle\Repository;
use SearchAwesome\CoreBundle\Document\Icon;
use SearchAwesome\CoreBundle\Document\Tag;

/**
 * IconRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class IconRepository extends Repository
{
    /**
     * @param string $search
     *
     * @return Icon[]
     */
    public function findByName($search)
    {
        $keywords = explode(' ', $search);
        $regexes = array();
        $soundexes = array();

        foreach ($keywords as $keyword) {
            $regexes[] = new \MongoRegex('/^' . strtolower($keyword) . '/');
            $soundex = soundex($keyword);
            if (!in_array($soundex, $soundexes)) {
                $soundexes[] = $soundex;
            }
        }

        $result = array();

        $qb = $this->createQueryBuilder();
        $qb->addOr($qb->expr()->field('tags.name')->all($regexes)->field('tags.deleted')->equals(false));
        $qb->addOr($qb->expr()->field('tags.soundex')->all($soundexes)->field('tags.deleted')->equals(false));

        foreach ($qb->getQuery()->execute() as $icon) {
            $result[] = $icon;
        }

        return $result;
    }
}