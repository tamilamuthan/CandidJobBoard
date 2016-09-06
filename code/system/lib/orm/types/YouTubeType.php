<?php

class SJB_YouTubeType extends SJB_Type
{
    public function __construct($property_info)
    {
        parent::__construct($property_info);

        if (isset($this->property_info['value'])) {
            if (is_array($this->property_info['value'])) {
                foreach ($this->property_info['value'] as $key => $value) {
                    $this->property_info['value'][$key] = strip_tags($value);
                }
            } else {
                $this->property_info['value'] = strip_tags($this->property_info['value']);
            }
        }

        $this->default_template = 'youtube.tpl';
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
        if (preg_match('|^https?://www\.youtube\.com/watch\?v=|u', $this->property_info['value'])) {
            return true;
        }
        if (preg_match('|^https?://youtu.be/\w+|u', $this->property_info['value'])) {
            return true;
        }
        return 'NOT_CORRECT_YOUTUBE_LINK';
    }

    function getSQLValue()
    {
        return $this->property_info['value'];
    }

    function getKeywordValue()
    {
        return "";
    }

}
