<?php
namespace Lyssal\StructureBundle\Repository;

use Doctrine\ORM\EntityRepository as BaseEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Doctrine\ORM\AbstractQuery;
use Lyssal\Chaine;

/**
 * Classe de base des repository.
 *
 * @author Rémi Leclerc
 */
class EntityRepository extends BaseEntityRepository
{
    /**
     * @var string Extra pour ajouter des addSelect()
     */
    const SELECTS = 'selects';
    
    /**
     * @var string Extra pour ajouter des leftJoin()
     */
    const LEFT_JOINS = 'leftJoins';
    
    /**
     * @var string Extra pour ajouter des innerJoin()
     */
    const INNER_JOINS = 'innerJoins';
    
    /**
     * @var string Extra pour ajouter des andGroupBy()
     */
    const GROUP_BYS = 'groupBys';
    
    /**
     * @var string Utilisé pour les extras SELECT pour ajouter l'entité d'une jointure à l'entité principale
     */
    const SELECT_JOIN = '__SELECT_JOIN__';
    
    /**
     * @var string Utilisé pour les (x OR y OR ...)
     */
    const OR_WHERE = '__OR_WHERE__';
    
    /**
     * @var string Utilisé pour les (x AND y AND ...)
     */
    const AND_WHERE = '__AND_WHERE__';
    
    /**
     * @var string Utilisé pour un WHERE ... LIKE ...
     */
    const WHERE_LIKE = '__LIKE__';
    
    /**
     * @var string Utilisé pour un WHERE ... IN (...)
     */
    const WHERE_IN = '__IN__';
    
    /**
     * @var string Utilisé pour un WHERE ... IS NULL
     */
    const WHERE_NULL = '__IS_NULL__';
    
    /**
     * @var string Utilisé pour un WHERE ... IS NOT NULL
     */
    const WHERE_NOT_NULL = '__IS_NOT_NULL__';

    
    /**
     * Retourne le nom de l'identifiant unique de l'entité.
     * 
     * @return string Identifiant
     */
    public function getSingleIdentifierFieldName()
    {
        return $this->getClassMetadata()->getSingleIdentifierFieldName();
    }
    
    
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
        $requete = $this->createQueryBuilder('entity');
        
        $requete = $this->processQueryBuilderExtras($requete, $extras);
        $requete = $this->processQueryBuilderConditions($requete, $conditions);
        $requete = $this->processQueryBuilderOrderBy($requete, $orderBy);
        $requete = $this->processQueryBuilderMaxResults($requete, $limit);
        $requete = $this->processQueryBuilderFirstResult($requete, $offset);

        return $requete;
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
        if (isset($extras[self::SELECTS]))
        {
            foreach ($extras[self::SELECTS] as $select => $selectAlias)
            {
                if (self::SELECT_JOIN == $selectAlias)
                    $queryBuilder->addSelect($select);
                elseif (false === strpos($select, '.') && false === strpos($select, '('))
                    $queryBuilder->addSelect('entity.'.$select.' AS '.$selectAlias);
                else $queryBuilder->addSelect($select.' AS '.$selectAlias);
            }
        }
        
        if (isset($extras[self::LEFT_JOINS]))
        {
            foreach ($extras[self::LEFT_JOINS] as $leftJoin => $leftJoinAlias)
            {
                if (false === strpos($leftJoin, '.'))
                    $queryBuilder->leftJoin('entity.'.$leftJoin, $leftJoinAlias);
                else $queryBuilder->leftJoin($leftJoin, $leftJoinAlias);
            }
        }
        
        if (isset($extras[self::INNER_JOINS]))
        {
            foreach ($extras[self::INNER_JOINS] as $innerJoin => $innerJoinAlias)
            {
                if (false === strpos($innerJoin, '.'))
                    $queryBuilder->innerJoin('entity.'.$innerJoin, $innerJoinAlias);
                else $queryBuilder->innerJoin($innerJoin, $innerJoinAlias);
            }
        }

        if (isset($extras[self::GROUP_BYS]))
        {
            foreach ($extras[self::GROUP_BYS] as $groupBy)
            {
                if (false !== strpos($groupBy, '.') || (isset($extras[self::SELECTS]) && in_array($groupBy, array_values($extras[self::SELECTS]))))
                    $queryBuilder->addGroupBy($groupBy);
                else $queryBuilder->addGroupBy('entity.'.$groupBy);
            }
        }
        
        return $queryBuilder;
    }
    
    /**
     * Traite les conditions de la requête.
     * 
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param array $conditions Conditions de la recherche
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder à jour
     */
    private function processQueryBuilderConditions(QueryBuilder $queryBuilder, array $conditions)
    {
        foreach ($conditions as $conditionPropriete => $conditionValeur)
        {
            $queryBuilder->andWhere($this->processQueryBuilderCondition($queryBuilder, $conditionPropriete, $conditionValeur));
        }
    
        return $queryBuilder;
    }
    /**
     * Traite une condition de la requête et la retourne.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder       QueryBuilder
     * @param string                     $conditionPropriete Nom de la propriété de la condition
     * @param string|array               $conditionValeur    Valeur(s) de la condition
     * @return string|\Query\Expr\Orx QueryBuilder à jour
     */
    private function processQueryBuilderCondition(QueryBuilder &$queryBuilder, $conditionPropriete, $conditionValeur)
    {
        if (is_int($conditionPropriete))
        {
            if (!is_array($conditionValeur) || count($conditionValeur) != 1)
                throw new \Exception('La valeur doit être un tableau associatif d\'une seule valeur.');
            foreach ($conditionValeur as $condition => $valeur)
                return $this->processQueryBuilderCondition($queryBuilder, $condition, $valeur);
        }
        
        if (self::OR_WHERE == $conditionPropriete)
        {
            $conditionsOr = array();
            foreach ($conditionValeur as $conditionOrPropriete => $conditionOrValeur)
                $conditionsOr[] = $this->processQueryBuilderCondition($queryBuilder, $conditionOrPropriete, $conditionOrValeur);

            return call_user_func_array(array($queryBuilder->expr(), 'orX'), $conditionsOr);
        }
        elseif (self::AND_WHERE == $conditionPropriete)
        {
            $conditionsAnd = array();
            foreach ($conditionValeur as $conditionOrPropriete => $conditionOrValeur)
                $conditionsAnd[] = $this->processQueryBuilderCondition($queryBuilder, $conditionOrPropriete, $conditionOrValeur);

            return call_user_func_array(array($queryBuilder->expr(), 'andX'), $conditionsAnd);
        }
        elseif (self::WHERE_LIKE == $conditionPropriete)
        {
            if (!is_array($conditionValeur) || count($conditionValeur) != 1)
                throw new \Exception('La valeur d\'un WHERE_LIKE doit être un tableau associatif d\'une seule valeur.');
            
            foreach ($conditionValeur as $likePropriete => $likeValeur)
            {
                $conditionValeurLabel = new Chaine($likePropriete.' '.$likeValeur);
                $conditionValeurLabel->minifie('_');
                $queryBuilder->setParameter($conditionValeurLabel->getTexte(), $likeValeur);
                
                if (false === strpos($likePropriete, '.') && property_exists($this->_class->getName(), $likePropriete))
                    return 'entity.'.$likePropriete.' LIKE :'.$conditionValeurLabel->getTexte();
                else return $likePropriete.' LIKE :'.$conditionValeurLabel->getTexte();
            }
        }
        elseif (self::WHERE_IN == $conditionPropriete)
        {
            if (!is_array($conditionValeur) || count($conditionValeur) != 1)
                throw new \Exception('La valeur d\'un WHERE_IN doit être un tableau associatif d\'une seule valeur.');
            
            foreach ($conditionValeur as $likePropriete => $likeValeur)
            {
                return call_user_func_array(array($queryBuilder->expr(), 'in'), array($likePropriete, $likeValeur));
            }
        }
        elseif (self::WHERE_NULL == $conditionValeur)
        {
            return call_user_func_array(array($queryBuilder->expr(), 'isNull'), array((false === strpos($conditionPropriete, '.') ? 'entity.' : '').$conditionPropriete));
        }
        elseif (self::WHERE_NOT_NULL == $conditionPropriete)
        {
            return call_user_func_array(array($queryBuilder->expr(), 'isNotNull'), array((false === strpos($conditionPropriete, '.') ? 'entity.' : '').$conditionPropriete));
        }
        else
        {
            $conditionString = $this->getQueryBuilderConditionString($conditionPropriete);
            $queryBuilder->setParameter($conditionString[1], $conditionValeur);
            return $conditionString[0];
        }
    }
    /**
     * Retourne la chaîne de condition ainsi que le nom du paramètre.
     *
     * @param string $conditionPropriete Propriété de la condition
     * @param string $conditionValeur    Valeur de la condition
     * @return array<string, string> Chaîne de la condition et paramêtres pour la QueryBuilder
     */
    private function getQueryBuilderConditionString($conditionPropriete)
    {
        $conditionValeurLabel = new Chaine($conditionPropriete);
        $conditionValeurLabel->minifie('_');

        if (false === strpos($conditionPropriete, '.') && property_exists($this->_class->getName(), $conditionPropriete))
            return array('entity.'.$conditionPropriete.' = :'.$conditionValeurLabel->getTexte(), $conditionValeurLabel->getTexte());
        else return array($conditionPropriete.' = :'.$conditionValeurLabel->getTexte(), $conditionValeurLabel->getTexte());
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
                if (is_int($propriete)) // Tableau non associatif
                    $queryBuilder->addOrderBy(($this->hasField($orderSens) ? 'entity.' : '').$orderSens, 'ASC');
                else $queryBuilder->addOrderBy(($this->hasField($propriete) ? 'entity.' : '').$propriete, $orderSens);
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
     * Retourne un résultat traduit ou NIL si non trouvé.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     * @param string $hydrationMode Hydration mode
     *
     * @return mixed Résultat
     */
    public function getOneOrNullTranslatedResult(QueryBuilder $queryBuilder, $locale, $hydrationMode = null)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getOneOrNullResult($hydrationMode);
    }
    /**
     * Retourne des résultats traduits.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     * @param string $hydrationMode Hydration mode
     *
     * @return mixed Résultats
     */
    public function getTranslatedResult(QueryBuilder $queryBuilder, $locale, $hydrationMode = AbstractQuery::HYDRATE_OBJECT)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getResult($hydrationMode);
    }
    /**
     * Retourne des résultats traduits.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     *
     * @return array Résultats
     */
    public function getArrayTranslatedResult(QueryBuilder $queryBuilder, $locale)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getArrayResult();
    }
    /**
     * Retourne un unique résultat traduit.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     *
     * @return mixed Résultat
     */
    public function getSingleTranslatedResult(QueryBuilder $queryBuilder, $locale, $hydrationMode = null)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getSingleResult($hydrationMode);
    }
    /**
     * Retourne un unique résultat traduit.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     *
     * @return mixed Résultat
     */
    public function getScalarTranslatedResult(QueryBuilder $queryBuilder, $locale)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getScalarResult();
    }
    /**
     * Retourne un unique résultat traduit.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     *
     * @return mixed Résultat
     */
    public function getSingleScalarTranslatedResult(QueryBuilder $queryBuilder, $locale)
    {
        return $this->getTranslatedQuery($queryBuilder, $locale)->getSingleScalarResult();
    }
    /**
     * Retourne une requête pour une traduction.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder QueryBuilder
     * @param string $locale Locale
     *
     * @return \Doctrine\ORM\QueryBuilder QueryBuilder
     */
    private function getTranslatedQuery(QueryBuilder $queryBuilder, $locale)
    {
        $locale = (null === $locale ? $this->defaultLocale : $locale);
    
        $query = $queryBuilder->getQuery();
    
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
    
        $query->setHint(\Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE, $locale);
    
        return $query;
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
        if (null !== $nombreResultatsParPage)
            $pagerFanta->setMaxPerPage($nombreResultatsParPage);
        $pagerFanta->setCurrentPage($currentPage);

        return $pagerFanta;
    }
}
