<?php

namespace Rz\OAuthBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationFormType extends AbstractType
{
    private $class;

    /**
     * @var array
     */
    protected $mergeOptions;

    /**
     * @param string $class        The User class name
     * @param array  $mergeOptions Add options to elements
     */
    public function __construct($class, array $mergeOptions = array())
    {
        $this->class        = $class;
        $this->mergeOptions = $mergeOptions;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, array_merge(array(
                'label' => 'form.username',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('email', 'email', array_merge(array(
                'label' => 'form.email',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('plainPassword', 'repeated', array_merge(array(
                'type' => 'password',
                'options' => array('translation_domain' => 'SonataUserBundle'),
                'first_options' => array_merge(array(
                    'label' => 'form.password',
                ), $this->mergeOptions),
                'second_options' => array_merge(array(
                    'label' => 'form.password_confirmation',
                ), $this->mergeOptions),
                'invalid_message' => 'fos_user.password.mismatch',
            ), $this->mergeOptions))

            ->add('facebookUid', 'hidden', array_merge(array(
                'label' => 'form.facebookUid',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('facebookName', 'hidden', array_merge(array(
                'label' => 'form.facebookName',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('facebookData', 'hidden', array_merge(array(
                'label' => 'form.facebookData',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))

            ->add('twitterUid', 'hidden', array_merge(array(
                'label' => 'form.twitterUid',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('twitterName', 'hidden', array_merge(array(
                'label' => 'form.twitterName',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('twitterData', 'hidden', array_merge(array(
                'label' => 'form.twitterData',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))

            ->add('gplusUid', 'hidden', array_merge(array(
                'label' => 'form.gplusUid',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('gplusName', 'hidden', array_merge(array(
                'label' => 'form.gplusName',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))
            ->add('gplusData', 'hidden', array_merge(array(
                'label' => 'form.gplusData',
                'translation_domain' => 'SonataUserBundle',
            ), $this->mergeOptions))


        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'rz_o_auth_user_registration';
    }
}
