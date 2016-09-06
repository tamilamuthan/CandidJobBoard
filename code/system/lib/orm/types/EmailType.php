<?php

class SJB_EmailType extends SJB_Type
{
    public function __construct($property_info)
    {
        parent::__construct($property_info);
        $this->default_template = 'email.tpl';
    }

    function getPropertyVariablesToAssign()
    {
        return array(
            'id' => $this->property_info['id'],
            'value' => $this->property_info['value'],
        );
    }

    function isValid()
    {
        if (!preg_match("/^[a-zA-Z0-9\\._-]+@[a-zA-Z0-9\\._-]+\\.[a-zA-Z]{2,}$/", $this->property_info['value'])) {
            return 'NOT_VALID_EMAIL_FORMAT';
        }
        return true;
    }
}
