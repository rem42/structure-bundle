<?php
namespace Lyssal\StructureBundle\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;

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
        
        $requete = $this->processQueryBuilderExtras($requete, $extras);
        $requete = $this->processQueryBuilderConditions($requete, $conditions);
        $requete = $this->processQueryBuilderOrderBy($requete, $orderBy);
        $requete = $this->processQueryBuilderMaxResults($requete, $limit);
        $requete = $this->processQueryBuilderFirstResult($requete, $offset);

        return $requete->getQuery();
    }
    /**
     * Traite les extras pour la requête.
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param array $extras Extras
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderExtras(QueryBuilder $queryBuilder, array $extras)
    {
        if (isset($extras['innerJoins']))
        {
            foreach ($extras['innerJoins'] as $innerJoin => $innerJoinAlias)
            {
                if (false === strpos($innerJoin, '.'))
                    $queryBuilder->innerJoin('entite.'.$innerJoin, $innerJoinAlias);
                else $queryBuilder->innerJoin($innerJoin, $innerJoinAlias);
            }
        }

        if (isset($extras['likes']))
        {
            foreach ($extras['likes'] as $champ => $resultat)
            {
                $champLabel = str_replace('.', '_', $champ);
            
                if (false === strpos($champ, '.'))
                {
                    $queryBuilder
                        ->andWhere('entite.'.$champ.' LIKE :'.$champLabel)
                        ->setParameter($champLabel, $resultat)
                    ;
                }
                else
                {
                    $queryBuilder
                        ->andWhere($champ.' LIKE :'.$champLabel)
                        ->setParameter($champLabel, $resultat)
                    ;
                }
            }
        }
        
        return $queryBuilder;
    }
    /**
     * Traite les extras pour la requête.
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param array $conditions Conditions de la recherche
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderConditions(QueryBuilder $queryBuilder, array $conditions)
    {
        foreach ($conditions as $conditionPropriete => $conditionValeur)
        {
            $conditionValeurLabel = str_replace('.', '_', $conditionPropriete);
            
            if (false === strpos($conditionPropriete, '.'))
                $queryBuilder->andWhere('entite.'.$conditionPropriete.' = :'.$conditionValeurLabel);
            else $queryBuilder->andWhere($conditionPropriete.' = :'.$conditionValeurLabel);
                
            $queryBuilder->setParameter($conditionValeurLabel, $conditionValeur);
        }
        
        return $queryBuilder;
    }
    /**
     * Traite les OrderBy pour la requête.
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param array|NULL $orderBy OrderBys
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderOrderBy(QueryBuilder $queryBuilder, array $orderBy = null)
    {
        if (null !== $orderBy)
        {
            foreach ($orderBy as $propriete => $orderSens)
            {
                if (is_int($propriete))
                    $queryBuilder->addOrderBy('entite.'.$orderSens, 'ASC');
                else $queryBuilder->addOrderBy('entite.'.$propriete, $orderSens);
            }
        }
        
        return $queryBuilder;
    }
    /**
     * Traite les OrderBy pour la requête.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param integer|NULL $limit Limite des résultats
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderMaxResults(QueryBuilder $queryBuilder, $limit = null)
    {
        if (null !== $limit)
            $queryBuilder->setMaxResults($limit);
    
        return $queryBuilder;
    }
    /**
     * Traite les OrderBy pour la requête.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param integer|NULL $offset Offset
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderFirstResult(QueryBuilder $queryBuilder, $offset = null)
    {
        if (null !== $offset)
            $queryBuilder->setFirstResult($offset);
    
        return $queryBuilder;
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
        $adapter = new \Pagerfanta\Adapter\DoctrineORMAdapter($this->getQueryBuilderFindBy($conditions, $orderBy, null, null, $extras), false);
        $pagerFanta = new \Pagerfanta\Pagerfanta($adapter);
        $pagerFanta->setMaxPerPage($nombreResultatsParPage);
        $pagerFanta->setCurrentPage($currentPage);

        return $pagerFanta;
    }
}
