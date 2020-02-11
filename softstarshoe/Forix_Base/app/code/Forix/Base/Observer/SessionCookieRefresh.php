<?php
namespace Forix\Base\Observer;


class SessionCookieRefresh Implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    protected $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    protected $sessionConfig;

    /**
     * SessionCookieRefresh constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\Cookie\PhpCookieManager $cookieManager
    ) {
        $this->session = $session;
        $this->sessionConfig = $sessionConfig;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * refreshes de duration of the session cookie
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $metadata->setPath($this->session->getCookiePath())
            ->setDomain($this->session->getCookieDomain())
            ->setDuration($this->session->getCookieLifetime())
            ->setSecure($this->sessionConfig->getCookieSecure())
            ->setHttpOnly($this->sessionConfig->getCookieHttpOnly());

        $this->cookieManager->setPublicCookie(
            $this->session->getName(),
            $this->session->getSessionId(),
            $metadata
        );
    }
}