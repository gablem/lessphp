<?php
require_once __DIR__ . "/../lessc.inc.php";

class ErrorHandlingTest extends PHPUnit\Framework\TestCase {
    protected $less;

    public function setUp(): void {
        $this->less = new lessc();
    }

    public function compile() {
        $source = join("\n", func_get_args());
        return $this->less->compile($source);
    }

    public function testRequiredParametersMissing() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('.parametric-mixin is undefined');

        $this->compile(
            '.parametric-mixin (@a, @b) { a: @a; b: @b; }',
            '.selector { .parametric-mixin(12px); }'
        );
    }

    public function testTooManyParameters() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('.parametric-mixin is undefined');

        $this->compile(
            '.parametric-mixin (@a, @b) { a: @a; b: @b; }',
            '.selector { .parametric-mixin(12px, 13px, 14px); }'
        );
    }

    public function testRequiredArgumentsMissing() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('unrecognised input');

        $this->compile('.selector { rule: e(); }');
    }

    public function testVariableMissing() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('variable @missing is undefined');

        $this->compile('.selector { rule: @missing; }');
    }

    public function testMixinMissing() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('.missing-mixin is undefined');

        $this->compile('.selector { .missing-mixin; }');
    }

    public function testGuardUnmatchedValue() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('.flipped is undefined');

        $this->compile(
            '.flipped(@x) when (@x =< 10) { rule: value; }',
            '.selector { .flipped(12); }'
        );
    }

    public function testGuardUnmatchedType() {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('.colors-only is undefined');

        $this->compile(
            '.colors-only(@x) when (iscolor(@x)) { rule: value; }',
            '.selector { .colors-only("string value"); }'
        );
    }
}
