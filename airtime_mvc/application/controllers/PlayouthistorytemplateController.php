<?php

class PlayouthistorytemplateController extends Zend_Controller_Action
{
    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext
            ->addActionContext('create-template', 'json')
            ->addActionContext('update-template', 'json')
            ->addActionContext('delete-template', 'json')
            ->addActionContext('set-template-default', 'json')
            ->initContext();
	}
	
	public function indexAction()
	{
		$CC_CONFIG = Config::getConfig();
		$baseUrl = Application_Common_OsPath::getBaseDir();
	
		$this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/template.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headLink()->appendStylesheet($baseUrl.'css/history_styles.css?'.$CC_CONFIG['airtime_version']);
	
		$historyService = new Application_Service_HistoryService();
		$this->view->template_list = $historyService->getListItemTemplates();
		$this->view->template_file = $historyService->getFileTemplates();
		$this->view->configured = $historyService->getConfiguredTemplateIds();
	}
	
	public function configureTemplateAction() {
	
		$CC_CONFIG = Config::getConfig();
		$baseUrl = Application_Common_OsPath::getBaseDir();
	
		$this->view->headScript()->appendFile($baseUrl.'js/airtime/playouthistory/configuretemplate.js?'.$CC_CONFIG['airtime_version'],'text/javascript');
		$this->view->headLink()->appendStylesheet($baseUrl.'css/history_styles.css?'.$CC_CONFIG['airtime_version']);
	
		try {
	
			$templateId = $this->_getParam('id');
	
			$historyService = new Application_Service_HistoryService();
			$template = $historyService->loadTemplate($templateId);
	
			$templateType = $template["type"];
			$supportedTypes = $historyService->getSupportedTemplateTypes();
	
			if (!in_array($templateType, $supportedTypes)) {
				throw new Exception("Error: $templateType is not supported.");
			}
	
			$getMandatoryFields = "mandatory".ucfirst($templateType)."Fields";
			$mandatoryFields = $historyService->$getMandatoryFields();
	
			$this->view->template_id = $templateId;
			$this->view->template_name = $template["name"];
			$this->view->template_fields = $template["fields"];
			$this->view->template_type = $templateType;
			$this->view->fileMD = $historyService->getFileMetadataTypes();
			$this->view->fields = $historyService->getFieldTypes();
			$this->view->required_fields = $mandatoryFields;
			$this->view->configured = $historyService->getConfiguredTemplateIds();
		}
		catch (Exception $e) {
			Logging::info("Error?");
			Logging::info($e);
			Logging::info($e->getMessage());
	
			$this->_forward('index', 'playouthistorytemplate');
		}
	}
	
	public function createTemplateAction()
	{
		$templateType = $this->_getParam('type', null);
	
		$request = $this->getRequest();
		$params = $request->getPost();
	
		try {
			$historyService = new Application_Service_HistoryService();
			$supportedTypes = $historyService->getSupportedTemplateTypes();
	
			if (!in_array($templateType, $supportedTypes)) {
				throw new Exception("Error: $templateType is not supported.");
			}
	
			$id = $historyService->createTemplate($params);
	
			$this->view->url = $this->view->baseUrl("Playouthistorytemplate/configure-template/id/{$id}");
		}
		catch (Exception $e) {
			Logging::info($e);
			Logging::info($e->getMessage());
	
			$this->view->error = $e->getMessage();
		}
	}
	
	public function setTemplateDefaultAction()
	{
		$templateId = $this->_getParam('id', null);
	
		try {
			$historyService = new Application_Service_HistoryService();
			$historyService->setConfiguredTemplate($templateId);
		}
		catch (Exception $e) {
			Logging::info($e);
			Logging::info($e->getMessage());
		}
	}
	
	public function updateTemplateAction()
	{
		$templateId = $this->_getParam('id', null);
		$name = $this->_getParam('name', null);
		$fields = $this->_getParam('fields', array());
	
		try {
			$historyService = new Application_Service_HistoryService();
			$historyService->updateItemTemplate($templateId, $name, $fields);
		}
		catch (Exception $e) {
			Logging::info($e);
			Logging::info($e->getMessage());
		}
	}
	
	public function deleteTemplateAction()
	{
		$templateId = $this->_getParam('id');
	
		try {
			$historyService = new Application_Service_HistoryService();
			$historyService->deleteTemplate($templateId);
		}
		catch (Exception $e) {
			Logging::info($e);
			Logging::info($e->getMessage());
		}
	}
}