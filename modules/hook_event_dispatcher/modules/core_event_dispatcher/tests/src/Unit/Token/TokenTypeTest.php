<?php

namespace Drupal\Tests\core_event_dispatcher\Unit\Token;

use Drupal\core_event_dispatcher\ValueObject\TokenType;
use Drupal\Tests\UnitTestCase;
use UnexpectedValueException;

/**
 * Class TokenTypeTest.
 *
 * @group hook_event_dispatcher
 */
class TokenTypeTest extends UnitTestCase {

  /**
   * Test TokenType invalid name exception.
   */
  public function testTokenTypeInvalidNameException(): void {
    $this->expectException(UnexpectedValueException::class);
    TokenType::create('', NULL);
  }

  /**
   * Test TokenType invalid description exception.
   */
  public function testTokenTypeInvalidDescriptionException(): void {
    $this->expectException(UnexpectedValueException::class);
    TokenType::create('', '')->setDescription(NULL);
  }

}
