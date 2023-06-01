<?php

namespace Tests\Feature\Api;

use App\Services\VehicleService;
use App\Repositories\VehicleRepository;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
  protected $vehicleService;
  protected $token;
  protected static $vehicleId;

  protected function setUp(): void
  {
    parent::setUp();

    // Inisialisasi database MongoDB dan koneksi
    $vehicleRepository = new VehicleRepository();
    $this->vehicleService = new VehicleService($vehicleRepository);

    // Generate JWT token untuk otentikasi
    $payload = ['email' => 'user@mail.com', 'password' => 'password'];
    $this->token = auth()->attempt($payload);
  }

  public function testGetVehicleStock()
  {
    // Persiapkan data kendaraan di database
    $formData = [
      "harga" => "1000000",
      "mesin" => "honda",
      "tipe_suspensi" => "K",
      "tipe_transmisi" => "manual",
      "tahun_keluaran" => "2023",
      "terjual" => false,
      "tipe_kendaraan" => "motor",
      "warna" => "Merah"
    ];

    // Menyimpan data kendaraan ke dalam database
    self::$vehicleId = $this->vehicleService->addVehicle($formData);

    // Kirim permintaan GET ke endpoint /api/v1/vehicles/stock dengan token otentikasi
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->get('/api/v1/vehicles/stock');

    // Pastikan respons berhasil dengan kode status 200
    $response->assertStatus(200);

    // Pastikan respons JSON sesuai dengan struktur yang diharapkan
    $response->assertJsonStructure([
      'success',
      'message',
      'data' => [
        '*' => [
          'harga',
          'motor' => [
            'mesin',
            'tipe_suspensi',
            'tipe_transmisi',
          ],
          'tahun_keluaran',
          'terjual',
          'tipe_kendaraan',
          'warna',
        ],
      ],
    ]);
  }

  public function testSellVehicle()
  {
    $id = self::$vehicleId;

    // Kirim permintaan POST ke endpoint /api/sell dengan token otentikasi
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/sell', ['kendaraan_id' => $id]);


    // Pastikan data kendaraan di database telah diperbarui
    $vehicle = $this->vehicleService->findById($id);
    $this->vehicleService->sellVehicle($vehicle);
    $updatedVehicle = $this->vehicleService->findById($id);
    $this->assertTrue($updatedVehicle['terjual']);

    // Pastikan respons berhasil dengan kode status 200
    $response->assertStatus(200);

    // Pastikan respons JSON sesuai dengan data yang diharapkan
    $response->assertJson([
      'success' => true,
      'message' => 'Vehicle sold successfully',
      'data' => [
        '_id' => self::$vehicleId,
        'harga' => '1000000',
        'motor' => [
          'mesin' => 'honda',
          'tipe_suspensi' => 'K',
          'tipe_transmisi' => 'manual'
        ],
        'tahun_keluaran' => '2023',
        'terjual' => true,
        'tipe_kendaraan' => 'motor',
        'warna' => 'Merah'
      ],
    ]);
  }

  public function testSalesReport()
  {
    // Kirim permintaan GET ke endpoint /api/v1/sales-report dengan token otentikasi
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->get('/api/v1/vehicles/sales-report');

    // Pastikan respons berhasil dengan kode status 200
    $response->assertStatus(200);

    // Pastikan respons JSON sesuai dengan struktur yang diharapkan
    $response->assertJsonStructure([
      'success',
      'message',
      'data' => [
        'mobil' => [
          'terjual',
          'tersisa',
          'total',
        ],
        'motor' => [
          'terjual',
          'tersisa',
          'total',
        ],
      ],
    ]);

  }
}