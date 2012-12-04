<?php

namespace Crud\Form;

use Zend\Form\Form;

abstract class AbstractForm extends Form
{

    use \Crud\Traits\getInputFilter;

}