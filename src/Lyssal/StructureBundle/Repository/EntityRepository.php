
<?php
namespace Lyssal\StructureBundle\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;

/**
 * Classe de base des repository.
 *
 * @author Rémi Leclerc
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * Retourne le QueryBuilder pour la méthode findBy().
     *
     * @param array $conditions Conditions de la recherche
     * @param array|NULL $orderBy Tri des résultats
     * @param integer|NULL $limit Limite des résultats
     * @param integer|NULL $offset Offset
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder
     */
    public function getQueryBuilderFindBy(array $conditions, array $orderBy = null, $limit = null, $offset = null, array $extras = array())
    {
        $requete = $this->createQueryBuilder('entite');

        if (isset($extras['innerJoins']))
        {
            foreach ($extras['innerJoins'] as $innerJoin => $innerJoinAlias)
            {
                if (false === strpos($innerJoin, '.'))
                    $requete->innerJoin('entite.'.$innerJoin, $innerJoinAlias);
                else $requete->innerJoin($innerJoin, $innerJoinAlias);
            }
        }

        foreach ($conditions as $conditionPropriete => $conditionValeur)
        {
            $conditionValeurLabel = str_replace('.', '_', $conditionPropriete);

            if (false === strpos($conditionPropriete, '.'))
$requete->andWhere('entite.'.$conditionPropriete.' = :'.$conditionValeurLabel);
            else $requete->andWhere($conditionPropriete.' = :'.$conditionValeurLabel);

            $requete->setParameter($conditionValeurLabel, $conditionValeur);
        }

        if (null !== $orderBy)
        {
            foreach ($orderBy as $propriete => $orderSens)
            {
                if (is_int($propriete))
                    $requete->addOrderBy('entite.'.$orderSens, 'ASC');
                else $requete->addOrderBy('entite.'.$propriete, $orderSens);
            }
        }

        if (null !== $limit)
            $requete->setMaxResults($limit);

        if (null !== $offset)
            $requete->setFirstResult($offset);

        return $requete->getQuery();
    }

    /**
     * Retourne le PagerFanta pour la méthode findBy().
     *
     * @param array $conditions Conditions de la recherche
     * @param array|NULL $orderBy Tri des résultats
     * @param integer $nombreResultatsParPage Nombre de résultats par page
     * @param integer $currentPage Page à afficher
     * @return \Pagerfanta\Pagerfanta Pagerfanta
     */
    public function getPagerFantaFindBy(array $conditions, array $orderBy = null, $nombreResultatsParPage = 20, $currentPage = 1, array $extras = array())
    {
        $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($this->getQueryBuilderFindBy($conditions, $orderBy, null, null, $extras));
        $pagerFanta = new \Pagerfanta\Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($nombreResultatsParPage);
        $pagerFanta->setCurrentPage($currentPage);

        return $pagerFanta;
    }
}
