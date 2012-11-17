<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{
    protected function _initDb(){
		$resource = $this->getPluginResource('multidb');
		$resource->init();
		$profiler = $resource->getDb()->getProfiler();
		$profiler->setFilterQueryType(Zend_Db_Profiler::SELECT | Zend_Db_Profiler::INSERT | Zend_Db_Profiler::UPDATE);
		Zend_Registry::set('db_pos', $resource->getDb());
                //Zend_Registry::set('db_int', $resource->getDb('db2'));
		return $resource->getDb();
	}

	protected function _initAutoLoad(){
		$modelLoader = new Zend_Application_Module_Autoloader(array(
		'namespace'=>'',
		'basePath'=>APPLICATION_PATH.'/modules/default'));
	}

	protected function _initView(){
            $view = new Zend_View();
            $view->headTitle('Quiero Una Farmacia')
                            ->setSeparator(' :: ');
            $view->headTitle('Codeando x Chile')
                            ->setSeparator(" - ");
            $view->doctype('XHTML1_STRICT');
            $view->headMeta()->appendName('author', 'Jose Careaga, Ariel Mora, Fernando Toloza, Robinson Navarro');
            $view->headMeta()->setHttpEquiv('content-type', 'text/html; charset=utf-8')
                            ->setHttpEquiv('Content-Language','es');
            $view->headScript()->appendFile('/js/jquery/jquery-1.8.2.min.js');
            //$view->headScript()->appendFile('/js/jquery/ui/1.9.0/js/jquery-ui-1.9.0.custom.min.js');
            $view->headScript()->appendFile('/js/libs/bootstrap/js/bootstrap.min.js');
            //$view->headLink()->appendStylesheet('/js/jquery/ui/1.9.0/css/redmond/jquery-ui-1.9.0.custom.min.css');
            $view->headLink()->appendStylesheet('/js/libs/bootstrap/css/bootstrap.min.css');
            $view->headLink()->appendStylesheet('/js/libs/bootstrap/css/bootstrap-responsive.min.css');
            $view->headLink()->appendStylesheet('/css/estilos.css');
            $view->headLink(array(
                            'rel' => 'shortcut icon',
                            'href' => '/favicon.ico'),
                            'APPEND');
            $view->addHelperPath(APPLICATION_PATH.'/helpers','My_Helper');
            $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
            $viewRenderer->setView($view);
            Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

            $view->postulacion_estado = true;

            return $view;
	}

	protected function _initSiteRoutes(){
		$this->bootstrap("frontController");
		$front = $this->getResource("frontController");
		$router = new Zend_Controller_Router_Rewrite();
		$sitio = new Zend_Config_Ini(APPLICATION_PATH . "/configs/rutas.ini");
		$router->addConfig($sitio, 'routes');
		$front->getRouter()->addConfig($sitio, "routes");
	}
}