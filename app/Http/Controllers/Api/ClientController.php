<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected $service;
    public function __construct(ClientService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $filters = [
            'q' => $request->input('q'),
            'status' => $request->input('status'),
            'responsible_id' => $request->input('responsible_id'),
        ];
        $perPage = (int)$request->input('per_page', 20);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'desc');

        $clients = $this->service->paginate($filters, $perPage, $sort, $order);

        return response()->json($clients);
    }

    public function show(Client $client)
    {
        $client->load(['responsible','tasks','files','interactions','deals','invoices','logs']);
        return response()->json($client);
    }

    // store/update/destroy — аналогично веб-методам, возвращают json
}
