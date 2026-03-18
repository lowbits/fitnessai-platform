<?php

use App\Enums\Equipment;
use App\Jobs\GenerateEquipmentImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Laravel\Ai\Image;

uses(RefreshDatabase::class);

function createTestPngForEquipment(): string
{
    $image = imagecreatetruecolor(10, 10);
    $white = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $white);
    ob_start();
    imagepng($image);
    $png = ob_get_clean();
    imagedestroy($image);

    return $png;
}

// -- GenerateEquipmentImage job tests --

test('GenerateEquipmentImage job generates image and stores locally', function () {
    Storage::fake('public');

    $pngData = createTestPngForEquipment();

    Image::fake([
        base64_encode($pngData),
    ]);

    (new GenerateEquipmentImage(Equipment::Barbell))->handle();

    Storage::disk('public')->assertExists('equipments/barbell.webp');
});

test('GenerateEquipmentImage job converts to webp format', function () {
    Storage::fake('public');

    $pngData = createTestPngForEquipment();

    Image::fake([
        base64_encode($pngData),
    ]);

    (new GenerateEquipmentImage(Equipment::Dumbbell))->handle();

    $content = Storage::disk('public')->get('equipments/dumbbell.webp');

    expect($content)->not->toBeEmpty();
});

// -- Command tests --

test('equipment:generate-images command dispatches jobs for all equipment', function () {
    Bus::fake();
    Storage::fake('public');

    $this->artisan('equipment:generate-images')
        ->assertSuccessful();

    Bus::assertDispatched(GenerateEquipmentImage::class);
});

test('equipment:generate-images command with --equipment targets specific item', function () {
    Bus::fake();
    Storage::fake('public');

    $this->artisan('equipment:generate-images --equipment=barbell')
        ->assertSuccessful();

    Bus::assertDispatched(GenerateEquipmentImage::class, function (GenerateEquipmentImage $job) {
        return $job->equipment === Equipment::Barbell;
    });

    Bus::assertDispatchedTimes(GenerateEquipmentImage::class, 1);
});

test('equipment:generate-images command with --dry-run does not dispatch jobs', function () {
    Bus::fake();
    Storage::fake('public');

    $this->artisan('equipment:generate-images --dry-run')
        ->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('equipment:generate-images command skips existing images', function () {
    Bus::fake();
    Storage::fake('public');

    Storage::disk('public')->put('equipments/barbell.webp', 'existing');

    $this->artisan('equipment:generate-images --equipment=barbell')
        ->assertSuccessful();

    Bus::assertNothingDispatched();
});

test('equipment:generate-images command with --overwrite regenerates existing images', function () {
    Bus::fake();
    Storage::fake('public');

    Storage::disk('public')->put('equipments/barbell.webp', 'existing');

    $this->artisan('equipment:generate-images --equipment=barbell --overwrite')
        ->assertSuccessful();

    Bus::assertDispatched(GenerateEquipmentImage::class);
});

test('equipment:generate-images command with invalid --equipment fails', function () {
    $this->artisan('equipment:generate-images --equipment=invalid')
        ->assertFailed();
});

test('equipment:generate-images command with --sync runs synchronously', function () {
    Storage::fake('public');

    $pngData = createTestPngForEquipment();

    Image::fake([
        base64_encode($pngData),
    ]);

    $this->artisan('equipment:generate-images --equipment=barbell --sync')
        ->assertSuccessful();

    Storage::disk('public')->assertExists('equipments/barbell.webp');
});
