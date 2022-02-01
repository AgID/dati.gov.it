<?php

namespace Drupal\linktitle;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Handles the retrieval an replacement of link titles.
 */
class LinkTitle implements LinkTitleInterface {

  /**
   * The http client.
   *
   * @var \GuzzleHttp\Client
   */
  protected $http;

  /**
   * Constructs a \Drupal\linktitle\LinkTitle object.
   *
   * @param \GuzzleHttp\Client $http_client
   *   The http client.
   */
  public function __construct(Client $http_client) {
    $this->client = $http_client;
  }

  /**
   * {@inheritdoc}
   */
  public function addTitles(string $snippet): string {
    $dom_document = Html::load($snippet);
    foreach ($dom_document->getElementsByTagName('a') as $link) {
      $title = $link->getAttribute('title');
      $href = $link->getAttribute('href');
      if (!empty($title) || empty($href)) {
        continue;
      }

      $url_title = $this->getTitleFromUrl($href);
      if (!empty($url_title)) {
        $link->setAttribute('title', $url_title);
      }
    }

    return $dom_document->saveHTML();
  }

  /**
   * {@inheritdoc}
   *
   * Use a Guzzle stream to prevent the whole page from being read. The PHP
   * stream API supports reading a stream to a given string through the
   * `stream_get_line`, however that isn't implemented in the Guzzle stream.
   * This method reads the stream in manageable chunks until the `</title>` or
   * `<body>` tag is found.
   */
  public function getTitleFromUrl(string $url): string {
    // Add support for internal urls.
    if (!UrlHelper::isExternal($url)) {
      $url = Url::fromUserInput($url, [
        'absolute' => TRUE,
      ])->toString();
    }

    try {
      $response = $this->client->request('GET', $url, [
        RequestOptions::STREAM => TRUE,
        RequestOptions::ALLOW_REDIRECTS => TRUE,
        RequestOptions::CONNECT_TIMEOUT => 5,
      ]);
    }
    catch (GuzzleException $e) {
      watchdog_exception('linktitle', $e);
      return '';
    }

    $buffer = '';
    while (!$response->getBody()->eof()) {
      $buffer .= $response->getBody()->read(static::SEEK_LENGTH);
      if (FALSE !== ($title_end = strpos($buffer, '</title>'))) {
        $response->getBody()->close();
        break;
      }

      if (FALSE !== strpos($buffer, '<body>')) {
        $response->getBody()->close();
        return '';
      }
    }

    if (FALSE === ($title_start = strpos($buffer, '<title>'))) {
      return '';
    }

    // Account for the tags.
    // substr('<title>') === 7.
    return _filter_html_escape(substr($buffer, ($title_start + 7), ($title_end - $title_start - 7)));
  }

}
