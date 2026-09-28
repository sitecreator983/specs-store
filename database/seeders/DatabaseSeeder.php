<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ]
        );

        User::query()->updateOrCreate(
            ['name' => 'admin'],
            [
                'email' => 'admin@example.com',
                'password' => '123',
                'is_admin' => true,
            ]
        );

        $components = Category::query()->updateOrCreate(
            ['slug' => 'components'],
            ['name' => 'Εξαρτήματα']
        );

        $processors = Category::query()->updateOrCreate(
            ['slug' => 'processors'],
            [
                'parent_id' => $components->id,
                'name' => 'Επεξεργαστές',
            ]
        );

        $graphicsCards = Category::query()->updateOrCreate(
            ['slug' => 'graphics-cards'],
            [
                'parent_id' => $components->id,
                'name' => 'Κάρτες γραφικών',
            ]
        );

        $storage = Category::query()->updateOrCreate(
            ['slug' => 'storage'],
            [
                'parent_id' => $components->id,
                'name' => 'Αποθήκευση',
            ]
        );

        $cases = Category::query()->updateOrCreate(
            ['slug' => 'cases'],
            [
                'parent_id' => $components->id,
                'name' => 'Κουτιά',
            ]
        );

        $ram = Category::query()->updateOrCreate(
            ['slug' => 'ram'],
            [
                'parent_id' => $components->id,
                'name' => 'Μνήμες RAM',
            ]
        );

        $motherboards = Category::query()->updateOrCreate(
            ['slug' => 'motherboards'],
            ['parent_id' => $components->id, 'name' => 'Μητρικές Κάρτες']
        );

        $powerSupplies = Category::query()->updateOrCreate(
            ['slug' => 'power-supplies'],
            ['parent_id' => $components->id, 'name' => 'Τροφοδοτικά']
        );

        $fans = Category::query()->updateOrCreate(
            ['slug' => 'fans'],
            ['parent_id' => $components->id, 'name' => 'Ανεμιστηράκια']
        );

        $storageDevices = Category::query()->updateOrCreate(
            ['slug' => 'storage-devices'],
            ['name' => 'Αποθήκευση', 'parent_id' => null]
        );

        $externalDrives = Category::query()->updateOrCreate(
            ['slug' => 'external-drives'],
            ['parent_id' => $storageDevices->id, 'name' => 'Εξωτερικοί Δίσκοι']
        );

        $internalDrives = Category::query()->updateOrCreate(
            ['slug' => 'ssd-hdd'],
            ['parent_id' => $storageDevices->id, 'name' => 'Δίσκοι SSD & HDD']
        );

        $usbSticks = Category::query()->updateOrCreate(
            ['slug' => 'usb-sticks'],
            ['parent_id' => $storageDevices->id, 'name' => 'USB sticks']
        );

        $memoryCards = Category::query()->updateOrCreate(
            ['slug' => 'memory-cards'],
            ['parent_id' => $storageDevices->id, 'name' => 'Κάρτες Μνήμης']
        );

        $driveEnclosures = Category::query()->updateOrCreate(
            ['slug' => 'drive-enclosures'],
            ['parent_id' => $storageDevices->id, 'name' => 'Θήκες σκληρών δίσκων']
        );

        $opticalMedia = Category::query()->updateOrCreate(
            ['slug' => 'cd-dvd'],
            ['parent_id' => $storageDevices->id, 'name' => 'CD-DVD']
        );

        $peripherals = Category::query()->updateOrCreate(
            ['slug' => 'peripherals'],
            ['name' => 'Οθόνες & Περιφερειακά', 'parent_id' => null]
        );

        $keyboards = Category::query()->updateOrCreate(
            ['slug' => 'keyboards'],
            ['parent_id' => $peripherals->id, 'name' => 'Πληκτρολόγια']
        );

        $headsets = Category::query()->updateOrCreate(
            ['slug' => 'headsets'],
            ['parent_id' => $peripherals->id, 'name' => 'Ακουστικά']
        );

        $monitors = Category::query()->updateOrCreate(
            ['slug' => 'monitors'],
            ['parent_id' => $peripherals->id, 'name' => 'Οθόνες']
        );

        $mice = Category::query()->updateOrCreate(
            ['slug' => 'mice'],
            ['parent_id' => $peripherals->id, 'name' => 'Ποντίκια']
        );

        $speakers = Category::query()->updateOrCreate(
            ['slug' => 'speakers'],
            ['parent_id' => $peripherals->id, 'name' => 'Ηχεία']
        );

        $joysticks = Category::query()->updateOrCreate(
            ['slug' => 'joysticks'],
            ['parent_id' => $peripherals->id, 'name' => 'Joysticks']
        );

        $brands = collect([
            ['name' => 'AMD', 'logo_path' => 'images/brands/amd.svg'],
            ['name' => 'ASRock', 'logo_path' => 'images/brands/asrock.svg'],
            ['name' => 'ASUS', 'logo_path' => 'images/brands/asus.svg'],
            ['name' => 'be quiet!', 'logo_path' => null],
            ['name' => 'Corsair', 'logo_path' => 'images/brands/corsair.svg'],
            ['name' => 'Creative', 'logo_path' => null],
            ['name' => 'Crucial', 'logo_path' => null],
            ['name' => 'Edifier', 'logo_path' => null],
            ['name' => 'Fractal Design', 'logo_path' => null],
            ['name' => 'G.Skill', 'logo_path' => null],
            ['name' => 'Gigabyte', 'logo_path' => null],
            ['name' => 'Intel', 'logo_path' => 'images/brands/intel.svg'],
            ['name' => 'Kingston', 'logo_path' => 'images/brands/kingston.png'],
            ['name' => 'HyperX', 'logo_path' => null],
            ['name' => 'Lian Li', 'logo_path' => null],
            ['name' => 'Lexar', 'logo_path' => null],
            ['name' => 'LG', 'logo_path' => null],
            ['name' => 'Logitech', 'logo_path' => null],
            ['name' => 'MSI', 'logo_path' => 'images/brands/msi.svg'],
            ['name' => 'Noctua', 'logo_path' => null],
            ['name' => 'NZXT', 'logo_path' => null],
            ['name' => 'ORICO', 'logo_path' => null],
            ['name' => 'ARCTIC', 'logo_path' => null],
            ['name' => 'Samsung', 'logo_path' => 'images/brands/samsung.svg'],
            ['name' => 'SanDisk', 'logo_path' => null],
            ['name' => 'Sabrent', 'logo_path' => null],
            ['name' => 'Razer', 'logo_path' => 'images/brands/razer.svg'],
            ['name' => 'Sapphire', 'logo_path' => null],
            ['name' => 'Seagate', 'logo_path' => null],
            ['name' => 'Seasonic', 'logo_path' => null],
            ['name' => 'TeamGroup', 'logo_path' => null],
            ['name' => 'Thrustmaster', 'logo_path' => null],
            ['name' => 'Toshiba', 'logo_path' => null],
            ['name' => 'UGREEN', 'logo_path' => null],
            ['name' => 'Verbatim', 'logo_path' => null],
            ['name' => 'Western Digital', 'logo_path' => null],
            ['name' => 'XFX', 'logo_path' => null],
            ['name' => 'Xbox', 'logo_path' => null],
            ['name' => 'Sony', 'logo_path' => null],
            ['name' => '8BitDo', 'logo_path' => null],
        ])->mapWithKeys(fn (array $brand): array => [
            $brand['name'] => Brand::query()->updateOrCreate(
                ['slug' => Str::slug($brand['name'])],
                $brand
            ),
        ]);

        $products = [
            ['AMD', 'AMD Ryzen 9 9950X3D', 'CPU-AMD-9950X3D', 'images/products/cpus/amd-ryzen-9-9950x3d.jpg', 69900, 8, ['cores' => '16', 'threads' => '32', 'socket' => 'AM5', 'boost_clock' => '5.7 GHz']],
            ['AMD', 'AMD Ryzen 9 9950X', 'CPU-AMD-9950X', 'images/products/cpus/amd-ryzen-9-9950x.jpg', 52900, 10, ['cores' => '16', 'threads' => '32', 'socket' => 'AM5', 'boost_clock' => '5.7 GHz']],
            ['AMD', 'AMD Ryzen 9 9900X', 'CPU-AMD-9900X', 'images/products/cpus/amd-ryzen-9-9900x.jpg', 38900, 12, ['cores' => '12', 'threads' => '24', 'socket' => 'AM5', 'boost_clock' => '5.6 GHz']],
            ['AMD', 'AMD Ryzen 7 9800X3D', 'CPU-AMD-9800X3D', 'images/products/cpus/amd-ryzen-7-9800x3d.jpg', 47900, 7, ['cores' => '8', 'threads' => '16', 'socket' => 'AM5', 'boost_clock' => '5.2 GHz']],
            ['AMD', 'AMD Ryzen 7 9700X', 'CPU-AMD-9700X', 'images/products/cpus/amd-ryzen-7-9700x.jpg', 31900, 14, ['cores' => '8', 'threads' => '16', 'socket' => 'AM5', 'boost_clock' => '5.5 GHz']],
            ['AMD', 'AMD Ryzen 5 9600X', 'CPU-AMD-9600X', 'images/products/cpus/amd-ryzen-5-9600x.jpg', 24900, 18, ['cores' => '6', 'threads' => '12', 'socket' => 'AM5', 'boost_clock' => '5.4 GHz']],
            ['AMD', 'AMD Ryzen 9 7950X3D', 'CPU-AMD-7950X3D', 'images/products/cpus/amd-ryzen-9-7950x3d.jpg', 58900, 6, ['cores' => '16', 'threads' => '32', 'socket' => 'AM5', 'boost_clock' => '5.7 GHz']],
            ['AMD', 'AMD Ryzen 9 7900X3D', 'CPU-AMD-7900X3D', 'images/products/cpus/amd-ryzen-9-7900x3d.jpg', 42900, 9, ['cores' => '12', 'threads' => '24', 'socket' => 'AM5', 'boost_clock' => '5.6 GHz']],
            ['AMD', 'AMD Ryzen 7 7800X3D', 'CPU-AMD-7800X3D', 'images/products/cpus/amd-ryzen-7-7800x3d.jpeg', 43900, 11, ['cores' => '8', 'threads' => '16', 'socket' => 'AM5', 'boost_clock' => '5.0 GHz']],
            ['AMD', 'AMD Ryzen 7 7700X', 'CPU-AMD-7700X', 'images/products/cpus/amd-ryzen-7-7700x.jpg', 28900, 16, ['cores' => '8', 'threads' => '16', 'socket' => 'AM5', 'boost_clock' => '5.4 GHz']],
            ['AMD', 'AMD Ryzen 5 7600X', 'CPU-AMD-7600X', 'images/products/cpus/amd-ryzen-5-7600x.png', 19900, 20, ['cores' => '6', 'threads' => '12', 'socket' => 'AM5', 'boost_clock' => '5.3 GHz']],
            ['Intel', 'Intel Core Ultra 9 285K', 'CPU-INT-285K', 'images/products/cpus/intel-core-ultra-9-285k.jpeg', 58900, 8, ['cores' => '24', 'threads' => '24', 'socket' => 'LGA1851', 'boost_clock' => '5.7 GHz']],
            ['Intel', 'Intel Core Ultra 7 265K', 'CPU-INT-265K', 'images/products/cpus/intel-core-ultra-7-265k.jpeg', 38900, 10, ['cores' => '20', 'threads' => '20', 'socket' => 'LGA1851', 'boost_clock' => '5.5 GHz']],
            ['Intel', 'Intel Core Ultra 5 245K', 'CPU-INT-245K', 'images/products/cpus/intel-core-ultra-5-245k.webp', 31900, 13, ['cores' => '14', 'threads' => '14', 'socket' => 'LGA1851', 'boost_clock' => '5.2 GHz']],
            ['Intel', 'Intel Core i9-14900K', 'CPU-INT-14900K', 'images/products/cpus/intel-core-i9-14900k.jpg', 51900, 9, ['cores' => '24', 'threads' => '32', 'socket' => 'LGA1700', 'boost_clock' => '6.0 GHz']],
            ['Intel', 'Intel Core i7-14700K', 'CPU-INT-14700K', 'images/products/cpus/intel-core-i7-14700k.jpg', 37900, 12, ['cores' => '20', 'threads' => '28', 'socket' => 'LGA1700', 'boost_clock' => '5.6 GHz']],
            ['Intel', 'Intel Core i5-14600K', 'CPU-INT-14600K', 'images/products/cpus/intel-core-i5-14600k.jpg', 24900, 15, ['cores' => '14', 'threads' => '20', 'socket' => 'LGA1700', 'boost_clock' => '5.3 GHz']],
            ['Intel', 'Intel Core i5-14400F', 'CPU-INT-14400F', 'images/products/cpus/intel-core-i5-14400f.jpg', 18900, 17, ['cores' => '10', 'threads' => '16', 'socket' => 'LGA1700', 'boost_clock' => '4.7 GHz']],
        ];

        foreach ($products as [$brandName, $name, $sku, $imagePath, $priceCents, $stock, $specs]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $processors->id,
                    'name' => $name,
                    'image_path' => $imagePath,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => $specs,
                ]
            );
        }

        $gpuProducts = [
            ['ASUS', 'ASUS ROG Astral GeForce RTX 5090 32GB GDDR7', 'GPU-ASUS-RTX5090-ASTRAL-32G', 'images/products/gpus/asus-rog-astral-rtx-5090.jpg', 369900, 3, ['chipset' => 'GeForce RTX 5090', 'vram' => '32GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.58 GHz']],
            ['MSI', 'MSI GeForce RTX 5080 Gaming Trio OC 16GB GDDR7', 'GPU-MSI-RTX5080-GTRIO-16G', 'images/products/gpus/msi-rtx-5080-gaming-trio-oc.jpg', 139900, 5, ['chipset' => 'GeForce RTX 5080', 'vram' => '16GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.71 GHz']],
            ['Gigabyte', 'Gigabyte GeForce RTX 5070 Ti Gaming OC 16GB GDDR7', 'GPU-GIG-RTX5070TI-GOC-16G', 'images/products/gpus/gigabyte-rtx-5070-ti-gaming-oc.jpg', 124900, 6, ['chipset' => 'GeForce RTX 5070 Ti', 'vram' => '16GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.59 GHz']],
            ['ASUS', 'ASUS Prime GeForce RTX 5070 12GB GDDR7', 'GPU-ASUS-RTX5070-PRIME-12G', 'images/products/gpus/asus-prime-rtx-5070.jpg', 69900, 8, ['chipset' => 'GeForce RTX 5070', 'vram' => '12GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.51 GHz']],
            ['Gigabyte', 'Gigabyte Radeon RX 9070 XT Gaming OC 16GB GDDR6', 'GPU-GIG-RX9070XT-GOC-16G', 'images/products/gpus/gigabyte-rx-9070-xt-gaming-oc.jpg', 89900, 7, ['chipset' => 'Radeon RX 9070 XT', 'vram' => '16GB', 'memory_type' => 'GDDR6', 'boost_clock' => '3.06 GHz']],
            ['ASRock', 'ASRock Taichi Radeon RX 9070 XT 16GB GDDR6', 'GPU-ASR-RX9070XT-TAICHI-16G', 'images/products/gpus/asrock-rx-9070-xt-taichi.webp', 94900, 4, ['chipset' => 'Radeon RX 9070 XT', 'vram' => '16GB', 'memory_type' => 'GDDR6', 'boost_clock' => '3.10 GHz']],
            ['Sapphire', 'Sapphire Pulse Radeon RX 9070 XT 16GB GDDR6', 'GPU-SAP-RX9070XT-PULSE-16G', 'images/products/gpus/sapphire-pulse-rx-9070-xt.jpg', 73900, 9, ['chipset' => 'Radeon RX 9070 XT', 'vram' => '16GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.97 GHz']],
            ['Sapphire', 'Sapphire Nitro+ Radeon RX 7900 XTX Vapor-X 24GB GDDR6', 'GPU-SAP-RX7900XTX-NITRO-24G', 'images/products/gpus/sapphire-nitro-rx-7900-xtx.jpg', 99900, 5, ['chipset' => 'Radeon RX 7900 XTX', 'vram' => '24GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.68 GHz']],
            ['XFX', 'XFX Speedster MERC310 Radeon RX 7900 XT 20GB GDDR6', 'GPU-XFX-RX7900XT-MERC310-20G', 'images/products/gpus/xfx-merc310-rx-7900-xt.jpg', 69900, 6, ['chipset' => 'Radeon RX 7900 XT', 'vram' => '20GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.54 GHz']],
            ['ASUS', 'ASUS TUF Gaming Radeon RX 7800 XT OC 16GB GDDR6', 'GPU-ASUS-RX7800XT-TUF-16G', 'images/products/gpus/asus-tuf-rx-7800-xt.jpg', 52900, 10, ['chipset' => 'Radeon RX 7800 XT', 'vram' => '16GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.57 GHz']],
            ['Sapphire', 'Sapphire Pulse Radeon RX 7700 XT 12GB GDDR6', 'GPU-SAP-RX7700XT-PULSE-12G', 'images/products/gpus/sapphire-pulse-rx-7700-xt.jpg', 42900, 11, ['chipset' => 'Radeon RX 7700 XT', 'vram' => '12GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.54 GHz']],
            ['ASRock', 'ASRock Steel Legend Radeon RX 7600 XT 16GB GDDR6', 'GPU-ASR-RX7600XT-SL-16G', 'images/products/gpus/asrock-steel-legend-rx-7600-xt.jpeg', 32900, 13, ['chipset' => 'Radeon RX 7600 XT', 'vram' => '16GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.81 GHz']],
            ['Gigabyte', 'Gigabyte GeForce RTX 5060 Ti Gaming OC 8GB GDDR7', 'GPU-GIG-RTX5060TI-GOC-8G', 'images/products/gpus/gigabyte-rtx-5060-ti-gaming-oc.jpg', 44900, 12, ['chipset' => 'GeForce RTX 5060 Ti', 'vram' => '8GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.41 GHz']],
            ['MSI', 'MSI GeForce RTX 5060 Gaming OC 8GB GDDR7', 'GPU-MSI-RTX5060-GOC-8G', 'images/products/gpus/msi-rtx-5060-gaming-oc.jpg', 34900, 15, ['chipset' => 'GeForce RTX 5060', 'vram' => '8GB', 'memory_type' => 'GDDR7', 'boost_clock' => '2.50 GHz']],
            ['ASUS', 'ASUS Dual GeForce RTX 4060 Ti OC 8GB GDDR6', 'GPU-ASUS-RTX4060TI-DUAL-8G', 'images/products/gpus/asus-dual-rtx-4060-ti.jpg', 39900, 14, ['chipset' => 'GeForce RTX 4060 Ti', 'vram' => '8GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.59 GHz']],
            ['ASUS', 'ASUS Dual GeForce RTX 4060 OC 8GB GDDR6', 'GPU-ASUS-RTX4060-DUAL-8G', 'images/products/gpus/asus-dual-rtx-4060.jpg', 29900, 18, ['chipset' => 'GeForce RTX 4060', 'vram' => '8GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.54 GHz']],
            ['Intel', 'Intel Arc B580 Limited Edition 12GB GDDR6', 'GPU-INT-ARC-B580-12G', 'images/products/gpus/intel-arc-b580.webp', 24900, 9, ['chipset' => 'Intel Arc B580', 'vram' => '12GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.67 GHz']],
            ['Intel', 'GUNNIR Intel Arc B570 Index 10GB GDDR6', 'GPU-INT-ARC-B570-10G', 'images/products/gpus/intel-arc-b570.png', 21900, 10, ['chipset' => 'Intel Arc B570', 'vram' => '10GB', 'memory_type' => 'GDDR6', 'boost_clock' => '2.50 GHz']],
        ];

        foreach ($gpuProducts as [$brandName, $name, $sku, $imagePath, $priceCents, $stock, $specs]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $graphicsCards->id,
                    'name' => $name,
                    'image_path' => $imagePath,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => $specs,
                ]
            );
        }

        $storageProducts = [
            ['Samsung', 'Samsung 990 PRO 1TB NVMe SSD', 'SSD-SAM-990PRO-1TB', 'samsung-990-pro.png', 20900, 12, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.450 MB/s'],
            ['Samsung', 'Samsung 990 PRO 2TB NVMe SSD', 'SSD-SAM-990PRO-2TB', 'samsung-990-pro.png', 35900, 9, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.450 MB/s'],
            ['Samsung', 'Samsung 990 PRO 4TB NVMe SSD', 'SSD-SAM-990PRO-4TB', 'samsung-990-pro.png', 61900, 5, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.450 MB/s'],
            ['Samsung', 'Samsung 990 EVO Plus 1TB NVMe SSD', 'SSD-SAM-990EVO-1TB', 'samsung-990-evo-plus.jpg', 15900, 14, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.150 MB/s'],
            ['Samsung', 'Samsung 990 EVO Plus 2TB NVMe SSD', 'SSD-SAM-990EVO-2TB', 'samsung-990-evo-plus.jpg', 28900, 11, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.250 MB/s'],
            ['Samsung', 'Samsung 990 EVO Plus 4TB NVMe SSD', 'SSD-SAM-990EVO-4TB', 'samsung-990-evo-plus.jpg', 51900, 6, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.250 MB/s'],
            ['Samsung', 'Samsung 9100 PRO 1TB NVMe SSD', 'SSD-SAM-9100PRO-1TB', 'samsung-9100-pro.jpg', 23900, 10, '1TB', 'PCIe 5.0 x4 NVMe', 'M.2 2280', '14.700 MB/s'],
            ['Samsung', 'Samsung 9100 PRO 2TB NVMe SSD', 'SSD-SAM-9100PRO-2TB', 'samsung-9100-pro.jpg', 42900, 7, '2TB', 'PCIe 5.0 x4 NVMe', 'M.2 2280', '14.700 MB/s'],
            ['Samsung', 'Samsung 9100 PRO 4TB NVMe SSD', 'SSD-SAM-9100PRO-4TB', 'samsung-9100-pro.jpg', 72900, 4, '4TB', 'PCIe 5.0 x4 NVMe', 'M.2 2280', '14.800 MB/s'],
            ['Kingston', 'Kingston NV3 500GB NVMe SSD', 'SSD-KIN-NV3-500GB', 'kingston-nv3.jpg', 10100, 20, '500GB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '5.000 MB/s'],
            ['Kingston', 'Kingston NV3 1TB NVMe SSD', 'SSD-KIN-NV3-1TB', 'kingston-nv3.jpg', 14900, 18, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '6.000 MB/s'],
            ['Kingston', 'Kingston NV3 2TB NVMe SSD', 'SSD-KIN-NV3-2TB', 'kingston-nv3.jpg', 24900, 13, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '6.000 MB/s'],
            ['Kingston', 'Kingston NV3 4TB NVMe SSD', 'SSD-KIN-NV3-4TB', 'kingston-nv3.jpg', 47900, 6, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '6.000 MB/s'],
            ['Kingston', 'Kingston KC3000 1TB NVMe SSD', 'SSD-KIN-KC3000-1TB', 'kingston-kc3000.jpg', 17900, 12, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.000 MB/s'],
            ['Kingston', 'Kingston KC3000 2TB NVMe SSD', 'SSD-KIN-KC3000-2TB', 'kingston-kc3000.jpg', 30900, 8, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.000 MB/s'],
            ['Kingston', 'Kingston KC3000 4TB NVMe SSD', 'SSD-KIN-KC3000-4TB', 'kingston-kc3000.jpg', 52900, 4, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.000 MB/s'],
            ['Crucial', 'Crucial T500 1TB NVMe SSD', 'SSD-CRU-T500-1TB', 'crucial-t500.png', 15900, 11, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.300 MB/s'],
            ['Crucial', 'Crucial T500 2TB NVMe SSD', 'SSD-CRU-T500-2TB', 'crucial-t500.png', 29900, 9, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.400 MB/s'],
            ['Crucial', 'Crucial T500 4TB NVMe SSD', 'SSD-CRU-T500-4TB', 'crucial-t500.png', 49900, 5, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.400 MB/s'],
            ['Crucial', 'Crucial MX500 500GB SATA SSD', 'SSD-CRU-MX500-500GB', 'crucial-mx500.jpg', 7900, 17, '500GB', 'SATA III', '2.5-inch', '560 MB/s'],
            ['Crucial', 'Crucial MX500 1TB SATA SSD', 'SSD-CRU-MX500-1TB', 'crucial-mx500.jpg', 14900, 13, '1TB', 'SATA III', '2.5-inch', '560 MB/s'],
            ['Crucial', 'Crucial MX500 2TB SATA SSD', 'SSD-CRU-MX500-2TB', 'crucial-mx500.jpg', 26900, 8, '2TB', 'SATA III', '2.5-inch', '560 MB/s'],
            ['Western Digital', 'WD_BLACK SN850X 1TB NVMe SSD', 'SSD-WD-SN850X-1TB', 'wd-black-sn850x.jpg', 19900, 12, '1TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.300 MB/s'],
            ['Western Digital', 'WD_BLACK SN850X 2TB NVMe SSD', 'SSD-WD-SN850X-2TB', 'wd-black-sn850x.jpg', 38900, 8, '2TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.300 MB/s'],
            ['Western Digital', 'WD_BLACK SN850X 4TB NVMe SSD', 'SSD-WD-SN850X-4TB', 'wd-black-sn850x.jpg', 64900, 4, '4TB', 'PCIe 4.0 x4 NVMe', 'M.2 2280', '7.300 MB/s'],
        ];

        foreach ($storageProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $capacity, $interface, $formFactor, $readSpeed]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $storage->id,
                    'name' => $name,
                    'image_path' => 'images/products/storage/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'capacity' => $capacity,
                        'interface' => $interface,
                        'form_factor' => $formFactor,
                        'read_speed' => $readSpeed,
                    ],
                ]
            );
        }

        $caseProducts = [
            ['Corsair', 'Corsair 4000D Airflow Black', 'CASE-COR-4000D-AF-BK', 'corsair-4000d-airflow.jpg', 11900, 12, 'Mid Tower', 'ATX', 'Standard'],
            ['Corsair', 'Corsair 5000D Airflow Black', 'CASE-COR-5000D-AF-BK', 'corsair-5000d-airflow.webp', 16900, 8, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Corsair', 'Corsair 3000D Airflow Black', 'CASE-COR-3000D-AF-BK', 'corsair-3000d-airflow.jpg', 8900, 15, 'Mid Tower', 'ATX', 'Standard'],
            ['Corsair', 'Corsair 3500X Black', 'CASE-COR-3500X-BK', 'corsair-3500x.png', 9900, 11, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Corsair', 'Corsair 6500X Black', 'CASE-COR-6500X-BK', 'corsair-6500x.jpg', 22900, 6, 'Mid Tower', 'E-ATX', 'Dual chamber'],
            ['NZXT', 'NZXT H3 Flow Black', 'CASE-NZXT-H3-FLOW-BK', 'nzxt-h3-flow.png', 6900, 14, 'Mini Tower', 'mATX', 'Standard'],
            ['NZXT', 'NZXT H5 Flow Black', 'CASE-NZXT-H5-FLOW-BK', 'nzxt-h5-flow.jpg', 9900, 12, 'Mid Tower', 'ATX', 'Standard'],
            ['NZXT', 'NZXT H6 Flow Black', 'CASE-NZXT-H6-FLOW-BK', 'nzxt-h6-flow.jpeg', 11900, 10, 'Mid Tower', 'ATX', 'Dual chamber'],
            ['NZXT', 'NZXT H7 Flow Black', 'CASE-NZXT-H7-FLOW-BK', 'nzxt-h7-flow.jpg', 14900, 9, 'Mid Tower', 'ATX', 'Standard'],
            ['NZXT', 'NZXT H9 Flow Black', 'CASE-NZXT-H9-FLOW-BK', 'nzxt-h9-flow.png', 18900, 7, 'Mid Tower', 'ATX', 'Dual chamber'],
            ['Fractal Design', 'Fractal Design North Charcoal Black TG', 'CASE-FRA-NORTH-BK-TG', 'fractal-north.jpg', 14900, 9, 'Mid Tower', 'ATX', 'Standard'],
            ['Fractal Design', 'Fractal Design North XL Charcoal Black Solid', 'CASE-FRA-NORTH-XL-BK-SOLID', 'fractal-north-xl.png', 19900, 6, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Fractal Design', 'Fractal Design Pop Air Black', 'CASE-FRA-POP-AIR-BK', 'fractal-pop-air.jpg', 10900, 10, 'Mid Tower', 'ATX', 'Standard'],
            ['Fractal Design', 'Fractal Design Meshify 2 RGB Black', 'CASE-FRA-MESHIFY2-RGB-BK', 'fractal-meshify-2.jpg', 18900, 7, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Fractal Design', 'Fractal Design Torrent Black Solid', 'CASE-FRA-TORRENT-BK', 'fractal-torrent.jpg', 22900, 5, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Lian Li', 'Lian Li Lancool 216 Black', 'CASE-LIA-LANCOOL216-BK', 'lian-li-lancool-216.jpeg', 11900, 12, 'Mid Tower', 'E-ATX', 'Standard'],
            ['Lian Li', 'Lian Li Lancool 207 Black', 'CASE-LIA-LANCOOL207-BK', 'lian-li-lancool-207.png', 8900, 15, 'Mid Tower', 'ATX', 'Standard'],
            ['Lian Li', 'Lian Li O11 Dynamic EVO Black', 'CASE-LIA-O11-EVO-BK', 'lian-li-o11-dynamic-evo.jpeg', 18900, 7, 'Mid Tower', 'E-ATX', 'Dual chamber'],
            ['Lian Li', 'Lian Li O11 Vision Black', 'CASE-LIA-O11-VISION-BK', 'lian-li-o11-vision.jpg', 15900, 8, 'Mid Tower', 'E-ATX', 'Dual chamber'],
            ['Lian Li', 'Lian Li A3-mATX Black', 'CASE-LIA-A3-MATX-BK', 'lian-li-a3-matx.jpg', 7900, 13, 'Mini Tower', 'mATX', 'Standard'],
            ['be quiet!', 'be quiet! Pure Base 500DX Black', 'CASE-BEQ-PB500DX-BK', 'be-quiet-pure-base-500dx.jpg', 11900, 10, 'Mid Tower', 'ATX', 'Standard'],
            ['be quiet!', 'be quiet! Pure Base 501 Black', 'CASE-BEQ-PB501-BK', 'be-quiet-pure-base-501.jpg', 8900, 14, 'Mid Tower', 'ATX', 'Standard'],
            ['be quiet!', 'be quiet! Shadow Base 800 Black', 'CASE-BEQ-SB800-BK', 'be-quiet-shadow-base-800.jpg', 14900, 8, 'Mid Tower', 'E-ATX', 'Standard'],
            ['be quiet!', 'be quiet! Light Base 600 LX Black', 'CASE-BEQ-LB600LX-BK', 'be-quiet-light-base-600-lx.webp', 18900, 6, 'Mid Tower', 'ATX', 'Dual chamber'],
            ['be quiet!', 'be quiet! Dark Base Pro 901 Black', 'CASE-BEQ-DBP901-BK', 'be-quiet-dark-base-pro-901.jpg', 32900, 4, 'Full Tower', 'E-ATX', 'Standard'],
        ];

        foreach ($caseProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $caseType, $motherboardSupport, $layout]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $cases->id,
                    'name' => $name,
                    'image_path' => 'images/products/cases/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'case_type' => $caseType,
                        'motherboard_support' => $motherboardSupport,
                        'layout' => $layout,
                        'color' => 'Μαύρο',
                    ],
                ]
            );
        }

        $ramProducts = [
            ['Corsair', 'Corsair Vengeance DDR5 32GB (2x16GB) 6000 CL36', 'RAM-COR-VENG-D5-32-6000', 'corsair-vengeance-ddr5.webp', 54900, 12, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['Corsair', 'Corsair Vengeance RGB DDR5 32GB (2x16GB) 6000 CL36', 'RAM-COR-VENGRGB-D5-32-6000', 'corsair-vengeance-rgb-ddr5.jpg', 58900, 9, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['Corsair', 'Corsair Dominator Titanium RGB DDR5 32GB (2x16GB) 6000 CL30', 'RAM-COR-DOMTIT-D5-32-6000', 'corsair-dominator-titanium.jpg', 67900, 6, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL30'],
            ['Corsair', 'Corsair Vengeance LPX DDR4 32GB (2x16GB) 3200 CL16', 'RAM-COR-LPX-D4-32-3200', 'corsair-vengeance-lpx-ddr4.jpg', 18900, 15, '32GB', '2x16GB', 'DDR4', '3200 MT/s', 'CL16'],
            ['Corsair', 'Corsair Dominator Platinum RGB DDR5 32GB (2x16GB) 6200 CL36', 'RAM-COR-DOMPLAT-D5-32-6200', 'corsair-dominator-platinum.jpg', 72900, 5, '32GB', '2x16GB', 'DDR5', '6200 MT/s', 'CL36'],
            ['Kingston', 'Kingston Fury Beast DDR5 32GB (2x16GB) 6000 CL40', 'RAM-KIN-BEAST-D5-32-6000', 'kingston-fury-beast-ddr5.jpg', 52900, 14, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL40'],
            ['Kingston', 'Kingston Fury Beast RGB DDR5 32GB (2x16GB) 6000 CL40', 'RAM-KIN-BEASTRGB-D5-32-6000', 'kingston-fury-beast-rgb.png', 57900, 10, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL40'],
            ['Kingston', 'Kingston Fury Renegade DDR5 32GB (2x16GB) 6400 CL32', 'RAM-KIN-RENEGADE-D5-32-6400', 'kingston-fury-renegade.jpg', 65900, 7, '32GB', '2x16GB', 'DDR5', '6400 MT/s', 'CL32'],
            ['Kingston', 'Kingston Fury Renegade RGB DDR5 32GB (2x16GB) 6400 CL32', 'RAM-KIN-RENEGRGB-D5-32-6400', 'kingston-fury-renegade-rgb.jpg', 69900, 6, '32GB', '2x16GB', 'DDR5', '6400 MT/s', 'CL32'],
            ['Kingston', 'Kingston Fury Beast DDR4 32GB (2x16GB) 3200 CL16', 'RAM-KIN-BEAST-D4-32-3200', 'kingston-fury-beast-ddr4.jpg', 19900, 17, '32GB', '2x16GB', 'DDR4', '3200 MT/s', 'CL16'],
            ['G.Skill', 'G.Skill Ripjaws S5 DDR5 32GB (2x16GB) 6000 CL36', 'RAM-GSK-RIPJAWS-S5-32-6000', 'gskill-ripjaws-s5.jpeg', 52900, 11, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['G.Skill', 'G.Skill Trident Z5 RGB DDR5 32GB (2x16GB) 6400 CL32', 'RAM-GSK-TRIDENT-Z5-32-6400', 'gskill-trident-z5-rgb.jpg', 63900, 8, '32GB', '2x16GB', 'DDR5', '6400 MT/s', 'CL32'],
            ['G.Skill', 'G.Skill Flare X5 DDR5 32GB (2x16GB) 6000 CL36', 'RAM-GSK-FLARE-X5-32-6000', 'gskill-flare-x5.jpg', 51900, 12, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['G.Skill', 'G.Skill Trident Z5 Neo RGB DDR5 32GB (2x16GB) 6000 CL30', 'RAM-GSK-Z5NEO-RGB-32-6000', 'gskill-trident-z5-neo-rgb.jpg', 64900, 7, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL30'],
            ['G.Skill', 'G.Skill Ripjaws V DDR4 32GB (2x16GB) 3600 CL16', 'RAM-GSK-RIPJAWS-V-32-3600', 'gskill-ripjaws-v.png', 21900, 14, '32GB', '2x16GB', 'DDR4', '3600 MT/s', 'CL16'],
            ['Crucial', 'Crucial Pro DDR5 32GB (2x16GB) 5600 CL46', 'RAM-CRU-PRO-D5-32-5600', 'crucial-pro-ddr5-32.jpg', 48900, 16, '32GB', '2x16GB', 'DDR5', '5600 MT/s', 'CL46'],
            ['Crucial', 'Crucial Pro DDR5 64GB (2x32GB) 5600 CL46', 'RAM-CRU-PRO-D5-64-5600', 'crucial-pro-ddr5-64.jpg', 92900, 5, '64GB', '2x32GB', 'DDR5', '5600 MT/s', 'CL46'],
            ['Crucial', 'Crucial Pro Overclocking DDR5 32GB (2x16GB) 6000 CL36 Black', 'RAM-CRU-PROOC-D5-32-6000-BK', 'crucial-pro-oc-black.jpg', 54900, 10, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['Crucial', 'Crucial Pro Overclocking DDR5 32GB (2x16GB) 6000 CL36 White', 'RAM-CRU-PROOC-D5-32-6000-WH', 'crucial-pro-oc-white.jpg', 55900, 8, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL36'],
            ['Crucial', 'Crucial Pro DDR4 32GB (2x16GB) 3200 CL22', 'RAM-CRU-PRO-D4-32-3200', 'crucial-pro-ddr4.jpg', 16900, 18, '32GB', '2x16GB', 'DDR4', '3200 MT/s', 'CL22'],
            ['TeamGroup', 'TeamGroup T-Force Delta RGB DDR5 32GB (2x16GB) 6000 CL38', 'RAM-TGR-DELTA-D5-32-6000', 'teamgroup-delta-rgb-ddr5.webp', 55900, 10, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL38'],
            ['TeamGroup', 'TeamGroup T-Force Vulcan DDR5 32GB (2x16GB) 6000 CL38', 'RAM-TGR-VULCAN-D5-32-6000', 'teamgroup-vulcan-ddr5.webp', 50900, 13, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL38'],
            ['TeamGroup', 'TeamGroup T-Force Delta RGB DDR4 32GB (2x16GB) 3600 CL18', 'RAM-TGR-DELTA-D4-32-3600', 'teamgroup-delta-rgb-ddr4.jpg', 21900, 12, '32GB', '2x16GB', 'DDR4', '3600 MT/s', 'CL18'],
            ['TeamGroup', 'TeamGroup T-Force Vulcan Z DDR4 32GB (2x16GB) 3200 CL16', 'RAM-TGR-VULCANZ-D4-32-3200', 'teamgroup-vulcan-z-ddr4.jpg', 17900, 17, '32GB', '2x16GB', 'DDR4', '3200 MT/s', 'CL16'],
            ['TeamGroup', 'TeamGroup T-Create Expert DDR5 32GB (2x16GB) 6000 CL38', 'RAM-TGR-TCREATE-D5-32-6000', 'teamgroup-tcreate-expert.png', 52900, 11, '32GB', '2x16GB', 'DDR5', '6000 MT/s', 'CL38'],
        ];

        foreach ($ramProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $capacity, $modules, $memoryType, $speed, $latency]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $ram->id,
                    'name' => $name,
                    'image_path' => 'images/products/ram/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'capacity' => $capacity,
                        'modules' => $modules,
                        'memory_type' => $memoryType,
                        'speed' => $speed,
                        'latency' => $latency,
                    ],
                ]
            );
        }

        $motherboardProducts = [
            ['ASUS', 'ASUS TUF Gaming B850-PLUS WIFI', 'MB-ASUS-TUF-B850-PLUS', 'asus-tuf-b850-plus.jpg', 22900, 12, 'AM5', 'B850', 'ATX'],
            ['ASUS', 'ASUS Prime B850-PLUS WIFI', 'MB-ASUS-PRIME-B850-PLUS', 'asus-prime-b850-plus.webp', 18900, 14, 'AM5', 'B850', 'ATX'],
            ['ASUS', 'ASUS ROG Strix B850-F Gaming WiFi', 'MB-ASUS-ROG-B850-F', 'asus-rog-b850-f.jpg', 31900, 8, 'AM5', 'B850', 'ATX'],
            ['MSI', 'MSI MAG B850 Tomahawk Max WiFi', 'MB-MSI-B850-TOMAHAWK', 'msi-b850-tomahawk.jpg', 24900, 11, 'AM5', 'B850', 'ATX'],
            ['MSI', 'MSI B850 Gaming Plus WiFi', 'MB-MSI-B850-GAMING-PLUS', 'msi-b850-gaming-plus.jpg', 19900, 13, 'AM5', 'B850', 'ATX'],
            ['MSI', 'MSI PRO B850-P WiFi', 'MB-MSI-PRO-B850-P', 'msi-pro-b850-p.jpg', 17900, 15, 'AM5', 'B850', 'ATX'],
            ['Gigabyte', 'Gigabyte B850 AORUS Elite WiFi7', 'MB-GIG-B850-AORUS-ELITE', 'gigabyte-b850-aorus-elite.webp', 23900, 10, 'AM5', 'B850', 'ATX'],
            ['Gigabyte', 'Gigabyte B850 Gaming X WiFi6E', 'MB-GIG-B850-GAMING-X', 'gigabyte-b850-gaming-x.jpg', 18900, 12, 'AM5', 'B850', 'ATX'],
            ['ASRock', 'ASRock B850 Steel Legend WiFi', 'MB-ASR-B850-STEEL-LEGEND', 'asrock-b850-steel-legend.jpg', 23900, 9, 'AM5', 'B850', 'ATX'],
            ['ASRock', 'ASRock B850 Pro RS WiFi', 'MB-ASR-B850-PRO-RS', 'asrock-b850-pro-rs.png', 18900, 14, 'AM5', 'B850', 'ATX'],
            ['ASUS', 'ASUS TUF Gaming B850M-PLUS WiFi', 'MB-ASUS-TUF-B850M-PLUS', 'asus-tuf-b850m-plus.jpg', 20900, 11, 'AM5', 'B850', 'micro-ATX'],
            ['ASUS', 'ASUS Prime B840M-A WiFi', 'MB-ASUS-PRIME-B840M-A', 'asus-prime-b840m-a.jpeg', 13900, 16, 'AM5', 'B840', 'micro-ATX'],
            ['MSI', 'MSI MAG B850M Mortar WiFi', 'MB-MSI-B850M-MORTAR', 'msi-b850m-mortar.jpg', 21900, 10, 'AM5', 'B850', 'micro-ATX'],
            ['MSI', 'MSI MAG X870 Tomahawk WiFi', 'MB-MSI-X870-TOMAHAWK', 'msi-x870-tomahawk.jpg', 28900, 8, 'AM5', 'X870', 'ATX'],
            ['Gigabyte', 'Gigabyte B850M AORUS Elite WiFi6E', 'MB-GIG-B850M-AORUS-ELITE', 'gigabyte-b850m-aorus-elite.jpg', 19900, 12, 'AM5', 'B850', 'micro-ATX'],
            ['Gigabyte', 'Gigabyte X870 AORUS Elite WiFi7', 'MB-GIG-X870-AORUS-ELITE', 'gigabyte-x870-aorus-elite.webp', 29900, 7, 'AM5', 'X870', 'ATX'],
            ['ASRock', 'ASRock B850M Steel Legend WiFi', 'MB-ASR-B850M-STEEL-LEGEND', 'asrock-b850m-steel-legend.jpg', 19900, 11, 'AM5', 'B850', 'micro-ATX'],
            ['ASRock', 'ASRock B850M Pro RS WiFi', 'MB-ASR-B850M-PRO-RS', 'asrock-b850m-pro-rs.jpg', 16900, 15, 'AM5', 'B850', 'micro-ATX'],
            ['ASRock', 'ASRock X870 Steel Legend WiFi', 'MB-ASR-X870-STEEL-LEGEND', 'asrock-x870-steel-legend.png', 28900, 8, 'AM5', 'X870', 'ATX'],
            ['Gigabyte', 'Gigabyte B650 Eagle AX', 'MB-GIG-B650-EAGLE-AX', 'gigabyte-b650-eagle-ax.webp', 14900, 17, 'AM5', 'B650', 'ATX'],
            ['ASUS', 'ASUS ROG Strix X870E-E Gaming WiFi', 'MB-ASUS-ROG-X870E-E', 'asus-rog-x870e-e.png', 48900, 5, 'AM5', 'X870E', 'ATX'],
            ['MSI', 'MSI PRO Z890-P WiFi', 'MB-MSI-PRO-Z890-P', 'msi-pro-z890-p.jpg', 23900, 9, 'LGA1851', 'Z890', 'ATX'],
            ['Gigabyte', 'Gigabyte Z890 AORUS Elite WiFi7', 'MB-GIG-Z890-AORUS-ELITE', 'gigabyte-z890-aorus-elite.jpeg', 32900, 7, 'LGA1851', 'Z890', 'ATX'],
            ['ASRock', 'ASRock B650M Pro RS WiFi', 'MB-ASR-B650M-PRO-RS', 'asrock-b650m-pro-rs.jpg', 14900, 16, 'AM5', 'B650', 'micro-ATX'],
            ['ASRock', 'ASRock Z890 Steel Legend WiFi', 'MB-ASR-Z890-STEEL-LEGEND', 'asrock-z890-steel-legend.jpg', 29900, 8, 'LGA1851', 'Z890', 'ATX'],
        ];

        foreach ($motherboardProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $socket, $chipset, $formFactor]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $motherboards->id,
                    'name' => $name,
                    'image_path' => 'images/products/motherboards/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'socket' => $socket,
                        'chipset' => $chipset,
                        'form_factor' => $formFactor,
                        'memory_type' => 'DDR5',
                    ],
                ]
            );
        }

        $powerSupplyProducts = [
            ['Corsair', 'Corsair RM650e (2025) 650W Gold', 'PSU-COR-RM650E-2025', 'corsair-rme.png', 10900, 14, '650W', '80 PLUS Gold', 'ATX 3.1'],
            ['Corsair', 'Corsair RM750e (2025) 750W Gold', 'PSU-COR-RM750E-2025', 'corsair-rme.png', 12900, 12, '750W', '80 PLUS Gold', 'ATX 3.1'],
            ['Corsair', 'Corsair RM850e (2025) 850W Gold', 'PSU-COR-RM850E-2025', 'corsair-rme.png', 14900, 10, '850W', '80 PLUS Gold', 'ATX 3.1'],
            ['Corsair', 'Corsair RM1000e (2025) 1000W Gold', 'PSU-COR-RM1000E-2025', 'corsair-rme.png', 19900, 7, '1000W', '80 PLUS Gold', 'ATX 3.1'],
            ['Corsair', 'Corsair RM1000x SHIFT 1000W Gold', 'PSU-COR-RM1000X-SHIFT', 'corsair-rmx-shift.jpg', 22900, 6, '1000W', '80 PLUS Gold', 'ATX 3.0'],
            ['MSI', 'MSI MAG A650GL PCIE5 650W Gold', 'PSU-MSI-MAG-A650GL', 'msi-mag-a-gl.png', 9900, 16, '650W', '80 PLUS Gold', 'ATX 3.0'],
            ['MSI', 'MSI MAG A750GL PCIE5 750W Gold', 'PSU-MSI-MAG-A750GL', 'msi-mag-a-gl.png', 11900, 14, '750W', '80 PLUS Gold', 'ATX 3.0'],
            ['MSI', 'MSI MAG A850GL PCIE5 850W Gold', 'PSU-MSI-MAG-A850GL', 'msi-mag-a-gl.png', 13900, 11, '850W', '80 PLUS Gold', 'ATX 3.0'],
            ['MSI', 'MSI MAG A1000GL PCIE5 1000W Gold', 'PSU-MSI-MAG-A1000GL', 'msi-mag-a-gl.png', 18900, 7, '1000W', '80 PLUS Gold', 'ATX 3.1'],
            ['MSI', 'MSI MPG A1000G PCIE5 1000W Gold', 'PSU-MSI-MPG-A1000G', 'msi-mpg-a1000g.jpg', 21900, 6, '1000W', '80 PLUS Gold', 'ATX 3.0'],
            ['be quiet!', 'be quiet! Pure Power 12 M 650W Gold', 'PSU-BEQ-PP12M-650', 'bequiet-pure-power-12m.jpg', 10900, 15, '650W', '80 PLUS Gold', 'ATX 3.0'],
            ['be quiet!', 'be quiet! Pure Power 12 M 750W Gold', 'PSU-BEQ-PP12M-750', 'bequiet-pure-power-12m.jpg', 12900, 12, '750W', '80 PLUS Gold', 'ATX 3.0'],
            ['be quiet!', 'be quiet! Pure Power 12 M 850W Gold', 'PSU-BEQ-PP12M-850', 'bequiet-pure-power-12m.jpg', 14900, 10, '850W', '80 PLUS Gold', 'ATX 3.0'],
            ['be quiet!', 'be quiet! Pure Power 12 M 1000W Gold', 'PSU-BEQ-PP12M-1000', 'bequiet-pure-power-12m.jpg', 18900, 7, '1000W', '80 PLUS Gold', 'ATX 3.0'],
            ['be quiet!', 'be quiet! Straight Power 12 1000W Platinum', 'PSU-BEQ-SP12-1000', 'bequiet-straight-power-12.jpg', 24900, 5, '1000W', '80 PLUS Platinum', 'ATX 3.0'],
            ['Seasonic', 'Seasonic Focus GX (2024) 750W Gold', 'PSU-SEA-FOCUS-GX-750', 'seasonic-focus-gx.jpg', 13900, 12, '750W', '80 PLUS Gold', 'ATX 3.1'],
            ['Seasonic', 'Seasonic Focus GX (2024) 850W Gold', 'PSU-SEA-FOCUS-GX-850', 'seasonic-focus-gx.jpg', 15900, 10, '850W', '80 PLUS Gold', 'ATX 3.1'],
            ['Seasonic', 'Seasonic Focus GX (2024) 1000W Gold', 'PSU-SEA-FOCUS-GX-1000', 'seasonic-focus-gx.jpg', 19900, 7, '1000W', '80 PLUS Gold', 'ATX 3.1'],
            ['Seasonic', 'Seasonic Vertex GX 850W Gold', 'PSU-SEA-VERTEX-GX-850', 'seasonic-vertex-gx.png', 18900, 8, '850W', '80 PLUS Gold', 'ATX 3.0'],
            ['Seasonic', 'Seasonic Vertex GX 1000W Gold', 'PSU-SEA-VERTEX-GX-1000', 'seasonic-vertex-gx.png', 22900, 6, '1000W', '80 PLUS Gold', 'ATX 3.0'],
            ['ASUS', 'ASUS TUF Gaming 750W Gold', 'PSU-ASUS-TUF-750G', 'asus-tuf-gold.jpg', 13900, 12, '750W', '80 PLUS Gold', 'ATX 3.0'],
            ['ASUS', 'ASUS TUF Gaming 850W Gold', 'PSU-ASUS-TUF-850G', 'asus-tuf-gold.jpg', 15900, 10, '850W', '80 PLUS Gold', 'ATX 3.0'],
            ['ASUS', 'ASUS TUF Gaming 1000W Gold', 'PSU-ASUS-TUF-1000G', 'asus-tuf-gold.jpg', 19900, 7, '1000W', '80 PLUS Gold', 'ATX 3.0'],
            ['ASUS', 'ASUS ROG Strix 850W Gold Aura Edition', 'PSU-ASUS-ROG-850G-AURA', 'asus-rog-strix-850.jpg', 20900, 7, '850W', '80 PLUS Gold', 'ATX 3.0'],
            ['ASUS', 'ASUS ROG Strix 1000W Gold Aura Edition', 'PSU-ASUS-ROG-1000G-AURA', 'asus-rog-strix-1000.jpg', 25900, 5, '1000W', '80 PLUS Gold', 'ATX 3.0'],
        ];

        foreach ($powerSupplyProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $wattage, $efficiency, $standard]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $powerSupplies->id,
                    'name' => $name,
                    'image_path' => 'images/products/power-supplies/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'wattage' => $wattage,
                        'efficiency' => $efficiency,
                        'modularity' => 'Fully modular',
                        'standard' => $standard,
                    ],
                ]
            );
        }

        $fanProducts = [
            ['ARCTIC', 'ARCTIC P12 PWM PST Black 120mm', 'FAN-ARC-P12-PST-BK', 'arctic-p12-black.jpg', 1090, 30, '120mm', 'PWM PST', 'Χωρίς RGB', '1'],
            ['ARCTIC', 'ARCTIC P12 PWM PST White 120mm', 'FAN-ARC-P12-PST-WH', 'arctic-p12-white.jpg', 1190, 25, '120mm', 'PWM PST', 'Χωρίς RGB', '1'],
            ['ARCTIC', 'ARCTIC P12 PWM PST Black 120mm 5-Pack', 'FAN-ARC-P12-PST-BK-5', 'arctic-p12-black.jpg', 3990, 18, '120mm', 'PWM PST', 'Χωρίς RGB', '5'],
            ['ARCTIC', 'ARCTIC P14 PWM PST Black 140mm', 'FAN-ARC-P14-PST-BK', 'arctic-p14-black.jpg', 1290, 26, '140mm', 'PWM PST', 'Χωρίς RGB', '1'],
            ['ARCTIC', 'ARCTIC P14 PWM PST White 140mm', 'FAN-ARC-P14-PST-WH', 'arctic-p14-white.jpg', 1390, 23, '140mm', 'PWM PST', 'Χωρίς RGB', '1'],
            ['ARCTIC', 'ARCTIC P14 PWM PST Black 140mm 5-Pack', 'FAN-ARC-P14-PST-BK-5', 'arctic-p14-black.jpg', 4790, 16, '140mm', 'PWM PST', 'Χωρίς RGB', '5'],
            ['ARCTIC', 'ARCTIC P12 Max Black 120mm', 'FAN-ARC-P12-MAX-BK', 'arctic-p12-max.jpg', 1490, 22, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['ARCTIC', 'ARCTIC P14 Max Black 140mm', 'FAN-ARC-P14-MAX-BK', 'arctic-p14-max.jpg', 1790, 20, '140mm', 'PWM', 'Χωρίς RGB', '1'],
            ['Corsair', 'Corsair RS120 ARGB 120mm', 'FAN-COR-RS120-ARGB', 'corsair-rs120-argb.jpg', 2290, 20, '120mm', 'PWM', 'ARGB', '1'],
            ['Corsair', 'Corsair RS120 ARGB 120mm 3-Pack', 'FAN-COR-RS120-ARGB-3', 'corsair-rs120-argb.jpg', 5990, 12, '120mm', 'PWM', 'ARGB', '3'],
            ['Corsair', 'Corsair RS140 ARGB 140mm', 'FAN-COR-RS140-ARGB', 'corsair-rs140-argb.jpg', 2690, 17, '140mm', 'PWM', 'ARGB', '1'],
            ['Corsair', 'Corsair iCUE LINK RX120 RGB 120mm', 'FAN-COR-RX120-RGB', 'corsair-rx120-rgb.jpg', 3590, 15, '120mm', 'PWM', 'RGB', '1'],
            ['Corsair', 'Corsair iCUE LINK RX120 RGB 120mm 3-Pack', 'FAN-COR-RX120-RGB-3', 'corsair-rx120-rgb.jpg', 9990, 8, '120mm', 'PWM', 'RGB', '3'],
            ['Corsair', 'Corsair AF120 RGB Elite 120mm', 'FAN-COR-AF120-RGB-ELITE', 'corsair-af120-rgb.jpg', 2990, 18, '120mm', 'PWM', 'RGB', '1'],
            ['Noctua', 'Noctua NF-A12x25 PWM 120mm', 'FAN-NOC-NFA12X25-PWM', 'noctua-nfa12x25.jpg', 3290, 16, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['Noctua', 'Noctua NF-A14 PWM 140mm', 'FAN-NOC-NFA14-PWM', 'noctua-nfa14.jpg', 2590, 15, '140mm', 'PWM', 'Χωρίς RGB', '1'],
            ['Noctua', 'Noctua NF-P12 redux-1700 PWM 120mm', 'FAN-NOC-NFP12-REDUX', 'noctua-nfp12-redux.jpg', 1690, 20, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['Noctua', 'Noctua NF-S12A PWM 120mm', 'FAN-NOC-NFS12A-PWM', 'noctua-nfs12a.jpg', 2390, 17, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Silent Wings 4 PWM 120mm', 'FAN-BEQ-SW4-120', 'bequiet-silent-wings-120.jpg', 2590, 15, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Silent Wings 4 PWM 140mm', 'FAN-BEQ-SW4-140', 'bequiet-silent-wings-140.jpg', 2990, 12, '140mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Pure Wings 3 PWM 120mm', 'FAN-BEQ-PW3-120', 'bequiet-pure-wings-120.jpg', 1290, 22, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Pure Wings 3 PWM 140mm', 'FAN-BEQ-PW3-140', 'bequiet-pure-wings-120.jpg', 1490, 18, '140mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Pure Wings 3 PWM High-Speed 120mm', 'FAN-BEQ-PW3-120-HS', 'bequiet-pure-wings-120.jpg', 1390, 19, '120mm', 'PWM', 'Χωρίς RGB', '1'],
            ['be quiet!', 'be quiet! Light Wings PWM ARGB 120mm', 'FAN-BEQ-LW-120-ARGB', 'bequiet-light-wings-120.jpg', 2590, 16, '120mm', 'PWM', 'ARGB', '1'],
            ['be quiet!', 'be quiet! Light Wings PWM ARGB 140mm', 'FAN-BEQ-LW-140-ARGB', 'bequiet-light-wings-120.jpg', 2890, 13, '140mm', 'PWM', 'ARGB', '1'],
        ];

        foreach ($fanProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $size, $control, $lighting, $quantity]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $fans->id,
                    'name' => $name,
                    'image_path' => 'images/products/fans/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'size' => $size,
                        'control' => $control,
                        'lighting' => $lighting,
                        'quantity' => $quantity,
                    ],
                ]
            );
        }

        $externalDriveProducts = [
            ['Western Digital', 'WD My Passport 1TB Portable HDD', 'EXT-WD-MYPASSPORT-1TB', 'wd-my-passport.jpg', 6490, 18, '1TB', 'HDD', 'USB 3.2 Gen 1', 'Micro-B'],
            ['Western Digital', 'WD My Passport 2TB Portable HDD', 'EXT-WD-MYPASSPORT-2TB', 'wd-my-passport.jpg', 8490, 16, '2TB', 'HDD', 'USB 3.2 Gen 1', 'Micro-B'],
            ['Western Digital', 'WD My Passport 4TB Portable HDD', 'EXT-WD-MYPASSPORT-4TB', 'wd-my-passport.jpg', 12900, 12, '4TB', 'HDD', 'USB 3.2 Gen 1', 'Micro-B'],
            ['Western Digital', 'WD My Passport 5TB Portable HDD', 'EXT-WD-MYPASSPORT-5TB', 'wd-my-passport.jpg', 15900, 9, '5TB', 'HDD', 'USB 3.2 Gen 1', 'Micro-B'],
            ['Samsung', 'Samsung Portable SSD T7 1TB', 'EXT-SAM-T7-1TB', 'samsung-t7.jpg', 11900, 15, '1TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T7 2TB', 'EXT-SAM-T7-2TB', 'samsung-t7.jpg', 19900, 12, '2TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T7 4TB', 'EXT-SAM-T7-4TB', 'samsung-t7.jpg', 36900, 6, '4TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T7 Shield 1TB', 'EXT-SAM-T7SHIELD-1TB', 'samsung-t7-shield.jpg', 13900, 13, '1TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T7 Shield 2TB', 'EXT-SAM-T7SHIELD-2TB', 'samsung-t7-shield.jpg', 22900, 10, '2TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T7 Shield 4TB', 'EXT-SAM-T7SHIELD-4TB', 'samsung-t7-shield.jpg', 39900, 5, '4TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T9 1TB', 'EXT-SAM-T9-1TB', 'samsung-t9.jpg', 15900, 12, '1TB', 'SSD', 'USB 3.2 Gen 2x2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T9 2TB', 'EXT-SAM-T9-2TB', 'samsung-t9.jpg', 26900, 9, '2TB', 'SSD', 'USB 3.2 Gen 2x2', 'USB-C'],
            ['Samsung', 'Samsung Portable SSD T9 4TB', 'EXT-SAM-T9-4TB', 'samsung-t9.jpg', 45900, 5, '4TB', 'SSD', 'USB 3.2 Gen 2x2', 'USB-C'],
            ['Seagate', 'Seagate Expansion Portable 1TB HDD', 'EXT-SEA-EXPANSION-1TB', 'seagate-expansion.png', 5990, 18, '1TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate Expansion Portable 2TB HDD', 'EXT-SEA-EXPANSION-2TB', 'seagate-expansion.png', 7990, 15, '2TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate Expansion Portable 4TB HDD', 'EXT-SEA-EXPANSION-4TB', 'seagate-expansion.png', 11900, 11, '4TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate Expansion Portable 5TB HDD', 'EXT-SEA-EXPANSION-5TB', 'seagate-expansion.png', 14900, 8, '5TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate One Touch Portable 1TB HDD', 'EXT-SEA-ONETOUCH-1TB', 'seagate-one-touch.jpg', 6990, 16, '1TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate One Touch Portable 2TB HDD', 'EXT-SEA-ONETOUCH-2TB', 'seagate-one-touch.jpg', 8990, 14, '2TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['Seagate', 'Seagate One Touch Portable 4TB HDD', 'EXT-SEA-ONETOUCH-4TB', 'seagate-one-touch.jpg', 13900, 9, '4TB', 'HDD', 'USB 3.0', 'Micro-B'],
            ['SanDisk', 'SanDisk Extreme Portable SSD 1TB', 'EXT-SAN-EXTREME-1TB', 'sandisk-extreme.jpg', 12900, 14, '1TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['SanDisk', 'SanDisk Extreme Portable SSD 2TB', 'EXT-SAN-EXTREME-2TB', 'sandisk-extreme.jpg', 21900, 10, '2TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['SanDisk', 'SanDisk Extreme Portable SSD 4TB', 'EXT-SAN-EXTREME-4TB', 'sandisk-extreme.jpg', 39900, 6, '4TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Crucial', 'Crucial X9 Portable SSD 2TB', 'EXT-CRU-X9-2TB', 'crucial-x9.jpg', 18900, 11, '2TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
            ['Kingston', 'Kingston XS1000 Portable SSD 1TB', 'EXT-KIN-XS1000-1TB', 'kingston-xs1000.jpg', 10900, 13, '1TB', 'SSD', 'USB 3.2 Gen 2', 'USB-C'],
        ];

        foreach ($externalDriveProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $capacity, $driveType, $interface, $connector]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $externalDrives->id,
                    'name' => $name,
                    'image_path' => 'images/products/external-drives/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'capacity' => $capacity,
                        'drive_type' => $driveType,
                        'interface' => $interface,
                        'connector' => $connector,
                    ],
                ]
            );
        }

        $internalDriveProducts = [
            ['Samsung', 'Samsung 870 EVO 500GB SATA SSD', 'DISK-SAM-870EVO-500GB', 'samsung-870-evo.jpg', 6900, 16, '500GB', 'SSD', '2.5-inch'],
            ['Samsung', 'Samsung 870 EVO 1TB SATA SSD', 'DISK-SAM-870EVO-1TB', 'samsung-870-evo.jpg', 11900, 14, '1TB', 'SSD', '2.5-inch'],
            ['Samsung', 'Samsung 870 EVO 2TB SATA SSD', 'DISK-SAM-870EVO-2TB', 'samsung-870-evo.jpg', 19900, 10, '2TB', 'SSD', '2.5-inch'],
            ['Samsung', 'Samsung 870 EVO 4TB SATA SSD', 'DISK-SAM-870EVO-4TB', 'samsung-870-evo.jpg', 36900, 5, '4TB', 'SSD', '2.5-inch'],
            ['Western Digital', 'WD Blue SA510 500GB SATA SSD', 'DISK-WD-SA510-500GB', 'wd-blue-sa510.jpg', 5900, 18, '500GB', 'SSD', '2.5-inch'],
            ['Western Digital', 'WD Blue SA510 1TB SATA SSD', 'DISK-WD-SA510-1TB', 'wd-blue-sa510.jpg', 9900, 15, '1TB', 'SSD', '2.5-inch'],
            ['Western Digital', 'WD Blue SA510 2TB SATA SSD', 'DISK-WD-SA510-2TB', 'wd-blue-sa510.jpg', 17900, 10, '2TB', 'SSD', '2.5-inch'],
            ['Kingston', 'Kingston KC600 256GB SATA SSD', 'DISK-KIN-KC600-256GB', 'kingston-kc600.jpg', 4900, 20, '256GB', 'SSD', '2.5-inch'],
            ['Kingston', 'Kingston KC600 512GB SATA SSD', 'DISK-KIN-KC600-512GB', 'kingston-kc600.jpg', 6900, 18, '512GB', 'SSD', '2.5-inch'],
            ['Kingston', 'Kingston KC600 1TB SATA SSD', 'DISK-KIN-KC600-1TB', 'kingston-kc600.jpg', 11900, 14, '1TB', 'SSD', '2.5-inch'],
            ['Kingston', 'Kingston KC600 2TB SATA SSD', 'DISK-KIN-KC600-2TB', 'kingston-kc600.jpg', 21900, 9, '2TB', 'SSD', '2.5-inch'],
            ['Western Digital', 'WD Blue 1TB 3.5-inch HDD', 'DISK-WD-BLUE-1TB', 'wd-blue-hdd.png', 5900, 20, '1TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Blue 2TB 3.5-inch HDD', 'DISK-WD-BLUE-2TB', 'wd-blue-hdd.png', 7500, 17, '2TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Blue 4TB 3.5-inch HDD', 'DISK-WD-BLUE-4TB', 'wd-blue-hdd.png', 10900, 12, '4TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Blue 6TB 3.5-inch HDD', 'DISK-WD-BLUE-6TB', 'wd-blue-hdd.png', 15900, 8, '6TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Black 2TB 3.5-inch HDD', 'DISK-WD-BLACK-2TB', 'wd-black-hdd.jpg', 12900, 12, '2TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Black 4TB 3.5-inch HDD', 'DISK-WD-BLACK-4TB', 'wd-black-hdd.jpg', 18900, 9, '4TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Black 6TB 3.5-inch HDD', 'DISK-WD-BLACK-6TB', 'wd-black-hdd.jpg', 24900, 6, '6TB', 'HDD', '3.5-inch'],
            ['Western Digital', 'WD Black 8TB 3.5-inch HDD', 'DISK-WD-BLACK-8TB', 'wd-black-hdd.jpg', 29900, 4, '8TB', 'HDD', '3.5-inch'],
            ['Seagate', 'Seagate BarraCuda 1TB 3.5-inch HDD', 'DISK-SEA-BARRACUDA-1TB', 'seagate-barracuda.jpg', 5500, 20, '1TB', 'HDD', '3.5-inch'],
            ['Seagate', 'Seagate BarraCuda 2TB 3.5-inch HDD', 'DISK-SEA-BARRACUDA-2TB', 'seagate-barracuda.jpg', 6900, 18, '2TB', 'HDD', '3.5-inch'],
            ['Seagate', 'Seagate BarraCuda 4TB 3.5-inch HDD', 'DISK-SEA-BARRACUDA-4TB', 'seagate-barracuda.jpg', 9900, 13, '4TB', 'HDD', '3.5-inch'],
            ['Seagate', 'Seagate BarraCuda 8TB 3.5-inch HDD', 'DISK-SEA-BARRACUDA-8TB', 'seagate-barracuda.jpg', 19900, 7, '8TB', 'HDD', '3.5-inch'],
            ['Toshiba', 'Toshiba P300 2TB 3.5-inch HDD', 'DISK-TOS-P300-2TB', 'toshiba-p300.jpg', 6900, 16, '2TB', 'HDD', '3.5-inch'],
            ['Toshiba', 'Toshiba P300 4TB 3.5-inch HDD', 'DISK-TOS-P300-4TB', 'toshiba-p300.jpg', 10900, 10, '4TB', 'HDD', '3.5-inch'],
        ];

        foreach ($internalDriveProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $capacity, $driveType, $formFactor]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $internalDrives->id,
                    'name' => $name,
                    'image_path' => 'images/products/ssd-hdd/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'capacity' => $capacity,
                        'drive_type' => $driveType,
                        'interface' => 'SATA III',
                        'form_factor' => $formFactor,
                    ],
                ]
            );
        }

        $usbStickProducts = [
            ['Kingston', 'Kingston DataTraveler Exodia 64GB', 'USB-KIN-EXODIA-64GB', 'kingston-exodia.jpg', 790, 40, '64GB', 'USB 3.2 Gen 1', 'USB-A', 'Καπάκι'],
            ['Kingston', 'Kingston DataTraveler Exodia 128GB', 'USB-KIN-EXODIA-128GB', 'kingston-exodia.jpg', 1190, 36, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Καπάκι'],
            ['Kingston', 'Kingston DataTraveler Exodia 256GB', 'USB-KIN-EXODIA-256GB', 'kingston-exodia.jpg', 2190, 29, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Καπάκι'],
            ['Kingston', 'Kingston DataTraveler Exodia M 64GB', 'USB-KIN-EXODIAM-64GB', 'kingston-exodia-m.jpg', 890, 38, '64GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['Kingston', 'Kingston DataTraveler Exodia M 128GB', 'USB-KIN-EXODIAM-128GB', 'kingston-exodia-m.jpg', 1390, 33, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['Kingston', 'Kingston DataTraveler Exodia M 256GB', 'USB-KIN-EXODIAM-256GB', 'kingston-exodia-m.jpg', 2490, 25, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['Kingston', 'Kingston DataTraveler Max USB-C 256GB', 'USB-KIN-DTMAX-256GB', 'kingston-dt-max.jpg', 3490, 24, '256GB', 'USB 3.2 Gen 2', 'USB-C', 'Συρόμενο'],
            ['Kingston', 'Kingston DataTraveler Max USB-C 512GB', 'USB-KIN-DTMAX-512GB', 'kingston-dt-max.jpg', 5990, 17, '512GB', 'USB 3.2 Gen 2', 'USB-C', 'Συρόμενο'],
            ['Kingston', 'Kingston DataTraveler Max USB-C 1TB', 'USB-KIN-DTMAX-1TB', 'kingston-dt-max.jpg', 10900, 9, '1TB', 'USB 3.2 Gen 2', 'USB-C', 'Συρόμενο'],
            ['SanDisk', 'SanDisk Ultra Flair 64GB', 'USB-SAN-ULTRAFLAIR-64GB', 'sandisk-ultra-flair.jpg', 990, 38, '64GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['SanDisk', 'SanDisk Ultra Flair 128GB', 'USB-SAN-ULTRAFLAIR-128GB', 'sandisk-ultra-flair.jpg', 1490, 34, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['SanDisk', 'SanDisk Ultra Flair 256GB', 'USB-SAN-ULTRAFLAIR-256GB', 'sandisk-ultra-flair.jpg', 2690, 27, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['SanDisk', 'SanDisk Ultra Flair 512GB', 'USB-SAN-ULTRAFLAIR-512GB', 'sandisk-ultra-flair.jpg', 4990, 18, '512GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['SanDisk', 'SanDisk Ultra Dual Drive Go 64GB', 'USB-SAN-DUALGO-64GB', 'sandisk-dual-drive-go.jpg', 1190, 35, '64GB', 'USB 3.2 Gen 1', 'USB-A / USB-C', 'Περιστρεφόμενο'],
            ['SanDisk', 'SanDisk Ultra Dual Drive Go 128GB', 'USB-SAN-DUALGO-128GB', 'sandisk-dual-drive-go.jpg', 1890, 30, '128GB', 'USB 3.2 Gen 1', 'USB-A / USB-C', 'Περιστρεφόμενο'],
            ['SanDisk', 'SanDisk Ultra Dual Drive Go 256GB', 'USB-SAN-DUALGO-256GB', 'sandisk-dual-drive-go.jpg', 3290, 24, '256GB', 'USB 3.2 Gen 1', 'USB-A / USB-C', 'Περιστρεφόμενο'],
            ['SanDisk', 'SanDisk Extreme PRO USB 3.2 128GB', 'USB-SAN-EXTPRO-128GB', 'sandisk-extreme-pro.jpg', 2990, 25, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['SanDisk', 'SanDisk Extreme PRO USB 3.2 256GB', 'USB-SAN-EXTPRO-256GB', 'sandisk-extreme-pro.jpg', 4990, 19, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['SanDisk', 'SanDisk Extreme PRO USB 3.2 512GB', 'USB-SAN-EXTPRO-512GB', 'sandisk-extreme-pro.jpg', 8990, 12, '512GB', 'USB 3.2 Gen 1', 'USB-A', 'Συρόμενο'],
            ['Samsung', 'Samsung BAR Plus 64GB', 'USB-SAM-BARPLUS-64GB', 'samsung-bar-plus.png', 1290, 32, '64GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['Samsung', 'Samsung BAR Plus 128GB', 'USB-SAM-BARPLUS-128GB', 'samsung-bar-plus.png', 1990, 28, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['Samsung', 'Samsung BAR Plus 256GB', 'USB-SAM-BARPLUS-256GB', 'samsung-bar-plus.png', 3490, 20, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Μεταλλικό'],
            ['Samsung', 'Samsung FIT Plus 64GB', 'USB-SAM-FITPLUS-64GB', 'samsung-fit-plus.jpg', 1190, 35, '64GB', 'USB 3.2 Gen 1', 'USB-A', 'Μικρό προφίλ'],
            ['Samsung', 'Samsung FIT Plus 128GB', 'USB-SAM-FITPLUS-128GB', 'samsung-fit-plus.jpg', 1790, 29, '128GB', 'USB 3.2 Gen 1', 'USB-A', 'Μικρό προφίλ'],
            ['Samsung', 'Samsung FIT Plus 256GB', 'USB-SAM-FITPLUS-256GB', 'samsung-fit-plus.jpg', 3290, 21, '256GB', 'USB 3.2 Gen 1', 'USB-A', 'Μικρό προφίλ'],
        ];

        foreach ($usbStickProducts as [$brandName, $name, $sku, $imageName, $priceCents, $stock, $capacity, $interface, $connector, $design]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $usbSticks->id,
                    'name' => $name,
                    'image_path' => 'images/products/usb-sticks/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => $stock,
                    'specs' => [
                        'capacity' => $capacity,
                        'interface' => $interface,
                        'connector' => $connector,
                        'design' => $design,
                    ],
                ]
            );
        }

        $memoryCardFamilies = [
            ['SanDisk', 'Ultra microSDXC', 'SAN-ULTRA-MICRO', 'sandisk-ultra.webp', 'microSDXC', 'Class 10 / U1', ['64GB' => 990, '128GB' => 1490, '256GB' => 2690, '512GB' => 4990]],
            ['SanDisk', 'Extreme microSDXC', 'SAN-EXTREME-MICRO', 'sandisk-extreme.jpg', 'microSDXC', 'U3 / V30', ['64GB' => 1490, '128GB' => 2290, '256GB' => 3990, '512GB' => 7490]],
            ['SanDisk', 'Extreme PRO SDXC', 'SAN-EXTREMEPRO-SD', 'sandisk-extreme-pro-sd.jpg', 'SDXC', 'U3 / V30', ['64GB' => 1990, '128GB' => 3290, '256GB' => 5990, '512GB' => 10990]],
            ['Samsung', 'EVO Plus microSDXC', 'SAM-EVOPLUS-MICRO', 'samsung-evo-plus.jpg', 'microSDXC', 'U3 / V30', ['128GB' => 1690, '256GB' => 2990, '512GB' => 5490]],
            ['Samsung', 'PRO Plus microSDXC', 'SAM-PROPLUS-MICRO', 'samsung-pro-plus.jpg', 'microSDXC', 'U3 / V30', ['128GB' => 2390, '256GB' => 4190, '512GB' => 7590]],
            ['Kingston', 'Canvas Select Plus microSDXC', 'KIN-SELECTPLUS-MICRO', 'kingston-canvas-select.jpg', 'microSDXC', 'U1 / V10', ['64GB' => 890, '128GB' => 1390, '256GB' => 2490]],
            ['Kingston', 'Canvas Go! Plus microSDXC', 'KIN-GOPLUS-MICRO', 'kingston-canvas-go.webp', 'microSDXC', 'U3 / V30', ['128GB' => 2090, '256GB' => 3690]],
            ['Lexar', 'Professional 1066x SDXC', 'LEX-1066X-SD', 'lexar-1066x.png', 'SDXC', 'U3 / V30', ['128GB' => 2990, '256GB' => 5190]],
        ];

        foreach ($memoryCardFamilies as [$brandName, $model, $skuPrefix, $imageName, $formFactor, $speedClass, $variants]) {
            foreach ($variants as $capacity => $priceCents) {
                Product::query()->updateOrCreate(
                    ['sku' => 'CARD-'.$skuPrefix.'-'.$capacity],
                    [
                        'brand_id' => $brands[$brandName]->id,
                        'category_id' => $memoryCards->id,
                        'name' => $brandName.' '.$model.' '.$capacity,
                        'image_path' => 'images/products/memory-cards/'.$imageName,
                        'price_cents' => $priceCents,
                        'stock' => 24,
                        'specs' => [
                            'capacity' => $capacity,
                            'form_factor' => $formFactor,
                            'interface' => 'UHS-I',
                            'speed_class' => $speedClass,
                        ],
                    ]
                );
            }
        }

        $enclosureProducts = [
            ['ORICO', '2139U3', 'orico-2139u3.jpg', 1290, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['ORICO', '25PW1-U3', 'orico-25pw1.jpg', 1390, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['ORICO', '25PW1-C3', 'orico-25pw1.jpg', 1790, '2.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
            ['ORICO', '2520U3', 'orico-2520u3.jpg', 1590, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['ORICO', '2520C3', 'orico-2520u3.jpg', 1990, '2.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
            ['ORICO', '2588U3', 'orico-2588u3.jpg', 1690, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['ORICO', '2588C3', 'orico-2588u3.jpg', 2090, '2.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
            ['ORICO', 'M2PV-C3', 'orico-m2pv.jpg', 2490, 'M.2 NVMe', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['ORICO', 'M2PJN-C3', 'orico-m2pjn.jpg', 2990, 'M.2 NVMe/SATA', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['ORICO', 'PWM2-G2', 'orico-pwm2.jpg', 3290, 'M.2 NVMe', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['ORICO', 'PWDM2-G2', 'orico-pwm2.jpg', 3690, 'M.2 NVMe/SATA', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['ORICO', 'M35C3', 'orico-m35c3.jpg', 3690, '3.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
            ['ORICO', '6518US3', 'orico-6518us3.jpg', 2990, '2.5"/3.5" SATA', 'USB 3.0', 'USB-B', 'Dock'],
            ['ORICO', '6228US3', 'orico-6228us3.jpg', 4990, '2 x 2.5"/3.5" SATA', 'USB 3.0', 'USB-B', 'Dock'],
            ['UGREEN', '2.5 SATA USB 3.0', 'ugreen-25.png', 1890, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['UGREEN', '2.5 SATA USB-C', 'ugreen-25.png', 2290, '2.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
            ['UGREEN', '3.5 SATA USB 3.0', 'ugreen-35.jpg', 3490, '3.5" SATA', 'USB 3.0', 'USB-B', 'Θήκη'],
            ['UGREEN', 'M.2 NVMe/SATA 10Gbps', 'ugreen-m2-10.jpg', 3690, 'M.2 NVMe/SATA', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['UGREEN', 'M.2 NVMe 20Gbps', 'ugreen-m2-20.jpg', 5990, 'M.2 NVMe', 'USB 3.2 Gen 2x2', 'USB-C', 'Θήκη'],
            ['Sabrent', 'EC-UASP', 'sabrent-ec-uasp.jpg', 1990, '2.5" SATA', 'USB 3.0', 'Micro-B', 'Θήκη'],
            ['Sabrent', 'EC-UM30', 'sabrent-ec-um30.jpg', 2490, 'M.2 SATA', 'USB 3.0', 'USB-A', 'Θήκη'],
            ['Sabrent', 'EC-NVME', 'sabrent-ec-nvme.jpg', 3490, 'M.2 NVMe', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['Sabrent', 'EC-SNVE', 'sabrent-ec-snve.webp', 3990, 'M.2 NVMe/SATA', 'USB 3.2 Gen 2', 'USB-C', 'Θήκη'],
            ['Sabrent', 'EC-DFLT', 'sabrent-ec-dflt.jpg', 3990, '2.5"/3.5" SATA', 'USB 3.0', 'USB-B', 'Dock'],
            ['ORICO', '2139C3', 'orico-2139u3.jpg', 1790, '2.5" SATA', 'USB 3.1', 'USB-C', 'Θήκη'],
        ];

        foreach ($enclosureProducts as [$brandName, $model, $imageName, $priceCents, $driveSupport, $interface, $connector, $enclosureType]) {
            Product::query()->updateOrCreate(
                ['sku' => 'ENC-'.Str::upper(Str::slug($brandName.'-'.$model))],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $driveEnclosures->id,
                    'name' => $brandName.' '.$model.' '.($enclosureType === 'Dock' ? 'Dock' : 'Θήκη δίσκου'),
                    'image_path' => 'images/products/drive-enclosures/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 18,
                    'specs' => [
                        'drive_support' => $driveSupport,
                        'interface' => $interface,
                        'connector' => $connector,
                        'enclosure_type' => $enclosureType,
                    ],
                ]
            );
        }

        $discFamilies = [
            ['DVD-R', 'verbatim-dvd-r.jpg', '4.7GB', '16x', [10 => 690, 25 => 1390, 50 => 2490, 100 => 4490]],
            ['DVD+R', 'verbatim-dvd-plus-r.jpg', '4.7GB', '16x', [10 => 790, 25 => 1490, 50 => 2590, 100 => 4690]],
            ['CD-R', 'verbatim-cd-r.jpg', '700MB', '52x', [10 => 590, 25 => 1090, 50 => 1890, 100 => 3490]],
            ['DVD-RW', 'verbatim-dvd-rw.jpg', '4.7GB', '4x', [5 => 990, 10 => 1690]],
            ['DVD+RW', 'verbatim-dvd-plus-rw.jpg', '4.7GB', '4x', [5 => 1090, 10 => 1790]],
            ['CD-RW', 'verbatim-cd-rw.png', '700MB', '12x', [10 => 1290]],
        ];

        foreach ($discFamilies as [$format, $imageName, $capacity, $writeSpeed, $packs]) {
            foreach ($packs as $quantity => $priceCents) {
                Product::query()->updateOrCreate(
                    ['sku' => 'DISC-VER-'.Str::upper(Str::slug($format)).'-'.$quantity],
                    [
                        'brand_id' => $brands['Verbatim']->id,
                        'category_id' => $opticalMedia->id,
                        'name' => 'Verbatim '.$format.' '.$writeSpeed.' '.$quantity.' τεμάχια',
                        'image_path' => 'images/products/cd-dvd/'.$imageName,
                        'price_cents' => $priceCents,
                        'stock' => 30,
                        'specs' => [
                            'format' => $format,
                            'capacity' => $capacity,
                            'write_speed' => $writeSpeed,
                            'quantity' => (string) $quantity,
                        ],
                    ]
                );
            }
        }

        $opticalDrives = [
            ['ASUS', 'SDRW-08D2S-U', 'asus-08d2s.webp', 3490, 'USB 2.0', '8x', 'Εξωτερικό'],
            ['ASUS', 'SDRW-08U9M-U', 'asus-08u9m.png', 4490, 'USB-C', '8x', 'Εξωτερικό'],
            ['ASUS', 'DRW-24D5MT', 'asus-24d5mt.jpg', 2490, 'SATA', '24x', 'Εσωτερικό'],
            ['ASUS', 'SDRW-08D6S-U', 'asus-08d6s.png', 3990, 'USB 2.0', '8x', 'Εξωτερικό'],
            ['LG', 'GP57EB40', 'lg-gp57.jpg', 3290, 'USB 2.0', '8x', 'Εξωτερικό'],
            ['LG', 'GP60NB50', 'lg-gp60.jpg', 3590, 'USB 2.0', '8x', 'Εξωτερικό'],
            ['LG', 'GH24NSD1', 'lg-gh24.jpg', 2390, 'SATA', '24x', 'Εσωτερικό'],
            ['LG', 'GP65NB60', 'lg-gp65.jpg', 3890, 'USB 2.0', '8x', 'Εξωτερικό'],
        ];

        foreach ($opticalDrives as [$brandName, $model, $imageName, $priceCents, $interface, $writeSpeed, $formFactor]) {
            Product::query()->updateOrCreate(
                ['sku' => 'ODD-'.Str::upper(Str::slug($brandName.'-'.$model))],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $opticalMedia->id,
                    'name' => $brandName.' '.$model.' DVD Writer',
                    'image_path' => 'images/products/cd-dvd/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 16,
                    'specs' => [
                        'drive_type' => 'DVD±RW',
                        'interface' => $interface,
                        'write_speed' => $writeSpeed,
                        'form_factor' => $formFactor,
                    ],
                ]
            );
        }
        $keyboardProducts = [
            ['Razer', 'Razer Huntsman V3 Pro 8KHz', 'KBD-RAZ-HUNTSMAN-V3-PRO-8KHZ', 'razer-huntsman-v3-pro-8khz.png', 26900, 'Full-size', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer Huntsman V3 Pro Tenkeyless 8KHz', 'KBD-RAZ-HUNTSMAN-V3-PRO-TENKEYLESS-8KHZ', 'razer-huntsman-v3-pro-tenkeyless-8khz.png', 23900, 'Tenkeyless', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer Huntsman V3 Pro', 'KBD-RAZ-HUNTSMAN-V3-PRO', 'razer-huntsman-v3-pro.png', 19900, 'Full-size', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer Huntsman V3 Pro Tenkeyless', 'KBD-RAZ-HUNTSMAN-V3-PRO-TENKEYLESS', 'razer-huntsman-v3-pro-tenkeyless.png', 21900, 'Tenkeyless', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer Huntsman V3 Pro Mini', 'KBD-RAZ-HUNTSMAN-V3-PRO-MINI', 'razer-huntsman-v3-pro-mini.png', 18900, '60%', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer BlackWidow V4 Pro', 'KBD-RAZ-BLACKWIDOW-V4-PRO', 'razer-blackwidow-v4-pro.png', 24900, 'Full-size', 'Ενσύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer BlackWidow V4 Low-Profile HyperSpeed', 'KBD-RAZ-BLACKWIDOW-V4-LOW-PROFILE-HYPERSPEED', 'razer-blackwidow-v4-low-profile-hyperspeed.png', 21900, 'Full-size', 'Ασύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer BlackWidow V4 Low-Profile Tenkeyless HyperSpeed', 'KBD-RAZ-BLACKWIDOW-V4-LOW-PROFILE-TENKEYLESS-HYPERSPEED', 'razer-blackwidow-v4-low-profile-tenkeyless-hyperspeed.png', 19900, 'Tenkeyless', 'Ασύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer DeathStalker V2 Pro', 'KBD-RAZ-DEATHSTALKER-V2-PRO', 'razer-deathstalker-v2-pro.png', 24900, 'Full-size', 'Ασύρματο', 'Οπτικοί'],
            ['Razer', 'Razer DeathStalker V2 Pro Tenkeyless', 'KBD-RAZ-DEATHSTALKER-V2-PRO-TENKEYLESS', 'razer-deathstalker-v2-pro-tenkeyless.png', 21900, 'Tenkeyless', 'Ασύρματο', 'Οπτικοί'],
            ['Razer', 'Razer DeathStalker V2', 'KBD-RAZ-DEATHSTALKER-V2', 'razer-deathstalker-v2.png', 19900, 'Full-size', 'Ενσύρματο', 'Οπτικοί'],
            ['Razer', 'Razer Ornata V3', 'KBD-RAZ-ORNATA-V3', 'razer-ornata-v3.png', 9900, 'Full-size', 'Ενσύρματο', 'Mecha-membrane'],
            ['Razer', 'Razer Ornata V3 Tenkeyless', 'KBD-RAZ-ORNATA-V3-TENKEYLESS', 'razer-ornata-v3-tenkeyless.png', 7900, 'Tenkeyless', 'Ενσύρματο', 'Mecha-membrane'],
            ['Razer', 'Razer Ornata V3 X', 'KBD-RAZ-ORNATA-V3-X', 'razer-ornata-v3-x.png', 4900, 'Full-size', 'Ενσύρματο', 'Mecha-membrane'],
            ['Razer', 'Razer BlackWidow V4 Pro 75', 'KBD-RAZ-BLACKWIDOW-V4-PRO-75', 'razer-blackwidow-v4-pro-75.png', 26900, '75%', 'Ασύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer BlackWidow V4 75', 'KBD-RAZ-BLACKWIDOW-V4-75', 'razer-blackwidow-v4-75.png', 19900, '75%', 'Ενσύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer BlackWidow V4 Tenkeyless HyperSpeed', 'KBD-RAZ-BLACKWIDOW-V4-TENKEYLESS-HYPERSPEED', 'razer-blackwidow-v4-tenkeyless-hyperspeed.png', 14900, 'Tenkeyless', 'Ασύρματο', 'Μηχανικοί'],
            ['Razer', 'Razer BlackWidow V4 X', 'KBD-RAZ-BLACKWIDOW-V4-X', 'razer-blackwidow-v4-x.png', 11900, 'Full-size', 'Ενσύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G213 Prodigy', 'KBD-LOG-G213-PRODIGY', 'logitech-g213-prodigy-keyboard.jpg', 5500, 'Full-size', 'Ενσύρματο', 'Membrane'],
            ['Logitech', 'Logitech G413 SE', 'KBD-LOG-G413-SE', 'logitech-g413-se-keyboard.png', 7900, 'Full-size', 'Ενσύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G512 Carbon', 'KBD-LOG-G512-CARBON', 'logitech-g512-carbon-keyboard.jpg', 10900, 'Full-size', 'Ενσύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G PRO X TKL', 'KBD-LOG-G-PRO-X-TKL', 'logitech-g-pro-x-tkl-keyboard.jpg', 17900, 'Tenkeyless', 'Ασύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G915 LIGHTSPEED', 'KBD-LOG-G915-LIGHTSPEED', 'logitech-g915-lightspeed-keyboard.jpg', 16900, 'Full-size', 'Ασύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G915 TKL', 'KBD-LOG-G915-TKL', 'logitech-g915-tkl-keyboard.jpg', 15900, 'Tenkeyless', 'Ασύρματο', 'Μηχανικοί'],
            ['Logitech', 'Logitech G715 wireless', 'KBD-LOG-G715-WIRELESS', 'logitech-g715-wireless-keyboard.jpg', 16900, 'Tenkeyless', 'Ασύρματο', 'Μηχανικοί'],
        ];

        foreach ($keyboardProducts as [$brandName, $name, $sku, $imageName, $priceCents, $layout, $connection, $keyType]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $keyboards->id,
                    'name' => $name,
                    'image_path' => 'images/products/keyboards/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 16,
                    'specs' => [
                        'layout' => $layout,
                        'connection' => $connection,
                        'key_type' => $keyType,
                    ],
                ]
            );
        }

        $headsetProducts = [
            ['Razer', 'Razer BlackShark V3 Pro', 'HSET-RAZ-BLACKSHARK-V3-PRO', 'razer-blackshark-v3-pro.png', 29900, 'Ασύρματο'],
            ['Razer', 'Razer BlackShark V3', 'HSET-RAZ-BLACKSHARK-V3', 'razer-blackshark-v3.png', 16900, 'Ασύρματο'],
            ['Razer', 'Razer BlackShark V3 X HyperSpeed', 'HSET-RAZ-BLACKSHARK-V3-X-HYPERSPEED', 'razer-blackshark-v3-x-hyperspeed.png', 10900, 'Ασύρματο'],
            ['Razer', 'Razer Kraken V4 Pro', 'HSET-RAZ-KRAKEN-V4-PRO', 'razer-kraken-v4-pro.png', 39900, 'Ασύρματο'],
            ['Razer', 'Razer Kraken V4', 'HSET-RAZ-KRAKEN-V4', 'razer-kraken-v4.png', 17900, 'Ασύρματο'],
            ['Razer', 'Razer Kraken V4 X', 'HSET-RAZ-KRAKEN-V4-X', 'razer-kraken-v4-x.png', 7900, 'Ενσύρματο'],
            ['Razer', 'Razer Barracuda Pro', 'HSET-RAZ-BARRACUDA-PRO', 'razer-barracuda-pro.png', 24900, 'Ασύρματο'],
            ['Razer', 'Razer Barracuda', 'HSET-RAZ-BARRACUDA', 'razer-barracuda.png', 15900, 'Ασύρματο'],
            ['Razer', 'Razer Barracuda X Chroma', 'HSET-RAZ-BARRACUDA-X-CHROMA', 'razer-barracuda-x-chroma.png', 12900, 'Ασύρματο'],
            ['Razer', 'Razer Barracuda X', 'HSET-RAZ-BARRACUDA-X', 'razer-barracuda-x.png', 9900, 'Ασύρματο'],
            ['Razer', 'Razer Kraken Kitty V3 Pro', 'HSET-RAZ-KRAKEN-KITTY-V3-PRO', 'razer-kraken-kitty-v3-pro.png', 21900, 'Ασύρματο'],
            ['Razer', 'Razer Kraken Kitty V3 X', 'HSET-RAZ-KRAKEN-KITTY-V3-X', 'razer-kraken-kitty-v3-x.png', 9900, 'Ενσύρματο'],
            ['Razer', 'Razer Kraken Kitty V2', 'HSET-RAZ-KRAKEN-KITTY-V2', 'razer-kraken-kitty-v2.png', 11900, 'Ενσύρματο'],
            ['Razer', 'Razer Kraken Kitty V2 BT', 'HSET-RAZ-KRAKEN-KITTY-V2-BT', 'razer-kraken-kitty-v2-bt.png', 12900, 'Ασύρματο'],
            ['Logitech', 'Logitech G335', 'HSET-LOG-G335', 'logitech-g335-headset.png', 4900, 'Ενσύρματο'],
            ['Logitech', 'Logitech G435', 'HSET-LOG-G435', 'logitech-g435-headset.jpg', 6900, 'Ασύρματο'],
            ['Logitech', 'Logitech G733', 'HSET-LOG-G733', 'logitech-g733-headset.jpg', 11900, 'Ασύρματο'],
            ['Logitech', 'Logitech G PRO X 2 LIGHTSPEED', 'HSET-LOG-G-PRO-X-2-LIGHTSPEED', 'logitech-g-pro-x-2-lightspeed-headset.jpg', 21900, 'Ασύρματο'],
            ['Logitech', 'Logitech G PRO X', 'HSET-LOG-G-PRO-X', 'logitech-g-pro-x-headset.jpg', 12900, 'Ενσύρματο'],
            ['Logitech', 'Logitech Astro A10 Gen 2', 'HSET-LOG-ASTRO-A10-GEN-2', 'logitech-astro-a10-gen-2-headset.jpg', 5500, 'Ενσύρματο'],
            ['HyperX', 'HyperX Cloud III', 'HSET-HYP-CLOUD-III', 'hyperx-cloud-iii-headset.jpg', 9900, 'Ενσύρματο'],
            ['HyperX', 'HyperX Cloud III Wireless', 'HSET-HYP-CLOUD-III-WIRELESS', 'hyperx-cloud-iii-wireless-headset.jpg', 14900, 'Ασύρματο'],
            ['HyperX', 'HyperX Cloud Alpha', 'HSET-HYP-CLOUD-ALPHA', 'hyperx-cloud-alpha-headset.jpg', 8900, 'Ενσύρματο'],
            ['HyperX', 'HyperX Cloud Alpha Wireless', 'HSET-HYP-CLOUD-ALPHA-WIRELESS', 'hyperx-cloud-alpha-wireless-headset.jpg', 15900, 'Ασύρματο'],
            ['HyperX', 'HyperX Cloud Stinger 2', 'HSET-HYP-CLOUD-STINGER-2', 'hyperx-cloud-stinger-2-headset.jpg', 5900, 'Ενσύρματο'],
        ];

        foreach ($headsetProducts as [$brandName, $name, $sku, $imageName, $priceCents, $connection]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $headsets->id,
                    'name' => $name,
                    'image_path' => 'images/products/headsets/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 16,
                    'specs' => [
                        'connection' => $connection,
                        'form_factor' => 'Over-ear',
                        'microphone' => 'Ναι',
                    ],
                ]
            );
        }

        $monitorProducts = [
            ['ASUS', 'ASUS TUF Gaming VG34WQML5F', 'MON-ASUS-VG34WQML5F', 'tuf-gaming-vg34wqml5f.png', 79900, '34"', '3440 x 1440', '260Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG27WQ5F', 'MON-ASUS-VG27WQ5F', 'tuf-gaming-vg27wq5f.png', 39900, '27"', '2560 x 1440', '220Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG34WQ5F', 'MON-ASUS-VG34WQ5F', 'tuf-gaming-vg34wq5f.png', 54900, '34"', '3440 x 1440', '200Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG27VQM5F', 'MON-ASUS-VG27VQM5F', 'tuf-gaming-vg27vqm5f.png', 29900, '27"', '1920 x 1080', '280Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG27AQL5F', 'MON-ASUS-VG27AQL5F', 'tuf-gaming-vg27aql5f.png', 45900, '27"', '2560 x 1440', '220Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG249QM5F', 'MON-ASUS-VG249QM5F', 'tuf-gaming-vg249qm5f.png', 24900, '23.8"', '1920 x 1080', '260Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG27AQML5F', 'MON-ASUS-VG27AQML5F', 'tuf-gaming-vg27aqml5f.png', 59900, '27"', '2560 x 1440', '320Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG27AQMEL5F', 'MON-ASUS-VG27AQMEL5F', 'tuf-gaming-vg27aqmel5f.png', 49900, '27"', '2560 x 1440', '255Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG27AQM5F', 'MON-ASUS-VG27AQM5F', 'tuf-gaming-vg27aqm5f.png', 54900, '27"', '2560 x 1440', '320Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG27AQ5F', 'MON-ASUS-VG27AQ5F', 'tuf-gaming-vg27aq5f.png', 36900, '27"', '2560 x 1440', '220Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG27AQME5F', 'MON-ASUS-VG27AQME5F', 'tuf-gaming-vg27aqme5f.png', 42900, '27"', '2560 x 1440', '255Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG279QM5F', 'MON-ASUS-VG279QM5F', 'tuf-gaming-vg279qm5f.png', 32900, '27"', '1920 x 1080', '260Hz', 'IPS'],
            ['ASUS', 'ASUS TUF Gaming VG34WQ5A', 'MON-ASUS-VG34WQ5A', 'tuf-gaming-vg34wq5a.png', 49900, '34"', '3440 x 1440', '200Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG34WQL5A', 'MON-ASUS-VG34WQL5A', 'tuf-gaming-vg34wql5a.png', 54900, '34"', '3440 x 1440', '200Hz', 'VA'],
            ['ASUS', 'ASUS TUF Gaming VG27UQEL5A', 'MON-ASUS-VG27UQEL5A', 'tuf-gaming-vg27uqel5a.png', 64900, '27"', '3840 x 2160', '80Hz', 'IPS'],
            ['ASUS', 'ASUS ProArt Display OLED PA329CDV', 'MON-ASUS-PA329CDV', 'proart-display-oled-pa329cdv.png', 149900, '32"', '3840 x 2160', '120Hz', 'QD-OLED'],
            ['ASUS', 'ASUS ProArt Display OLED PA279CDV', 'MON-ASUS-PA279CDV', 'proart-display-oled-pa279cdv.png', 119900, '27"', '3840 x 2160', '120Hz', 'QD-OLED'],
            ['ASUS', 'ASUS ProArt Display PA249CGV', 'MON-ASUS-PA249CGV', 'proart-display-pa249cgv.png', 44900, '24"', '1920 x 1080', '144Hz', 'IPS'],
            ['ASUS', 'ASUS ProArt Display OLED PA32USD', 'MON-ASUS-PA32USD', 'proart-display-oled-pa32usd.png', 289900, '32"', '3840 x 2160', '240Hz', 'QD-OLED'],
            ['ASUS', 'ASUS ProArt Display OLED PA27USD', 'MON-ASUS-PA27USD', 'proart-display-oled-pa27usd.png', 269900, '27"', '3840 x 2160', '240Hz', 'QD-OLED'],
            ['ASUS', 'ASUS VY279HG2', 'MON-ASUS-VY279HG2', 'vy279hg2.png', 19900, '27"', '1920 x 1080', '144Hz', 'IPS'],
            ['ASUS', 'ASUS VA329QG3', 'MON-ASUS-VA329QG3', 'va329qg3.png', 25900, '31.5"', '1920 x 1080', '180Hz', 'VA'],
            ['ASUS', 'ASUS VY249HG2', 'MON-ASUS-VY249HG2', 'vy249hg2.png', 15900, '23.8"', '1920 x 1080', '144Hz', 'IPS'],
            ['ASUS', 'ASUS ZenScreen Duo MB14FCD', 'MON-ASUS-MB14FCD', 'zenscreen-duo-mb14fcd.png', 54900, '14"', '1920 x 1200', '60Hz', 'IPS'],
            ['ASUS', 'ASUS ZenScreen MB16AHVE', 'MON-ASUS-MB16AHVE', 'zenscreen-mb16ahve.png', 22900, '16"', '1920 x 1080', '60Hz', 'IPS'],
        ];

        foreach ($monitorProducts as [$brandName, $name, $sku, $imageName, $priceCents, $screenSize, $resolution, $refreshRate, $panelType]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $monitors->id,
                    'name' => $name,
                    'image_path' => 'images/products/monitors/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 12,
                    'specs' => [
                        'screen_size' => $screenSize,
                        'resolution' => $resolution,
                        'refresh_rate' => $refreshRate,
                        'panel_type' => $panelType,
                    ],
                ]
            );
        }
        $mouseProducts = [
            ['Razer', 'Razer Viper V3 Pro', 'MOUSE-RAZ-VIPER-V3-PRO', 'razer-viper-v3-pro.png', 17900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Viper V3 HyperSpeed', 'MOUSE-RAZ-VIPER-V3-HYPERSPEED', 'razer-viper-v3-hyperspeed.png', 7900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer DeathAdder V4 Pro', 'MOUSE-RAZ-DEATHADDER-V4-PRO', 'razer-deathadder-v4-pro.png', 18900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer DeathAdder V3 HyperSpeed', 'MOUSE-RAZ-DEATHADDER-V3-HYPERSPEED', 'razer-deathadder-v3-hyperspeed.png', 10900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer DeathAdder V3', 'MOUSE-RAZ-DEATHADDER-V3', 'razer-deathadder-v3.png', 7900, 'Ενσύρματο', 'Gaming'],
            ['Razer', 'Razer Basilisk Mobile', 'MOUSE-RAZ-BASILISK-MOBILE', 'razer-basilisk-mobile.png', 9900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Basilisk V3 Pro 35K', 'MOUSE-RAZ-BASILISK-V3-PRO-35K', 'razer-basilisk-v3-pro-35k.png', 17900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Basilisk V3 35K', 'MOUSE-RAZ-BASILISK-V3-35K', 'razer-basilisk-v3-35k.png', 8900, 'Ενσύρματο', 'Gaming'],
            ['Razer', 'Razer Cobra Pro', 'MOUSE-RAZ-COBRA-PRO', 'razer-cobra-pro.png', 14900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Cobra HyperSpeed', 'MOUSE-RAZ-COBRA-HYPERSPEED', 'razer-cobra-hyperspeed.png', 10900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Cobra', 'MOUSE-RAZ-COBRA', 'razer-cobra.png', 4900, 'Ενσύρματο', 'Gaming'],
            ['Razer', 'Razer Naga V2 Pro', 'MOUSE-RAZ-NAGA-V2-PRO', 'razer-naga-v2-pro.png', 18900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Naga V2 HyperSpeed', 'MOUSE-RAZ-NAGA-V2-HYPERSPEED', 'razer-naga-v2-hyperspeed.png', 10900, 'Ασύρματο', 'Gaming'],
            ['Razer', 'Razer Naga Left Handed Edition', 'MOUSE-RAZ-NAGA-LEFT-HANDED-EDITION', 'razer-naga-left-handed-edition.png', 11900, 'Ενσύρματο', 'Gaming'],
            ['Logitech', 'Logitech G502 X', 'MOUSE-LOG-G502-X', 'logitech-g502-x.jpg', 7900, 'Ενσύρματο', 'Gaming'],
            ['Logitech', 'Logitech G502 X LIGHTSPEED', 'MOUSE-LOG-G502-X-LS', 'logitech-g502-x.jpg', 12900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech G502 X PLUS', 'MOUSE-LOG-G502-X-PLUS', 'logitech-g502-x.jpg', 15900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech PRO X SUPERLIGHT 2', 'MOUSE-LOG-PRO-X-SL2', 'logitech-superlight-dex.jpg', 15900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech PRO X SUPERLIGHT 2 DEX', 'MOUSE-LOG-PRO-X-SL2-DEX', 'logitech-superlight-dex.jpg', 17900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech G309 LIGHTSPEED', 'MOUSE-LOG-G309', 'logitech-g-lineup.jpg', 8900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech G305 LIGHTSPEED', 'MOUSE-LOG-G305', 'logitech-g-lineup.jpg', 4900, 'Ασύρματο', 'Gaming'],
            ['Logitech', 'Logitech G203 LIGHTSYNC', 'MOUSE-LOG-G203', 'logitech-g-lineup.jpg', 2900, 'Ενσύρματο', 'Gaming'],
            ['Logitech', 'Logitech MX Master 3S', 'MOUSE-LOG-MX-MASTER-3S', 'logitech-mx-anywhere.jpg', 10900, 'Ασύρματο', 'Productivity'],
            ['Logitech', 'Logitech MX Anywhere 3S', 'MOUSE-LOG-MX-ANYWHERE-3S', 'logitech-mx-anywhere.jpg', 8900, 'Ασύρματο', 'Productivity'],
            ['Logitech', 'Logitech G703 LIGHTSPEED', 'MOUSE-LOG-G703', 'logitech-g-lineup.jpg', 9900, 'Ασύρματο', 'Gaming'],
        ];

        foreach ($mouseProducts as [$brandName, $name, $sku, $imageName, $priceCents, $connection, $useCase]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $mice->id,
                    'name' => $name,
                    'image_path' => 'images/products/mice/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 20,
                    'specs' => [
                        'connection' => $connection,
                        'sensor' => 'Optical',
                        'use_case' => $useCase,
                    ],
                ]
            );
        }

        $speakerProducts = [
            ['Logitech', 'Logitech Z120', 'SPK-LOG-Z120', 'logitech-z200.jpg', 1590, '2.0', 'USB / 3.5mm', '1.2W'],
            ['Logitech', 'Logitech Z150', 'SPK-LOG-Z150', 'logitech-z200.jpg', 2490, '2.0', '3.5mm', '3W'],
            ['Logitech', 'Logitech Z200', 'SPK-LOG-Z200', 'logitech-z200.jpg', 3990, '2.0', '3.5mm', '5W'],
            ['Logitech', 'Logitech Z207', 'SPK-LOG-Z207', 'logitech-z407.png', 5490, '2.0', 'Bluetooth / 3.5mm', '10W'],
            ['Logitech', 'Logitech Z313', 'SPK-LOG-Z313', 'logitech-z407.png', 5990, '2.1', '3.5mm', '25W'],
            ['Logitech', 'Logitech Z333', 'SPK-LOG-Z333', 'logitech-z407.png', 7990, '2.1', '3.5mm', '40W'],
            ['Logitech', 'Logitech Z407', 'SPK-LOG-Z407', 'logitech-z407.png', 11900, '2.1', 'Bluetooth / USB / 3.5mm', '40W'],
            ['Logitech', 'Logitech Z533', 'SPK-LOG-Z533', 'logitech-z407.png', 12900, '2.1', '3.5mm', '60W'],
            ['Logitech', 'Logitech Z625', 'SPK-LOG-Z625', 'logitech-z407.png', 19900, '2.1', 'Optical / 3.5mm', '200W'],
            ['Logitech', 'Logitech G560 LIGHTSYNC', 'SPK-LOG-G560', 'logitech-g560.png', 21900, '2.1', 'Bluetooth / USB', '120W'],
            ['Creative', 'Creative Pebble 2.0', 'SPK-CRE-PEBBLE', 'creative-pebble.png', 2490, '2.0', 'USB / 3.5mm', '4.4W'],
            ['Creative', 'Creative Pebble V2', 'SPK-CRE-PEBBLE-V2', 'creative-pebble.png', 3290, '2.0', 'USB-C / 3.5mm', '8W'],
            ['Creative', 'Creative Pebble V3', 'SPK-CRE-PEBBLE-V3', 'creative-pebble.png', 4490, '2.0', 'Bluetooth / USB-C', '8W'],
            ['Creative', 'Creative Pebble Pro', 'SPK-CRE-PEBBLE-PRO', 'creative-pebble.png', 6990, '2.0', 'Bluetooth / USB-C / 3.5mm', '20W'],
            ['Creative', 'Creative T60', 'SPK-CRE-T60', 'creative-t100.png', 7990, '2.0', 'Bluetooth / USB / 3.5mm', '30W'],
            ['Creative', 'Creative T100', 'SPK-CRE-T100', 'creative-t100.png', 10900, '2.0', 'Bluetooth / Optical / 3.5mm', '40W'],
            ['Creative', 'Creative Stage SE', 'SPK-CRE-STAGE-SE', 'creative-stage.jpg', 6990, 'Soundbar', 'Bluetooth / USB-C', '24W'],
            ['Creative', 'Creative Stage Air V2', 'SPK-CRE-STAGE-AIR-V2', 'creative-stage.jpg', 5990, 'Soundbar', 'Bluetooth / USB-C / 3.5mm', '20W'],
            ['Creative', 'Creative SBS E2900', 'SPK-CRE-SBS-E2900', 'creative-stage.jpg', 9990, '2.1', 'Bluetooth / USB / 3.5mm', '60W'],
            ['Creative', 'Creative Sound Blaster Katana SE', 'SPK-CRE-KATANA-SE', 'creative-stage.jpg', 29900, 'Soundbar', 'Bluetooth / USB / Optical', '90W'],
            ['Edifier', 'Edifier R19U', 'SPK-EDI-R19U', 'edifier-g2000.jpg', 2990, '2.0', 'USB / 3.5mm', '4W'],
            ['Edifier', 'Edifier R980T', 'SPK-EDI-R980T', 'edifier-r1280t.jpg', 7990, '2.0', 'RCA', '24W'],
            ['Edifier', 'Edifier R1280T', 'SPK-EDI-R1280T', 'edifier-r1280t.jpg', 10900, '2.0', 'RCA', '42W'],
            ['Edifier', 'Edifier R1700BT', 'SPK-EDI-R1700BT', 'edifier-r1280t.jpg', 15900, '2.0', 'Bluetooth / RCA', '66W'],
            ['Edifier', 'Edifier G2000', 'SPK-EDI-G2000', 'edifier-g2000.jpg', 9900, '2.0', 'Bluetooth / USB / 3.5mm', '16W'],
        ];

        foreach ($speakerProducts as [$brandName, $name, $sku, $imageName, $priceCents, $systemType, $connection, $outputPower]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $speakers->id,
                    'name' => $name,
                    'image_path' => 'images/products/speakers/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 14,
                    'specs' => [
                        'system_type' => $systemType,
                        'connection' => $connection,
                        'output_power' => $outputPower,
                    ],
                ]
            );
        }

        $joystickProducts = [
            ['Logitech', 'Logitech F310 Gamepad', 'JOY-LOG-F310', 'logitech-f310.jpg', 2990, 'Gamepad', 'Ενσύρματο', 'PC', 'Δόνηση'],
            ['Logitech', 'Logitech F710 Wireless Gamepad', 'JOY-LOG-F710', 'logitech-f310.jpg', 4990, 'Gamepad', 'Ασύρματο', 'PC', 'Δόνηση'],
            ['Logitech', 'Logitech Extreme 3D Pro', 'JOY-LOG-EXTREME-3D', 'logitech-extreme.png', 4490, 'Flight stick', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Logitech', 'Logitech X52 HOTAS', 'JOY-LOG-X52', 'logitech-x52.jpg', 16900, 'HOTAS', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Logitech', 'Logitech X52 Pro HOTAS', 'JOY-LOG-X52-PRO', 'logitech-x52.jpg', 21900, 'HOTAS', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Logitech', 'Logitech X56 HOTAS', 'JOY-LOG-X56', 'logitech-x52.jpg', 27900, 'HOTAS', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Logitech', 'Logitech G923 Racing Wheel', 'JOY-LOG-G923', 'logitech-g923.jpg', 34900, 'Racing wheel', 'Ενσύρματο', 'PC / PlayStation', 'TRUEFORCE'],
            ['Logitech', 'Logitech G29 Driving Force', 'JOY-LOG-G29', 'logitech-g923.jpg', 29900, 'Racing wheel', 'Ενσύρματο', 'PC / PlayStation', 'Force feedback'],
            ['Logitech', 'Logitech G920 Driving Force', 'JOY-LOG-G920', 'logitech-g923.jpg', 29900, 'Racing wheel', 'Ενσύρματο', 'PC / Xbox', 'Force feedback'],
            ['Logitech', 'Logitech G PRO Racing Wheel', 'JOY-LOG-G-PRO', 'logitech-g923.jpg', 109900, 'Racing wheel', 'Ενσύρματο', 'PC / Console', 'Direct Drive'],
            ['Thrustmaster', 'Thrustmaster T.16000M FCS', 'JOY-THR-T16000M', 'thrustmaster-hotas.webp', 7990, 'Flight stick', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Thrustmaster', 'Thrustmaster TCA Sidestick Airbus Edition', 'JOY-THR-TCA-AIRBUS', 'thrustmaster-tca.png', 8990, 'Flight stick', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Thrustmaster', 'Thrustmaster HOTAS Warthog', 'JOY-THR-WARTHOG', 'thrustmaster-warthog.png', 44900, 'HOTAS', 'Ενσύρματο', 'PC', 'Όχι'],
            ['Thrustmaster', 'Thrustmaster T.Flight Hotas 4', 'JOY-THR-HOTAS-4', 'thrustmaster-tflight.jpg', 8990, 'HOTAS', 'Ενσύρματο', 'PC / PlayStation', 'Όχι'],
            ['Thrustmaster', 'Thrustmaster T.Flight Hotas One', 'JOY-THR-HOTAS-ONE', 'thrustmaster-tflight.jpg', 9990, 'HOTAS', 'Ενσύρματο', 'PC / Xbox', 'Όχι'],
            ['Thrustmaster', 'Thrustmaster eSwap X2 Pro', 'JOY-THR-ESWAP-X2', 'thrustmaster-eswap.jpg', 17900, 'Gamepad', 'Ενσύρματο', 'PC / Xbox', 'Δόνηση'],
            ['Thrustmaster', 'Thrustmaster GP XID Pro', 'JOY-THR-GP-XID', 'thrustmaster-eswap.jpg', 2990, 'Gamepad', 'Ενσύρματο', 'PC', 'Δόνηση'],
            ['Thrustmaster', 'Thrustmaster Ferrari 458 Spider', 'JOY-THR-F458', 'thrustmaster-t248-correct.jpg', 12900, 'Racing wheel', 'Ενσύρματο', 'Xbox', 'Bungee cord'],
            ['Thrustmaster', 'Thrustmaster TMX Force Feedback', 'JOY-THR-TMX', 'thrustmaster-t248-correct.jpg', 22900, 'Racing wheel', 'Ενσύρματο', 'PC / Xbox', 'Force feedback'],
            ['Thrustmaster', 'Thrustmaster T248', 'JOY-THR-T248', 'thrustmaster-t248-correct.jpg', 32900, 'Racing wheel', 'Ενσύρματο', 'PC / Console', 'Force feedback'],
            ['Xbox', 'Xbox Wireless Controller', 'JOY-XBOX-WIRELESS', 'xbox-controller.png', 6490, 'Gamepad', 'Ασύρματο', 'PC / Xbox', 'Δόνηση'],
            ['Xbox', 'Xbox Elite Wireless Controller Series 2', 'JOY-XBOX-ELITE-2', 'xbox-controller.png', 17900, 'Gamepad', 'Ασύρματο', 'PC / Xbox', 'Δόνηση'],
            ['Sony', 'Sony DualSense Wireless Controller', 'JOY-SONY-DUALSENSE', 'dualsense-edge.png', 7490, 'Gamepad', 'Ασύρματο', 'PC / PlayStation', 'Haptic feedback'],
            ['Sony', 'Sony DualSense Edge', 'JOY-SONY-DUALSENSE-EDGE', 'dualsense-edge.png', 23900, 'Gamepad', 'Ασύρματο', 'PC / PlayStation', 'Haptic feedback'],
            ['8BitDo', '8BitDo Ultimate 2.4G', 'JOY-8BITDO-ULTIMATE', '8bitdo-ultimate.jpg', 5990, 'Gamepad', 'Ασύρματο', 'PC / Switch', 'Δόνηση'],
        ];

        foreach ($joystickProducts as [$brandName, $name, $sku, $imageName, $priceCents, $controllerType, $connection, $compatibility, $feedback]) {
            Product::query()->updateOrCreate(
                ['sku' => $sku],
                [
                    'brand_id' => $brands[$brandName]->id,
                    'category_id' => $joysticks->id,
                    'name' => $name,
                    'image_path' => 'images/products/joysticks/'.$imageName,
                    'price_cents' => $priceCents,
                    'stock' => 12,
                    'specs' => [
                        'controller_type' => $controllerType,
                        'connection' => $connection,
                        'compatibility' => $compatibility,
                        'feedback' => $feedback,
                    ],
                ]
            );
        }

        Product::query()->update([
            'sale_price_cents' => null,
            'sale_discount_percent' => null,
        ]);

        $offerDiscounts = collect([12, 15, 18, 20, 22, 10, 14, 17, 19, 25]);
        $offerCategorySlugs = [
            'processors',
            'graphics-cards',
            'storage',
            'cases',
            'ram',
            'motherboards',
            'power-supplies',
            'monitors',
            'mice',
            'joysticks',
        ];

        Product::query()
            ->with('category:id,slug')
            ->whereHas('category', fn ($query) => $query->whereIn('slug', $offerCategorySlugs))
            ->orderBy('category_id')
            ->orderBy('id')
            ->get()
            ->toBase()
            ->groupBy(fn (Product $product): ?string => $product->category?->slug)
            ->only($offerCategorySlugs)
            ->map(fn ($products) => $products->take(3)->values())
            ->collapse()
            ->values()
            ->each(function (Product $product, int $index) use ($offerDiscounts): void {
                $discountPercent = $offerDiscounts[$index % $offerDiscounts->count()];

                $product->update([
                    'sale_price_cents' => (int) round($product->price_cents * (100 - $discountPercent) / 100),
                    'sale_discount_percent' => $discountPercent,
                ]);
            });
    }
}
