<?php
namespace Lyssal\StructureBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Lyssal\StructureBundle\Repository\EntityRepository;

/**
 * Classe de base des managers.
 *
 * @author Rémi Leclerc
 */
abstract class Manager
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface EntityManager
     */
    private $entityManager;
    
    /**
     * @var \Doctrine\ORM\EntityRepository EntityRepository
     */
    private $repository;
    
    /**
     * @var string Classe de l'entité
     */
    private $class;

    
    /**
     * Constructeur du manager de base.
     * 
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager EntityManager
     * @param string                               $class         Classe de l'entité
     */
    public function __construct(EntityManagerInterface $entityManager, $class)
    {
        $this->entityManager = $entityManager;
        $this->class = $class;
        
        $this->repository = $this->entityManager->getRepository($this->class);
    }
    
    
    /**
     * Retourne le EntityRepository.
     * 
     * @return \Doctrine\ORM\EntityRepository Le repository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Retourne un tableau d'entités.
     *
     * @param array        $conditions Conditions de la recherche
     * @param array|NULL   $orderBy    Tri des résultats
     * @param integer|NULL $limit      Limite des résultats
     * @param integer|NULL $offset     Offset
     * @param array        $extras     Extras
     * @return array Entités
     */
    public function findBy(array $conditions, array $orderBy = null, $limit = null, $offset = null, $extras = array())
    {
        return $this->getRepository()->getQueryBuilderFindBy($conditions, $orderBy, $limit, $offset, $extras)->getQuery()->getResult();
    }
    
    /**
     * Retourne un tableau d'entités en effectuant une recherche avec des "%LIKE%".
     *
     * @param array $conditions Conditions de la recherche
     * @param array|NULL $orderBy Tri des résultats
     * @param integer|NULL $limit Limite des résultats
     * @param integer|NULL $offset Offset
     * @return array Entités
     */
    public function findLikeBy(array $conditions, array $orderBy = null, $limit = null, $offset = null)
    {
        $likes = array(EntityRepository::AND_WHERE => array());
        foreach ($conditions as $i => $condition)
            $likes[EntityRepository::AND_WHERE][] = array(EntityRepository::WHERE_LIKE => array($i => $condition));

        return $this->getRepository()->getQueryBuilderFindBy($likes, $orderBy, $limit, $offset)->getQuery()->getResult();
    }
    
    /**
     * Retourne une entité.
     *
     * @param array $conditions Conditions de la recherche
     * @param array $extras     Extras
     * @return object|NULL L'entité ou NIL si rien trouvé
     */
    public function findOneBy(array $conditions, $extras = array())
    {
        if (count($extras) > 0)
            return $this->getRepository()->getQueryBuilderFindBy($conditions, null, null, null, $extras)->getQuery()->getSingleResult();
        return $this->getRepository()->findOneBy($conditions);
    }
    
    /**
     * Retourne une entité avec son identifiant.
     *
     * @param mixed $id     L'identifiant
     * @param array $extras Extras
     * @return object|NULL L'entité ou NIL si rien trouvé
     */
    public function findOneById($id, $extras = array())
    {
        $identifierFieldName = 'id';
        $classMetadata = $this->entityManager->getClassMetadata($this->class);
        if (method_exists($classMetadata, 'getSingleIdentifierFieldName'))
            $identifierFieldName = $classMetadata->getSingleIdentifierFieldName();
        
        if (count($extras) > 0)
            return $this->getRepository()->getQueryBuilderFindBy(array($identifierFieldName => $id), null, null, null, $extras)->getQuery()->getSingleResult();
        return $this->entityManager->find($this->class, $id);
    }
    
    /**
     * Retourne toutes les entités.
     *
     * @return array Les entités
     */
    public function findAll()
    {
        return $this->getRepository()->findAll();
    }

    
    /**
     * Retourne un entité vierge.
     * 
     * @return object Nouvelle entité
     */
    public function create()
    {
        return new $this->class;
    }

    /**
     * Enregistre une ou plusieurs entités.
     *
     * @param object|array<object> $donnees Une entité ou un tableau d'entités
     * @return void
     */
    public function save($entites)
    {
        $this->persist($entites);
        $this->flush();
    }
    
    /**
     * Persiste une ou plusieurs entités.
     * 
     * @param object|array<object> $donnees Une entité ou un tableau d'entités
     * @return void
     */
    public function persist($entites)
    {
        if (is_array($entites))
        {
            foreach ($entites as $entite)
                $this->entityManager->persist($entite);
        }
        else $this->entityManager->persist($entites);
    }
    
    /**
     * Flush.
     * 
     * @return void
     */
    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * Détache tous les objets.
     *
     * @return void
     */
    public function clear()
    {
        $this->entityManager->clear($this->class);
    }

    /**
     * Supprime une ou plusieurs entités.
     *
     * @param object|array<object> $donnees Une entité ou un tableau d'entités
     * @return void
     */
    public function remove($entites)
    {
        if (is_array($entites))
        {
            foreach($entites as $entite)
                $this->entityManager->remove($entite);
        }
        else $this->entityManager->remove($entites);
    
        $this->flush();
    }

    /**
     * Supprime toutes plusieurs entités.
     *
     * @param boolean $initAutoIncrement Initialise ou pas l'AUTO_INCREMENT à 1
     * @return void
     */
    public function removeAll($initAutoIncrement = false)
    {
        $this->remove($this->findAll());
        if (true === $initAutoIncrement)
            $this->initAutoIncrement();
    }
    
    /**
     * Effectue un TRUNCATE sur la table (ne fonctionne pas si la table possède des contraintes).
     * 
     * @param boolean $initAutoIncrement Initialise ou pas l'AUTO_INCREMENT à 1
     * @return void
     */
    public function truncate($initAutoIncrement = false)
    {
        $this->entityManager->getConnection()->prepare('TRUNCATE TABLE '.$this->getTableName())->execute();
        if (true === $initAutoIncrement)
            $this->initAutoIncrement();
    }

    /**
     * Spécifie le nouveau AUTO_INCREMENT de l'identifiant de la table à 1.
     */
    public function initAutoIncrement()
    {
        $this->setAutoIncrement(1);
    }

    /**
     * Spécifie le nouveau AUTO_INCREMENT de l'identifiant de la table.
     * 
     * @param integer $autoIncrement Valeur de l'AUTO_INCREMENT
     */
    public function setAutoIncrement($autoIncrement)
    {
        $this->entityManager->getConnection()->prepare('ALTER TABLE '.$this->getTableName().' auto_increment = '.$autoIncrement)->execute();
    }
    
    /**
     * Retourne le nom de la table en base de données.
     * 
     * @return string Nom de la table
     */
    public function getTableName()
    {
        return $this->entityManager->getMetadataFactory()->getMetadataFor($this->repository->getClassName())->getTableName();
    }

    /**
     * Retourne si l'entité gérée possède un champ.
     *
     * @param string $fieldName Nom du champ
     * @return boolean Vrai si le champ existe
     */
    public function hasField($fieldName)
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $entityMetadata)
        {
            if ($entityMetadata->hasField($fieldName))
                return true;
        }
    
        return false;
    }
    /**
     * Retourne si l'entité gérée possède une association.
     * 
     * @param string $fieldName Nom de l'association
     * @return boolean Vrai si l'association existe
     */
    public function hasAssociation($fieldName)
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $entityMetadata)
        {
            if ($entityMetadata->hasAssociation($fieldName))
                return true;
        }
        
        return false;
    }
}
