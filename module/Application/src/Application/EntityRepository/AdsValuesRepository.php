<?php

namespace Application\EntityRepository;


use Application\Entity\AdsValues;
use Application\Entity\CategoryAttributesValues;
use Doctrine\ORM\EntityRepository;

class AdsValuesRepository extends EntityRepository
{
    public function saveValues($props, $ads)
    {
        if (!empty($props)) {
            foreach ($props as $id => $values) {
                if (!is_array($values)) {
                    $exist = $this->_em
                        ->getRepository('Application\Entity\CategoryAttributesValues')
                        ->findOneBy(array('attrid' => $id, 'id' => $values));

                    if (!is_object($exist)) {
                        $attr = $this->_em
                            ->find('Application\Entity\CategoryAttributes', $id);

                        $attrValue = new CategoryAttributesValues();
                        $attrValue->setAttrid($attr)
                            ->setValue($values)
                            ->setOwner('user');

                        $this->_em->persist($attrValue);
                        $this->_em->flush($attrValue);

                        $exist = $attrValue;
                    }

                    $adsValue = new AdsValues();
                    $adsValue->setAdsid($ads)
                        ->setValueid($exist);

                    $this->_em->persist($adsValue);
                } else {
                    foreach ($values as $value) {
                        $exist = $this->_em
                            ->getRepository('Application\Entity\CategoryAttributesValues')
                            ->findOneBy(array('attrid' => $id, 'id' => $value));

                        $adsValue = new AdsValues();
                        $adsValue->setAdsid($ads)
                            ->setValueid($exist);

                        $this->_em->persist($adsValue);
                    }
                }
            }
            $this->_em->flush();
        }
    }

}