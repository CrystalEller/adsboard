<?php

namespace FormBuilder\Controller;


use Zend\Mvc\Controller\AbstractActionController;

class FormBuilderController extends AbstractActionController
{

    protected $em;

    public function getFormAction()
    {
        $response = $this->getResponse();
        $em = $this->getEntityManager();
        $values = array();
        $attrs = $em->getRepository('\Application\Entity\CategoryAttributes')
            ->findBy(array('catid' => $this->params()->fromRoute('catid')));

        foreach ($attrs as $attr) {
            $unVal = unserialize($attr->getValues());
            $unVal['id'] = $attr->getId();
            $values[] = $unVal;
        }

        $response->setContent(\Zend\Json\Json::encode(['fields' => $values]));
        return $response;
    }

    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

    public function changeFormAction()
    {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $conn = $this->getEntityManager()->getConnection();

        if ($request->isPost()) {

            $conn->beginTransaction();

            try {
                foreach ($request->getPost('fieldsActions') as $action => $data) {
                    switch ($action) {
                        case 'insertFields':
                            foreach ($data as $field) {
                                $conn->insert('category_attributes',
                                    array(
                                        'name' => $field['name'],
                                        'catid' => $this->params()->fromRoute('catid'),
                                        '`values`' => serialize($field)
                                    )
                                );
                            }
                            break;
                        case 'updateFields':
                            foreach ($data as $field) {
                                $conn->update('category_attributes',
                                    array(
                                        '`values`' => serialize($field),
                                        'name' => $field['name']
                                    ),
                                    array('id' => $field['id'])
                                );
                            }
                            break;
                        case 'deleteFields':
                            foreach ($data as $field) {
                                $conn->delete('category_attributes',
                                    array('id' => $field['id'])
                                );
                            }
                            break;
                    }
                }
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollback();
                $response->setContent(\Zend\Json\Json::encode(0));
                return $response;
            }
            $response->setContent(\Zend\Json\Json::encode(1));
            return $response;
        }
    }
}