<?php
namespace Hexasoft\MailboxValidator\Plugin;

// use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultFactory as resultFactory;

use Magento\Framework\App\Request\DataPersistorInterface;

use Magento\Framework\Controller\Result\Redirect;

class Validatenewsletter
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
     * @var \Magento\Framework\Controller\ResultFactory
     */
	
	protected $resultFactory;
	
	/**
     * @var \MailboxValidator\EmailValidator\Helper\Validators
     */
	
	protected $validators;

    public function __construct(
		resultFactory $resultFactory,
		DataPersistorInterface $dataPersistor,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		\Magento\Framework\App\Response\RedirectInterface $_redirect,
		\Hexasoft\MailboxValidator\Helper\Validators $validators
    )
	{
		$this->messageManager = $messageManager;
		$this->scopeConfig = $scopeConfig;
		$this->dataPersistor = $dataPersistor;
		$this->resultRedirectFactory = $resultRedirectFactory;
		$this->validators = $validators;
		$this->resultFactory = $resultFactory;
		$this->_redirect = $_redirect;
	}
	
	public function aroundExecute (\Magento\Newsletter\Controller\Subscriber\NewAction $subject, \Closure $proceed)
	{
		if ($this->scopeConfig->getValue('mailboxvalidator/active_display/active', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
			// the config path can get from system.xml, format is section_id/group_id/field_id
			$apiKey = $this->scopeConfig->getValue('mailboxvalidator/active_display/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			$post = $subject->getRequest()->getPostValue();
			$singleResult = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_invalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE) || $this->scopeConfig->getValue('mailboxvalidator/active_display/validate_role', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvsingle($post['email'], $apiKey) : '';
			$isValid = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_invalid', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisvalidemail($singleResult) : true;
			$isDisposable = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_disposable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisdisposable($post['email'], $apiKey) : false;
			$isFree = ($this->scopeConfig->getValue('mailboxvalidator/active_display/validate_free', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) ? $this->validators->mbvisfree($post['email'], $apiKey) : false;
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
				/** @var Redirect $redirect */
				$redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$redirectUrl = $this->_redirect->getRedirectUrl();
				return $redirect->setUrl($redirectUrl);
			}
		}
		return $proceed();
	}
}