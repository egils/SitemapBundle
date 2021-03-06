<?php

/*
 * This file is part of the Tadcka package.
 *
 * (c) Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tadcka\Bundle\SitemapBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Tadas Gliaubicas <tadcka89@gmail.com>
 *
 * @since  14.6.29 14.05
 */
class SeoTranslationFormType extends AbstractType
{
    /**
     * @var bool
     */
    private $hasControllerByType;

    /**
     * Constructor.
     *
     * @param bool $hasControllerByType
     */
    public function __construct($hasControllerByType)
    {
        $this->hasControllerByType = $hasControllerByType;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'metaTitle',
            'text',
            array(
                'label' => 'form.seo_translation.meta_title',
                'constraints' => array(new NotBlank()),
                'required' => false,
            )
        );

        $builder->add(
            'metaDescription',
            'textarea',
            array(
                'label' => 'form.seo_translation.meta_description',
                'required' => false,
            )
        );

        $builder->add(
            'metaKeywords',
            'textarea',
            array(
                'label' => 'form.seo_translation.meta_keywords',
                'required' => false,
            )
        );

        if ($this->hasControllerByType) {
            $builder->add('route', 'tadcka_route', array('label' => false));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'translation_domain' => 'TadckaSitemapBundle',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tadcka_sitemap_seo_translation';
    }
}
