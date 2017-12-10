<?php

namespace Teamleader\Helpers;

use Teamleader\DependencyInjection\Container;

class FormsHelper extends AbstractHelper
{
    /**
     * @param $data
     * @return int
     * @throws \Exception
     * @throws \LogicException
     */
    public function createForm($data)
    {
        $forms = OptionsHelper::getForms();
        $last_id = OptionsHelper::getLastFromId();
        $last_id++;

        $data = $this->processData($data);

        $forms[$last_id] = $data;
        OptionsHelper::setForms($forms);

        print_r($data);
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
    public function buildForm($id)
    {
        $forms = OptionsHelper::getForms();

        $form = null;

        if (!isset($forms[$id])) {
            return null;
        }
    }

    /**
     * Prepare user data to own format
     * @param array $data
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     * @throws \LogicException
     */
    protected function processData(array $data)
    {
        $return = [];

        if (empty($data['form']['title'])) {
            throw new \LogicException('Form title is empty');
        }

        $return['form']['title'] = sanitize_text_field($data['form']['title']);
        $return['form']['submit'] = !empty($data['form']['submit']) ? sanitize_text_field($data['form']['submit']) : __('Submit',
            Container::key());
        $return['form']['success'] = !empty($data['form']['success']) ? sanitize_text_field($data['form']['success']) : __('Thank you',
            Container::key());

        /**
         * @var $fieldsHelper FieldsHelper
         */
        $fieldsHelper = $this->container->get(FieldsHelper::class);
        $fields = $fieldsHelper->getFields();

        foreach ($fields as $key => $field) {
            //remove non required fields that bot exists in users data
            if (false === $field['required'] && !isset($data[$key]['active'])) {
                continue;
            }

            $return[$key]['active'] = true;

            if (!empty($data[$key]['label'])) {
                $return[$key]['label'] = sanitize_text_field($data[$key]['label']);
            }

            if (!empty($data[$key]['default'])) {
                $return[$key]['default'] = sanitize_text_field($data[$key]['default']);
            }

            if (!empty($data[$key]['required'])) {
                $return[$key]['required'] = true;
            }

            if (!empty($data[$key]['hidden']) && empty($data[$key]['required'])) {
                $return[$key]['hidden'] = true;
            }
        }

        return $return;
    }
}