<?php
namespace Townspot\Authentication\Adapter;

use ZfcUser\Authentication\Adapter\AdapterChainEvent as AuthenticationEvent;
use Zend\Session\Container as SessionContainer;
use Zend\Authentication\Result as AuthenticationResult;

class Db extends \ZfcUser\Authentication\Adapter\Db
{
    public function authenticate(AuthenticationEvent $event)
    {
        if($event->getCode() == 1) return;
        if ($this->isSatisfied()) {
            $storage = $this->getStorage()->read();
            $event->setIdentity($storage['identity'])
                  ->setCode(AuthenticationResult::SUCCESS)
                  ->setMessages(array('Authentication successful.'));
            return;
        }
        $identity   = $event->getRequest()->getPost()->get('identity');
        $credential = $event->getRequest()->getPost()->get('credential');
        $credential = $this->preProcessCredential($credential);
        $userObject = null;

        // Cycle through the configured identity sources and test each
        $fields = $this->getOptions()->getAuthIdentityFields();
        while (!is_object($userObject) && count($fields) > 0) {
            $mode = array_shift($fields);
            switch ($mode) {
                case 'username':
                    $userObject = $this->getMapper()->findByUsername($identity);
                    break;
                case 'email':
                    $userObject = $this->getMapper()->findByEmail($identity);
                    break;
            }
        }

        if (!$userObject) {
            $event->setCode(AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND)
                  ->setMessages(array('A record with the supplied identity could not be found.'));
            $this->setSatisfied(false);
            return false;
        }

        if ($this->getOptions()->getEnableUserState()) {
            // Don't allow user to login if state is not in allowed list
            if (!in_array($userObject->getState(), $this->getOptions()->getAllowedLoginStates())) {
                $event->setCode(AuthenticationResult::FAILURE_UNCATEGORIZED)
                      ->setMessages(array('A record with the supplied identity is not active.'));
                $this->setSatisfied(false);
                return false;
            }
        }

        //$cryptoService = $this->getHydrator()->getCryptoService();
		//Password MD5 Encrypted
		if ($userObject->getPassword() != md5($credential)) {
            $event->setCode(AuthenticationResult::FAILURE_CREDENTIAL_INVALID)
                  ->setMessages(array('Supplied credential is invalid.'));
            $this->setSatisfied(false);
            return false;
        }

        // regen the id
        SessionContainer::getDefaultManager()->regenerateId();

        // Success!
        $event->setIdentity($userObject->getId());

        $this->setSatisfied(true);
        $storage = $this->getStorage()->read();
        $storage['identity'] = $event->getIdentity();
        $this->getStorage()->write($storage);

        $event->setCode(AuthenticationResult::SUCCESS)
              ->setMessages(array('Authentication successful.'));
    }
}
