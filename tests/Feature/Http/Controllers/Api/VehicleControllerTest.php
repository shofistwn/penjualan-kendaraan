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

    $vehicleRepository = new VehicleRepository();
    $this->vehicleService = new VehicleService($vehicleRepository);

    $payload = ['email' => 'user@mail.com', 'password' => 'password'];
    $this->token = auth()->attempt($payload);
  }

  public function testGetVehicleStock()
  {
    $formData = $this->getVehicleFormData();
    self::$vehicleId = $this->vehicleService->addVehicle($formData);

    $response = $this->getVehicleStockRequest();

    $response->assertStatus(200);
    $response->assertJsonStructure([
      'success',
      'message',
      'data' => [
        '*' => [
          'harga',
          'tahun_keluaran',
          'terjual',
          'tipe_kendaraan',
          'warna',
        ],
      ],
    ]);
  }

  public function testSellVehicleWithSuccessfully()
  {
    $id = self::$vehicleId;

    $response = $this->sellVehicleRequest(['kendaraan_id' => $id]);

    $this->assertVehicleSold($id);

    $response->assertStatus(200);
    $response->assertJson([
      'success' => true,
      'message' => 'Kendaraan terjual',
      'data' => $this->getExpectedVehicleData($id, true),
    ]);
  }

  public function testSellVehicleValidationError()
  {
    $response = $this->sellVehicleRequest([]);

    $response->assertStatus(422);
    $response->assertJson([
      "success" => false,
      "message" => "Validasi gagal",
      "errors" => [
        "kendaraan_id" => [
          "The kendaraan id field is required."
        ]
      ]
    ]);
  }

  public function testSellVehicleWithNotFoundVehicle()
  {
    $response = $this->sellVehicleRequest(['kendaraan_id' => '1']);

    $response->assertStatus(404);
    $response->assertJson([
      "success" => false,
      "message" => "Kendaraan tidak ditemukan"
    ]);
  }

  public function testSellVehicleWithAlreadySoldVehicle()
  {
    $id = self::$vehicleId;
    $this->assertVehicleSold($id);

    $response = $this->sellVehicleRequest(['kendaraan_id' => $id]);

    $response->assertStatus(404);
    $response->assertJson([
      "success" => false,
      "message" => "Kendaraan sudah terjual"
    ]);
  }

  public function testSalesReport()
  {
    $response = $this->getSalesReportRequest();

    $response->assertStatus(200);
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

  protected function getVehicleFormData()
  {
    return [
      "harga" => "40000000",
      "mesin" => "400cc",
      "tipe_suspensi" => "Mono Shock",
      "tipe_transmisi" => "Manual",
      "tahun_keluaran" => "2023",
      "terjual" => false,
      "tipe_kendaraan" => "motor",
      "warna" => "Biru"
    ];
  }

  protected function getVehicleStockRequest()
  {
    return $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->get('/api/v1/vehicles/stock');
  }

  protected function sellVehicleRequest($data)
  {
    return $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/sell', $data);
  }

  protected function getSalesReportRequest()
  {
    return $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->get('/api/v1/vehicles/sales-report');
  }

  protected function assertVehicleSold($id)
  {
    $vehicle = $this->vehicleService->findById($id);
    $this->vehicleService->sellVehicle($vehicle);
    $updatedVehicle = $this->vehicleService->findById($id);
    $this->assertTrue($updatedVehicle['terjual']);
  }

  protected function getExpectedVehicleData($id, $isSold)
  {
    return [
      '_id' => $id,
      'harga' => '40000000',
      'motor' => [
        'mesin' => '400cc',
        'tipe_suspensi' => 'Mono Shock',
        'tipe_transmisi' => 'Manual'
      ],
      'tahun_keluaran' => '2023',
      'terjual' => $isSold,
      'tipe_kendaraan' => 'motor',
      'warna' => 'Biru'
    ];
  }
}