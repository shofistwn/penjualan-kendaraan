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
    $formData = $this->prepareVehicleData();
    self::$vehicleId = $this->vehicleService->addVehicle($formData);

    $response = $this->getVehicleStockRequest();

    $response->assertStatus(200);
    $response->assertJsonStructure($this->getVehicleStockJsonStructure());
  }

  public function testSellVehicle()
  {
    $id = self::$vehicleId;
    $response = $this->sellVehicleRequest($id);

    $this->assertVehicleSold($id);
    $response->assertStatus(200);
    $response->assertJson($this->getSellVehicleJsonData());
  }

  public function testSalesReport()
  {
    $response = $this->getSalesReportRequest();

    $response->assertStatus(200);
    $response->assertJsonStructure($this->getSalesReportJsonStructure());
  }

  protected function prepareVehicleData()
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

  protected function getVehicleStockJsonStructure()
  {
    return [
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
    ];
  }

  protected function sellVehicleRequest($id)
  {
    return $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/sell', ['kendaraan_id' => $id]);
  }

  protected function assertVehicleSold($id)
  {
    $vehicle = $this->vehicleService->findById($id);
    $this->vehicleService->sellVehicle($vehicle);
    $updatedVehicle = $this->vehicleService->findById($id);
    $this->assertTrue($updatedVehicle['terjual']);
  }

  protected function getSellVehicleJsonData()
  {
    return [
      'success' => true,
      'message' => 'Kendaraan terjual',
      'data' => [
        '_id' => self::$vehicleId,
        'harga' => '40000000',
        'motor' => [
          'mesin' => '400cc',
          'tipe_suspensi' => 'Mono Shock',
          'tipe_transmisi' => 'Manual'
        ],
        'tahun_keluaran' => '2023',
        'terjual' => true,
        'tipe_kendaraan' => 'motor',
        'warna' => 'Biru'
      ],
    ];
  }

  protected function getSalesReportRequest()
  {
    return $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->get('/api/v1/vehicles/sales-report');
  }

  protected function getSalesReportJsonStructure()
  {
    return [
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
    ];
  }
}