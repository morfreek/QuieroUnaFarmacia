<?php
class App_Controller_Plugin_ModuleSwitcher extends Zend_Controller_Plugin_Abstract
{
    protected $_view = null;

    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $request->getModuleName();

        Zend_Layout::startMvc();
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayoutPath('../application/modules/' . $moduleName . '/layouts/scripts/')->setLayout($moduleName);

        $view = new Zend_View();
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->init();

        $this->_view = $viewRenderer->view;
        $this->_view->doctype('XHTML1_STRICT');
        
        $this->_view->headMeta()->appendName('author', 'Departamento de procesos de Innova Bio Bio 2011-2011');
        $this->_view->headMeta()->setHttpEquiv('content-type', 'text/html; charset=utf-8')
                                ->setHttpEquiv('Content-Language','es');
        return $request;
    }
}