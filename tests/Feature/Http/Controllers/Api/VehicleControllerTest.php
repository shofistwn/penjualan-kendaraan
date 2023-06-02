<?php

namespace Tests\Feature\Api;

use App\Services\VehicleService;
use App\Repositories\VehicleRepository;
use Tests\TestCase;

class VehicleControllerTest extends TestCase
{
  private $vehicleService;
  private $token;
  private static $vehicleId;

  protected function setUp(): void
  {
    parent::setUp();

    $vehicleRepository = new VehicleRepository();
    $this->vehicleService = new VehicleService($vehicleRepository);

    $payload = ['email' => 'user@mail.com', 'password' => 'password'];
    $this->token = auth()->attempt($payload);
  }

  public function testAddMotorSuccess()
  {
    $requestData = [
      'tahun_keluaran' => '2018',
      'warna' => 'Putih',
      'harga' => '40000000',
      'mesin' => '400cc',
      'tipe_suspensi' => 'Mono Shock',
      'tipe_transmisi' => 'Automatic',
    ];

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/add-motor', $requestData);

    $response->assertStatus(200);
  }

  public function testAddMotorValidationFailed()
  {
    $requestData = [
      'tahun_keluaran' => '2018',
      'warna' => 'Putih',
      'harga' => '40000000',
      'tipe_suspensi' => 'Mono Shock',
      'tipe_transmisi' => 'Automatic',
    ];

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/add-motor', $requestData);

    $response->assertStatus(422);
    $response->assertJson([
      "success" => false,
      "message" => "Validasi gagal",
      "errors" => [
        "mesin" => [
          "The mesin field is required."
        ]
      ]
    ]);
  }

  public function testAddMotorInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->postJson('/api/v1/vehicles/add-motor');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
    ]);
  }

  public function testAddCarSuccess()
  {
    $requestData = [
      'tahun_keluaran' => 2022,
      'warna' => 'Putih',
      'harga' => 300000000,
      'mesin' => '2000cc',
      'kapasitas_penumpang' => 5,
      'tipe' => 'MPV',
    ];

    $response = $this->postJson('/api/v1/vehicles/add-car', $requestData);

    $response->assertStatus(200);
  }

  public function testAddCarValidationFailed()
  {
    $requestData = [
      'tahun_keluaran' => 2022,
      'warna' => 'Putih',
      'harga' => 300000000,
      'kapasitas_penumpang' => 5,
      'tipe' => 'MPV',
    ];

    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . $this->token,
    ])->postJson('/api/v1/vehicles/add-car', $requestData);

    $response->assertStatus(422);
    $response->assertJson([
      "success" => false,
      "message" => "Validasi gagal",
      "errors" => [
        "mesin" => [
          "The mesin field is required."
        ]
      ]
    ]);
  }

  public function testAddCarInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->postJson('/api/v1/vehicles/add-car');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
    ]);
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

  public function testGetVehicleStockInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->get('/api/v1/vehicles/stock');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
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

  public function testSellVehicleValidationFailed()
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

  public function testSellVehicleInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->postJson('/api/v1/vehicles/sell');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
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

  public function testSalesReportInvalidToken()
  {
    $response = $this->withHeaders([
      'Authorization' => 'Bearer ' . 'salahhtoken',
    ])->get('/api/v1/vehicles/sales-report');

    $response->assertStatus(200);
    $response->assertJson([
      "status" => "Token is Invalid"
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