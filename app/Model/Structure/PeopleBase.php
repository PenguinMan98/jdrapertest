<?php
/**
* AUTO-GENERATED
* DO NOT EDIT
*/
class Model_Structure_PeopleBase
{
    protected $m_id;
    protected $m_first_name;
    protected $m_last_name;
    protected $m_id_Orig;

    public function __construct($arrData = null)
    {
        if (isset($arrData)) {
            $this->loadFromArray($arrData);
        }
        else {
        }
        return;
    }
    public function PeopleBase($arrData = null)
    {
        $this->__construct($arrData);
        return;
    }

    public function getId()
    {
        return $this->m_id;
    }
    public function setId($value)
    {
        $this->m_id = $value;
        $this->setOrigId($value);
        return;
    }

    public function getFirstName()
    {
        return $this->m_first_name;
    }
    public function setFirstName($value)
    {
        $this->m_first_name = $value;
        return;
    }

    public function getLastName()
    {
        return $this->m_last_name;
    }
    public function setLastName($value)
    {
        $this->m_last_name = $value;
        return;
    }

    public function getOrigId()
    {
        return $this->m_id_Orig;
    }
    public function setOrigId($value)
    {
        if (isset($this->m_id_Orig)) { return; }
        $this->m_id_Orig = $value;
        return;
    }

    public function loadFromArray($arrValues)
    {
        $this->setId($arrValues['id']);
        $this->setFirstName($arrValues['first_name']);
        $this->setLastName($arrValues['last_name']);
        return;
    }

    public function updateFromArray($arrValues)
    {
        foreach ($arrValues as $key=>$val) {
            switch ($key) {
                case 'id':
                    $this->setId($val);
                    break;
                case 'first_name':
                    $this->setFirstName($val);
                    break;
                case 'last_name':
                    $this->setLastName($val);
                    break;
                default:
                    break;
            }
        }
        return;
    }

    public function getAsArray()
    {
        $arrValues = array();
        $arrValues['id'] = $this->getId();
        $arrValues['first_name'] = $this->getFirstName();
        $arrValues['last_name'] = $this->getLastName();
        return $arrValues;
    }

    public function validateInsert(&$arrErrors)
    {
        return true;
    }

    public function validateUpdate(&$arrErrors)
    {
        return true;
    }
}
