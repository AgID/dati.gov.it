<?php

namespace Drupal\linktitle\Plugin\Filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\linktitle\LinkTitleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to automatically attach a title attribute to links.
 *
 * @Filter(
 *   id = "filter_link_title",
 *   title = @translation("Link title"),
 *   description = @Translation("Adds a title attribute to links."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class FilterLinkTitle extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The link title service.
   *
   * @var \Drupal\linktitle\LinkTitleInterface
   */
  protected $linkTitle;

  /**
   * Constructs a \Drupal\linktitle\Plugin\Filter\LinkTitle object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\linktitle\LinkTitleInterface $linktitle
   *   The link title service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LinkTitleInterface $linktitle
  ) {

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->linkTitle = $linktitle;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('linktitle')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    return new FilterProcessResult($this->linkTitle->addTitles($text));
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Adds a title attribute to links found in the content.');
  }

}
