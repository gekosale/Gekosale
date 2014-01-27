<?php

namespace FormEngine\Rules;

class Required extends \FormEngine\Rule
{

    protected function _Check($value)
    {
        if (is_array($value)) {
            return !empty($value);
        } else {
            if (strlen($value) > 0) {
                return true;
            }

            return false;
        }
    }
}
