<?php

namespace Drupal\linktitle;

/**
 * Handles the retrieval an replacement of link titles.
 */
interface LinkTitleInterface {

  /**
   * The number of bytes read while seeking the title tag.
   *
   * @var int
   */
  const SEEK_LENGTH = 100;

  /**
   * Add title attributes to all link tags in a HTML snippet.
   *
   * @param string $snippet
   *   The snippet in which the tags are replaced.
   *
   * @return string
   *   The updated snippet.
   */
  public function addTitles(string $snippet): string;

  /**
   * Retrieves the page title for a remote URL.
   *
   * @param string $url
   *   The URL to retrieve the page title from.
   *
   * @return string
   *   The page title or an empty string.
   */
  public function getTitleFromUrl(string $url): string;

}
