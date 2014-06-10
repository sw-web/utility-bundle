<?php
namespace Sw\UtilityBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Evoucher\DataBundle\Entity\Promotion;
use Evoucher\DataBundle\Entity\CronHistory;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Cron History entity repository
 *
 * @package Evoucher\DataBundle
 * @author  Guillaume Petit <guillaume.petit@sword-group.com>
 */
class SearchableRepository extends EntityRepository
{
    /**
     * @var string
     */
    const ANNOTATION_CLASS = 'Sw\\UtilityBundle\\Annotation\\Searchable';

    /**
     * Add a where assertion to a query builder by looking for searchable fields in the repository entity
     *
     * @param QueryBuilder $builder    the query builder
     * @param string       $predicates the string to be searched
     *
     * @return QueryBuilder
     */
    public function search(QueryBuilder $builder, $predicates)
    {
        // Create an annotation reader
        $reader = new AnnotationReader();

        // Create a new OR expression
        $where = $builder->expr()->orX();

        // Get entity fields
        $reflectionEntity = new \ReflectionClass($this->getClassName());
        $fields = $reflectionEntity->getProperties();

        foreach ($fields as $field) {
            // Look for the Searchable annotation on the field
            $searchable = $reader->getPropertyAnnotation($field, self::ANNOTATION_CLASS);
            if ($searchable) {
                // If the field is searchable, add a like condition in the OR expression
                $where->add($builder->expr()->like($builder->getRootAlias() . '.' . $field->getName(), $builder->expr()->literal($predicates)));
            }
        }

        // Add the or expression to the query builder
        $builder->andWhere($where);

        // Return the builder
        return $builder;
    }
}