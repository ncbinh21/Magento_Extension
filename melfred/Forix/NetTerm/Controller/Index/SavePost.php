<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 */
namespace  Forix\NetTerm\Controller\Index;

class SavePost extends \Magento\Framework\App\Action\Action
{

    /**
     * name sender
     */
    const NAME_SENDER = 'netterm/configuration/name_sender';

    /**
     * email sender
     */
    const EMAIL_SENDER = 'netterm/configuration/email_sender';

    /**
     * email receiver
     */
    const EMAIL_RECEIVER = 'netterm/configuration/email_receiver';

    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'netterm/configuration/send_email_netterm';

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Forix\NetTerm\Model\NettermFactoryf
     */
    protected $nettermFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * SavePost constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Forix\NetTerm\Model\NettermFactory $nettermFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Action\Context $context,
        \Forix\NetTerm\Model\NettermFactory $nettermFactory
    ) {
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->nettermFactory = $nettermFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $request = $this->getRequest();
        $redirectUrl = $this->resultRedirectFactory->create();
        $resultRedirect = $redirectUrl->setPath('net-terms-thank-you');
        try {
            $netterm = $this->nettermFactory->create();
            $nettermData = $request->getParams();

            foreach ($nettermData as $key => $value) {
                $netterm->setData($key, $value);
            }

            for ($i = 1; $i < 4; $i++) {
                $owner['name_title_' . $i] = $request->getParam('name_title_' . $i);
            }
            $netterm->setOwnersOfficers(serialize($owner));
            for ($i = 1; $i < 5; $i++) {
                $company[$i]['company_' . $i] = $request->getParam('company_' . $i);
                $company[$i]['fax_number_' . $i] = $request->getParam('fax_number_' . $i);
                $company[$i]['email_' . $i] = $request->getParam('email_' . $i);
            }
            $netterm->setCompanyReferences(serialize($company));
            $netterm->setIsActive(0);
            $netterm->save();

            $this->messageManager->addSuccessMessage(
                __('Thank you, you\'re application has been successfully submitted. You will receive a response from a representative with the application approval or denial.')
            );
            $this->sendMailRegister($netterm);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $redirectUrl->setPath('*/*');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('An error occurred on the server. Your changes have not been saved.')
            );
            $this->logger->critical($e);
            $resultRedirect = $redirectUrl->setPath('*/*');
        }

        return $resultRedirect;
    }

    /**
     * @param $name
     * @param $number
     * @param $request
     * @return mixed
     */
    public function getParamNetterm($name, $number, $request) {
        $param[$name . '_' . $number] = $request->getParam($name . '_' . $number);
        return $param;
    }

    public function sendMailRegister($netterms)
    {
        try {
            $sender = [
                'name' => $this->escaper->escapeHtml($this->scopeConfig->getValue(SELF::NAME_SENDER)),
                'email' => $this->escaper->escapeHtml($this->scopeConfig->getValue(SELF::EMAIL_SENDER)),
            ];

            $templateVars = [
                'netterms' => $netterms
            ];
            $ownersOfficers = unserialize($netterms->getOwnersOfficers());
            for ($i = 1; $i < 4; $i++) {
                if($ownersOfficers['name_title_' . $i]) {
                    $templateVars['name_title_'. $i] = $ownersOfficers['name_title_' . $i];
                }
            }

            $companyReferences = unserialize($netterms->getCompanyReferences());
            for ($i = 1; $i < 5; $i++) {
                if($companyReferences[$i]['company_' . $i]) {
                    $templateVars['company_'. $i] = $companyReferences[$i]['company_' . $i];
                }
                if($companyReferences[$i]['fax_number_' . $i]) {
                    $templateVars['fax_number_'. $i] = $companyReferences[$i]['fax_number_' . $i];
                }
                if($companyReferences[$i]['email_' . $i]) {
                    $templateVars['email_'. $i] = $companyReferences[$i]['email_' . $i];
                }
            }

            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeId = $this->storeManager->getStore()->getStoreId();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $scopeStore , $storeId)) // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($this->scopeConfig->getValue(SELF::EMAIL_RECEIVER))
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            return;
        }
    }
}
