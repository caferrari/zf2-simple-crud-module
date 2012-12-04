<?php

namespace Crud\Repository;

use Doctrine\ORM\EntityRepository;

abstract class AbstractRepository extends EntityRepository
{

    protected $pairColumn = 'nome';

    public function findAll()
    {
        $query = $this->getEntityManager()->createQuery($this->listQuery);
        return $query->getResult();
    }

    public function insert(array $data)
    {
        $entity = $this->createEntity($data);
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
        return $entity;
    }

    public function update(array $data)
    {
        $entity = $this->getReference($data['id']);
        $entity->setData($data);

        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function delete($id)
    {
        $entity = $this->getReference($id);
        if ($entity) {
            $em = $this->getEntityManager();
            $em->remove($entity);
            $em->flush();
            return $entity;
        }
    }

    public function fetchPairs()
    {
        $entities = $this->findAll();
        $list = array();
        foreach ($entities as $item) {
            $list[$item->id] = $item->{$this->pairColumn};
        }
        return $list;
    }

    protected function createEntity(array $data)
    {
        $entity = $this->getEntityName();
        return new $entity($data);
    }

    protected function getReference($id, $entity = null)
    {
        if (null === $entity) {
            $entity = $this->getEntityName();
        }
        return $this->getEntityManager()->getReference($entity, $id);
    }

}