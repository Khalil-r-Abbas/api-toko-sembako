<?php

namespace App\Libraries;

/**
 * Sumber resep. Saat ini memakai data dummy (all()).
 * Ketika API resep siap, cukup ganti isi all() dengan pemanggilan API
 * (atau ganti findByProducts() bila API sudah melakukan pencocokan sendiri).
 * Format keluaran ke view tetap sama.
 */
class RecipeService
{
    /**
     * Resep yang dikembalikan HARUS memuat semua produk terpilih.
     * Bila resep masih punya bahan lain yang belum dipilih => complete=false ("Kurang bahan").
     * Urutan: yang lengkap dulu, lalu yang paling sedikit kurangnya.
     */
    public function findByProducts(array $selected): array
    {
        $sel = array_values(array_unique(array_map([$this, 'norm'], $selected)));
        if (! $sel) {
            return [];
        }

        $out = [];
        foreach ($this->all() as $r) {
            $names = array_map(fn ($i) => $this->norm($i['name']), $r['ingredients']);

            if (array_diff($sel, $names)) {
                continue; // ada produk terpilih yang tidak ada di resep ini
            }

            $ingredients = [];
            $missing     = [];
            foreach ($r['ingredients'] as $i) {
                $has = in_array($this->norm($i['name']), $sel, true);
                if (! $has) {
                    $missing[] = $i['name'];
                }
                $ingredients[] = $i + ['selected' => $has];
            }

            $out[] = [
                'id'          => $r['id'],
                'name'        => $r['name'],
                'minutes'     => $r['minutes'],
                'ingredients' => $ingredients,
                'steps'       => $r['steps'],
                'missing'     => $missing,
                'complete'    => $missing === [],
            ];
        }

        usort($out, static fn ($a, $b) => count($a['missing']) <=> count($b['missing']));

        return $out;
    }

    private function norm(string $s): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $s)));
    }

    /** DUMMY: nama bahan harus sama dengan nama produk di database (tidak peka huruf besar/kecil). */
    private function all(): array
    {
        return [
            [
                'id' => 1, 'name' => 'Nasi Goreng Telur', 'minutes' => 15,
                'ingredients' => [
                    ['name' => 'Nasi putih', 'qty' => '2 piring'],
                    ['name' => 'Telur', 'qty' => '2 butir'],
                    ['name' => 'Bawang putih', 'qty' => '3 siung'],
                    ['name' => 'Bawang merah', 'qty' => '4 siung'],
                    ['name' => 'Kecap manis', 'qty' => '2 sdm'],
                    ['name' => 'Garam', 'qty' => '1/2 sdt'],
                    ['name' => 'Minyak goreng', 'qty' => '3 sdm'],
                ],
                'steps' => [
                    'Haluskan bawang putih dan bawang merah, tumis dengan minyak hingga harum.',
                    'Masukkan telur, orak-arik hingga setengah matang.',
                    'Tambahkan nasi, kecap manis, dan garam. Aduk rata.',
                    'Masak 3–4 menit hingga nasi kering dan harum, lalu sajikan.',
                ],
            ],
            [
                'id' => 2, 'name' => 'Telur Dadar', 'minutes' => 7,
                'ingredients' => [
                    ['name' => 'Telur', 'qty' => '3 butir'],
                    ['name' => 'Garam', 'qty' => '1/4 sdt'],
                    ['name' => 'Minyak goreng', 'qty' => '2 sdm'],
                ],
                'steps' => [
                    'Kocok telur bersama garam.',
                    'Panaskan minyak, tuang telur, masak dengan api sedang.',
                    'Balik saat bagian bawah matang, angkat dan sajikan.',
                ],
            ],
            [
                'id' => 3, 'name' => 'Mie Goreng Telur', 'minutes' => 12,
                'ingredients' => [
                    ['name' => 'Mie instan', 'qty' => '1 bungkus'],
                    ['name' => 'Telur', 'qty' => '1 butir'],
                    ['name' => 'Bawang putih', 'qty' => '2 siung'],
                    ['name' => 'Cabai', 'qty' => '3 buah'],
                    ['name' => 'Tomat', 'qty' => '1 buah'],
                    ['name' => 'Kecap manis', 'qty' => '1 sdm'],
                    ['name' => 'Minyak goreng', 'qty' => '2 sdm'],
                ],
                'steps' => [
                    'Rebus mie setengah matang, tiriskan.',
                    'Tumis bawang putih dan cabai, masukkan tomat dan telur.',
                    'Tambahkan mie, bumbu mie, dan kecap. Aduk hingga rata.',
                ],
            ],
            [
                'id' => 4, 'name' => 'Tumis Tempe Kecap', 'minutes' => 15,
                'ingredients' => [
                    ['name' => 'Tempe', 'qty' => '1 papan'],
                    ['name' => 'Bawang merah', 'qty' => '3 siung'],
                    ['name' => 'Bawang putih', 'qty' => '2 siung'],
                    ['name' => 'Cabai', 'qty' => '4 buah'],
                    ['name' => 'Kecap manis', 'qty' => '3 sdm'],
                    ['name' => 'Minyak goreng', 'qty' => '3 sdm'],
                ],
                'steps' => [
                    'Potong tempe dadu, goreng hingga kecokelatan.',
                    'Tumis bawang dan cabai hingga harum.',
                    'Masukkan tempe dan kecap, aduk hingga bumbu meresap.',
                ],
            ],
            [
                'id' => 5, 'name' => 'Ayam Goreng Bawang', 'minutes' => 35,
                'ingredients' => [
                    ['name' => 'Ayam', 'qty' => '500 g'],
                    ['name' => 'Bawang putih', 'qty' => '4 siung'],
                    ['name' => 'Kunyit bubuk', 'qty' => '1/2 sdt'],
                    ['name' => 'Garam', 'qty' => '1 sdt'],
                    ['name' => 'Minyak goreng', 'qty' => '200 ml'],
                ],
                'steps' => [
                    'Haluskan bawang putih, campur dengan kunyit dan garam.',
                    'Lumuri ayam, diamkan 15 menit.',
                    'Goreng dalam minyak panas hingga matang kecokelatan.',
                ],
            ],
        ];
    }
}
