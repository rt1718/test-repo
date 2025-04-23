<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Services\ConvertImageToWebP;
use Imagick;
use Mockery;

class ConvertImageToWebPTest extends TestCase
{
    public function test_to_webp_calls_imagick_methods(): void
    {
        $inputPath = '/fake/input.jpg';
        $outputPath = '/fake/output.webp';

        $imagickMock = Mockery::mock(Imagick::class);
        $imagickMock->shouldReceive('readImage')
            ->once()
            ->with($inputPath);
        $imagickMock->shouldReceive('setImageFormat')
            ->once()
            ->with('webp');
        $imagickMock->shouldReceive('writeImage')
            ->once()
            ->with($outputPath);
        $imagickMock->shouldReceive('clear')
            ->once();
        $imagickMock->shouldReceive('destroy')
            ->once();

        $service = new ConvertImageToWebP($imagickMock);
        $service->toWebP($inputPath, $outputPath);
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
