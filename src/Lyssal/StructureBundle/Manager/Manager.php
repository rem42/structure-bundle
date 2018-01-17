<?php
namespace Lyssal\StructureBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Lyssal\StructureBundle\Repository\EntityRepository;

/**
 * Classe de base des managers.
 *
 * @author Rémi Leclerc
 */
class Manager
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
     * Retourne des entités.
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
        foreach ($conditions as $i => $condition) {
            $likes[EntityRepository::AND_WHERE][] = array(EntityRepository::WHERE_LIKE => array($i => $condition));
        }

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
            return $this->getRepository()->getQueryBuilderFindBy($conditions, null, null, null, $extras)->setMaxResults(1)->getQuery()->getOneOrNullResult();
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

        if (method_exists($classMetadata, 'getSingleIdentifierFieldName')) {
            $identifierFieldName = $classMetadata->getSingleIdentifierFieldName();
        }
        
        if (count($extras) > 0) {
            return $this->getRepository()->getQueryBuilderFindBy(array($identifierFieldName => $id), null, null, null, $extras)->getQuery()->getSingleResult();
        }

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
     * Retourne des entités indexées par leur identifiant.
     *
     * @param array        $conditions Conditions de la recherche
     * @param array|NULL   $orderBy    Tri des résultats
     * @param integer|NULL $limit      Limite des résultats
     * @param integer|NULL $offset     Offset
     * @param array        $extras     Extras
     * @return array<mixed, object> Entités
     */
    public function findByKeyedById(array $conditions, array $orderBy = null, $limit = null, $offset = null, $extras = array())
    {
        return $this->getEntitiesKeyedById($this->findBy($conditions, $orderBy, $limit, $offset, $extras));
    }

    /**
     * Retourne des entités en effectuant une recherche avec des "%LIKE%" indexées par leur identifiant.
     *
     * @param array $conditions Conditions de la recherche
     * @param array|NULL $orderBy Tri des résultats
     * @param integer|NULL $limit Limite des résultats
     * @param integer|NULL $offset Offset
     * @return array<mixed, object> Entités
     */
    public function findLikeByKeyedById(array $conditions, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getEntitiesKeyedById($this->findLikeBy($conditions, $orderBy, $limit, $offset));
    }

    /**
     * Retourne toutes les entités indexées par leur identifiant.
     *
     * @return array<mixed, object> Entités
     */
    public function findAllKeyedById()
    {
        return $this->getEntitiesKeyedById($this->findAll());
    }

    /**
     * Retourne un tableau d'entités indexés par leur identifiant.
     *
     * @param array<object> $entities Entités
     * @return array<mixed, object> Entités
     */
    public function getEntitiesKeyedById(array $entities)
    {
        $identifiants = $this->getIdentifier();
        if (1 !== count($identifiants)) {
            throw new \Exception('L\'entité ne doit avoir qu\'un seul identifiant.');
        }
        if (0 === count($entities)) {
            return $entities;
        }
        $identifiantAccesseur = 'get'.ucfirst($identifiants[0]);
        if (!method_exists(reset($entities), $identifiantAccesseur)) {
            throw new \Exception('L\'entité ne possède pas d\'accesseur "'.$identifiantAccesseur.'".');
        }
        $entitiesById = array();

        foreach ($entities as $entity) {
            $entitiesById[$entity->$identifiantAccesseur()] = $entity;
        }

        return $entitiesById;
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
    public function getPagerFantaFindBy(array $conditions = array(), array $orderBy = null, $nombreResultatsParPage = 20, $currentPage = 1, array $extras = array())
    {
        return $this->getRepository()->getPagerFantaFindBy($conditions, $orderBy, $nombreResultatsParPage, $currentPage, $extras);
    }

    /**
     * Retourne le nombre de lignes en base.
     * 
     * @return integer Nombre de lignes
     */
    public function count()
    {
        return $this->getRepository()->count(["class" => $this->class]);
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
     * Vérifie si l'entité existe en vérifiant que les identifiants ne sont pas à NULL. Dans le cas d'une clef étrangère, cette méthode peut retourner VRAI si l'entité vient d'être créée et la clef étrangère assignée mais que l'entité n'a pas encore été enregistrée.
     * 
     * @return boolean VRAI si l'entité existe
     */
    public function exists($entity)
    {
        if (!is_object($entity))
            'L\'entité de type '.gettype($entity).' n\'est pas un objet.';
        
        foreach ($this->getIdentifier() as $identifiant)
        {
            if (!method_exists($entity, 'get'.ucfirst($identifiant)))
                throw new \Exception('La méthode get'.ucfirst($identifiant).' n\'existe pas pour l\'objet '.get_class($entity).'.');
                
            if (null === call_user_func_array(array($entity, 'get'.ucfirst($identifiant)), array()))
                return false;
        }
        
        return true;
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
     * Retourne les noms des identifiants de l'entité.
     *
     * @return array<string> Identifiants
     */
    public function getIdentifier()
    {
        return $this->entityManager->getClassMetadata($this->class)->getIdentifier();
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
