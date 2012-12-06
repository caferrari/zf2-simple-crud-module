<?php

namespace Crud\Controller;

use Zend\Mvc\Controller\AbstractActionController,
    Zend\Paginator\Paginator,
    Zend\Paginator\Adapter\ArrayAdapter,
    Zend\View\Model\ViewModel,
    Zend\ServiceManager\Exception\ServiceNotFoundException;

abstract class AbstractController extends AbstractActionController
{

    protected $entityManager = null;
    protected $service;
    protected $entity;
    protected $form;
    protected $controller;

    protected $messages = array(
        'success' => array(
            'insert' => 'Item inserido com sucesso',
            'edit' => 'Item editado com sucesso',
            'delete' => 'Item excluído com sucesso!'
        ),
        'error' => array(
            'insert' => 'Não foi possível inserir',
            'edit' => 'Falha ao editar',
            'delete' => 'Este item não pode ser excluído'
        )
    );

    public function __construct()
    {
        $class = get_called_class();
        $this->service = str_replace('\Controller\\', '\Service\\', $class);
        $this->entity = str_replace('\Controller\\', '\Entity\\', $class);
        $this->form = str_replace('\Controller\\', '\Form\\', $class);
        $this->controller = trim(strtolower(preg_replace('@([A-Z])@', "-$1", explode('\\', $class)[2])), '-');
    }

    public function indexAction()
    {
        $especies = $this->getRepository()->findAll();

        $page = (int)$this->getRequest()->getQuery('page', '1');

        $paginator = new Paginator(new ArrayAdapter($especies));
        $paginator->setCurrentPageNumber($page);
        $paginator->setDefaultItemCountPerPage(20);
        return array('data' => $paginator, 'page' => $page);
    }

    public function novoAction()
    {
        $form = $this->getForm();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRepository()->insert($form->getData());
                $this->success($this->getMessage('insert', 'success'));
                $this->redirect()->toRoute('crud', array('controller' => $this->controller));
            } else {
                $this->error($this->getMessage('insert', 'error'));
            }
        }

        return array('form' => $form);
    }

    public function editarAction()
    {

        $form = $this->getForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getRepository()->update($form->getData());
                $this->success($this->getMessage('edit', 'success'));
                return $this->redirect()->toRoute('crud', array('controller' => $this->controller));
            } else {
                $this->error($this->getMessage('edit', 'error'));
            }
        }

        $id = $this->getRequest()->getQuery('id', false);
        if (is_numeric($id)) {
            $entity = $this->getRepository()->find($id);
            $this->form = $form->setData($entity->toArray());

            $children = $this->layout()->getChildren();
            return $this->render($this->editView);
        }

        $this->error('ID deve ser numérico');
        return $this->redirect()->toRoute('crud', array('controller' => $this->controller));
    }

    public function excluirAction()
    {
        $id = $this->getRequest()->getQuery('id', false);
        if (is_numeric($id)){
            $this->getRepository()->delete($id);
            $this->success($this->getMessage('delete', 'success'));
        } else {
            $this->success($this->getMessage('delete', 'error'));
        }

        return $this->redirect()->toRoute('crud', array('controller' => $this->controller));
    }

    protected function getRepository($entity = null)
    {
        if (null == $this->entityManager) {
            $this->entityManager = $this->getService('Doctrine\ORM\EntityManager');
        }
        if (null == $entity) {
            $entity = $this->entity;
        }
        return $this->entityManager->getRepository($entity);
    }

    protected function getService($service)
    {
        return $this->getServiceLocator()->get($service);
    }

    public function getForm()
    {
        try {
            return $this->getService($this->form);
        } catch (ServiceNotFoundException $e) {
            return new $this->form();
        }
    }

    public function getData()
    {
        $dontMap = array('entityManager', 'service', 'entity', 'eventIdentifier',
                         'plugins', 'request', 'response', 'event', 'events',
                         'serviceLocator', 'controller', 'messages', 'editView');

        $localVars = array_keys(get_object_vars($this));

        $data = array();
        foreach (array_diff($localVars, $dontMap) as $key) {
            $data[$key] = $this->$key;
        }

        return $data;
    }

    public function render($view = null, $data = null) {

        if (null === $data) {
            $data = array();
        }

        $result = new ViewModel($data + $this->getData());
        if (is_string($view)) {
            $result->setTemplate($view);
        }

        return $result;
    }

    public function getMessage($action, $type) {
        return $this->messages[$type][$action];
    }

    public function success($message)
    {
        $this->setFlash($message, 'success');
    }

    public function error($message)
    {
        $this->setFlash($message, 'error');
    }

    public function setFlash($message, $namespace = 'success')
    {
        $this->flashMessenger()->setNamespace($namespace)->addMessage($message);
    }

}