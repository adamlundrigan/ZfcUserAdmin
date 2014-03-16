<?php

namespace ZfcUserAdmin\Form;

use ZfcUser\Entity\UserInterface;
use ZfcUser\Form\Register;
use ZfcUser\Options\RegistrationOptionsInterface;
use ZfcUserAdmin\Options\UserEditOptionsInterface;
use Zend\Form\Form;
use Zend\Form\Element;

class EditUser extends Register
{
    /**
     * @var \ZfcUserAdmin\Options\UserEditOptionsInterface
     */
    protected $userEditOptions;
    protected $userEntity;
    protected $serviceManager;

    public function __construct($name = null, UserEditOptionsInterface $options, RegistrationOptionsInterface $registerOptions, $serviceManager)
    {
        $this->setUserEditOptions($options);
        $this->setServiceManager($serviceManager);
        parent::__construct($name, $registerOptions);

        $this->remove('captcha');

        if ($this->userEditOptions->getAllowPasswordChange()) {
            $this->add(array(
                'name' => 'reset_password',
                'type' => 'Zend\Form\Element\Checkbox',
                'options' => array(
                    'label' => 'Reset password to random',
                ),
            ));

            $password = $this->get('password');
            $password->setAttribute('required', false);
            $password->setOptions(array('label' => 'Password (only if want to change)'));

            $this->remove('passwordVerify');
        } else {
            $this->remove('password')->remove('passwordVerify');
        }

        foreach ($this->getUserEditOptions()->getEditFormElements() as $name => $element) {
            // avoid adding fields twice (e.g. email)
            // if ($this->get($element)) continue;

            $this->add(array(
                'name' => $element,
                'options' => array(
                    'label' => $name,
                ),
                'attributes' => array(
                    'type' => 'text'
                ),
            ));
        }

        $this->get('submit')->setLabel('Edit')->setValue('Edit');

        $this->add(array(
            'name' => 'userId',
            'attributes' => array(
                'type' => 'hidden'
            ),
        ));
    }

    public function setUser($userEntity)
    {
        $this->userEntity = $userEntity;
        $this->getEventManager()->trigger('userSet', $this, array('user' => $userEntity));
        $this->bind($userEntity);
    }

    public function getUser()
    {
        return $this->userEntity;
    }

    public function setUserEditOptions(UserEditOptionsInterface $userEditOptions)
    {
        $this->userEditOptions = $userEditOptions;
        return $this;
    }

    public function getUserEditOptions()
    {
        return $this->userEditOptions;
    }

    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function getServiceManager()
    {
        return $this->serviceManager;
    }
}
