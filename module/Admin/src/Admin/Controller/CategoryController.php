<?php

namespace Admin\Controller;

use Application\Entity\Categories;
use Zend\View\Model\JsonModel;
use Zend\Mvc\Controller\AbstractActionController;

class CategoryController extends AbstractActionController
{
    public function indexAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $id = $request->getPost('id');
            $size = 0;

            if ($id === '0') {
                $cats = $this->em()->createQuery("select c.id, c.name, c.level from Application\Entity\Categories c where c.id=c.root")
                    ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
            } else {
                $rootCat = $this->em()->find('Application\Entity\Categories', $id);
                $cats = $this->nsm('Application\Entity\Categories')
                    ->wrapNode($rootCat)
                    ->getChildren();
                $data = array();
                $size = sizeof($cats);

                for ($i = 0; $i < $size; $i++) {
                    $node = $cats[$i]->getNode();
                    $data[$i]['id'] = $node->getId();
                    $data[$i]['name'] = $node->getName();
                    $data[$i]['level'] = $node->getLevel();
                }
                $cats = $data;
            }

            return new JsonModel(array(
                'nodes' => $cats
            ));
        }
    }

    public function addAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $filter = $this->getServiceLocator()->get('Admin\Form\CategoryFilter');
            $filter->setData($request->getPost());
            $filter->setValidationGroup(array('name'));

            if ($filter->isValid()) {
                $nsm = $this->nsm('Application\Entity\Categories');
                $name = $request->getPost('name');
                $pid = $request->getPost('id');

                $newCat = new Categories();
                $newCat->setName($name);

                if (!empty($pid)) {
                    $parentCat = $this->em()->find('Application\Entity\Categories', $pid);
                    if (is_object($parentCat)) {
                        $newCat->setLevel($parentCat->getLevel() + 1);
                        $nsm->wrapNode($parentCat)->addChild($newCat);
                    }
                } else {
                    $newCat->setLevel(0);
                    $nsm->createRoot($newCat);
                }

                return new JsonModel(array(
                    'id' => $newCat->getId(),
                    'name' => $name
                ));
            } else {
                return new JsonModel(array(
                    'message' => current($filter->getMessages())
                ));
            }
        }
    }

    public function updateAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $filter = $this->getServiceLocator()->get('Admin\Form\CategoryFilter');
            $filter->setData($request->getPost());

            if ($filter->isValid()) {
                $name = $request->getPost('name');
                $id = $request->getPost('id');

                $this->em()
                    ->getConnection()
                    ->update(
                        'categories',
                        array('name' => $name),
                        array('id' => $id)
                    );

                return new JsonModel(array(
                    'id' => $id,
                    'name' => $name
                ));
            } else {
                return new JsonModel(array(
                    'message' => current($filter->getMessages())
                ));
            }
        }
    }

    public function deleteAction()
    {
        $request = $this->getRequest();

        if ($request->isXmlHttpRequest()) {
            $filter = $this->getServiceLocator()->get('Admin\Form\CategoryFilter');
            $filter->setData($request->getPost())
                ->setValidationGroup('id');

            if ($filter->isValid()) {
                $id = $request->getPost('id');

                $category = $this->em()->find('Application\Entity\Categories', $id);
                $node = $this->nsm('Application\Entity\Categories')->wrapNode($category);
                $node->delete();

                return new JsonModel(array(1));
            } else {
                return new JsonModel(array(
                    'message' => current($filter->getMessages())
                ));
            }
        }
    }
}
