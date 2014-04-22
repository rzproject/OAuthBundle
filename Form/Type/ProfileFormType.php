<?php

namespace Rz\OAuthBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraint\UserPassword as OldUserPassword;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ProfileFormType extends AbstractType
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


        if (class_exists('Symfony\Component\Security\Core\Validator\Constraints\UserPassword')) {
            $constraint = new UserPassword();
        } else {
            // Symfony 2.1 support with the old constraint class
            $constraint = new OldUserPassword();
        }

        $builder->add('current_password', 'password', array(
            'attr'=>array('class'=>'span12'),
            'label' => 'form.current_password',
            'translation_domain' => 'FOSUserBundle',
            'mapped' => false,
            'constraints' => $constraint,
        ));

        $builder
            ->add('username', null, array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.username',
                'translation_domain' => 'RzOAuthBundle',
                'read_only'=>true
            ), $this->mergeOptions))
            ->add('email', 'email', array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.email',
                'translation_domain' => 'RzOAuthBundle',
                'read_only'=>true
            ), $this->mergeOptions))

            ->add('firstname', null, array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.firstname',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('lastname', null, array_merge(array(
                'attr'=>array('class'=>'span12'),
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('website', 'url', array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.website',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('gender',  'sonata_user_gender', array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.gender',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('dateOfBirth',  'birthday', array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.date_of_birth',
                'translation_domain' => 'RzOAuthBundle',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'picker_settings' =>  array('data-date-format'=>'yyyy-mm-dd'),
                'invalid_message'=>'Birthdate is not valid.'
            ), $this->mergeOptions))
            ->add('phone', 'url', array_merge(array(
                'attr'=>array('class'=>'span12'),
                'label' => 'form.phone',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('facebookUid', 'hidden', array_merge(array(
                'label' => 'form.facebookUid',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('facebookName', 'hidden', array_merge(array(
                'label' => 'form.facebookName',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('facebookData', 'hidden', array_merge(array(
                'label' => 'form.facebookData',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('twitterUid', 'hidden', array_merge(array(
                'label' => 'form.twitterUid',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('twitterName', 'hidden', array_merge(array(
                'label' => 'form.twitterName',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('twitterData', 'hidden', array_merge(array(
                'label' => 'form.twitterData',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))

            ->add('gplusUid', 'hidden', array_merge(array(
                'label' => 'form.gplusUid',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('gplusName', 'hidden', array_merge(array(
                'label' => 'form.gplusName',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
            ->add('gplusData', 'hidden', array_merge(array(
                'label' => 'form.gplusData',
                'translation_domain' => 'RzOAuthBundle',
            ), $this->mergeOptions))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'profile',
        ));
    }

    public function getName()
    {
        return 'rz_o_auth_user_profile';
    }
}
