<?php
namespace MailboxValidator\EmailValidator\Plugin;

use Magento\Framework\Controller\ResultFactory;

use Magento\Framework\App\Request\DataPersistorInterface;

use Magento\Framework\Controller\Result\Redirect;

class Validatecontactform
{
	
	/**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
	
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
	
	protected $scopeConfig;
	
	/**
     * @var DataPersistorInterface
     */
	
	protected $dataPersistor;
	
	/**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
	
	protected $resultRedirectFactory;
	
	/**
     * @var \MailboxValidator\EmailValidator\Helper\Validators
     */
	
	protected $validators;

    public function __construct(
		DataPersistorInterface $dataPersistor,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\MailboxValidator\EmailValidator\Helper\Validators $validators
    )
	{
		$this->messageManager = $messageManager;
		$this->inlineTranslation = $inlineTranslation;
		$this->scopeConfig = $scopeConfig;
		$this->dataPersistor = $dataPersistor;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->validators = $validators;
		// $this->proceed = $proceed;
		// return parent::__construct($context);
	}
	
	public function aroundExecute (\Magento\Contact\Controller\Index\Post $subject, \Closure $proceed)
	{
		if ($this->scopeConfig->getValue('mailboxvalidator/active_display/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			// the config path can get from system.xml, format is section_id/group_id/field_id
			$apiKey = $this->scopeConfig->getValue('mailboxvalidator/active_display/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$post = $subject->getRequest()->getPostValue();
			$singleResult = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_invalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) || $this->scopeConfig->getValue('mailboxvalidator/active_display/validate_role', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvsingle($post['email'], $apiKey) : '';
			$isValid = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_invalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisvalidemail($singleResult) : true;
			$isDisposable = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_disposable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisdisposable($singleResult) : false;
			$isFree = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_free', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisfree($singleResult) : false;
			$isRole = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_role', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisrole($singleResult) : false;
			if (($isValid === false) || ($isDisposable === true) || ($isFree === true) || ($isRole === true)) {
				if ($isValid === false) {
					$this->messageManager->addErrorMessage(__(($this->scopeConfig->getValue('mailboxvalidator/active_display/invalid_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != '') ? $this->scopeConfig->getValue('mailboxvalidator/active_display/invalid_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 'Please enter a valid email address.'));
				} else if ($isDisposable === true) {
					$this->messageManager->addErrorMessage(__(($this->scopeConfig->getValue('mailboxvalidator/active_display/disposable_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != '') ? $this->scopeConfig->getValue('mailboxvalidator/active_display/disposable_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 'Please enter a non-disposable email address.'));
				} else if ($isFree === true) {
					$this->messageManager->addErrorMessage(__(($this->scopeConfig->getValue('mailboxvalidator/active_display/free_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != '') ? $this->scopeConfig->getValue('mailboxvalidator/active_display/free_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 'Please enter a non-free email address.'));
				} else if ($isRole === true) {
					$this->messageManager->addErrorMessage(__(($this->scopeConfig->getValue('mailboxvalidator/active_display/role_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) != '') ? $this->scopeConfig->getValue('mailboxvalidator/active_display/role_error_message', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : 'Please enter a non-role-based email address.'));
				}
				$this->dataPersistor->set('contact_us', $subject->getRequest()->getParams());
				return $this->resultRedirectFactory->create()->setPath('contact/index');
			}
		}
		return $proceed();
	}
}