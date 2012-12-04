<?php

namespace Crud\View\Helper;

use Zend\View\Helper\AbstractHelper;

class FlashMessages extends AbstractHelper
{

    protected $flashMessenger;

    public function setFlashMessager($flashMessenger)
    {
        if (null == $this->flashMessenger) {
            $this->flashMessenger = $flashMessenger;
        }
    }

    public function __invoke()
    {

        // twitter bootstrap namespaces
        $namespaces = array(
            'error' ,'success', 'info','warning'
        );

        $nsTitles = array(
            'Erro!', 'Sucesso!', 'Info:', 'Atenção!'
        );

        $messageString = '';

        foreach ($namespaces as $k => $ns) {
            $this->flashMessenger->setNamespace( $ns );
            $messages = $this->flashMessenger->getCurrentMessages();
            $this->flashMessenger->clearCurrentMessages();
            if (!$messages) continue;

            $messages = implode( '<br />', $messages);

            $messageString .= <<<EOD
<div class="alert alert-block alert-{$ns}">
<button type="button" class="close" data-dismiss="alert">×</button>
<h4>{$nsTitles[$k]}</h4>
$messages</div>
EOD;
        }

        return $messageString;
    }

}