<?php

namespace Drupal\linktitle\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\linktitle\LinkTitleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'link' formatter.
 *
 * Adds the remote page title as link text.
 *
 * @FieldFormatter(
 *   id = "link_title",
 *   label = @Translation("Link (automatic link title)"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkTitleFormatter extends LinkFormatter implements ContainerFactoryPluginInterface {

  /**
   * The link title service.
   *
   * @var \Drupal\linktitle\LinkTitleInterface
   */
  protected $linkTitle;

  /**
   * Constructs a new LinkTitleFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator service.
   * @param \Drupal\linktitle\LinkTitleInterface $link_title
   *   The link title service.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    PathValidatorInterface $path_validator,
    LinkTitleInterface $link_title
  ) {

    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $path_validator);
    $this->linkTitle = $link_title;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('path.validator'),
      $container->get('linktitle')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = parent::viewElements($items, $langcode);

    foreach (Element::children($element) as $child) {
      if (empty($element[$child]['#url'])) {
        continue;
      }

      $link_title = $this->linkTitle->getTitleFromUrl($element[$child]['#url']->toString());
      if (!empty($link_title)) {
        $element[$child]['#title'] = $link_title;
      }
    }

    return $element;
  }

}
