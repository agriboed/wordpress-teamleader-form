<?php

namespace Teamleader\Helpers;

class FormsHelper extends AbstractHelper
{
    /**
     * @param $data
     * @return int
     */
    public function createForm($data)
    {
        $forms = OptionsHelper::getForms();
        $last_id = OptionsHelper::getLastFromId();
        $last_id++;
        $forms[$last_id] = $data;

        OptionsHelper::setForms($forms);

        return $last_id;
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteForm($id)
    {
        $forms = OptionsHelper::getForms();

        foreach ($forms as $key => $form) {
            if ((int)$key === (int)$id) {
                unset($forms[$key]);
            }
        }

        return OptionsHelper::setForms($forms);
    }

    /**
     * @param $id
     * @return null
     */
    public function buildForm($id) {
        $forms = OptionsHelper::getForms();

        $form = null;

        if (!isset($forms[$id]))
        {
            return null;
        }


    }
}