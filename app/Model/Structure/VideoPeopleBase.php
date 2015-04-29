<?php
/**
* AUTO-GENERATED
* DO NOT EDIT
*/
class Model_Structure_VideoPeopleBase
{
    protected $m_video_id;
    protected $m_people_id;
    protected $m_relationship;
    protected $m_video_id_Orig;
    protected $m_people_id_Orig;
    protected $m_relationship_Orig;

    public function __construct($arrData = null)
    {
        if (isset($arrData)) {
            $this->loadFromArray($arrData);
        }
        else {
        }
        return;
    }
    public function VideoPeopleBase($arrData = null)
    {
        $this->__construct($arrData);
        return;
    }

    public function getVideoId()
    {
        return $this->m_video_id;
    }
    public function setVideoId($value)
    {
        $this->m_video_id = $value;
        $this->setOrigVideoId($value);
        return;
    }

    public function getPeopleId()
    {
        return $this->m_people_id;
    }
    public function setPeopleId($value)
    {
        $this->m_people_id = $value;
        $this->setOrigPeopleId($value);
        return;
    }

    public function getRelationship()
    {
        return $this->m_relationship;
    }
    public function setRelationship($value)
    {
        $this->m_relationship = $value;
        $this->setOrigRelationship($value);
        return;
    }

    public function getOrigVideoId()
    {
        return $this->m_video_id_Orig;
    }
    public function setOrigVideoId($value)
    {
        if (isset($this->m_video_id_Orig)) { return; }
        $this->m_video_id_Orig = $value;
        return;
    }

    public function getOrigPeopleId()
    {
        return $this->m_people_id_Orig;
    }
    public function setOrigPeopleId($value)
    {
        if (isset($this->m_people_id_Orig)) { return; }
        $this->m_people_id_Orig = $value;
        return;
    }

    public function getOrigRelationship()
    {
        return $this->m_relationship_Orig;
    }
    public function setOrigRelationship($value)
    {
        if (isset($this->m_relationship_Orig)) { return; }
        $this->m_relationship_Orig = $value;
        return;
    }

    public function loadFromArray($arrValues)
    {
        $this->setVideoId($arrValues['video_id']);
        $this->setPeopleId($arrValues['people_id']);
        $this->setRelationship($arrValues['relationship']);
        return;
    }

    public function updateFromArray($arrValues)
    {
        foreach ($arrValues as $key=>$val) {
            switch ($key) {
                case 'video_id':
                    $this->setVideoId($val);
                    break;
                case 'people_id':
                    $this->setPeopleId($val);
                    break;
                case 'relationship':
                    $this->setRelationship($val);
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
        $arrValues['video_id'] = $this->getVideoId();
        $arrValues['people_id'] = $this->getPeopleId();
        $arrValues['relationship'] = $this->getRelationship();
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
