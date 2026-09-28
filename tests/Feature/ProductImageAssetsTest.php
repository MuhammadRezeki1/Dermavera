<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductImageAssetsTest extends TestCase
{
    public function test_all_37_catalog_records_have_unique_local_product_images_and_sources(): void
    {
        $expectedCodes = [
            'K01', 'K02', 'K03', 'K04', 'K05', 'K06', 'K07', 'M01', 'M02', 'M03', 'M04',
            'G01', 'G02', 'G03', 'G04', 'G05', 'G06', 'G07', 'G08', 'G09',
            'N01', 'N02', 'N03', 'N04', 'N05', 'N06', 'N07', 'N08',
            'B01', 'B02', 'B03', 'B04', 'B05', 'B06', 'B07', 'B08', 'B03N',
        ];
        $manifest = config('product-images');

        $this->assertSame($expectedCodes, array_keys($manifest));
        $this->assertCount(37, $manifest);

        $hashes = [];
        foreach ($manifest as $code => $image) {
            $sourcePath = public_path('images/product/'.$image['file']);
            $cutoutPath = public_path('images/product-cutout/'.pathinfo($image['file'], PATHINFO_FILENAME).'.png');

            $this->assertFileExists($sourcePath, "Gambar sumber {$code} tidak ditemukan.");
            $this->assertFileExists($cutoutPath, "Cutout transparan {$code} tidak ditemukan.");
            $this->assertGreaterThan(5_000, filesize($cutoutPath), "Cutout {$code} terlalu kecil atau rusak.");
            $this->assertSame('image/png', mime_content_type($cutoutPath), "Cutout {$code} harus berupa PNG.");
            $cutout = imagecreatefrompng($cutoutPath);
            $cornerAlpha = (imagecolorat($cutout, 0, 0) >> 24) & 0x7F;
            imagedestroy($cutout);
            $this->assertGreaterThan(0, $cornerAlpha, "Cutout {$code} tidak memiliki sudut transparan.");
            $this->assertStringStartsWith('https://', $image['source'], "Sumber {$code} harus memakai HTTPS.");
            $hashes[] = hash_file('sha256', $cutoutPath);
        }

        $this->assertCount(37, array_unique($hashes), 'Setiap kode produk harus menggunakan gambar yang berbeda.');
    }
}
