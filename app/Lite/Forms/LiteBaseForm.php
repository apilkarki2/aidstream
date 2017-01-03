<?php namespace App\Lite\Forms;


use App\Core\Form\BaseForm;

class LiteBaseForm extends BaseForm
{
    /**
     * @param        $name
     * @param        $label
     * @param bool   $required
     * @param string $wrapperClass
     * @return $this
     */
    public function addText($name, $label, $required = true, $wrapperClass = 'form-group col-sm-6')
    {
        return $this->add($name, 'text', ['label' => $label, 'required' => $required, 'wrapper' => ['class' => $wrapperClass]]);
    }

    /**
     * @param        $name
     * @param        $label
     * @param        $childFormPath
     * @param string $wrapperClass
     * @return $this
     */
    public function addToCollection($name, $label, $childFormPath, $wrapperClass = 'collection_form has_add_more')
    {
        return $this->add(
            $name,
            'collection',
            [
                'label'   => $label,
                'type'    => 'form',
                'options' => [
                    'class' => $childFormPath,
                    'label' => false
                ],
                'wrapper' => [
                    'class' => $wrapperClass
                ]
            ]
        );
    }

    /**
     * @param $name
     * @param $label
     * @param $buttonType
     * @return $this
     */
    public function addButton($name, $label, $buttonType)
    {
        $class = ($buttonType === 'add_more') ? 'add_to_collection' : 'remove_from_collection';

        return $this->add(
            $name,
            'button',
            [
                'label' => $label,
                'attr'  => [
                    'class' => $class
                ]
            ]
        );
    }
}