<?php

namespace FormBuilder\Controller;


use Doctrine\ORM\Query;
use Zend\Mvc\Controller\AbstractActionController;
use \Doctrine\DBAL\Connection;
use Zend\View\Model\JsonModel;

class FormBuilderController extends AbstractActionController
{

    public function getFormAction()
    {
        $data = array();
        $categoryId = $this->params()->fromRoute('catid');
        $attributes = $this->em()
            ->getRepository('Application\Entity\CategoryAttributes')
            ->getAttrsByCategoryId($categoryId)
            ->getResult(Query::HYDRATE_ARRAY);

        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                $values = $this->em()
                    ->getRepository('Application\Entity\CategoryAttributesValues')
                    ->getValuesByAttrId($attribute['id'], 'admin')
                    ->getResult(Query::HYDRATE_ARRAY);
                if (!empty($values)) {
                    $attribute['values']['values'] = $values;
                    $attribute['values']['id'] = $attribute['id'];
                    $data[] = $attribute['values'];
                } else {
                    $attribute['values']['id'] = $attribute['id'];
                    $data[] = $attribute['values'];
                }
            }
        }

        return new JsonModel(array(
            'fields' => $data
        ));
    }

    public function changeFormAction()
    {
        $request = $this->getRequest();
        $conn = $this->em()->getConnection();

        if ($request->isPost()) {

            $conn->beginTransaction();

            try {
                foreach ($request->getPost('fieldsActions') as $action => $data) {
                    switch ($action) {
                        case 'insertFields':
                            $this->insertFields($conn, $data);
                            break;
                        case 'updateFields':
                            $this->updateFields($conn, $data);
                            break;
                        case 'deleteFields':
                            $this->deleteFields($conn, $data);
                            break;
                    }
                }
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollback();
                return new JsonModel(array(0));
            }
            return new JsonModel(array(1));
        }
    }

    private function insertFields(Connection $conn, array $data)
    {
        foreach ($data as $field) {
            $values = array_diff_key($field, array('name' => 0, 'values' => 0));

            $conn->insert('category_attributes',
                array(
                    'name' => $field['name'],
                    'catid' => $this->params()->fromRoute('catid'),
                    '`values`' => serialize($values)
                )
            );
            $lastId = $conn->lastInsertId();

            if (!empty($field['values'])) {
                foreach ($field['values'] as $value) {
                    $conn->insert('category_attributes_values',
                        array(
                            'attrid' => $lastId,
                            '`value`' => $value['value'],
                            'owner' => 'admin'
                        )
                    );
                }
            }
        }
    }

    private function updateFields(Connection $conn, array $data)
    {
        foreach ($data as $field) {
            $values = array_diff_key($field, array('name' => 0, 'values' => 0));

            $conn->update('category_attributes',
                array(
                    '`values`' => serialize($values),
                    'name' => $field['name']
                ),
                array('id' => $field['id'])
            );

            if (!empty($field['values'])) {
                foreach ($field['values'] as $value) {
                    if ($value['status'] === 0) {
                        $conn->insert('category_attributes_values',
                            array(
                                'attrid' => $field['id'],
                                '`value`' => $value['value'],
                                'owner' => 'admin'
                            )
                        );
                    } elseif ($value['status'] === 1) {
                        $conn->update('category_attributes_values',
                            array(
                                '`value`' => $value['value']
                            ),
                            array('id' => $value['id'])
                        );
                    } else {
                        $conn->delete('category_attributes_values',
                            array('id' => $value['id'])
                        );
                    }
                }
            }
        }
    }

    private function deleteFields(Connection $conn, array $data)
    {
        foreach ($data as $field) {
            $conn->delete('category_attributes',
                array('id' => $field['id'])
            );
        }
    }
}