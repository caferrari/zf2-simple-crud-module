<?php

namespace Crud\Form\View\Helper;

use Zend\Form\View\Helper\FormRow,
    Zend\Form\ElementInterface;

class BootstrapRow extends FormRow
{

    public function render(ElementInterface $element)
    {
        $escapeHtmlHelper    = $this->getEscapeHtmlHelper();
        $labelHelper         = $this->getLabelHelper();
        $elementHelper       = $this->getElementHelper();
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label           = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors   = $elementErrorsHelper->render($element);

        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if (!$element->hasAttribute('id')){
            $inputId = 'input' . ucfirst($element->getAttribute('name'));
            $element->setAttribute('id', $inputId);
        }else
            $inputId = $element->getAttribute('id');

        $markup = <<<EOD
<div class="control-group%extraClass">
    <label for="{$inputId}" class="control-label">%label:</label>
    <div class="controls">
        %input
        %help
    </div>
</div>
EOD;

        $elementString = $elementHelper->render($element);

        if (null !== ($translator = $this->getTranslator())) {
            $label = $translator->translate(
                $label, $this->getTranslatorTextDomain()
            );
        }

        $label = $escapeHtmlHelper($label);

        if (!$this->renderErrors) {
            $elementErrors = '';
            $markup = str_replace('%extraClass', '', $markup);
        }
        $markup = str_replace('%label', $label, $markup);
        $markup = str_replace('%input', $elementString, $markup);
        if ($elementErrors) {
            $elementErrors = '<span class="help-inline">' . $elementErrors . '</span>';
            $markup = str_replace('%extraClass', ' error', $markup);
        }else {
            $markup = str_replace('%extraClass', '', $markup);
        }
        $markup = str_replace('%help', $elementErrors, $markup);

        return $markup;
    }

}
