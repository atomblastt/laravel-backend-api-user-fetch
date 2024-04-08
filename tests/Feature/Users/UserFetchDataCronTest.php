<?php 
it('has home', function () {
    echo get_class($this); // \PHPUnit\Framework\TestCase
 
    $this->assertTrue(true);
});

it('should fail intentionally', function () {
    expect(true)->toBeFalse();
});
